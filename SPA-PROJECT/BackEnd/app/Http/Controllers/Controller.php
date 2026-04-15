<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;

class ServicioController extends Controller
{
    // 📌 Listar todos los servicios
    public function index()
    {
        return response()->json(Servicio::all(), 200);
    }

    // 📌 Crear un nuevo servicio
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'descripcion' => 'required|string',
            'precio' => 'required|numeric',
            'duracion' => 'required|integer',
            'estado' => 'required|string'
        ]);

        $servicio = Servicio::create($request->all());

        return response()->json([
            'mensaje' => 'Servicio creado correctamente',
            'data' => $servicio
        ], 201);
    }

    // 📌 Mostrar un servicio por ID
    public function show($id)
    {
        $servicio = Servicio::find($id);

        if (!$servicio) {
            return response()->json(['mensaje' => 'No encontrado'], 404);
        }

        return response()->json($servicio, 200);
    }

    // 📌 Actualizar servicio
    public function update(Request $request, $id)
    {
        $servicio = Servicio::find($id);

        if (!$servicio) {
            return response()->json(['mensaje' => 'No encontrado'], 404);
        }

        $servicio->update($request->all());

        return response()->json([
            'mensaje' => 'Actualizado correctamente',
            'data' => $servicio
        ], 200);
    }

    // 📌 Eliminar servicio
    public function destroy($id)
    {
        $servicio = Servicio::find($id);

        if (!$servicio) {
            return response()->json(['mensaje' => 'No encontrado'], 404);
        }

        $servicio->delete();

        return response()->json([
            'mensaje' => 'Eliminado correctamente'
        ], 200);
    }
}
