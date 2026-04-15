<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\ClienteController;

Route::apiResource('usuarios', UsuarioController::class);
Route::apiResource('servicios', ServicioController::class);
Route::post('clientes', [ClienteController::class, 'store']);