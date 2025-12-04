<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAulaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'nombre' => 'required|string|max:255|unique:aulas,nombre',
            'edad_min' => 'required|integer|min:0|lt:edad_max',
            'edad_max' => 'required|integer|min:0|gt:edad_min',
            'descripcion' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'El nombre ya está registrado.',
            'edad_min.required' => 'La edad mínima es obligatoria.',
            'edad_max.required' => 'La edad máxima es obligatoria.',
            'edad_min.lt' => 'La edad mínima debe ser menor a la edad máxima.',
            'edad_max.gt' => 'La edad máxima debe ser mayor a la edad máxima.',
            'descripcion.string' => 'El campo descripción debe ser una cadena de texto.',
        ];
    }
}
