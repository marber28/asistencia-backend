<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAnexoRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\Anexo;
use App\Models\UserAnexoAula;

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

    public function list(Request $request)
    {
        // Obtener todos los anexos
        $anexos = Anexo::where('activo', true)
            ->get();
        return response()->json($anexos);
    }

    public function store(StoreAnexoRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('anexos', 'public');
        }

        unset($data['user_id']);

        $anexo = Anexo::create($data);

        //Asignar responsable de anexo
        UserAnexoAula::firstOrCreate([
            'anexo_id' => $anexo->id,
            'user_id' => $request->input('user_id'),
        ]);

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

        unset($data['user_id']);

        $anexo->update($data);

        //Asignar/actualizar responsable de anexo
        UserAnexoAula::updateOrCreate([
            'anexo_id' => $anexo->id,
            'user_id' => $request->input('user_id'),
        ]);

        return response()->json($anexo);
    }

    public function destroy(Anexo $anexo)
    {
        $anexo->delete();
        return response()->noContent();
    }
}
