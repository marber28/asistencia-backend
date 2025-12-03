<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAsistenciaAlumnoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'aula_id' => 'required|exists:aulas,id',
            'dia' => 'required|date',
            'asistencias' => 'required|array',
            'asistencias.*.alumno_id' => 'required|exists:alumnos,id',
            'asistencias.*.estado' => 'required|in:presente,ausente,tarde,justificado'
        ];
    }
}
