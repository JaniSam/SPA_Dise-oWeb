<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $table = 'reservas';
    public $timestamps = false;
    const CREATED_AT = 'creado_en';

    protected $fillable = [
        'cliente_id',
        'terapeuta_id',
        'servicio_id',
        'sala_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'estado',
    ];

    // Relación con cliente
    public function cliente()
    {
        return $this->belongsTo(Usuario::class, 'cliente_id');
    }

    // Relación con terapeuta
    public function terapeuta()
    {
        return $this->belongsTo(Usuario::class, 'terapeuta_id');
    }

    // Relación con servicio
    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id');
    }
}