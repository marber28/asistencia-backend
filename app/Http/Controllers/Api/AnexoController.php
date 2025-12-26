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
        $perPage = $request->get('per_page', 10);

        $q->with('user');
        if ($request->filled('search')) {
            $q->where('nombre', 'like', '%' . $request->search . '%');
        }
        return $q->paginate($perPage);
    }

    public function store(StoreAnexoRequest $request)
    {
        $data = $request->validated();

        $anexo = Anexo::create($data);
        return response()->json($anexo, 201);
    }

    public function show(Anexo $anexo)
    {
        return $anexo->load('anexos');
    }

    public function update(Request $request, Anexo $anexo)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'fecha_creacion' => 'required|date|date_format:Y-m-d',
            'logo' => 'nullable',
            'activo' => 'sometimes|boolean',
        ]);
        $anexo->update($data);
        return response()->json($anexo);
    }

    public function destroy(Anexo $anexo)
    {
        $anexo->delete();
        return response()->noContent();
    }
}
