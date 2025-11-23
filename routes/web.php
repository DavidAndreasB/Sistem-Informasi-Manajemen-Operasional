<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\SpkController;         // S BESAR
use App\Http\Controllers\JobSheetController;    // J S BESAR
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QcController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// GANTI MENJADI INI:
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');

    // --- SPK ---
    Route::resource('spk', SpkController::class);

    // --- JOBSHEET ROUTES ---
    Route::get('/jobsheet', [JobSheetController::class, 'index'])->name('jobsheet.index');
    Route::get('/jobsheet/{spk_id}', [JobSheetController::class, 'show'])->name('jobsheet.show');
    Route::post('/jobsheet', [JobSheetController::class, 'store'])->name('jobsheet.store');
    
    // PERHATIKAN BAGIAN '/{id}' DI BAWAH INI. JANGAN SAMPAI HILANG!
    Route::delete('/jobsheet/{id}', [JobSheetController::class, 'destroy'])->name('jobsheet.destroy');

// --- ADMIN ---
 Route::middleware(['admin'])->group(function () {
        Route::get('/register', [UserController::class, 'create'])->name('register'); 
        Route::post('/register', [UserController::class, 'store'])->name('register.store');

        // --- MANAJEMEN USER (CRUD) ---
    // index (daftar), edit (form edit), update (proses edit), destroy (hapus)
    Route::resource('user', UserController::class)->except(['show', 'create', 'store']); 

    // Kita keep route register manual untuk create/store agar konsisten
    Route::get('/register', [UserController::class, 'create'])->name('register'); 
    Route::post('/register', [UserController::class, 'store'])->name('register.store');
    });

    // --- RUTE KHUSUS QC ---
    Route::put('/qc/item/{id}', [QcController::class, 'update'])->name('qc.update');

    // Di dalam middleware auth
    Route::post('/jobsheet/item/{id}/complete', [JobSheetController::class, 'completeItem'])->name('item.complete');
    Route::post('/jobsheet/item/{id}/undo', [JobSheetController::class, 'undoCompleteItem'])->name('item.undo');

    // RUTE SIMULASI HARGA (Penawaran)
        Route::get('/simulasi', [App\Http\Controllers\SimulasiController::class, 'index'])->name('simulasi.index');
});

require __DIR__.'/auth.php';