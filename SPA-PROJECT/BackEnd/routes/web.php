<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ServicioController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/servicios', [ServicioController::class, 'index']);
