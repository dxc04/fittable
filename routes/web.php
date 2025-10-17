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

Route::get('/analyze', [JobAnalysisController::class, 'index'])->name('job.index');
Route::get('/recruiter', [RecruiterController::class, 'index'])->name('recruiter.index');
Route::post('/recruiter/match', [RecruiterController::class, 'calculateMatch'])->name('recruiter.match');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/analyze', [JobAnalysisController::class, 'analyze'])->name('job.analyze');
    Route::get('/analyze/process', [JobAnalysisController::class, 'processPendingAnalysis'])->name('job.analyze.process');
    Route::post('/assess-resume', [JobAnalysisController::class, 'assessResume'])->name('job.assessResume');
    Route::get('/assessments', [AssessmentController::class, 'index'])->name('assessments.index');
    Route::get('/assessments/{assessment}', [AssessmentController::class, 'show'])->name('assessments.show');
    Route::get('/job-postings', [JobPostingController::class, 'index'])->name('job-postings.index');
    Route::get('/job-postings/{jobPosting}', [JobPostingController::class, 'show'])->name('job-postings.show');
    Route::get('/resumes', [ResumeController::class, 'index'])->name('resumes.index');
    Route::get('/resumes/{resume}', [ResumeController::class, 'show'])->name('resumes.show');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
