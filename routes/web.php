<?php

use App\Http\Controllers\JobAnalysisController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [JobAnalysisController::class, 'index'])->name('home');
Route::post('/analyze', [JobAnalysisController::class, 'analyze'])->name('job.analyze');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
