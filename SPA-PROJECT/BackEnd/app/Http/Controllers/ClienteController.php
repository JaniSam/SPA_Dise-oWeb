<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    // GET: Listar clientes
    public function index() {
        return Cliente::all();
    }

    // POST: Guardar cliente
    public function store(Request $request) {
        $data = $request->validate([
            'numero_documento' => 'required|unique:clientes',
            'nombres'          => 'required|string',
            'apellidos'        => 'required|string',
            'email'            => 'required|email|unique:clientes',
            'telefono'         => 'nullable'
        ]);

        return Cliente::create($data);
    }

    // GET: Ver un cliente
    public function show(Cliente $cliente) {
        return $cliente;
    }

    // PUT: Actualizar cliente
    public function update(Request $request, Cliente $cliente) {
        $data = $request->validate([
            'numero_documento' => "required|unique:clientes,numero_documento,{$cliente->id}",
            'nombres'          => 'required|string',
            'apellidos'        => 'required|string',
            'email'            => "required|email|unique:clientes,email,{$cliente->id}",
            'telefono'         => 'nullable'
        ]);

        $cliente->update($data);
        return $cliente;
    }

    // DELETE: Eliminar cliente
    public function destroy(Cliente $cliente) {
        $cliente->delete();
        return response()->noContent();
    }
}

