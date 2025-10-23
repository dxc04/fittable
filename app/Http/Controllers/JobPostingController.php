<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class JobPostingController extends Controller
{
    public function index(Request $request): Response
    {
        $showClosed = $request->query('show_closed', '0') === '1';

        $query = auth()->user()
            ->jobPostings()
            ->with('jobAnalysis')
            ->latest();

        // Filter out closed postings by default
        if (! $showClosed) {
            $query->whereNull('closed_at');
        }

        $jobPostings = $query->get();

        return Inertia::render('JobPostings/Index', [
            'jobPostings' => $jobPostings,
            'showClosed' => $showClosed,
        ]);
    }

    public function show(JobPosting $jobPosting): Response
    {
        // Ensure the job posting belongs to the authenticated user
        if ($jobPosting->user_id !== auth()->id()) {
            abort(403);
        }

        $jobPosting->load('jobAnalysis');

        $analysis = null;
        if ($jobPosting->jobAnalysis) {
            $analysis = [
                'jobTitle' => $jobPosting->job_title,
                'company' => $jobPosting->company,
                'companyBackground' => $jobPosting->jobAnalysis->company_background,
                'location' => $jobPosting->jobAnalysis->location,
                'jobType' => $jobPosting->jobAnalysis->job_type,
                'summary' => $jobPosting->jobAnalysis->summary,
                'requiredSkills' => $jobPosting->jobAnalysis->required_skills,
                'niceToHaveSkills' => $jobPosting->jobAnalysis->nice_to_have_skills,
                'responsibilities' => $jobPosting->jobAnalysis->responsibilities,
                'requirements' => $jobPosting->jobAnalysis->requirements,
                'benefits' => $jobPosting->jobAnalysis->benefits,
                'salaryRange' => $jobPosting->jobAnalysis->salary_range,
                'hiringProcess' => $jobPosting->jobAnalysis->hiring_process,
                'warnings' => $jobPosting->jobAnalysis->warnings ?? [],
            ];
        }

        // Get user's latest resume and check for existing assessment
        $userResume = auth()->user()->resumes()->latest()->first();
        $existingAssessment = null;

        if ($userResume) {
            $existingAssessment = auth()->user()
                ->assessments()
                ->where('resume_id', $userResume->id)
                ->where('job_posting_id', $jobPosting->id)
                ->first();
        }

        return Inertia::render('JobPostings/Show', [
            'jobPosting' => $jobPosting,
            'analysis' => $analysis,
            'originalText' => $jobPosting->original_text,
            'userResume' => $userResume,
            'existingAssessment' => $existingAssessment,
        ]);
    }

    public function close(JobPosting $jobPosting): RedirectResponse
    {
        // Ensure the job posting belongs to the authenticated user
        if ($jobPosting->user_id !== auth()->id()) {
            abort(403);
        }

        $jobPosting->close();

        return back()->with('success', 'Job posting has been closed.');
    }

    public function reopen(JobPosting $jobPosting): RedirectResponse
    {
        // Ensure the job posting belongs to the authenticated user
        if ($jobPosting->user_id !== auth()->id()) {
            abort(403);
        }

        $jobPosting->reopen();

        return back()->with('success', 'Job posting has been reopened.');
    }
}
