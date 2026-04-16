<?php

namespace App\Models;

// Cambiamos el uso de Model por Authenticatable
use Illuminate\Foundation\Auth\User as Authenticatable;
//use Laravel\Sanctum\HasApiTokens;


class Usuario extends Authenticatable
{
    ////use HasApiTokens; // Importante para la comunicación con Vue

    protected $table = 'usuarios';
    public $timestamps = false; 

    protected $fillable = [
        'nombre', 'apellido', 'email', 'telefono', 'password', 'rol_id', 'activo'
    ];

    protected $hidden = [
        'password', // Para que la contraseña no viaje al frontend
    ];
}