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

    protected function findOrCreateJobPosting(string $jobText, int $userId): \App\Models\JobPosting
    {
        $existingPosting = \App\Models\JobPosting::where('original_text', $jobText)
            ->where('user_id', $userId)
            ->first();

        if ($existingPosting) {
            return $existingPosting;
        }

        $analysis = $this->jobAnalysisService->analyzeJobAd($jobText);

        $jobPosting = \App\Models\JobPosting::create([
            'user_id' => $userId,
            'job_title' => $analysis['jobTitle'] ?? 'Unknown Position',
            'company' => $analysis['company'] ?? 'Unknown Company',
            'original_text' => $jobText,
        ]);

        \App\Models\JobAnalysis::create([
            'job_posting_id' => $jobPosting->id,
            'company_background' => $analysis['companyBackground'] ?? null,
            'location' => $analysis['location'] ?? null,
            'job_type' => $analysis['jobType'] ?? null,
            'summary' => $analysis['summary'] ?? null,
            'required_skills' => $analysis['requiredSkills'] ?? [],
            'nice_to_have_skills' => $analysis['niceToHaveSkills'] ?? [],
            'responsibilities' => $analysis['responsibilities'] ?? [],
            'requirements' => $analysis['requirements'] ?? [],
            'benefits' => $analysis['benefits'] ?? [],
            'salary_range' => $analysis['salaryRange'] ?? null,
            'hiring_process' => $analysis['hiringProcess'] ?? null,
            'warnings' => $analysis['warnings'] ?? [],
        ]);

        return $jobPosting;
    }

    protected function findOrCreateResume(string $resumeText): \App\Models\Resume
    {
        $existingResume = \App\Models\Resume::where('resume_text', $resumeText)->first();

        if ($existingResume) {
            return $existingResume;
        }

        return \App\Models\Resume::create([
            'user_id' => null,
            'resume_text' => $resumeText,
        ]);
    }

    protected function createAssessment(array $assessmentData, int $resumeId, int $jobPostingId, ?int $userId): \App\Models\Assessment
    {
        return \App\Models\Assessment::create([
            'user_id' => $userId,
            'resume_id' => $resumeId,
            'job_posting_id' => $jobPostingId,
            'overall_match' => $assessmentData['overallMatch'] ?? 0,
            'skill_breakdown' => $assessmentData['skillBreakdown'] ?? [],
            'summary' => $assessmentData['summary'] ?? null,
            'strengths' => $assessmentData['strengths'] ?? [],
            'gaps' => $assessmentData['gaps'] ?? [],
            'application_strategy' => $assessmentData['applicationStrategy'] ?? [],
            'interview_preparation' => $assessmentData['interviewPreparation'] ?? [],
            'personalized_recommendation' => $assessmentData['personalizedRecommendation'] ?? [],
        ]);
    }

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
            $assessmentData = $this->jobAnalysisService->assessCandidateForRecruiter(
                $pendingData['candidateResume'],
                $pendingData['jobDescription']
            );

            // Save job posting, resume, and assessment to database
            $jobPosting = $this->findOrCreateJobPosting($pendingData['jobDescription'], auth()->id());
            $resume = $this->findOrCreateResume($pendingData['candidateResume']);
            $assessment = $this->createAssessment($assessmentData, $resume->id, $jobPosting->id, null);

            // Create recruiter_assessments pivot entry
            \DB::table('recruiter_assessments')->insert([
                'user_id' => auth()->id(),
                'resume_id' => $resume->id,
                'assessment_id' => $assessment->id,
                'job_posting_id' => $jobPosting->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Clear pending match from session
            session()->forget('pending_recruiter_match');

            return Inertia::render('Recruiter/MatchResult', [
                'assessment' => $assessmentData,
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
            $assessmentData = $this->jobAnalysisService->assessCandidateForRecruiter(
                $candidateResume,
                $validated['jobDescription']
            );

            // Save job posting, resume, and assessment to database
            $jobPosting = $this->findOrCreateJobPosting($validated['jobDescription'], auth()->id());
            $resume = $this->findOrCreateResume($candidateResume);
            $assessment = $this->createAssessment($assessmentData, $resume->id, $jobPosting->id, null);

            // Create recruiter_assessments pivot entry
            \DB::table('recruiter_assessments')->insert([
                'user_id' => auth()->id(),
                'resume_id' => $resume->id,
                'assessment_id' => $assessment->id,
                'job_posting_id' => $jobPosting->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return Inertia::render('Recruiter/MatchResult', [
                'assessment' => $assessmentData,
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

    public function evaluations(): Response
    {
        $recruiter = auth()->user();

        $jobPostings = \App\Models\JobPosting::whereHas('recruiterAssessments', function ($query) use ($recruiter) {
            $query->where('user_id', $recruiter->id);
        })
            ->withCount(['recruiterAssessments as assessment_count' => function ($query) use ($recruiter) {
                $query->where('user_id', $recruiter->id);
            }])
            ->with(['jobAnalysis', 'recruiterAssessments' => function ($query) use ($recruiter) {
                $query->where('user_id', $recruiter->id)->with('assessment')->latest();
            }])
            ->latest()
            ->get()
            ->map(function ($jobPosting) {
                $assessments = $jobPosting->recruiterAssessments->pluck('assessment');

                return [
                    'id' => $jobPosting->id,
                    'job_title' => $jobPosting->job_title,
                    'company' => $jobPosting->company,
                    'assessment_count' => $jobPosting->assessment_count,
                    'average_match_score' => $assessments->avg('overall_match'),
                    'highest_match_score' => $assessments->max('overall_match'),
                    'latest_evaluation_date' => $jobPosting->recruiterAssessments->first()?->created_at,
                    'job_analysis' => $jobPosting->jobAnalysis ? [
                        'location' => $jobPosting->jobAnalysis->location,
                        'job_type' => $jobPosting->jobAnalysis->job_type,
                        'summary' => $jobPosting->jobAnalysis->summary,
                    ] : null,
                ];
            });

        return Inertia::render('Recruiter/Evaluations/Index', [
            'jobPostings' => $jobPostings,
        ]);
    }

    public function candidates(\App\Models\JobPosting $jobPosting): Response
    {
        $recruiter = auth()->user();

        if (! $jobPosting->recruiterAssessments()->where('user_id', $recruiter->id)->exists()) {
            abort(403, 'You have not evaluated any candidates for this job posting.');
        }

        $candidates = \App\Models\RecruiterAssessment::where('user_id', $recruiter->id)
            ->where('job_posting_id', $jobPosting->id)
            ->with(['assessment', 'resume'])
            ->latest()
            ->get()
            ->map(function ($recruiterAssessment) {
                return [
                    'id' => $recruiterAssessment->id,
                    'assessment_id' => $recruiterAssessment->assessment_id,
                    'resume_id' => $recruiterAssessment->resume_id,
                    'overall_match' => $recruiterAssessment->assessment->overall_match,
                    'summary' => $recruiterAssessment->assessment->summary,
                    'strengths' => $recruiterAssessment->assessment->strengths,
                    'gaps' => $recruiterAssessment->assessment->gaps,
                    'evaluated_at' => $recruiterAssessment->created_at,
                    'resume_preview' => \Str::limit($recruiterAssessment->resume->resume_text, 200),
                ];
            });

        return Inertia::render('Recruiter/Evaluations/Candidates', [
            'jobPosting' => [
                'id' => $jobPosting->id,
                'job_title' => $jobPosting->job_title,
                'company' => $jobPosting->company,
            ],
            'candidates' => $candidates,
        ]);
    }

    public function viewAssessment(\App\Models\JobPosting $jobPosting, \App\Models\Assessment $assessment): Response
    {
        $recruiter = auth()->user();

        $recruiterAssessment = \App\Models\RecruiterAssessment::where('user_id', $recruiter->id)
            ->where('job_posting_id', $jobPosting->id)
            ->where('assessment_id', $assessment->id)
            ->firstOrFail();

        $assessment->load('resume');

        return Inertia::render('Recruiter/Evaluations/AssessmentDetail', [
            'jobPosting' => [
                'id' => $jobPosting->id,
                'job_title' => $jobPosting->job_title,
                'company' => $jobPosting->company,
            ],
            'assessment' => [
                'id' => $assessment->id,
                'overall_match' => $assessment->overall_match,
                'summary' => $assessment->summary,
                'strengths' => $assessment->strengths,
                'gaps' => $assessment->gaps,
                'skill_breakdown' => $assessment->skill_breakdown,
                'evaluated_at' => $recruiterAssessment->created_at,
            ],
            'resume' => [
                'id' => $assessment->resume->id,
                'resume_text' => $assessment->resume->resume_text,
            ],
        ]);
    }
}
