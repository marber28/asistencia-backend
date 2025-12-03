<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMaestroRequest;
use App\Models\Maestro;

class MaestroController extends Controller
{
    public function index(Request $request)
    {
        $q = Maestro::query();
        if ($request->filled('search')) {
            $q->where('nombres', 'like', '%' . $request->search . '%')
                ->orWhere('apellidos', 'like', '%' . $request->search . '%')
                ->orWhere('telefono', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('activo')) {
            $q->where('activo', filter_var($request->activo, FILTER_VALIDATE_BOOLEAN));
        }
        return $q->paginate(20);
    }

    public function store(StoreMaestroRequest $request)
    {
        $data = $request->validated();
        $maestro = Maestro::create($data);
        return response()->json($maestro, 201);
    }

    public function show(Maestro $maestro)
    {
        return $maestro->load('aulas');
    }

    public function update(Request $request, Maestro $maestro)
    {
        $data = $request->validate([
            'nombres' => 'sometimes|required|string|max:255',
            'apellidos' => 'sometimes|required|string|max:255',
            'email' => 'nullable|email|unique:maestros,email,' . $maestro->id,
            'telefono' => 'nullable|numeric|unique:maestros,telefono,' . $maestro->id,
            'activo' => 'sometimes|boolean',
        ]);
        $maestro->update($data);
        return response()->json($maestro);
    }

    public function destroy(Maestro $maestro)
    {
        $maestro->delete();
        return response()->noContent();
    }
}
