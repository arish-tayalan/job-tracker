<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Breeze sends users here after login.
Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Creates index, create, store, show, edit, update and destroy routes in one line.
    // URLs: /applications, /applications/create, /applications/{id}, /applications/{id}/edit ...
    Route::resource('applications', JobApplicationController::class)
        ->names('job-applications')
        ->parameters(['applications' => 'jobApplication']);

    Route::get('/board', BoardController::class)->name('board');
    // Used by the board's drag-and-drop. Declared before the resource so the URL is unambiguous.
    Route::patch('/applications/{jobApplication}/status', [JobApplicationController::class, 'updateStatus'])
        ->name('job-applications.status');

    // Interviews belong to an application: add via the application, delete by interview id.
    Route::post('/applications/{jobApplication}/interviews', [InterviewController::class, 'store'])
        ->name('interviews.store');
    Route::delete('/interviews/{interview}', [InterviewController::class, 'destroy'])
        ->name('interviews.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
