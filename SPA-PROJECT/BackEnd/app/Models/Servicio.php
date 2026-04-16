<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    // Definimos los campos de tu tabla
    protected $fillable = [
        'nombre',
        'precio', 
        'duracion_minutos', 
        'descripcion', 
        'activo'
    ];
    
    // Si no usas los campos created_at y updated_at, desactívalos:
    public $timestamps = false;
}