<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMaestroRequest;
use App\Models\User;

class MaestroController extends Controller
{
    public function index(Request $request)
    {
        $q = User::query();
        $q->with('anexosConAulas')
            ->whereHas('roles', function ($query) {
                $query->where('name', 'maestro');
            });

        if ($request->filled('search')) {
            $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('lastname', 'like', '%' . $request->search . '%')
                ->orWhere('phone', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('enabled')) {
            $q->where('enabled', filter_var($request->enabled, FILTER_VALIDATE_BOOLEAN));
        }
        return $q->paginate(20);
    }

    public function store(StoreMaestroRequest $request)
    {
        $data = $request->validated();
        //remover de data aula_id y anexo_id antes de crear maestro
        unset($data['aula_id']);
        unset($data['anexo_id']);
        $maestro = User::create($data);

        // STORE o UPDATE (comparten la misma lógica)
        if ($request->has('asignaciones')) {
            // Validar: Un maestro no puede tener dos aulas para el mismo anexo
            $asignaciones = collect($request->asignaciones);
            $duplicados = $asignaciones->groupBy('anexo_id')->filter(fn($g) => $g->count() > 1);

            if ($duplicados->isNotEmpty()) {
                return response()->json([
                    'message' => 'Solo se puede asignar una aula por anexo.',
                    'detalles' => $duplicados
                ], 422);
            }

            $asignaciones = collect($request->asignaciones)
                ->filter(fn($a) => !empty($a['anexo_id']) && !empty($a['aula_id']))
                ->mapWithKeys(fn($a) => [
                    $a['anexo_id'] => [
                        'aula_id' => $a['aula_id'],
                        'user_id' => $maestro->id
                    ]
                ])
                ->toArray();

            $maestro->anexosConAulas()->sync($asignaciones);
        }

        return response()->json($maestro, 201);
    }

    public function show(User $maestro)
    {
        return $maestro->load('aulas');
    }

    public function update(Request $request, User $maestro)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'lastname' => 'sometimes|required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $maestro->id,
            'phone' => 'nullable|numeric|unique:users,phone,' . $maestro->id,
            'enabled' => 'sometimes|boolean',

            //relacion a tabla anexo_maestro_aula
            'aula_id' => 'sometimes|exists:aulas,id',
            'anexo_id' => 'sometimes|exists:anexos,id',
        ]);
        //remover de data aula_id y anexo_id antes de crear maestro
        unset($data['aula_id']);
        unset($data['anexo_id']);
        $maestro->update($data);

        if ($request->has('asignaciones')) {
            $asignaciones = collect($request->asignaciones)
                ->filter(fn($a) => !empty($a['anexo_id']) && !empty($a['aula_id']))
                ->mapWithKeys(fn($a) => [
                    $a['anexo_id'] => [
                        'aula_id' => $a['aula_id'],
                        'user_id' => $maestro->id
                    ]
                ])
                ->toArray();

            $maestro->anexosConAulas()->sync($asignaciones);
        }

        return response()->json($maestro);
    }

    public function destroy(User $maestro)
    {
        $maestro->delete();
        return response()->noContent();
    }
}
