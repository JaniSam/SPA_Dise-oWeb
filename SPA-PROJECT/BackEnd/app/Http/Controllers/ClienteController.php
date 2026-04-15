<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use Illuminate\Support\Facades\Hash;

class ClienteController extends Controller
{
    public function store(Request $request)
    {
        // Validamos los datos según tu estructura
        $validated = $request->validate([
            'nombre'   => 'required|string|max:50',
            'apellido' => 'nullable|string|max:50',
            'email'    => 'required|email|unique:usuarios,email',
            'telefono' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
        ]);

        // Creamos el registro
        $cliente = Cliente::create([
            'nombre'   => $validated['nombre'],
            'apellido' => $validated['apellido'],
            'email'    => $validated['email'],
            'telefono' => $validated['telefono'],
            'password' => Hash::make($validated['password']), // Siempre encriptada
            'rol_id'   => 4,    // Forzamos el ID de Cliente
            'activo'   => true, // Por defecto activo
        ]);

        return response()->json([
            'res' => true,
            'msg' => 'Cliente guardado correctamente',
            'data' => $cliente
        ], 201);
    }
}
