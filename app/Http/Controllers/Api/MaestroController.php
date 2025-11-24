<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Maestro;

class MaestroController extends Controller
{
    public function index(Request $request)
    {
        $q = Maestro::query();
        if ($request->filled('search')) {
            $q->where('nombres', 'like', '%' . $request->search . '%')
                ->orWhere('apellidos', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('activo')) {
            $q->where('activo', filter_var($request->activo, FILTER_VALIDATE_BOOLEAN));
        }
        return $q->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email' => 'nullable|email|unique:maestros,email',
            'activo' => 'sometimes|boolean',
        ]);
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
