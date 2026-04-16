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
    public function register(Request $request)
    {
        try {
            $request->validate([
                'nombre'   => 'required|string|max:50',
                'apellido' => 'nullable|string|max:50',
                'email'    => 'required|email|unique:usuarios,email',
                'telefono' => 'nullable|string|max:20',
                'password' => 'required|min:6',
                // ← rol_id ya no se valida, lo asignamos nosotros
            ]);

            $usuario = Usuario::create([
                'nombre'   => $request->nombre,
                'apellido' => $request->apellido,
                'email'    => $request->email,
                'telefono' => $request->telefono,
                'password' => bcrypt($request->password),
                'rol_id'   => 4, // ← siempre Cliente al registrarse
                'activo'   => true,
            ]);

            $token = $usuario->createToken('auth_token')->plainTextToken;

            return response()->json(['token' => $token, 'user' => $usuario], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Error de validación', 'fields' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error en el servidor', 'message' => $e->getMessage()], 500);
        }
    }
}