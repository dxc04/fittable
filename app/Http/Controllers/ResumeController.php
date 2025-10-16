<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use Inertia\Inertia;
use Inertia\Response;

class ResumeController extends Controller
{
    public function index(): Response
    {
        $resumes = auth()->user()
            ->resumes()
            ->withCount('assessments')
            ->latest()
            ->get();

        return Inertia::render('Resumes/Index', [
            'resumes' => $resumes,
        ]);
    }

    public function show(Resume $resume): Response
    {
        // Ensure the resume belongs to the authenticated user
        if ($resume->user_id !== auth()->id()) {
            abort(403);
        }

        $resume->loadCount('assessments');
        $resume->load(['assessments.jobPosting']);

        return Inertia::render('Resumes/Show', [
            'resume' => $resume,
        ]);
    }
}
