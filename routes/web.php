<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminKaryaController;
use App\Http\Controllers\AdminPenjualanController;
use App\Http\Controllers\AdminSetoranController;
use App\Http\Controllers\AdminStokController;
use App\Http\Controllers\KategoriSampahController;
use App\Http\Controllers\MasterKategoriSampahController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PetugasSetoranController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SetoranSampahController;
use App\Http\Controllers\UserBantuanController;
use App\Http\Controllers\UserStatistikController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ROOT
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| DASHBOARD (AUTO REDIRECT BY ROLE)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    $user = auth()->user();
    $role = $user->role ?? null;

    if ($role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    if ($role === 'petugas') {
        return redirect()->route('petugas.setoran.index');
    }

    return redirect()->route('user.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| PROFILE (ALL AUTH)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])->name('notif.read');
    Route::get('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notif.read.all');
});

require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| USER ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:user'])
    ->prefix('user')
    ->as('user.')
    ->group(function () {

        Route::get('/dashboard', [SetoranSampahController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/setoran', [SetoranSampahController::class, 'index'])
            ->name('setoran.index');

        Route::get('/setoran/create', [SetoranSampahController::class, 'create'])
            ->name('setoran.create');

        Route::post('/setoran', [SetoranSampahController::class, 'store'])
            ->name('setoran.store');

        Route::get('/setoran/{id}', [SetoranSampahController::class, 'show'])
            ->name('setoran.show');

        Route::get('/setoran/{id}/petugas-location', [SetoranSampahController::class, 'petugasLocation'])
            ->name('setoran.petugas_location');

        Route::get('/peta', [SetoranSampahController::class, 'mapUser'])
            ->name('map');

        Route::get('/peta/data', [SetoranSampahController::class, 'mapUserData'])
            ->name('map.data');

        Route::get('/statistik', [UserStatistikController::class, 'index'])
            ->name('statistik.index');

        Route::get('/bantuan', [UserBantuanController::class, 'index'])
            ->name('bantuan.index');
    });

/*
|--------------------------------------------------------------------------
| PETUGAS ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:petugas'])
    ->prefix('petugas')
    ->as('petugas.')
    ->group(function () {

        Route::get('/setoran', [PetugasSetoranController::class, 'index'])
            ->name('setoran.index');

        Route::get('/setoran/{id}', [PetugasSetoranController::class, 'show'])
            ->name('setoran.show');

        Route::post('/setoran/{id}/ambil', [PetugasSetoranController::class, 'ambil'])
            ->name('setoran.ambil');

        Route::post('/setoran/{id}/status', [PetugasSetoranController::class, 'updateStatus'])
            ->name('setoran.status');

        Route::post('/setoran/{id}/lokasi', [PetugasSetoranController::class, 'updateLocation'])
            ->name('setoran.lokasi');

        Route::get('/peta', [PetugasSetoranController::class, 'map'])
            ->name('map');

        Route::get('/peta/data', [PetugasSetoranController::class, 'mapData'])
            ->name('map.data');
    });

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (nama route pakai admin.* untuk dashboard/setoran/map)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        Route::post('/keuangan', [AdminDashboardController::class, 'storeKeuangan'])
            ->name('keuangan.store');

        Route::get('/setoran', [AdminSetoranController::class, 'index'])
            ->name('setoran.index');

        Route::get('/setoran/{id}', [AdminSetoranController::class, 'show'])
            ->name('setoran.show');

        Route::get('/setoran/{id}/petugas-location', [AdminSetoranController::class, 'petugasLocation'])
            ->name('setoran.petugas_location');

        Route::get('/stok', [AdminStokController::class, 'index'])->name('stok.index');
        Route::post('/stok', [AdminStokController::class, 'store'])->name('stok.store');
        Route::get('/stok/{id}/edit', [AdminStokController::class, 'edit'])->name('stok.edit');

        Route::resource('penjualan', AdminPenjualanController::class)->only(['index', 'store', 'destroy']);
        Route::resource('karya', AdminKaryaController::class)->only(['index', 'store', 'destroy']);

        Route::get('/peta', [AdminSetoranController::class, 'mapAdmin'])
            ->name('map');

        Route::get('/peta/data', [AdminSetoranController::class, 'mapAdminData'])
            ->name('map.data');
    });

/*
|--------------------------------------------------------------------------
| ADMIN MASTER DATA ROUTES
|--------------------------------------------------------------------------
| ✅ URL tetap /admin/...
| ✅ Tapi NAMA ROUTE tetap seperti view kamu:
| - master_kategori_sampah.*
| - kategori_sampah.*
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->group(function () {

        // Kategori Sampah (nama route tetap kategori_sampah.*)
        Route::get('/kategori-sampah', [KategoriSampahController::class, 'index'])
            ->name('kategori_sampah.index');

        Route::get('/kategori-sampah/create', [KategoriSampahController::class, 'create'])
            ->name('kategori_sampah.create');

        Route::post('/kategori-sampah', [KategoriSampahController::class, 'store'])
            ->name('kategori_sampah.store');

        Route::get('/kategori-sampah/{id}', [KategoriSampahController::class, 'show'])
            ->name('kategori_sampah.show');

        Route::get('/kategori-sampah/{id}/edit', [KategoriSampahController::class, 'edit'])
            ->name('kategori_sampah.edit');

        Route::put('/kategori-sampah/{id}', [KategoriSampahController::class, 'update'])
            ->name('kategori_sampah.update');

        Route::delete('/kategori-sampah/{id}', [KategoriSampahController::class, 'destroy'])
            ->name('kategori_sampah.destroy');

        // Master Kategori (nama route tetap master_kategori_sampah.*)
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
