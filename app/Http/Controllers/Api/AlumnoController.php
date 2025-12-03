<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAlumnoRequest;
use App\Models\Alumno;

class AlumnoController extends Controller
{
    public function index(Request $request)
    {
        $q = Alumno::with('anexo');
        if ($request->filled('search')) {
            $q->where('nombres', 'like', '%' . $request->search . '%')
                ->orWhere('apellidos', 'like', '%' . $request->search . '%');
        }
        return $q->paginate(20);
    }

    public function store(StoreAlumnoRequest $request)
    {
        $data = $request->validated();

        $alumno = Alumno::create($data);
        return response()->json($alumno, 201);
    }

    public function show(Alumno $alumno)
    {
        return $alumno->load('aulas');
    }

    public function update(Request $request, Alumno $alumno)
    {
        $data = $request->validate([
            'nombres' => 'sometimes|required|string|max:255',
            'apellidos' => 'sometimes|required|string|max:255',
            'anexo_id' => 'required|exists:anexos,id',
            'fecha_nacimiento' => 'nullable|date:format:Y-m-d',
        ]);
        $alumno->update($data);
        return response()->json($alumno);
    }

    public function destroy(Alumno $alumno)
    {
        $alumno->delete();
        return response()->noContent();
    }
}
