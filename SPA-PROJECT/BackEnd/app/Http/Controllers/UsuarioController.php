<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;

class UsuarioController extends Controller
{
    // 📄 LISTAR USUARIOS
    public function index()
{
    return Usuario::where('rol_id', 4)->get();
}

    // ➕ CREAR USUARIO
    public function store(Request $request)
    {
        // 🔴 VALIDACIÓN
        $request->validate([
            'email' => 'required|email|unique:usuarios,email',
            'nombre' => 'required',
            'password' => 'required|min:6'
        ]);

        $usuario = Usuario::create([
    'nombre'    => $request->nombre,
    'apellido'  => $request->apellido,
    'email'     => $request->email,
    'telefono'  => $request->telefono,
    'password'  => $request->password ? bcrypt($request->password) : null,
    'rol_id'    => $request->rol_id,
    'activo'    => true,
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

        $request->validate([
            'email' => 'required|email|unique:usuarios,email,' . $id
        ]);

        $data = $request->all();

        if (!empty($request->password)) {
            $data['password'] = bcrypt($request->password);
        } else {
            unset($data['password']);
        }

        $usuario->update($data);

        return response()->json($usuario, 200);
    }
    
    // ❌ ELIMINAR
    public function destroy($id)
    {
        Usuario::destroy($id);

        return response()->json(['mensaje' => 'Usuario eliminado']);
    }
}