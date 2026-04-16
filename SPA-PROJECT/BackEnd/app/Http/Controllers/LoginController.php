<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario; // Asegúrate de que tu modelo se llame Usuario
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validar que el usuario envió los datos
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Buscar al usuario en la tabla 'usuarios'
        // Incluimos la relación con el rol si la tienes configurada
        $usuario = Usuario::where('email', $request->email)->first();

        // 3. Verificar si existe y si la contraseña es correcta
        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            return response()->json([
                'error' => 'Credenciales inválidas. Revisa tu correo o contraseña.'
            ], 401);
        }

        // 4. Verificar si el usuario está activo
        if (!$usuario->activo) {
            return response()->json(['error' => 'Tu cuenta está desactivada.'], 403);
        }

        // 5. Respuesta exitosa con el ROL
        return response()->json([
            'mensaje' => 'Bienvenido al sistema',
            'user' => [
                'id' => $usuario->id,
                'nombre' => $usuario->nombre,
                'apellido' => $usuario->apellido,
                'email' => $usuario->email,
                'rol_id' => $usuario->rol_id, // Aquí va el 1, 2, 3 o 4 de tu DB
            ],
            // Si usas Sanctum, aquí generarías el token:
            // 'token' => $usuario->createToken('token-name')->plainTextToken,
        ]);
    }
}