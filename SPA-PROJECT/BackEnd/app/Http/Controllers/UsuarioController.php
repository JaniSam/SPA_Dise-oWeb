<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;

class UsuarioController extends Controller
{
    // 📄 LISTAR USUARIOS
    public function index()
    {
        return Usuario::all();
    }

    // ➕ CREAR USUARIO
    public function store(Request $request)
    {
        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'password' => bcrypt($request->password),
            'rol_id' => $request->rol_id,
            'activo' => true
        ]);

        return response()->json($usuario, 201);
    }

    // 🔍 VER UNO
    public function show($id)
    {
        return Usuario::findOrFail($id);
    }

    // ✏️ ACTUALIZAR
    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $usuario->update($request->all());

        return response()->json($usuario, 200);
    }

    // ❌ ELIMINAR
    public function destroy($id)
    {
        Usuario::destroy($id);

        return response()->json(['mensaje' => 'Usuario eliminado']);
    }
}