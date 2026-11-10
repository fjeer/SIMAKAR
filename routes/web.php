<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Mahasiswa\KeahlianController;
use App\Http\Controllers\Mahasiswa\PendidikanController;
use App\Http\Controllers\Mahasiswa\PengalamanController;
use App\Http\Controllers\Mahasiswa\PreviewController;
use App\Http\Controllers\Mahasiswa\ProfilController;
use App\Http\Controllers\Mahasiswa\SertifikasiController;
use App\Jobs\GeneratePdfCvJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard.'.Auth::user()->peran);
    }

    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::middleware('can:mahasiswa')->get('/mahasiswa/dashboard', [DashboardController::class, 'mahasiswa'])->name('dashboard.mahasiswa');

    Route::middleware('can:mahasiswa')
        ->prefix('mahasiswa')
        ->name('mahasiswa.')
        ->group(function () {
            Route::get('profil', [ProfilController::class, 'index'])->name('profil.index');
            Route::put('profil', [ProfilController::class, 'update'])->name('profil.update');
            Route::resource('pendidikan', PendidikanController::class)->except(['show', 'create', 'edit']);
            Route::resource('pengalaman', PengalamanController::class)->except(['show', 'create', 'edit']);
            Route::resource('sertifikasi', SertifikasiController::class)->except(['show', 'create', 'edit']);
            Route::resource('keahlian', KeahlianController::class)->except(['show', 'create', 'edit']);
            Route::get('preview', [PreviewController::class, 'index'])->name('preview.index');
            Route::get('preview/cv', [PreviewController::class, 'cv'])->name('preview.cv');
            Route::get('preview/download', [PreviewController::class, 'download'])->name('preview.download');
        });
    Route::middleware('can:dosen')->get('/dosen/dashboard', [DashboardController::class, 'dosen'])->name('dashboard.dosen');
    Route::middleware('can:kaprodi')->get('/kaprodi/dashboard', [DashboardController::class, 'kaprodi'])->name('dashboard.kaprodi');
    Route::middleware('can:dekan')->get('/dekan/dashboard', [DashboardController::class, 'dekan'])->name('dashboard.dekan');
    Route::middleware('can:admin')->get('/admin/dashboard', [DashboardController::class, 'admin'])->name('dashboard.admin');
});

Route::get('/test-redis', function () {
    Cache::put('test_redis_simakar', 'Berhasil terhubung ke Redis!', 60);

    $cacheValue = Cache::get('test_redis_simakar');

    GeneratePdfCvJob::dispatch(Auth::id() ?? 1);

    return response()->json([
        'success' => true,
        'message' => 'Cache berhasil ditulis dan Job berhasil masuk antrean.',
        'cache' => $cacheValue,
    ]);
});

Route::get('/test-ui', function () {
    return view('test-ui');
});
