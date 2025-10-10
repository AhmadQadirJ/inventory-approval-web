<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

// Redirect dari halaman root ke dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Grup untuk semua route yang memerlukan login
Route::middleware(['auth', 'verified'])->group(function () {
    
    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // PROFILE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // SUBMISSION
    Route::get('/submission', [SubmissionController::class, 'index'])->name('submission');
    Route::get('/submission/lend', [SubmissionController::class, 'createLend'])->name('submission.lend.create');
    Route::post('/submission/lend', [SubmissionController::class, 'storeLend'])->name('submission.lend.store');
    Route::get('/submission/procure', [SubmissionController::class, 'createProcure'])->name('submission.procure.create');
    Route::post('/submission/procure', [SubmissionController::class, 'storeProcure'])->name('submission.procure.store');

    // HISTORY (SHARED DETAILS & PRINT)
    Route::get('/history', [HistoryController::class, 'index'])->name('history');
    Route::get('/submission/{proposal_id}', [HistoryController::class, 'show'])->name('history.show');
    Route::get('/submission/{proposal_id}/print', [HistoryController::class, 'printPdf'])->name('history.print');
    Route::get('/submission/{proposal_id}/print-detail', [HistoryController::class, 'printDetail'])->name('history.printDetail');
    
    // INVENTORY
    // Route yang bisa diakses semua orang (harus di atas route dinamis)
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
    Route::get('/inventory/create', [InventoryController::class, 'create'])->name('inventory.create')->middleware('can.manage.inventory'); // Create harus di atas {inventory}
    Route::get('/inventory/{inventory}', [InventoryController::class, 'show'])->name('inventory.show');

    // Route yang hanya bisa diakses oleh GA dan Admin
    Route::middleware('can.manage.inventory')->group(function () {
        Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
        Route::get('/inventory/{inventory}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
        Route::patch('/inventory/{inventory}', [InventoryController::class, 'update'])->name('inventory.update');
        Route::delete('/inventory/{inventory}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
    });

    // Grup untuk API internal (ambil data untuk dropdown dinamis)
    Route::prefix('api/inventory')->group(function () {
        Route::get('/categories', [InventoryController::class, 'getCategoriesForBranch'])->name('api.inventory.categories');
        Route::get('/items', [InventoryController::class, 'getItemsForCategory'])->name('api.inventory.items');
    });

    // APPROVAL (Hanya untuk Approver)
    Route::middleware('has.approval.role')->prefix('approval')->name('approval.')->group(function () {
        Route::get('/', [ApprovalController::class, 'index'])->name('index');
        Route::post('/{proposal_id}/act', [ApprovalController::class, 'act'])->name('act');
        Route::get('/{proposal_id}/process', [ApprovalController::class, 'process'])->name('process');
        Route::post('/{proposal_id}/approve', [ApprovalController::class, 'approve'])->name('approve');
        Route::post('/{proposal_id}/reject', [ApprovalController::class, 'reject'])->name('reject');
        Route::get('/{proposal_id}', [ApprovalController::class, 'show'])->name('show');
        Route::get('/{proposal_id}/print', [ApprovalController::class, 'printPdf'])->name('print');
        Route::get('/{proposal_id}/print-detail', [ApprovalController::class, 'printDetail'])->name('printDetail');
    });
    
    // USER MANAGEMENT (Hanya untuk Admin)
    Route::resource('user-management', UserManagementController::class)
        ->except(['create', 'store', 'show'])
        ->middleware('is.admin');

});

require __DIR__.'/auth.php';