<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\AbsenAdminController;
use App\Http\Controllers\Admin\EvaluasiController;
use App\Http\Controllers\Admin\PengajuanAdminController;
use App\Http\Controllers\Admin\ProfilAdminController;
use App\Http\Controllers\JurnalController;
use Illuminate\Support\Facades\Route;

// ========================================
// GUEST ROUTES (Belum Login)
// ========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'loginProses']);
    Route::get('/register', [AuthController::class, 'register']);
    Route::post('/register', [AuthController::class, 'registerProses']);
});

// ========================================
// AUTHENTICATED ROUTES - GURU (Sudah Login)
// ========================================
Route::middleware(['auth'])->group(function () {
    
    // Logout (untuk semua role)
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // ========================================
    // ABSENSI ROUTES (GURU)
    // ========================================
    Route::prefix('absensi')->group(function () {
        // Dashboard Absensi
        Route::get('/', [AbsensiController::class, 'index']);
        
        // Aksi Absensi (Basic)
        Route::post('/masuk', [AbsensiController::class, 'absenMasuk']);
        Route::post('/keluar', [AbsensiController::class, 'absenKeluar']);
        Route::post('/izin', [AbsensiController::class, 'izin']);
        Route::post('/sakit', [AbsensiController::class, 'sakit']);
        
        // Aksi Absensi (Hybrid - GPS + Liveness + Face Recognition)
        Route::post('/masuk-hybrid', [AbsensiController::class, 'absenMasukHybrid']);
        Route::post('/keluar-hybrid', [AbsensiController::class, 'absenKeluarHybrid']);
        
        // Rekap & Kalender
        Route::get('/rekap', [AbsensiController::class, 'rekap']);
        Route::get('/kalender', [AbsensiController::class, 'kalender']);
        
        // Pengajuan Izin/Sakit
        Route::get('/pengajuan', [PengajuanController::class, 'index']);
        Route::post('/pengajuan', [PengajuanController::class, 'store']);
        Route::get('/pengajuan/{id}', [PengajuanController::class, 'show']);
    });
    
    // ========================================
    // PROFIL ROUTES (GURU)
    // ========================================
    Route::prefix('profil')->group(function () {
        Route::get('/', [ProfilController::class, 'index']);
        Route::post('/update', [ProfilController::class, 'update']);
        Route::post('/password', [ProfilController::class, 'updatePassword']);
        Route::post('/foto', [ProfilController::class, 'updateFoto']);
    });
    
    // ========================================
    // NOTIFIKASI ROUTES (GURU)
    // ========================================
    Route::prefix('notifikasi')->group(function () {
        Route::get('/', [NotifikasiController::class, 'index']);
        Route::post('/{id}/read', [NotifikasiController::class, 'markAsRead']);
    });

    // ========================================
    // JURNAL ROUTES (GURU)
    // ========================================
        // Dashboard
            Route::resource('jurnal', JurnalController::class)
        ->whereNumber('jurnal');

        });



// ========================================
// ADMIN ROUTES (Hanya Admin)
// ========================================
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    
    // ========================================
    // DASHBOARD ADMIN
    // ========================================
    Route::get('/', [DashboardController::class, 'index']);
    
    // ========================================
    // PROFIL ADMIN (TERPISAH DARI GURU)
    // ========================================
    Route::prefix('profil')->group(function () {
        Route::get('/', [ProfilAdminController::class, 'index']);
        Route::post('/update', [ProfilAdminController::class, 'update']);
        Route::post('/password', [ProfilAdminController::class, 'updatePassword']);
        Route::post('/foto', [ProfilAdminController::class, 'updateFoto']);
    });
    
    // ========================================
    // GURU MANAGEMENT (CRUD)
    // ========================================
    Route::prefix('guru')->group(function () {
        Route::get('/', [GuruController::class, 'index']);
        Route::get('/create', [GuruController::class, 'create']);
        Route::post('/', [GuruController::class, 'store']);
        Route::get('/{id}/edit', [GuruController::class, 'edit']);
        Route::put('/{id}', [GuruController::class, 'update']);
        Route::delete('/{id}', [GuruController::class, 'destroy']);
    });
    
    // ========================================
    // ABSENSI MANAGEMENT (VIEW & DELETE)
    // ========================================
    Route::prefix('absensi')->group(function () {
        Route::get('/', [AbsenAdminController::class, 'index']);
        Route::get('/export-pdf', [AbsenAdminController::class, 'exportPdf'])->name('admin.absensi.pdf');  // ← pindah ke sini
        Route::delete('/{id}', [AbsenAdminController::class, 'destroy']);
    });
    // ========================================
    // PENGAJUAN MANAGEMENT (APPROVE & REJECT)
    // ========================================
    Route::prefix('pengajuan')->group(function () {
        Route::get('/', [PengajuanAdminController::class, 'index']);
        Route::post('/{id}/approve', [PengajuanAdminController::class, 'approve']);
        Route::post('/{id}/reject', [PengajuanAdminController::class, 'reject']);
    });
    //Kepsek Evaluasi Jurnal
    
    Route::get('/evaluasi', [EvaluasiController::class, 'index'])->name('admin.evaluasi.index');
    Route::get('/evaluasi/{id}', [EvaluasiController::class, 'show'])->name('admin.evaluasi.show');
    Route::post('/evaluasi/{id}', [EvaluasiController::class, 'store'])->name('admin.evaluasi.store');
    
});



// ========================================
// DEFAULT ROUTE (Root)
// ========================================
Route::get('/', function () {
    if (auth()->check()) {
        // Redirect berdasarkan role
        if (auth()->user()->role === 'admin') {
            return redirect('/admin');
        }
        return redirect('/absensi');
    }
    return redirect('/login');
});
// ========================================
// DEFAULT ROUTE
// ========================================
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->role === 'admin') {
            return redirect('/admin');
        }
        return redirect('/absensi');
    }
    return redirect('/login');
});
