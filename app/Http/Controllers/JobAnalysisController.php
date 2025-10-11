<?php

namespace App\Http\Controllers;

use App\Services\DocumentTextExtractor;
use App\Services\JobAnalysisService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class JobAnalysisController extends Controller
{
    public function __construct(
        protected JobAnalysisService $jobAnalysisService,
        protected DocumentTextExtractor $documentTextExtractor
    ) {}

    public function index(): Response
    {
        return Inertia::render('JobAnalysis/Index');
    }

    /**
     * Process pending job analysis after login.
     */
    public function processPendingAnalysis(Request $request)
    {
        $pendingData = session('pending_job_analysis');

        if (! $pendingData) {
            return redirect()->route('job.index');
        }

        try {
            $analysis = $this->jobAnalysisService->analyzeJobAd(
                $pendingData['jobAdText'],
                $pendingData['jobTitle'] ?? null,
                $pendingData['company'] ?? null
            );

            // Clear pending analysis from session
            session()->forget('pending_job_analysis');

            return Inertia::render('JobAnalysis/Results', [
                'analysis' => $analysis,
                'originalText' => $pendingData['jobAdText'],
            ]);
        } catch (\Exception $e) {
            session()->forget('pending_job_analysis');

            return redirect()->route('job.index')->withErrors([
                'jobAdText' => 'Failed to analyze job advertisement. Please try again.',
            ]);
        }
    }

    public function analyze(Request $request)
    {
        $validated = $request->validate([
            'jobTitle' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'jobAdText' => ['required', 'string', 'min:50', 'max:10000'],
        ]);

        // Check if user is authenticated
        if (! auth()->check()) {
            // Store the job analysis data in session
            session([
                'pending_job_analysis' => $validated,
                'intended_url' => route('job.analyze'),
            ]);

            return redirect()->route('register')
                ->with('message', 'Please register to analyze job advertisements.');
        }

        try {
            $analysis = $this->jobAnalysisService->analyzeJobAd(
                $validated['jobAdText'],
                $validated['jobTitle'] ?? null,
                $validated['company'] ?? null
            );

            // Clear pending analysis if exists
            session()->forget('pending_job_analysis');

            return Inertia::render('JobAnalysis/Results', [
                'analysis' => $analysis,
                'originalText' => $validated['jobAdText'],
            ]);
        } catch (\Exception $e) {
            return back()->withErrors([
                'jobAdText' => 'Failed to analyze job advertisement. Please try again.',
            ]);
        }
    }

    public function assessResume(Request $request)
    {
        // Clean up empty strings to null for proper validation
        $request->merge([
            'resumeText' => $request->resumeText ?: null,
        ]);

        \Log::debug('Assessment request data:', [
            'resumeText' => $request->resumeText ? 'present ('.strlen($request->resumeText).' chars)' : 'missing',
            'resumeFile' => $request->hasFile('resumeFile') ? 'file present' : 'no file',
            'jobAdText' => $request->jobAdText ? 'present' : 'missing',
            'jobTitle' => $request->jobTitle,
            'company' => $request->company,
        ]);

        $validated = $request->validate([
            'resumeText' => ['nullable', 'required_without:resumeFile', 'string', 'min:50', 'max:10000'],
            'resumeFile' => ['nullable', 'required_without:resumeText', 'file', 'mimes:pdf,doc,docx,txt', 'max:5120'],
            'jobAdText' => ['required', 'string'],
            'jobTitle' => ['nullable', 'string'],
            'company' => ['nullable', 'string'],
        ]);

        try {
            $resumeText = $validated['resumeText'] ?? null;

            // If file uploaded, extract text from it
            if (! $resumeText && $request->hasFile('resumeFile')) {
                try {
                    $resumeText = $this->documentTextExtractor->extractText($validated['resumeFile']);
                    \Log::debug('Extracted text from file', [
                        'filename' => $validated['resumeFile']->getClientOriginalName(),
                        'length' => strlen($resumeText),
                    ]);
                } catch (\Exception $e) {
                    return back()->withErrors([
                        'resumeFile' => $e->getMessage(),
                    ]);
                }
            }

            // If no text and no file, this shouldn't happen due to validation but handle it
            if (! $resumeText) {
                return back()->withErrors([
                    'resumeText' => 'Please provide your resume text.',
                ]);
            }

            $assessment = $this->jobAnalysisService->assessResume(
                $resumeText,
                $validated['jobAdText']
            );

            return Inertia::render('JobAnalysis/Assessment', [
                'assessment' => $assessment,
                'jobInfo' => [
                    'jobTitle' => $validated['jobTitle'] ?? 'Job Position',
                    'company' => $validated['company'] ?? 'Company',
                ],
            ]);
        } catch (\Exception $e) {
            return back()->withErrors([
                'resumeText' => 'Failed to assess resume. Please try again.',
            ]);
        }
    }
}
