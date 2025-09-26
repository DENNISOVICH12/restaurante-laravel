<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicController;

Route::get('/', fn () => redirect()->route('login'));

// --- Auth ---
Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',   [AuthController::class, 'doLogin']);
Route::get('/logout',   [AuthController::class, 'logout'])->name('logout');

Route::get('/registro', [AuthController::class, 'showRegister'])->name('registro');
Route::post('/registro',[AuthController::class, 'doRegister'])->name('registro.post');

// --- Paneles por rol ---
Route::view('/administracion', 'administracion')
    ->middleware('role:admin')
    ->name('admin.panel');

Route::view('/cocina', 'cocina')
    ->middleware('role:admin,cocinero')
    ->name('cocina.panel');

Route::view('/meseros', 'meseros') // usa 'meseros' o 'mesero' según el nombre real de tu blade
    ->middleware('role:admin,mesero')
    ->name('meseros.panel');

Route::view('/dashboard', 'dashboard')
    ->middleware('role:cliente,admin,mesero,cocinero') // o solo 'cliente' si prefieres
    ->name('cliente.panel');

// --- Pública (link por QR / mesa) ---
Route::get('/orden/{mesa?}', [PublicController::class, 'orden'])->name('orden.publica');
