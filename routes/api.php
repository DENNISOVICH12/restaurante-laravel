<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HealthController,
    ClienteController,
    MenuItemController,
    PedidoController,
    UsuarioController,
    MenuController,
    Auth\ApiLoginController,
    PlatoTableController,
    BebidaTableController
};

/*
|--------------------------------------------------------------------------
| Rutas Públicas (sin autenticación)
|--------------------------------------------------------------------------
*/

Route::get('/ping', [HealthController::class, 'ping']);

// 🔐 Login público
Route::post('login', [ApiLoginController::class, 'login']);

// Menú público del día
Route::get('/menu/today', [MenuController::class, 'showToday']);

// Recursos de solo lectura públicos (si lo deseas)
Route::get('platos-fisicos', [PlatoTableController::class, 'index']);
Route::get('bebidas-fisicas', [BebidaTableController::class, 'index']);

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (requieren token Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware([])->group(function () {

    // --- Logout ---
    Route::post('logout', [\App\Http\Controllers\Auth\ApiLoginController::class, 'logout'])->middleware('auth:sanctum');
    /*
    |--------------------------------------------------------------------------
    | CLIENTES
    |--------------------------------------------------------------------------
    */
    Route::get('/clientes', [ClienteController::class, 'index']);
    Route::get('/clientes/{id}', [ClienteController::class, 'show']);
    Route::post('/clientes', [ClienteController::class, 'store']);
    Route::put('/clientes/{id}', [ClienteController::class, 'update']);
    Route::delete('/clientes/{id}', [ClienteController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | MENU ITEMS
    |--------------------------------------------------------------------------
    */
    Route::post('/menu-items', [MenuItemController::class, 'store']);
    Route::get('/menu-items', [MenuItemController::class, 'index']);
    Route::get('/menu-items/{id}', [MenuItemController::class, 'show']);
    Route::post('/menu-items', [MenuItemController::class, 'store'])->middleware('can:admin');
    Route::put('/menu-items/{id}', [MenuItemController::class, 'update'])->middleware('can:admin');
    Route::delete('/menu-items/{id}', [MenuItemController::class, 'destroy'])->middleware('can:admin');
    Route::post('/menu-items/bulk', [MenuItemController::class, 'storeBulk'])->middleware('can:admin');

    /*
    |--------------------------------------------------------------------------
    | PEDIDOS
    |--------------------------------------------------------------------------
    */
    Route::get('/pedidos', [PedidoController::class, 'index']);
    Route::get('/pedidos/{id}', [PedidoController::class, 'show']);
    Route::post('/pedidos', [PedidoController::class, 'store']);
    Route::put('/pedidos/{id}', [PedidoController::class, 'update']);
    Route::delete('/pedidos/{id}', [PedidoController::class, 'destroy']);
    Route::get('/pedidos/{id}/detalle', [PedidoController::class, 'detalle']);

    /*
    |--------------------------------------------------------------------------
    | USUARIOS (Solo administradores)
    |--------------------------------------------------------------------------
    */
    Route::middleware('can:admin')->group(function () {
        Route::apiResource('usuarios', UsuarioController::class);
        Route::get('admin/dashboard', fn() => response()->json(['ok' => true, 'message' => 'Panel de administración']));
    });
});
