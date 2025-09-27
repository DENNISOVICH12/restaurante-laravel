<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\MenuController;


Route::get('/ping', [HealthController::class, 'ping']);

// Clientes
Route::get('/clientes', [ClienteController::class, 'index']);
Route::get('/clientes/{id}', [ClienteController::class, 'show']);
Route::post('/clientes', [ClienteController::class, 'store']);
Route::put('/clientes/{id}', [ClienteController::class, 'update']);
Route::delete('/clientes/{id}', [ClienteController::class, 'destroy']);

// Menú-Items
Route::get('/menu-items', [MenuItemController::class, 'index']);
Route::get('/menu-items/{id}', [MenuItemController::class, 'show']);
Route::post('/menu-items', [MenuItemController::class, 'store']);
Route::put('/menu-items/{id}', [MenuItemController::class, 'update']);
Route::delete('/menu-items/{id}', [MenuItemController::class, 'destroy']);

// Menú
Route::get('/menu/today', [MenuController::class, 'showToday']);
// usuarios
Route::apiResource('usuarios', UsuarioController::class);


// Pedidos
Route::get('/pedidos', [PedidoController::class, 'index']);
Route::get('/pedidos/{id}', [PedidoController::class, 'show']);
Route::post('/pedidos', [PedidoController::class, 'store']);
Route::put('/pedidos/{id}', [PedidoController::class, 'update']);
Route::delete('/pedidos/{id}', [PedidoController::class, 'destroy']);
Route::put   ('pedidos/{id}',        [PedidoController::class, 'update']);
Route::delete('pedidos/{id}',        [PedidoController::class, 'destroy']);
Route::get   ('pedidos/{id}/detalle',[PedidoController::class, 'detalle']);

// Detalle (lectura; crear/editar/eliminar se haría en otra iteración si quieres)
Route::get('/pedidos/{id}/detalle', [PedidoController::class, 'detalle']);
Route::apiResource('usuarios', UsuarioController::class);
Route::post('login', [UsuarioController::class, 'login']);
use App\Http\Controllers\PlatoTableController;
use App\Http\Controllers\BebidaTableController;

Route::get('platos-fisicos', [PlatoTableController::class, 'index']);
Route::get('bebidas-fisicas', [BebidaTableController::class, 'index']);


