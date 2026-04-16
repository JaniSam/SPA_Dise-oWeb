<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\LoginController; 

Route::post('/login', [LoginController::class, 'login']);
Route::apiResource('usuarios', UsuarioController::class);
Route::apiResource('servicios', ServicioController::class);
Route::apiResource('clientes', [ClienteController::class, 'store']);
