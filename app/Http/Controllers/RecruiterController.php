<?php

namespace App\Http\Controllers;

use App\Services\DocumentTextExtractor;
use App\Services\JobAnalysisService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RecruiterController extends Controller
{
    public function __construct(
        protected JobAnalysisService $jobAnalysisService,
        protected DocumentTextExtractor $documentTextExtractor
    ) {}

    public function index(): Response
    {
        // Set session to indicate user is on recruiter flow
        // If they register, they'll get the recruiter role
        session(['registration_role' => 'recruiter']);

        return Inertia::render('Recruiter/Index');
    }

    public function processPendingMatch(Request $request)
    {
        $pendingData = session('pending_recruiter_match');

        if (! $pendingData) {
            return redirect()->route('recruiter.index');
        }

        try {
            // Run AI assessment with recruiter-specific prompt (3rd person)
            $assessment = $this->jobAnalysisService->assessCandidateForRecruiter(
                $pendingData['candidateResume'],
                $pendingData['jobDescription']
            );

            // Clear pending match from session
            session()->forget('pending_recruiter_match');

            return Inertia::render('Recruiter/MatchResult', [
                'assessment' => $assessment,
                'jobDescription' => $pendingData['jobDescription'],
                'candidateResume' => $pendingData['candidateResume'],
            ]);
        } catch (\Exception $e) {
            \Log::error('Pending recruiter match processing error: '.$e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            session()->forget('pending_recruiter_match');

            return redirect()->route('recruiter.index')->withErrors([
                'jobDescription' => 'Failed to calculate match score. Please try again.',
            ]);
        }
    }

    public function calculateMatch(Request $request)
    {
        // Clean up empty strings to null for proper validation
        $request->merge([
            'candidateResume' => $request->candidateResume ?: null,
        ]);

        $validated = $request->validate([
            'jobDescription' => ['required', 'string', 'min:50', 'max:10000'],
            'candidateResume' => ['nullable', 'required_without:resumeFile', 'string', 'min:50', 'max:10000'],
            'resumeFile' => ['nullable', 'required_without:candidateResume', 'file', 'mimes:pdf,doc,docx,txt', 'max:5120'],
        ]);

        // Check if user is authenticated
        if (! auth()->check()) {
            // Extract text from file if uploaded
            $candidateResumeText = $validated['candidateResume'] ?? null;

            if (! $candidateResumeText && $request->hasFile('resumeFile')) {
                try {
                    $candidateResumeText = $this->documentTextExtractor->extractText($validated['resumeFile']);
                } catch (\Exception $e) {
                    return back()->withErrors([
                        'resumeFile' => $e->getMessage(),
                    ]);
                }
            }

            // Store the match data in session
            session([
                'pending_recruiter_match' => [
                    'jobDescription' => $validated['jobDescription'],
                    'candidateResume' => $candidateResumeText,
                ],
            ]);

            return redirect()->route('register')
                ->with('message', 'Please register to see the match results.');
        }

        try {
            $candidateResume = $validated['candidateResume'] ?? null;

            // If file uploaded, extract text from it
            if (! $candidateResume && $request->hasFile('resumeFile')) {
                try {
                    $candidateResume = $this->documentTextExtractor->extractText($validated['resumeFile']);
                    \Log::debug('Extracted text from file', [
                        'filename' => $validated['resumeFile']->getClientOriginalName(),
                        'length' => strlen($candidateResume),
                    ]);
                } catch (\Exception $e) {
                    return back()->withErrors([
                        'resumeFile' => $e->getMessage(),
                    ]);
                }
            }

            // If no text and no file, this shouldn't happen due to validation but handle it
            if (! $candidateResume) {
                return back()->withErrors([
                    'candidateResume' => 'Please provide candidate resume text or upload a file.',
                ]);
            }

            // Run AI assessment with recruiter-specific prompt (3rd person)
            $assessment = $this->jobAnalysisService->assessCandidateForRecruiter(
                $candidateResume,
                $validated['jobDescription']
            );

            // For authenticated recruiters, optionally save to database
            // Note: We don't save recruiter assessments to the database by default
            // since they're typically ad-hoc evaluations without stored job postings
            // If needed in the future, we can add job posting and resume storage here

            return Inertia::render('Recruiter/MatchResult', [
                'assessment' => $assessment,
                'jobDescription' => $validated['jobDescription'],
                'candidateResume' => $candidateResume,
            ]);
        } catch (\Exception $e) {
            \Log::error('Recruiter match calculation error: '.$e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'jobDescription' => 'Failed to calculate match score. Please try again.',
            ]);
        }
    }
}
