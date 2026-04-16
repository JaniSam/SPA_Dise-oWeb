<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $table = 'reservas';

    // ESTA ES LA LÍNEA QUE VA A SOLUCIONAR EL ERROR:
    public $timestamps = false; 

    protected $fillable = [
        'cliente_id', 
        'terapeuta_id', 
        'servicio_id', 
        'fecha', 
        'hora_inicio', 
        'hora_fin', 
        'monto_total', 
        'estado',
    ];
}