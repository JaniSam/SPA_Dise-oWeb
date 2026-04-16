<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'clientes';

    // Campos que permitimos recibir desde el Service de Vue
    protected $fillable = [
        'numero_documento',
        'nombres',
        'apellidos',
        'email',
        'telefono'
    ];
}