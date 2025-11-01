<?php

use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\JobAnalysisController;
use App\Http\Controllers\JobPostingController;
use App\Http\Controllers\RecruiterController;
use App\Http\Controllers\ResumeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Landing');
})->name('home');

// Public route for recruiter landing page
Route::get('/recruiter', [RecruiterController::class, 'index'])->name('recruiter.index');
Route::post('/recruiter/match', [RecruiterController::class, 'calculateMatch'])->name('recruiter.match');

// Public job analysis routes (accessible without authentication)
Route::get('/analyze', [JobAnalysisController::class, 'index'])->name('job.index');
Route::post('/analyze', [JobAnalysisController::class, 'analyze'])->name('job.analyze');

Route::middleware(['auth'])->group(function () {
    // Dashboard redirect to job analysis
    Route::get('dashboard', function () {
        return redirect()->route('job.index');
    })->name('dashboard');

    // Authenticated job analysis routes
    Route::get('/analyze/process', [JobAnalysisController::class, 'processPendingAnalysis'])->name('job.analyze.process');
    Route::post('/assess-resume', [JobAnalysisController::class, 'assessResume'])->name('job.assessResume');

    // Authenticated recruiter routes
    Route::get('/recruiter/match/process', [RecruiterController::class, 'processPendingMatch'])->name('recruiter.match.process');

    // Job postings (accessible to both job seekers and recruiters)
    Route::get('/job-postings', [JobPostingController::class, 'index'])->name('job-postings.index');
    Route::get('/job-postings/{jobPosting}', [JobPostingController::class, 'show'])->name('job-postings.show');
    Route::post('/job-postings/{jobPosting}/close', [JobPostingController::class, 'close'])->name('job-postings.close');
    Route::post('/job-postings/{jobPosting}/reopen', [JobPostingController::class, 'reopen'])->name('job-postings.reopen');
});

// Job Seeker protected routes
Route::middleware(['auth', 'job_seeker'])->group(function () {
    Route::get('/assessments', [AssessmentController::class, 'index'])->name('assessments.index');
    Route::get('/assessments/{assessment}', [AssessmentController::class, 'show'])->name('assessments.show');
    Route::get('/resumes', [ResumeController::class, 'index'])->name('resumes.index');
    Route::post('/resumes', [ResumeController::class, 'store'])->name('resumes.store');
    Route::get('/resumes/{resume}', [ResumeController::class, 'show'])->name('resumes.show');
});

// Recruiter protected routes
Route::middleware(['auth', 'recruiter'])->prefix('recruiter')->name('recruiter.')->group(function () {
    Route::get('/evaluations', [RecruiterController::class, 'evaluations'])->name('evaluations.index');
    Route::get('/evaluations/{jobPosting}', [RecruiterController::class, 'candidates'])->name('evaluations.candidates');
    Route::get('/evaluations/{jobPosting}/assessment/{assessment}', [RecruiterController::class, 'viewAssessment'])->name('evaluations.assessment');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
