<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\JobAnalysis;
use App\Models\JobPosting;
use App\Models\Resume;
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
        // Set session to indicate user is on job seeker flow
        // If they register, they'll get the job_seeker role
        session(['registration_role' => 'job_seeker']);

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
            // Check if job posting already exists for this user
            $jobPosting = JobPosting::where('user_id', auth()->id())
                ->where('original_text', $pendingData['jobAdText'])
                ->first();

            if ($jobPosting) {
                // Check if analysis exists
                $jobAnalysis = $jobPosting->jobAnalysis;

                if ($jobAnalysis) {
                    // Return existing analysis without calling AI
                    $analysis = [
                        'jobTitle' => $jobPosting->job_title,
                        'company' => $jobPosting->company,
                        'companyBackground' => $jobAnalysis->company_background,
                        'location' => $jobAnalysis->location,
                        'jobType' => $jobAnalysis->job_type,
                        'summary' => $jobAnalysis->summary,
                        'requiredSkills' => $jobAnalysis->required_skills,
                        'niceToHaveSkills' => $jobAnalysis->nice_to_have_skills,
                        'responsibilities' => $jobAnalysis->responsibilities,
                        'requirements' => $jobAnalysis->requirements,
                        'benefits' => $jobAnalysis->benefits,
                        'salaryRange' => $jobAnalysis->salary_range,
                        'hiringProcess' => $jobAnalysis->hiring_process,
                        'warnings' => $jobAnalysis->warnings ?? [],
                    ];

                    session()->forget('pending_job_analysis');

                    return Inertia::render('JobAnalysis/Results', [
                        'analysis' => $analysis,
                        'originalText' => $pendingData['jobAdText'],
                        'jobPostingId' => $jobPosting->id,
                        'userResume' => auth()->user()->resumes()->latest()->first(),
                    ]);
                }
            }

            // Run AI analysis if posting doesn't exist or has no analysis
            $analysis = $this->jobAnalysisService->analyzeJobAd(
                $pendingData['jobAdText'],
                $pendingData['jobTitle'] ?? null,
                $pendingData['company'] ?? null
            );

            // Create job posting if it doesn't exist
            if (! $jobPosting) {
                $jobPosting = JobPosting::create([
                    'user_id' => auth()->id(),
                    'job_title' => $analysis['jobTitle'],
                    'company' => $analysis['company'],
                    'original_text' => $pendingData['jobAdText'],
                ]);
            } else {
                // Update job posting details from AI analysis
                $jobPosting->update([
                    'job_title' => $analysis['jobTitle'],
                    'company' => $analysis['company'],
                ]);
            }

            // Create analysis
            JobAnalysis::create([
                'job_posting_id' => $jobPosting->id,
                'company_background' => $analysis['companyBackground'],
                'location' => $analysis['location'],
                'job_type' => $analysis['jobType'],
                'summary' => $analysis['summary'],
                'required_skills' => $analysis['requiredSkills'],
                'nice_to_have_skills' => $analysis['niceToHaveSkills'],
                'responsibilities' => $analysis['responsibilities'],
                'requirements' => $analysis['requirements'],
                'benefits' => $analysis['benefits'],
                'salary_range' => $analysis['salaryRange'],
                'hiring_process' => $analysis['hiringProcess'],
                'warnings' => $analysis['warnings'] ?? [],
            ]);

            // Clear pending analysis from session
            session()->forget('pending_job_analysis');

            return Inertia::render('JobAnalysis/Results', [
                'analysis' => $analysis,
                'originalText' => $pendingData['jobAdText'],
                'jobPostingId' => $jobPosting->id,
                'userResume' => auth()->user()->resumes()->latest()->first(),
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
            // Check if job posting already exists for this user
            $jobPosting = JobPosting::where('user_id', auth()->id())
                ->where('original_text', $validated['jobAdText'])
                ->first();

            if ($jobPosting) {
                // Check if analysis exists
                $jobAnalysis = $jobPosting->jobAnalysis;

                if ($jobAnalysis) {
                    // Return existing analysis without calling AI
                    $analysis = [
                        'jobTitle' => $jobPosting->job_title,
                        'company' => $jobPosting->company,
                        'companyBackground' => $jobAnalysis->company_background,
                        'location' => $jobAnalysis->location,
                        'jobType' => $jobAnalysis->job_type,
                        'summary' => $jobAnalysis->summary,
                        'requiredSkills' => $jobAnalysis->required_skills,
                        'niceToHaveSkills' => $jobAnalysis->nice_to_have_skills,
                        'responsibilities' => $jobAnalysis->responsibilities,
                        'requirements' => $jobAnalysis->requirements,
                        'benefits' => $jobAnalysis->benefits,
                        'salaryRange' => $jobAnalysis->salary_range,
                        'hiringProcess' => $jobAnalysis->hiring_process,
                        'warnings' => $jobAnalysis->warnings ?? [],
                    ];

                    session()->forget('pending_job_analysis');

                    return Inertia::render('JobAnalysis/Results', [
                        'analysis' => $analysis,
                        'originalText' => $validated['jobAdText'],
                        'jobPostingId' => $jobPosting->id,
                        'userResume' => auth()->user()->resumes()->latest()->first(),
                    ]);
                }
            }

            // Run AI analysis if posting doesn't exist or has no analysis
            $analysis = $this->jobAnalysisService->analyzeJobAd(
                $validated['jobAdText'],
                $validated['jobTitle'] ?? null,
                $validated['company'] ?? null
            );

            // Create job posting if it doesn't exist
            if (! $jobPosting) {
                $jobPosting = JobPosting::create([
                    'user_id' => auth()->id(),
                    'job_title' => $analysis['jobTitle'],
                    'company' => $analysis['company'],
                    'original_text' => $validated['jobAdText'],
                ]);
            } else {
                // Update job posting details from AI analysis
                $jobPosting->update([
                    'job_title' => $analysis['jobTitle'],
                    'company' => $analysis['company'],
                ]);
            }

            // Create analysis
            JobAnalysis::create([
                'job_posting_id' => $jobPosting->id,
                'company_background' => $analysis['companyBackground'],
                'location' => $analysis['location'],
                'job_type' => $analysis['jobType'],
                'summary' => $analysis['summary'],
                'required_skills' => $analysis['requiredSkills'],
                'nice_to_have_skills' => $analysis['niceToHaveSkills'],
                'responsibilities' => $analysis['responsibilities'],
                'requirements' => $analysis['requirements'],
                'benefits' => $analysis['benefits'],
                'salary_range' => $analysis['salaryRange'],
                'hiring_process' => $analysis['hiringProcess'],
                'warnings' => $analysis['warnings'] ?? [],
            ]);

            // Clear pending analysis if exists
            session()->forget('pending_job_analysis');

            return Inertia::render('JobAnalysis/Results', [
                'analysis' => $analysis,
                'originalText' => $validated['jobAdText'],
                'jobPostingId' => $jobPosting->id,
                'userResume' => auth()->user()->resumes()->latest()->first(),
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
            'jobPostingId' => ['nullable', 'integer', 'exists:job_postings,id'],
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

            // Check if resume already exists for this user
            $resume = Resume::where('user_id', auth()->id())
                ->where('resume_text', $resumeText)
                ->first();

            if ($resume) {
                // Check if assessment exists for this resume + job posting combination
                $existingAssessment = Assessment::where('user_id', auth()->id())
                    ->where('resume_id', $resume->id)
                    ->where('job_posting_id', $validated['jobPostingId'] ?? null)
                    ->first();

                if ($existingAssessment) {
                    // Return existing assessment without calling AI
                    $assessment = [
                        'overallMatch' => $existingAssessment->overall_match,
                        'skillBreakdown' => $existingAssessment->skill_breakdown,
                        'summary' => $existingAssessment->summary,
                        'strengths' => $existingAssessment->strengths,
                        'gaps' => $existingAssessment->gaps,
                        'applicationStrategy' => $existingAssessment->application_strategy,
                        'interviewPreparation' => $existingAssessment->interview_preparation,
                        'personalizedRecommendation' => $existingAssessment->personalized_recommendation,
                    ];

                    return Inertia::render('JobAnalysis/Assessment', [
                        'assessment' => $assessment,
                        'jobInfo' => [
                            'jobTitle' => $validated['jobTitle'] ?? 'Job Position',
                            'company' => $validated['company'] ?? 'Company',
                        ],
                        'assessmentId' => $existingAssessment->id,
                    ]);
                }
            }

            // Run AI assessment if resume doesn't exist or no assessment for this combination
            $assessment = $this->jobAnalysisService->assessResume(
                $resumeText,
                $validated['jobAdText']
            );

            // Create resume if it doesn't exist
            if (! $resume) {
                $resume = Resume::create([
                    'user_id' => auth()->id(),
                    'resume_text' => $resumeText,
                ]);
            }

            // Create assessment
            $assessmentRecord = Assessment::create([
                'user_id' => auth()->id(),
                'resume_id' => $resume->id,
                'job_posting_id' => $validated['jobPostingId'] ?? null,
                'overall_match' => $assessment['overallMatch'],
                'skill_breakdown' => $assessment['skillBreakdown'],
                'summary' => $assessment['summary'],
                'strengths' => $assessment['strengths'],
                'gaps' => $assessment['gaps'],
                'application_strategy' => $assessment['applicationStrategy'],
                'interview_preparation' => $assessment['interviewPreparation'],
                'personalized_recommendation' => $assessment['personalizedRecommendation'],
            ]);

            // Sync the user to job posting relationship
            if ($validated['jobPostingId'] ?? null) {
                $jobPosting = JobPosting::find($validated['jobPostingId']);
                if ($jobPosting && ! $jobPosting->users()->where('user_id', auth()->id())->exists()) {
                    $jobPosting->users()->attach(auth()->id());
                }
            }

            return Inertia::render('JobAnalysis/Assessment', [
                'assessment' => $assessment,
                'jobInfo' => [
                    'jobTitle' => $validated['jobTitle'] ?? 'Job Position',
                    'company' => $validated['company'] ?? 'Company',
                ],
                'assessmentId' => $assessmentRecord->id,
            ]);
        } catch (\Exception $e) {
            \Log::error('Assessment error: '.$e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'resumeText' => 'Failed to assess resume. Please try again.',
            ]);
        }
    }
}
