<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\PublicRegistration;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\TournamentList;
use App\Livewire\Admin\CreateTournament;
use App\Livewire\Admin\EditTournament;
use App\Livewire\Admin\ParticipantManager;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\FirebaseAuthController;
use App\Livewire\UserProfile;

// Public Routes
Route::get('/', function () {
    $tournaments = \App\Models\Tournament::where('status', 'open')
        ->with('game')
        ->withCount('participants')
        ->latest()
        ->take(6)
        ->get();
    return view('home', compact('tournaments'));
})->name('home');

Route::get('/register/{slug}', PublicRegistration::class)->name('register')->middleware('auth');

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/daftar', [AuthController::class, 'showRegister'])->name('auth.register');
    Route::post('/daftar', [AuthController::class, 'register'])->name('auth.register.submit');
    
    // Google OAuth (Socialite - backup)
    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);
    
    // Firebase Auth (client-side)
    Route::post('/auth/firebase/verify', [FirebaseAuthController::class, 'verify'])->name('auth.firebase.verify');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// User Profile
Route::get('/profile', UserProfile::class)->name('profile')->middleware('auth');

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');
    
    Route::get('/tournaments', TournamentList::class)->name('tournaments.index');
    Route::get('/tournaments/create', CreateTournament::class)->name('tournaments.create');
    Route::get('/tournaments/{tournament}/edit', EditTournament::class)->name('tournaments.edit');
    Route::get('/tournaments/{tournament}/participants', ParticipantManager::class)->name('tournaments.participants');
    Route::get('/tournaments/{tournament}/export', [ExportController::class, 'participants'])->name('tournaments.export');
});
