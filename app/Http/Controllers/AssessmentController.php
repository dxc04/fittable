<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class AssessmentController extends Controller
{
    public function index(): Response
    {
        $assessments = auth()->user()
            ->assessments()
            ->with(['resume', 'jobPosting'])
            ->latest()
            ->get();

        return Inertia::render('Assessments/Index', [
            'assessments' => $assessments,
        ]);
    }

    public function show(\App\Models\Assessment $assessment): Response
    {
        // Ensure the assessment belongs to the authenticated user
        if ($assessment->user_id !== auth()->id()) {
            abort(403);
        }

        $assessment->load(['resume', 'jobPosting.jobAnalysis']);

        // Format job analysis if available
        $jobAnalysis = null;
        if ($assessment->jobPosting && $assessment->jobPosting->jobAnalysis) {
            $analysis = $assessment->jobPosting->jobAnalysis;
            $jobAnalysis = [
                'jobTitle' => $assessment->jobPosting->job_title,
                'company' => $assessment->jobPosting->company,
                'companyBackground' => $analysis->company_background,
                'location' => $analysis->location,
                'jobType' => $analysis->job_type,
                'summary' => $analysis->summary,
                'requiredSkills' => $analysis->required_skills,
                'niceToHaveSkills' => $analysis->nice_to_have_skills,
                'responsibilities' => $analysis->responsibilities,
                'requirements' => $analysis->requirements,
                'benefits' => $analysis->benefits,
                'salaryRange' => $analysis->salary_range,
                'hiringProcess' => $analysis->hiring_process,
            ];
        }

        return Inertia::render('Assessments/Show', [
            'assessment' => $assessment,
            'jobAnalysis' => $jobAnalysis,
        ]);
    }
}
