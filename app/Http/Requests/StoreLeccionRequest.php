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
                'date_from' => 'nullable|date|before:date_to',
                'date_to' => 'nullable|date|after:date_from',
                'versiculo' => 'nullable|string|max:255',
                'archivo_pdf' => 'nullable|mimes:pdf|max:10240'
            ];
        } else {
            $leccion = $this->route('leccion');
            return [
                'titulo' => 'sometimes|required|string|max:255|unique:lecciones,titulo,' . $leccion->id,
                'date_from' => 'nullable|date|date_format:Y-m-d|before:date_to',
                'date_to' => 'nullable|date|date_format:Y-m-d|after:date_from',
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
            'date_from.date' => 'La fecha desde no es correcta',
            'date_to.date' => 'La fecha hasta no es correcta',
            'date_from.before' => 'La fecha debe ser menor',
            'date_to.after' => 'La fecha debe ser mayor',
            'archivo_pdf' => 'Solo se permite un archivo .pdf'
        ];
    }
}
