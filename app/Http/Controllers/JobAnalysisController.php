<?php

namespace App\Http\Controllers;

use App\Services\JobAnalysisService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class JobAnalysisController extends Controller
{
    public function __construct(
        protected JobAnalysisService $jobAnalysisService
    ) {}

    public function index(): Response
    {
        return Inertia::render('JobAnalysis/Index');
    }

    public function analyze(Request $request)
    {
        $validated = $request->validate([
            'jobTitle' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'jobAdText' => ['required', 'string', 'min:50', 'max:10000'],
        ]);

        try {
            $analysis = $this->jobAnalysisService->analyzeJobAd(
                $validated['jobAdText'],
                $validated['jobTitle'] ?? null,
                $validated['company'] ?? null
            );

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
}
