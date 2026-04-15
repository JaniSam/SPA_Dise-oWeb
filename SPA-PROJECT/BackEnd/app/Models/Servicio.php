<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    use HasFactory;

    // Nombre de la tabla (opcional si sigue la convención: servicios)
    protected $table = 'servicios';

    // Campos que se pueden llenar (importante para insert/update)
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'duracion',
        'estado'
    ];

    // Si no usas created_at y updated_at puedes desactivarlo
    // public $timestamps = false;
}