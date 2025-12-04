<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAnexoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {
        $rules = [
            'nombre' => 'required|string|unique:anexos,nombre|max:255',
            'direccion' => 'required|string|max:255',
            'fecha_creacion' => 'required|date|date_format:Y-m-d',
            'user_id' => 'nullable|exists:users,id',
            'activo' => 'sometimes|boolean',
        ];

        if($this->hasFile('logo')) {
            $rules['logo'] = 'image|mimes:jpeg,png|max:100';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'nombre.required' => 'El campo nombres es obligatorio.',
            'direccion.required' => 'El campo direccion es obligatorio.',
            'fecha_creacion.required' => 'La fecha de creación es obligatorio.',
            'fecha_creacion.date' => 'La fecha de creación no es una fecha.',
            'activo.required' => 'El campo activo es obligatorio.',
        ];
    }
}
