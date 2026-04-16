<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva; // <--- AGREGÁ ESTA LÍNEA AQUÍ ARRIBA

class ReservaController extends Controller
{
    public function store(Request $request)
{
    try {
        $reserva = \App\Models\Reserva::create([
    'cliente_id'    => $request->usuario_id, 
    'terapeuta_id'  => $request->especialista_id, 
    'servicio_id'   => $request->servicio_id ?? 1, 
    'fecha'         => $request->fecha, 
    'hora_inicio'   => $request->hora_inicio, 
    'hora_fin'      => $request->hora_fin ?? $request->hora_inicio, 
    'monto_total'   => $request->monto ?? 0,
    'estado'        => 'Confirmado', // <--- Así, directo entre comillas
    ]);

        return response()->json($reserva, 201);

    } catch (\Exception $e) {
        // ESTO ES CLAVE: Si falla, te va a decir la verdad en el Preview
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
}