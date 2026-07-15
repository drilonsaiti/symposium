<?php

use App\Http\Controllers\BioController;
use App\Http\Controllers\ConferenceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TalkController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


});

Route::middleware('auth')->prefix('my')->group(function () {
    Route::resource('talks', TalkController::class);
    Route::resource('conferences', ConferenceController::class)->except(['index', 'show']);
    Route::post('conferences/{conference}/talks/{talk}', [\App\Http\Controllers\TalkSubmissionController::class, 'store'])->name('conferences.talks.submit');
    Route::patch('conferences/{conference}/talks/{talk}/status', [\App\Http\Controllers\TalkSubmissionController::class, 'changeStatus'])->name('conferences.talks.status');
    Route::resource('bios', BioController::class);
    Route::resource('conferences', \App\Http\Controllers\MyConferenceController::class)->only(['index', 'show'])->names('conferences');
});

Route::resource('conferences', ConferenceController::class)->only(['index', 'show'])->names('public.conferences');


require __DIR__ . '/auth.php';
