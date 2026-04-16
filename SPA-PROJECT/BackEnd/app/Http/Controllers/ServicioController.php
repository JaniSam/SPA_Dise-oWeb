<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;

class ServicioController extends Controller
{
    // GET /api/servicios
    public function index()
    {
        return response()->json(Servicio::all(), 200);
    }

    // POST /api/servicios
    public function store(Request $request)
    {
        $validado = $request->validate([
            'servicio' => 'required|string|max:100',
            'precio' => 'required|numeric'
        ]);

        $servicio = Servicio::create($validado);
        return response()->json($servicio, 201);
    }

    // GET /api/servicios/{id}
    public function show(Servicio $servicio)
    {
        return $servicio;
    }

    // PUT /api/servicios/{id}
    public function update(Request $request, Servicio $servicio)
    {
        $servicio->update($request->all());
        return response()->json($servicio, 200);
    }

    // DELETE /api/servicios/{id}
    public function destroy(Servicio $servicio)
    {
        $servicio->delete();
        return response()->json(null, 204);
    }
}