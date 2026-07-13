<?php

use App\Http\Controllers\ConferenceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TalkController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('talks', TalkController::class);
    Route::resource('conferences', ConferenceController::class)->except(['index', 'show']);
});
Route::resource('conferences', ConferenceController::class)->only(['index', 'show']);



require __DIR__.'/auth.php';
