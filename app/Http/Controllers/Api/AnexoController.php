<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAnexoRequest;
use App\Models\Anexo;

class AnexoController extends Controller
{
    public function index(Request $request)
    {
        $q = Anexo::query();
        if ($request->filled('search')) {
            $q->where('nombre', 'like', '%' . $request->search . '%');
        }
        return $q->paginate(20);
    }

    public function store(StoreAnexoRequest $request)
    {
        $data = $request->validated();

        $maestro = Anexo::create($data);
        return response()->json($maestro, 201);
    }

    public function show(Anexo $maestro)
    {
        return $maestro->load('anexos');
    }

    public function update(Request $request, Anexo $maestro)
    {
        $data = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'direccion' => 'sometimes|required|string|max:255',
            'user_id' => 'sometimes|exists:users,id',
            'fecha_creacion' => 'required|date|format:Y-m-d',
            'logo' => 'nullable',
            'activo' => 'sometimes|boolean',
        ]);
        $maestro->update($data);
        return response()->json($maestro);
    }

    public function destroy(Anexo $maestro)
    {
        $maestro->delete();
        return response()->noContent();
    }
}
