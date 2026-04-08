<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuarios';

    public $timestamps = false; 

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'telefono',
        'password',
        'rol_id',
        'activo'
    ];
}
