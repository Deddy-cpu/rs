<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\TindakanController;
use App\Http\Controllers\ResepController;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\Api\KasirController;


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
    Route::get('/users/create', [UsersController::class, 'create'])->name('users.create');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    

    //user management
    Route::get('/users', [UsersController::class, 'index'])->name('users.index');
    Route::get('/users/{id}/edit', [UsersController::class, 'edit'])->name('users.edit');
    Route::get('/users/{user}', [UsersController::class, 'show'])->name('users.show');
    Route::post('/users', [UsersController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UsersController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UsersController::class, 'destroy'])->name('users.destroy');

    //tindakan management
    Route::get('/tindakan', [TindakanController::class, 'index'])->name('tindakan.index');
    Route::get('/tindakan/create', [TindakanController::class, 'create'])->name('tindakan.create');
    Route::get('/tindakan/{id}/edit', [TindakanController::class, 'edit'])->name('tindakan.edit');
    Route::get('/tindakan/{user}', [TindakanController::class, 'show'])->name('tindakan.show');
    Route::post('/tindakan', [TindakanController::class, 'store'])->name('tindakan.store');
    Route::put('/tindakan/{user}', [TindakanController::class, 'update'])->name('tindakan.update');
    Route::delete('/tindakan/{user}', [TindakanController::class, 'destroy'])->name('tindakan.destroy');

    //resep management
    Route::get('/resep', [ResepController::class, 'index'])->name('resep.index');
    Route::get('/resep/create', [ResepController::class, 'create'])->name('resep.create');
    Route::get('/resep/{id}/edit', [ResepController::class, 'edit'])->name('resep.edit');
    Route::get('/resep/{user}', [ResepController::class, 'show'])->name('   resep.show');
    Route::post('/resep', [ResepController::class, 'store'])->name('resep.store');
    Route::put('/resep/{user}', [ResepController::class, 'update'])->name('resep.update');
    Route::delete('/resep/{user}', [ResepController::class, 'destroy'])->name('resep.destroy');

    //kasir management
    Route::get('/kasir', [KasirController::class, 'index'])->name('kasir.index');
    Route::get('/kasir/{pasien}/create', [KasirController::class, 'create'])->name('kasir.create');
    Route::get('/kasir/{id}/edit', [KasirController::class, 'edit'])->name('kasir.edit');
    Route::get('/kasir/{user}', [KasirController::class, 'show'])->name('   kasir.show');
    Route::post('/kasir', [KasirController::class, 'store'])->name('kasir.store');
    Route::put('/kasir/{user}', [KasirController::class, 'update'])->name('kasir.update');
    Route::delete('/kasir/{user}', [KasirController::class, 'destroy'])->name('kasir.destroy');


    //pasien management
    Route::get('/pasien/create', [\App\Http\Controllers\Api\PasienController::class, 'create'])->name('pasien.create');
    Route::post('/pasien', [\App\Http\Controllers\Api\PasienController::class, 'store'])->name('pasien.store');

  
});

require __DIR__.'/auth.php';
