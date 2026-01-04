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
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'nullable|numeric|unique:users,phone',
            'birthday' => 'nullable|date:format:Y-m-d',
            'enabled' => 'sometimes|boolean',

            //relacion a tabla anexo_maestro_aula
            'asignaciones' => 'required|array|min:1',
            'asignaciones.*.anexo_id' => 'required|exists:anexos,id',
            'asignaciones.*.aula_id' => 'required|exists:aulas,id'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El campo nombres es obligatorio.',
            'lastname.required' => 'El campo apellidos es obligatorio.',
            'email.email' => 'El campo email debe ser una dirección de correo válida.',
            'email.unique' => 'El email ya está en uso.',
            'phone.numeric' => 'El campo teléfono debe ser un número válido.',
            'phone.unique' => 'El teléfono ya está en uso.',
            'birthday.date' => 'La fecha de nacimiento debe ser una fecha válida.',

            'aula_id.required' => 'El campo aula es obligatoria',
            'anexo_id.required' => 'El campo anexo es obligatorio',
        ];
    }
}
