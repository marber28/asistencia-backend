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
        $isPost = FormRequest::isMethod('post');
        if ($isPost) {
            return [
                'titulo' => 'required|string|max:255|unique:lecciones,titulo',
                'fecha' => 'nullable|date',
                'versiculo' => 'nullable|string|max:255',
                'archivo_pdf' => 'nullable|mimes:pdf|max:10240'
            ];
        } else {
            $leccion = $this->route('leccion');
            return [
                'titulo' => 'sometimes|required|string|max:255|unique:lecciones,titulo,' . $leccion->id,
                'fecha' => 'nullable|date|date_format:Y-m-d',
                'versiculo' => 'nullable|string|max:255',
                'archivo_pdf' => 'nullable|mimes:pdf|max:10240'
            ];
        }
    }

    public function messages()
    {
        return [
            'titulo.required' => 'El título es obligatorio.',
            'titulo.unique' => 'El título ya ha sido usado.',
            'fecha.date' => 'La fecha no es correcta',
            'archivo_pdf' => 'Solo se permite un archivo .pdf'
        ];
    }
}
