<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAnexoRequest;
use Illuminate\Support\Facades\Storage;
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

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('anexos', 'public');
        }

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
            'nombre' => 'sometimes|string|max:255',
            'direccion' => 'sometimes|string|max:255',
            'user_id' => 'sometimes|exists:users,id',
            'fecha_creacion' => 'sometimes|date|date_format:Y-m-d',
            'logo' => 'nullable|image|mimes:jpeg,png|max:500',
            'activo' => 'sometimes|boolean',
        ]);

        if ($request->hasFile('logo')) {
            // eliminar archivo anterior si existe
            if ($anexo->logo && Storage::disk('public')->exists($anexo->logo)) {
                Storage::disk('public')->delete($anexo->logo);
            }
            $data['logo'] = $request->file('logo')->store('anexos', 'public');
        }

        $anexo->update($data);

        return response()->json($anexo);
    }

    public function destroy(Anexo $anexo)
    {
        $anexo->delete();
        return response()->noContent();
    }
}
