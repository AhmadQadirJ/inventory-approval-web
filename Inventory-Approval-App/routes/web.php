<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubmissionController;
use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
});

// Semua route yang butuh login kita masukkan ke dalam grup ini
Route::middleware(['auth', 'verified'])->group(function () {
    // Halaman Home/Dashboard Utama
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Halaman Submission
    Route::get('/submission', [SubmissionController::class, 'index'])->name('submission'); // <-- NAMA DIPERBAIKI

    // Route untuk form di dalam submission
    Route::get('/submission/lend', [SubmissionController::class, 'createLend'])->name('submission.lend.create');
    Route::get('/submission/procure', [SubmissionController::class, 'createProcure'])->name('submission.procure.create');

    // Route untuk MENYIMPAN data form
    Route::post('/submission/lend', [SubmissionController::class, 'storeLend'])->name('submission.lend.store');
    Route::post('/submission/procure', [SubmissionController::class, 'storeProcure'])->name('submission.procure.store');
    
    // Halaman History
    Route::get('/history', function () {
        return view('history');
    })->name('history');

    // Halaman Inventory
    Route::get('/inventory', function () {
        return view('inventory');
    })->name('inventory');

    // Halaman Approval
    Route::get('/approval', function () {
        return view('approval');
    })->name('approval');

    // Halaman User Management
    Route::get('/user-management', function () {
        return view('user-management');
    })->name('user-management');

    // Route untuk Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';