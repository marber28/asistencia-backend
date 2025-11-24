<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeccionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'titulo' => 'required|string|max:255',
            'fecha' => 'nullable|date',
            'versiculo' => 'nullable|string|max:255',
            'archivo_pdf' => 'nullable|mimes:pdf|max:10240'
        ];
    }
}
