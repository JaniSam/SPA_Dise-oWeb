<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario; // Verifica que tu modelo se llame así
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Para ver errores en el log

class LoginController extends Controller
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            // Intento de login manual para asegurar que use tu tabla 'usuarios'
            $user = Usuario::where('email', $credentials['email'])->first();

            if ($user && Hash::check($credentials['password'], $user->password)) {
                // Si la clave es correcta, generamos el token
                // Nota: Asegúrate de tener instalado Laravel Sanctum
                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'token' => $token,
                    'user' => $user
                ], 200);
            }

            return response()->json(['error' => 'Credenciales inválidas'], 401);

        } catch (\Exception $e) {
            // Si algo falla, esto nos dirá qué es en formato JSON en vez de código HTML
            return response()->json([
                'error' => 'Error en el servidor',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}