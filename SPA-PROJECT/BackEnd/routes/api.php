<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\ClienteController;

Route::post('/reservas', [ReservaController::class, 'store']);
Route::apiResource('usuarios', UsuarioController::class);
Route::apiResource('clientes', ClienteController::class);