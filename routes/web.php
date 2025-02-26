<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ThreadController;


use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');





Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::prefix('threads')->group(function () {
        Route::get('/', [ThreadController::class, 'index'])->name('thread.index');
        Route::post('/', [ThreadController::class, 'store'])->name('thread.store');  //with POST
        Route::get('/{thread}', [ThreadController::class, 'edit'])->name('thread.edit');  //with GET
        Route::put('/{thread}', [ThreadController::class, 'update'])->name('thread.update');  //with GET
    });
});

require __DIR__.'/auth.php';
