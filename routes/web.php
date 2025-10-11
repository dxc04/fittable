<?php

use App\Http\Controllers\JobAnalysisController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Landing');
})->name('home');

Route::get('/analyze', [JobAnalysisController::class, 'index'])->name('job.index');
Route::post('/analyze', [JobAnalysisController::class, 'analyze'])->name('job.analyze');
Route::middleware('auth')->group(function () {
    Route::get('/analyze/process', [JobAnalysisController::class, 'processPendingAnalysis'])->name('job.analyze.process');
    Route::post('/assess-resume', [JobAnalysisController::class, 'assessResume'])->name('job.assessResume');
});

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
