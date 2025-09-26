<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubmissionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\UserManagementController;
use App\Models\User;

Route::get('/', function () {
        // if user not logged in, redirect to login
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    // if user is logged in, redirect to dashboard
    return redirect()->route('dashboard');
});

// Semua route yang butuh login kita masukkan ke dalam grup ini
Route::middleware(['auth', 'verified'])->group(function () {
    // Halaman Home/Dashboard Utama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Halaman Submission
    Route::get('/submission', [SubmissionController::class, 'index'])->name('submission'); // <-- NAMA DIPERBAIKI

    // Route untuk form di dalam submission
    Route::get('/submission/lend', [SubmissionController::class, 'createLend'])->name('submission.lend.create');
    Route::get('/submission/procure', [SubmissionController::class, 'createProcure'])->name('submission.procure.create');

    // Route untuk MENYIMPAN data form
    Route::post('/submission/lend', [SubmissionController::class, 'storeLend'])->name('submission.lend.store');
    Route::post('/submission/procure', [SubmissionController::class, 'storeProcure'])->name('submission.procure.store');
    
    // Halaman History
    Route::get('/history', [HistoryController::class, 'index'])->name('history');

    // Halaman Inventory
    Route::get('/inventory', function () {
        return view('inventory');
    })->name('inventory');

    // Halaman Approval
    Route::get('/approval', function () {
        return view('approval');
    })->name('approval');

    // Halaman User Management
    Route::resource('user-management', UserManagementController::class)
        ->except(['create', 'store', 'show']) // Kita tidak butuh form 'create' atau 'show'
        ->middleware('is.admin');

    // Route untuk Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route untuk menampilkan detail submission
    Route::get('/submission/{proposal_id}', [HistoryController::class, 'show'])->name('history.show');

    // Route untuk Approval Page, dilindungi oleh middleware role
    Route::get('/approval', [ApprovalController::class, 'index'])->name('approval')->middleware('has.approval.role');

     // Route BARU untuk aksi "Act" oleh General Affair
    Route::post('/approval/{proposal_id}/act', [ApprovalController::class, 'act'])->name('approval.act')->middleware('has.approval.role');
    
    // Route BARU untuk menampilkan halaman proses approval
    Route::get('/approval/{proposal_id}/process', [ApprovalController::class, 'process'])->name('approval.process')->middleware('has.approval.role');

    // Route untuk aksi "approve" dan "reject"
    Route::post('/approval/{proposal_id}/approve', [ApprovalController::class, 'approve'])->name('approval.approve')->middleware('has.approval.role');
    Route::post('/approval/{proposal_id}/reject', [ApprovalController::class, 'reject'])->name('approval.reject')->middleware('has.approval.role');
    
    // Route untuk aksi "detail" approval
    Route::get('/approval/{proposal_id}', [ApprovalController::class, 'show'])->name('approval.show')->middleware('has.approval.role');

});

require __DIR__.'/auth.php';