<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialAuthController;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/settings', App\Livewire\Settings::class)
    ->middleware(['auth'])
    ->name('settings');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/estimator', function () {
    return view('estimator');
})->middleware(['auth'])->name('estimator');

Route::get('/estimates/{estimate}', App\Livewire\ViewEstimate::class)
    ->middleware(['auth'])
    ->name('estimates.view');

// Social Auth
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');

require __DIR__.'/auth.php';
