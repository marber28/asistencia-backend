<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaestroRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {
        return [
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email' => 'nullable|email|unique:maestros,email',
            'telefono' => 'nullable|numeric|unique:maestros,telefono',
            'fecha_nacimiento' => 'nullable|date:format:Y-m-d',
            'activo' => 'sometimes|boolean',

            //relacion a tabla anexo_maestro_aula
            'aula_id' => 'required|exists:aulas,id',
            'anexo_id' => 'required|exists:anexos,id',
            'current' => 'sometimes|in:on,off',
        ];
    }

    public function messages()
    {
        return [
            'nombres.required' => 'El campo nombres es obligatorio.',
            'apellidos.required' => 'El campo apellidos es obligatorio.',
            'email.email' => 'El campo email debe ser una dirección de correo válida.',
            'email.unique' => 'El email ya está en uso.',
            'telefono.numeric' => 'El campo teléfono debe ser un número válido.',
            'telefono.unique' => 'El teléfono ya está en uso.',
            'fecha_nacimiento.date' => 'El campo fecha de nacimiento debe ser una fecha válida.',

            'aula_id.required' => 'El campo aula es obligatoria',
            'anexo_id.required' => 'El campo anexo es obligatorio',
        ];
    }
}
