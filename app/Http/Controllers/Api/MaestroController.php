<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMaestroRequest;
use App\Models\Maestro;
use App\Models\AnexoMaestroAula;
use App\Models\User;

class MaestroController extends Controller
{
    public function index(Request $request)
    {
        $q = Maestro::query();
        $q->with('anexos', 'aulas');
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
        //remover de data aula_id y anexo_id antes de crear maestro
        unset($data['aula_id']);
        unset($data['anexo_id']);
        $maestro = Maestro::create($data);

        //crear relacion en tabla anexo_maestro_aula si vienen los ids
        if ($request->filled('anexo_id') && $request->filled('aula_id')) {
            AnexoMaestroAula::create([
                'anexo_id' => $request->anexo_id,
                'maestro_id' => $maestro->id,
                'aula_id' => $request->aula_id,
                'current' => true,
            ]);
        }

        //crear usuario asociado
        $pl = User::firstOrCreate(['email' => $maestro->email], [
            'name' => $maestro->nombres,
            'lastname' => $maestro->apellidos,
            'password' => bcrypt('123456'),
            'in_anexo' => 1,
            'visible' => 1,
            'enabled' => 1
        ]);
        $pl->assignRole('maestro');

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

            //relacion a tabla anexo_maestro_aula
            'aula_id' => 'sometimes|exists:aulas,id',
            'anexo_id' => 'sometimes|exists:anexos,id',
        ]);
        //remover de data aula_id y anexo_id antes de crear maestro
        unset($data['aula_id']);
        unset($data['anexo_id']);
        $maestro->update($data);

        //update/create relacion en tabla anexo_maestro_aula si vienen los ids
        if ($request->filled('anexo_id') && $request->filled('aula_id')) {
            $relation = AnexoMaestroAula::where('maestro_id', $maestro->id)->first();
            if ($relation) {
                $relation->update([
                    'anexo_id' => $request->anexo_id,
                    'aula_id' => $request->aula_id,
                    'current' => true,
                ]);
            } else {
                AnexoMaestroAula::create([
                    'anexo_id' => $request->anexo_id,
                    'maestro_id' => $maestro->id,
                    'aula_id' => $request->aula_id,
                    'current' => true,
                ]);
            }
        }

        //actualizar suario asociado
        $user = User::where('email', $maestro->email)->first();
        if ($user) {
            $user->name = $maestro->nombres;
            $user->lastname = $maestro->apellidos;
            $user->save();

            //actualizar rol de usuario
            if (!$user->hasRole('maestro')) {
                $user->assignRole('maestro');
            }
        }

        return response()->json($maestro);
    }

    public function destroy(Maestro $maestro)
    {
        $maestro->delete();
        return response()->noContent();
    }
}
