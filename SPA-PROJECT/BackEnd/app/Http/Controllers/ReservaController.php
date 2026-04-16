<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Usuario;
use App\Models\Servicio;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    // GET /api/reservas — lista todas con relaciones
    public function index()
    {
        $reservas = Reserva::with(['cliente', 'terapeuta', 'servicio'])->get();
        return response()->json($reservas, 200);
    }

    // POST /api/reservas — crear nueva reserva
    public function store(Request $request)
    {
        $validado = $request->validate([
            'cliente_id'    => 'required|integer|exists:usuarios,id',
            'terapeuta_id'  => 'required|integer|exists:usuarios,id',
            'servicio_id'   => 'required|integer|exists:servicios,id',
            'sala_id'       => 'nullable|integer|exists:salas,id',
            'fecha'         => 'required|date',
            'hora_inicio'   => 'required|date_format:H:i',
            'hora_fin'      => 'required|date_format:H:i|after:hora_inicio',
            'estado'        => 'nullable|string|in:PENDIENTE,CONFIRMADA,CANCELADA,COMPLETADA',
        ]);

        if (!isset($validado['estado'])) {
            $validado['estado'] = 'PENDIENTE';
        }

        $reserva = Reserva::create($validado);
        // Cargamos las relaciones para devolver datos completos
        $reserva->load(['cliente', 'terapeuta', 'servicio']);

        return response()->json($reserva, 201);
    }

    // GET /api/reservas/{id}
    public function show($id)
    {
        $reserva = Reserva::with(['cliente', 'terapeuta', 'servicio'])->findOrFail($id);
        return response()->json($reserva, 200);
    }

    // PUT /api/reservas/{id} — actualizar reserva
    public function update(Request $request, $id)
    {
        $reserva = Reserva::findOrFail($id);

        $request->validate([
            'cliente_id'   => 'sometimes|integer|exists:usuarios,id',
            'terapeuta_id' => 'sometimes|integer|exists:usuarios,id',
            'servicio_id'  => 'sometimes|integer|exists:servicios,id',
            'sala_id'      => 'nullable|integer|exists:salas,id',
            'fecha'        => 'sometimes|date',
            'hora_inicio'  => 'sometimes|date_format:H:i',
            'hora_fin'     => 'sometimes|date_format:H:i',
            'estado'       => 'sometimes|string|in:PENDIENTE,CONFIRMADA,CANCELADA,COMPLETADA',
        ]);

        $reserva->update($request->all());
        $reserva->load(['cliente', 'terapeuta', 'servicio']);

        return response()->json($reserva, 200);
    }

    // DELETE /api/reservas/{id}
    public function destroy($id)
    {
        Reserva::destroy($id);
        return response()->json(null, 204);
    }

    // GET /api/reservas/form-data — trae clientes, terapeutas y servicios para los selects
    public function formData()
    {
        return response()->json([
            'clientes'   => Usuario::where('rol_id', 4)->where('activo', true)
                                ->select('id', 'nombre', 'apellido', 'cedula')->get(),
            'terapeutas' => Usuario::where('rol_id', 3)->where('activo', true)
                                ->select('id', 'nombre', 'apellido')->get(),
            'servicios'  => Servicio::where('activo', true)
                                ->select('id', 'nombre', 'duracion_minutos', 'precio')->get(),
        ]);
    }
}