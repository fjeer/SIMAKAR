<?php

use App\Http\Controllers\AuthController;
use App\Jobs\GeneratePdfCvJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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

Route::get('/dashboard', function () {
    return 'Selamat datang di SIMAKAR';
})->middleware('auth')->name('dashboard');

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
