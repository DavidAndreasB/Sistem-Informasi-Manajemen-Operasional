<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
// Import Controller
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\SpkController;
use App\Http\Controllers\JobSheetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QcController;
use App\Http\Controllers\SimulasiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman Depan
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// GROUP ROUTE: USER LOGIN
Route::middleware('auth')->group(function () {

    // --- PROFILE ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- PENGATURAN ---
    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');

    // --- SPK ---
    Route::resource('spk', SpkController::class);
    Route::get('/spk/{spk}/pdf', [SpkController::class, 'printPdf'])->name('spk.pdf');

    // --- JOBSHEET ---
    Route::get('/jobsheet', [JobSheetController::class, 'index'])->name('jobsheet.index');
    Route::get('/jobsheet/{spk_id}', [JobSheetController::class, 'show'])->name('jobsheet.show');
    Route::post('/jobsheet', [JobSheetController::class, 'store'])->name('jobsheet.store');
    Route::delete('/jobsheet/{id}', [JobSheetController::class, 'destroy'])->name('jobsheet.destroy');

    // Aksi Operator
    Route::post('/jobsheet/item/{id}/complete', [JobSheetController::class, 'completeItem'])->name('item.complete');
    Route::post('/jobsheet/item/{id}/undo', [JobSheetController::class, 'undoCompleteItem'])->name('item.undo');

    // --- QC ---
    Route::put('/qc/item/{id}', [QcController::class, 'update'])->name('qc.update');

    // ==================================================================
    // GROUP ROUTE: KHUSUS SUPER ADMIN
    // ==================================================================
    Route::middleware(['admin'])->group(function () {

        // 1. Tambah User Baru (Register) - INI YANG SEBELUMNYA ERROR
        Route::get('/register', [UserController::class, 'create'])->name('register');
        Route::post('/register', [UserController::class, 'store'])->name('register.store');

        // 2. Manajemen User (Edit, Update, Delete)
        Route::resource('user', UserController::class)->except(['create', 'store', 'show']);

        // 3. Simulasi Harga
        Route::get('/simulasi', [SimulasiController::class, 'index'])->name('simulasi.index');
    });

});

require __DIR__ . '/auth.php';