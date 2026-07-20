<?php

use App\Http\Controllers\BioController;
use App\Http\Controllers\ConferenceController;
use App\Http\Controllers\ConferenceDismissedController;
use App\Http\Controllers\ConferenceFavoriteController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TalkController;
use App\Http\Controllers\TalkRevisionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('conferences/{conference}/favorite', [ConferenceFavoriteController::class, 'store'])
        ->name('conferences.favorite');

    Route::delete('conferences/{conference}/favorite', [ConferenceFavoriteController::class, 'destroy'])
        ->name('conferences.unfavorite');

    Route::post('conferences/{conference}/dismissed', [ConferenceDismissedController::class, 'store'])
        ->name('conferences.dismissed');

    Route::delete('conferences/{conference}/dismissed ', [ConferenceDismissedController::class, 'destroy'])
        ->name('conferences.undismissed');

    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.read-all');

    Route::get('/notifications/{notification}', [NotificationController::class, 'show'])
        ->name('notifications.show');
});

Route::middleware('auth')->prefix('my')->group(function () {
    Route::resource('talks', TalkController::class);
    Route::resource('conferences', ConferenceController::class)->except(['index', 'show']);
    Route::post('conferences/{conference}/talks/{talk}', [\App\Http\Controllers\TalkSubmissionController::class, 'store'])->name('conferences.talks.submit');
    Route::patch('conferences/{conference}/talks/{talk}/status', [\App\Http\Controllers\TalkSubmissionController::class, 'changeStatus'])->name('conferences.talks.status');
    Route::resource('bios', BioController::class);
    Route::resource('conferences', \App\Http\Controllers\MyConferenceController::class)->only(['index', 'show'])->names('conferences');

    Route::get('talks/{talk}/revisions', [TalkRevisionController::class, 'index'])
        ->name('talks.revisions.index');

    Route::post('talks/{talk}/revisions/{revision}/restore', [TalkRevisionController::class, 'restore'])
        ->name('talks.revisions.restore');
});

Route::resource('conferences', ConferenceController::class)->only(['index', 'show'])->names('public.conferences');


require __DIR__ . '/auth.php';
