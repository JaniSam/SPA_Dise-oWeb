<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    // Apuntamos a tu tabla existente
    protected $table = 'usuarios'; 
    
    // Desactivamos timestamps si tu tabla no tiene 'updated_at'
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

    protected $hidden = [
        'password',
    ];
}
