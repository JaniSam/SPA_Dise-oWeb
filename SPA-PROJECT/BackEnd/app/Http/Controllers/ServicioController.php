<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Servicio;

class ServicioController extends Controller
{
    /**
     * Mostrar todos los servicios
     */
    public function index()
    {
        return response()->json(Servicio::all());
    }

    /**
     * Crear un nuevo servicio
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric'
        ]);

        $servicio = Servicio::create([
            'nombre' => $request->nombre,
            'precio' => $request->precio
        ]);

        return response()->json([
            'message' => 'Servicio creado correctamente',
            'data' => $servicio
        ], 201);
    }

    /**
     * Mostrar un servicio específico
     */
    public function show($id)
    {
        $servicio = Servicio::find($id);

        if (!$servicio) {
            return response()->json([
                'message' => 'Servicio no encontrado'
            ], 404);
        }

        return response()->json($servicio);
    }

    /**
     * Actualizar un servicio
     */
    public function update(Request $request, $id)
    {
        $servicio = Servicio::find($id);

        if (!$servicio) {
            return response()->json([
                'message' => 'Servicio no encontrado'
            ], 404);
        }

        $servicio->update($request->only(['nombre', 'precio']));

        return response()->json([
            'message' => 'Servicio actualizado correctamente',
            'data' => $servicio
        ]);
    }

    /**
     * Eliminar un servicio
     */
    public function destroy($id)
    {
        $servicio = Servicio::find($id);

        if (!$servicio) {
            return response()->json([
                'message' => 'Servicio no encontrado'
            ], 404);
        }

        $servicio->delete();

        return response()->json([
            'message' => 'Servicio eliminado correctamente'
        ]);
    }
}