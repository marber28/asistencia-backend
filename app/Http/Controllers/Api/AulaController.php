<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Aula;
use Illuminate\Validation\Rule;
use App\Http\Requests\StoreAulaRequest;

class AulaController extends Controller
{
    public function index(Request $request)
    {
        $q = Aula::query();
        if ($request->filled('search')) {
            $q->where('nombre', 'like', '%' . $request->search . '%');
        }
        return $q->paginate(20);
    }

    public function store(StoreAulaRequest $request)
    {
        $data = $request->validated();

        $aula = Aula::create($data);
        return response()->json($aula, 201);
    }

    public function show(Aula $aula)
    {
        return $aula->load('maestros');
    }

    public function update(Request $request, Aula $aula)
    {
        $data = $request->validate([
            'nombre' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('aulas')->ignore($aula->id)],
            'edad_min' => 'nullable|integer|min:0',
            'edad_max' => 'nullable|integer|min:0',
            'descripcion' => 'nullable|string',
        ]);
        $aula->update($data);
        return response()->json($aula);
    }

    public function destroy(Aula $aula)
    {
        $aula->delete();
        return response()->noContent();
    }
}
