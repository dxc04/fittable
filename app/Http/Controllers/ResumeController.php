<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResumeRequest;
use App\Models\Resume;
use App\Services\DocumentTextExtractor;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ResumeController extends Controller
{
    public function __construct(private DocumentTextExtractor $documentTextExtractor) {}

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

    public function store(StoreResumeRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $resumeText = $validated['resumeText'] ?? null;

            // If file uploaded, extract text from it
            if (! $resumeText && $request->hasFile('resumeFile')) {
                try {
                    $resumeText = $this->documentTextExtractor->extractText($validated['resumeFile']);
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
            $existingResume = Resume::where('user_id', auth()->id())
                ->where('resume_text', $resumeText)
                ->first();

            if ($existingResume) {
                return back()->withErrors([
                    'resumeText' => 'This resume already exists.',
                ]);
            }

            // Create new resume
            $resume = Resume::create([
                'user_id' => auth()->id(),
                'resume_text' => $resumeText,
            ]);

            return redirect()->route('resumes.index')
                ->with('success', 'Resume saved successfully!');
        } catch (\Exception $e) {
            \Log::error('Failed to save resume', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'resumeText' => 'Failed to save resume. Please try again.',
            ]);
        }
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
