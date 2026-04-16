<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'usuarios';
    
    // ← ESTAS DOS LÍNEAS SON LA CLAVE
    public $timestamps = false;
    const CREATED_AT = 'creado_en';

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'telefono',
        'cedula',
        'password',
        'rol_id',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}