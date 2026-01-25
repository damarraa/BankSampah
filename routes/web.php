<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KategoriSampahController;
use App\Http\Controllers\SetoranSampahController;
use App\Http\Controllers\MasterKategoriSampahController;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


//KategoriSampah (Admin only)
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Index
    Route::get('/kategori-sampah', [KategoriSampahController::class, 'index'])
        ->name('kategori_sampah.index');

    // CREATE
    Route::get('/kategori-sampah/create', [KategoriSampahController::class, 'create'])
        ->name('kategori_sampah.create');

    Route::post('/kategori-sampah', [KategoriSampahController::class, 'store'])
        ->name('kategori_sampah.store');

    // SHOW (detail)
    Route::get('/kategori-sampah/{id}', [KategoriSampahController::class, 'show'])
        ->name('kategori_sampah.show');

    // EDIT
    Route::get('/kategori-sampah/{id}/edit', [KategoriSampahController::class, 'edit'])
        ->name('kategori_sampah.edit');

    Route::put('/kategori-sampah/{id}', [KategoriSampahController::class, 'update'])
        ->name('kategori_sampah.update');

    // DELETE
    Route::delete('/kategori-sampah/{id}', [KategoriSampahController::class, 'destroy'])
        ->name('kategori_sampah.destroy');
});


    // Master Kategori (Admin only)
    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/master-kategori-sampah', [MasterKategoriSampahController::class, 'index'])
            ->name('master_kategori_sampah.index');

        Route::get('/master-kategori-sampah/create', [MasterKategoriSampahController::class, 'create'])
            ->name('master_kategori_sampah.create');

        Route::post('/master-kategori-sampah', [MasterKategoriSampahController::class, 'store'])
            ->name('master_kategori_sampah.store');

        Route::get('/master-kategori-sampah/{id}/edit', [MasterKategoriSampahController::class, 'edit'])
            ->name('master_kategori_sampah.edit');

        Route::put('/master-kategori-sampah/{id}', [MasterKategoriSampahController::class, 'update'])
            ->name('master_kategori_sampah.update');

        Route::delete('/master-kategori-sampah/{id}', [MasterKategoriSampahController::class, 'destroy'])
            ->name('master_kategori_sampah.destroy');
    });
//Setoran Sampah

// USER - MAP SEMUA TITIK JEMPUT (milik user)
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user/peta', [\App\Http\Controllers\SetoranSampahController::class, 'mapUser'])
        ->name('user.map');

    Route::get('/user/peta/data', [\App\Http\Controllers\SetoranSampahController::class, 'mapUserData'])
        ->name('user.map.data');
});

// ADMIN - MAP SEMUA TITIK JEMPUT
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/peta', [\App\Http\Controllers\AdminSetoranController::class, 'mapAdmin'])
        ->name('admin.map');

    Route::get('/admin/peta/data', [\App\Http\Controllers\AdminSetoranController::class, 'mapAdminData'])
        ->name('admin.map.data');
});


use App\Http\Controllers\PetugasSetoranController;
use App\Http\Controllers\AdminSetoranController;

// USER Routes
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user/dashboard', [SetoranSampahController::class, 'dashboard'])->name('user.dashboard');
    Route::get('/user/setoran', [SetoranSampahController::class, 'index'])->name('user.setoran.index');
    Route::get('/user/setoran/create', [SetoranSampahController::class, 'create'])->name('user.setoran.create');
    Route::post('/user/setoran', [SetoranSampahController::class, 'store'])->name('user.setoran.store');
    Route::get('/user/setoran/{id}', [SetoranSampahController::class, 'show'])->name('user.setoran.show');
    Route::get('/user/setoran/{id}/petugas-location', [SetoranSampahController::class, 'petugasLocation'])->name('user.setoran.petugas_location');
});

// PETUGAS Routes
Route::middleware(['auth', 'role:petugas'])->group(function () {
    Route::get('/petugas/setoran', [PetugasSetoranController::class, 'index'])->name('petugas.setoran.index');
    Route::get('/petugas/setoran/{id}', [PetugasSetoranController::class, 'show'])->name('petugas.setoran.show');
    Route::post('/petugas/setoran/{id}/ambil', [PetugasSetoranController::class, 'ambil'])->name('petugas.setoran.ambil');
    Route::post('/petugas/setoran/{id}/status', [PetugasSetoranController::class, 'updateStatus'])->name('petugas.setoran.status');
    Route::post('/petugas/setoran/{id}/lokasi', [PetugasSetoranController::class, 'updateLocation'])->name('petugas.setoran.lokasi');
    Route::get('/petugas/peta', [PetugasSetoranController::class, 'map'])->name('petugas.map');
    Route::get('/petugas/peta/data', [PetugasSetoranController::class, 'mapData'])->name('petugas.map.data');
});

// ADMIN Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/setoran', [AdminSetoranController::class, 'index'])->name('admin.setoran.index');
    Route::get('/admin/setoran/{id}', [AdminSetoranController::class, 'show'])->name('admin.setoran.show');
    Route::get('/admin/setoran/{id}/petugas-location', [AdminSetoranController::class, 'petugasLocation'])->name('admin.setoran.petugas_location');
});




