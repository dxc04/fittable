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

        try {
            $candidateResume = $validated['candidateResume'] ?? null;

            // If file uploaded, extract text from it
            if (! $candidateResume && $request->hasFile('resumeFile')) {
                try {
                    $candidateResume = $this->documentTextExtractor->extractText($validated['resumeFile']);
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

            $assessment = $this->jobAnalysisService->assessResume(
                $candidateResume,
                $validated['jobDescription']
            );

            return Inertia::render('Recruiter/MatchResult', [
                'assessment' => $assessment,
                'jobDescription' => $validated['jobDescription'],
                'candidateResume' => $candidateResume,
            ]);
        } catch (\Exception $e) {
            return back()->withErrors([
                'jobDescription' => 'Failed to calculate match score. Please try again.',
            ]);
        }
    }
}
