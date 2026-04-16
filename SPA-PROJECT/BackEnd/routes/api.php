<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\LoginController; 
use App\Http\Controllers\ReservaController;

Route::get('/reservas/form-data', [ReservaController::class, 'formData']);
Route::apiResource('reservas', ReservaController::class);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [LoginController::class, 'register']);
Route::apiResource('usuarios', UsuarioController::class);
Route::apiResource('servicios', ServicioController::class);

