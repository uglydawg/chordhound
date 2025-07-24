<?php

use App\Http\Controllers\ChordController;
use App\Http\Controllers\SocialAuthController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;
use Laravel\Pulse\Facades\Pulse;

Route::get('/test', function () {
    return view('test');
});

Route::get('/piano-test', function () {
    return view('piano-test');
})->name('piano.test');

Route::get('/debug/math-chords', function () {
    return view('debug.math-chords');
})->name('debug.math-chords');

Route::get('/', function () {
    return view('home');
})->name('home');
Route::get('/chords', [ChordController::class, 'index'])->name('chords.index');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Social Authentication Routes
Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

// Magic Link Authentication
Route::get('/auth/magic-link/{token}', [SocialAuthController::class, 'verifyMagicLink'])->name('auth.magic-link.verify');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    // Chord routes
    Route::get('/my-chord-sets', [ChordController::class, 'mySets'])->name('chords.my-sets');
    Route::get('/chords/{chordSet}/edit', [ChordController::class, 'edit'])->name('chords.edit');
    
    // Learning routes
    Route::get('/learning', [App\Http\Controllers\LearningController::class, 'index'])->name('learning.index');
    Route::get('/learning/progress', [App\Http\Controllers\LearningController::class, 'progress'])->name('learning.progress');
    Route::get('/learning/achievements', [App\Http\Controllers\LearningController::class, 'achievements'])->name('learning.achievements');
    
    // Lesson routes
    Route::get('/modules/{module}/lessons', [App\Http\Controllers\LessonController::class, 'index'])->name('lessons.index');
    Route::get('/lessons/{lesson}', [App\Http\Controllers\LessonController::class, 'show'])->name('lessons.show');
    Route::post('/lessons/{lesson}/complete', [App\Http\Controllers\LessonController::class, 'complete'])->name('lessons.complete');
    Route::post('/lessons/{lesson}/progress', [App\Http\Controllers\LessonController::class, 'updateProgress'])->name('lessons.progress');
    
    // Quiz routes
    Route::get('/quizzes/{quiz}', [App\Http\Controllers\QuizController::class, 'show'])->name('quizzes.show');
    Route::post('/quizzes/{quiz}/start', [App\Http\Controllers\QuizController::class, 'start'])->name('quizzes.start');
    Route::post('/quiz-attempts/{attempt}/answer', [App\Http\Controllers\QuizController::class, 'submitAnswer'])->name('quizzes.answer');
    Route::post('/quiz-attempts/{attempt}/complete', [App\Http\Controllers\QuizController::class, 'complete'])->name('quizzes.complete');
    Route::get('/quiz-attempts/{attempt}/results', [App\Http\Controllers\QuizController::class, 'results'])->name('quizzes.results');
    Route::get('/quizzes/history', [App\Http\Controllers\QuizController::class, 'history'])->name('quizzes.history');
    
    // Teacher dashboard routes (requires teacher middleware)
    Route::middleware(['teacher'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/', [App\Http\Controllers\TeacherDashboardController::class, 'index'])->name('dashboard');
        Route::get('/students', [App\Http\Controllers\TeacherDashboardController::class, 'students'])->name('students');
        Route::get('/students/{student}', [App\Http\Controllers\TeacherDashboardController::class, 'studentDetail'])->name('student.detail');
        Route::get('/modules/{module}/progress', [App\Http\Controllers\TeacherDashboardController::class, 'moduleProgress'])->name('module.progress');
        Route::get('/quizzes/{quiz}/results', [App\Http\Controllers\TeacherDashboardController::class, 'quizResults'])->name('quiz.results');
    });
});

require __DIR__.'/auth.php';

// Donation routes
Route::get('/donate', function () {
    return view('donate');
})->name('donate');

Route::get('/donation/success', function () {
    return view('donation.success');
})->name('donation.success');

// Admin monitoring routes
Route::middleware(['auth'])->group(function () {
    Route::get('/pulse', function () {
        return Pulse::dashboard();
    })->name('pulse');
});
