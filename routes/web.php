<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\TindakanController;
use App\Http\Controllers\ResepController;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Middleware\AdminMiddleware;

use App\Http\Controllers\KasirController;
use App\Http\Controllers\Api\PsnController;
use App\Http\Controllers\DokterController;
use App\Http\Controllers\KunjunganController;
use App\Http\Controllers\TransaksiController;




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

Route::get('/admin-dashboard', function () {
    return Inertia::render('AdminDashboard');
})->middleware(['auth', 'admin'])->name('admin.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Admin-only routes (User Management)
    Route::middleware('admin')->group(function () {
        Route::get('/users/create', [UsersController::class, 'create'])->name('users.create');
        
        //user management
        Route::get('/users', [UsersController::class, 'index'])->name('users.index');
        Route::get('/users/{id}/edit', [UsersController::class, 'edit'])->name('users.edit');
        Route::get('/users/{user}', [UsersController::class, 'show'])->name('users.show');
        Route::post('/users', [UsersController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UsersController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UsersController::class, 'destroy'])->name('users.destroy');
    });

    // Dokter and Admin routes (Resep, Tindakan Medis)
    Route::middleware('dokter')->group(function () {
        //resep management
        Route::get('/resep', [ResepController::class, 'index'])->name('resep.index');
        Route::get('/resep/create', [ResepController::class, 'create'])->name('resep.create');
        Route::get('/resep/{id}/edit', [ResepController::class, 'edit'])->name('resep.edit');
        Route::get('/resep/{user}', [ResepController::class, 'show'])->name('resep.show');
        Route::post('/resep', [ResepController::class, 'store'])->name('resep.store');
        Route::put('/resep/{user}', [ResepController::class, 'update'])->name('resep.update');
        Route::delete('/resep/{user}', [ResepController::class, 'destroy'])->name('resep.destroy');

        //tindakan management
        Route::get('/tindakan', [TindakanController::class, 'index'])->name('tindakan.index');
        Route::get('/tindakan/create', [TindakanController::class, 'create'])->name('tindakan.create');
        Route::get('/tindakan/{id}/edit', [TindakanController::class, 'edit'])->name('tindakan.edit');
        Route::get('/tindakan/{user}', [TindakanController::class, 'show'])->name('tindakan.show');
        Route::post('/tindakan', [TindakanController::class, 'store'])->name('tindakan.store');
        Route::put('/tindakan/{user}', [TindakanController::class, 'update'])->name('tindakan.update');
        Route::delete('/tindakan/{user}', [TindakanController::class, 'destroy'])->name('tindakan.destroy');
        
        // Pasien kunjungan untuk dokter
        Route::get('/dokter/pasien-kunjungan', [DokterController::class, 'pasienKunjungan'])->name('dokter.pasien-kunjungan');
    });

    // Kasir routes (accessible by dokter, kasir, pendaftaran, and admin)
    Route::middleware('kasir')->group(function () {
        Route::get('/kasir', [KasirController::class, 'index'])->name('kasir.index');
        Route::get('/kasir/create', [KasirController::class, 'create'])->name('kasir.create');
        Route::get('/kasir/{pasien}/create', [KasirController::class, 'create'])->name('kasir.create.pasien');
        Route::get('/kasir/{id}/edit', [KasirController::class, 'edit'])->name('kasir.edit');
        Route::get('/kasir/kunjungan/{id}', [KasirController::class, 'show'])->name('kasir.kunjungan.show');
        Route::get('/kasir/kunjungan/{id}/print', [KasirController::class, 'print'])->name('kasir.kunjungan.print');
        Route::get('/kasir/kunjungan/{id}/pdf', [KasirController::class, 'pdf'])->name('kasir.kunjungan.pdf');
        Route::post('/kasir', [KasirController::class, 'store'])->name('kasir.store');
        Route::put('/kasir/{pasien}', [KasirController::class, 'update'])->name('kasir.update');
        Route::delete('/kasir/{pasien}', [KasirController::class, 'destroy'])->name('kasir.destroy');
        // Removed conflicting route: Route::get('/kasir/{pasien}/pdf', [KasirController::class, 'pdf'])->name('kasir.pdf');


        Route::get('/pasien', [PsnController::class, 'index'])->name('pasien.index');
        Route::get('/pasien/create', [PsnController::class, 'create'])->name('pasien.create');
        Route::post('/pasien', [PsnController::class, 'store'])->name('pasien.store');
        Route::get('/pasien/{id}', [PsnController::class, 'show'])->name('pasien.show');
        Route::get('/pasien/{id}/edit', [PsnController::class, 'edit'])->name('pasien.edit');
        Route::put('/pasien/{id}', [PsnController::class, 'update'])->name('pasien.update');
        Route::delete('/pasien/{id}', [PsnController::class, 'destroy'])->name('pasien.destroy');
        
        // Kunjungan routes
        Route::get('/pasien/{id}/kunjungan/create', [PsnController::class, 'createKunjungan'])->name('pasien.kunjungan.create');
        Route::post('/pasien/kunjungan', [PsnController::class, 'storeKunjungan'])->name('pasien.kunjungan.store');
        Route::get('/pasien/{psnId}/kunjungan/{kunjunganId}/edit', [PsnController::class, 'editKunjungan'])->name('pasien.kunjungan.edit');
        Route::put('/pasien/{psnId}/kunjungan/{kunjunganId}', [PsnController::class, 'updateKunjungan'])->name('pasien.kunjungan.update');
        
        // Kunjungan dengan transaksi routes
        Route::get('/pasien/{psnId?}/kunjungan-with-transaksi/create', [PsnController::class, 'createKunjunganWithTransaction'])->name('pasien.kunjungan.with.transaksi.create');
        Route::post('/pasien/kunjungan-with-transaksi', [PsnController::class, 'storeKunjunganWithTransaction'])->name('pasien.kunjungan.with.transaksi.store');
        Route::get('/pasien/{psnId}/kunjungan-with-transaksi/{kunjunganId}/edit', [PsnController::class, 'editKunjunganWithTransaction'])->name('pasien.kunjungan.with.transaksi.edit');
        Route::put('/pasien/{psnId}/kunjungan-with-transaksi/{kunjunganId}', [PsnController::class, 'updateKunjunganWithTransaction'])->name('pasien.kunjungan.with.transaksi.update');
    });


    

    // Dokter management routes
    Route::get('/dokter', [DokterController::class, 'index'])->name('dokter.index');
    
    // Admin-only dokter management routes
    Route::middleware('admin')->group(function () {
        Route::get('/dokter/create', [DokterController::class, 'create'])->name('dokter.create');
        Route::get('/dokter/{id}', [DokterController::class, 'show'])->name('dokter.show');
        Route::get('/dokter/{id}/edit', [DokterController::class, 'edit'])->name('dokter.edit');
        Route::post('/dokter', [DokterController::class, 'store'])->name('dokter.store');
        Route::put('/dokter/{id}', [DokterController::class, 'update'])->name('dokter.update');
        Route::delete('/dokter/{id}', [DokterController::class, 'destroy'])->name('dokter.destroy');
    });

    //pasien management
    Route::get('/dokter/poli_layanan/poli_umum', [DokterController::class, 'poliUmum'])->name('dokter.poli_layanan.poli_umum');
    Route::get('/dokter/poli_layanan/poli_gigi', [DokterController::class, 'poliGigi'])->name('dokter.poli_layanan.poli_gigi');
    Route::get('/dokter/poli_layanan/kia', [DokterController::class, 'kia'])->name('dokter.poli_layanan.kia');
    Route::get('/dokter/poli_layanan/laboratorium', [DokterController::class, 'laboratorium'])->name('dokter.poli_layanan.laboratorium');
    Route::get('/dokter/poli_layanan/apotek', [DokterController::class, 'apotek'])->name('dokter.poli_layanan.apotek');
  
    // Kunjungan Routes
    Route::resource('kunjungan', KunjunganController::class);
    Route::get('/kunjungan/patient/{psnId}', [KunjunganController::class, 'getByPatient'])->name('kunjungan.by.patient');
    Route::get('/kunjungan/statistics', [KunjunganController::class, 'statistics'])->name('kunjungan.statistics');
    Route::get('/kunjungan/search', [KunjunganController::class, 'search'])->name('kunjungan.search');
    Route::get('/kunjungan/export', [KunjunganController::class, 'export'])->name('kunjungan.export');

    // Transaksi Routes
    Route::resource('transaksi', TransaksiController::class);
    Route::get('/kunjungan/{kunjungan}/transaksi/create', [TransaksiController::class, 'create'])->name('transaksi.create');

  
});

require __DIR__.'/auth.php';
