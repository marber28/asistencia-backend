<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsuarioRequest extends FormRequest
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
            //'phone' => 'nullable|numeric|unique:users,phone',
            'in_anexo' => 'sometimes|boolean',
            'password' => 'required|min:6',
            'enabled' => 'sometimes|boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El campo nombres es obligatorio.',
            'lastname.required' => 'El campo apellidos es obligatorio.',
            'email.email' => 'El campo email debe ser una dirección de correo válida.',
            'email.unique' => 'El email ya está en uso.',
            //'phone.numeric' => 'El campo teléfono debe ser un número válido.',
            //'phone.unique' => 'El teléfono ya está en uso.',
            'password.required' => 'LA contraseña es obligatoria.',
            'password.min' => 'LA contraseña debe tener 6 caracteres como mínimo.',
        ];
    }
}
