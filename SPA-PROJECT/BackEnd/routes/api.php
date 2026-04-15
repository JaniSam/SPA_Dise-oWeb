<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ServicioController;

Route::apiResource('usuarios', UsuarioController::class);
Route::apiResource('servicios', ServicioController::class);
