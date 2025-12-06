<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAsistenciaAlumnoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        $isPost = $this->isMethod('post');
        if ($isPost) {
            $rules = [
                //validacion de alumno_id requerida y unica por alumno_id, aula_id y dia
                'alumno_id' => [
                    'required',
                    Rule::unique('asistencia_alumnos')->where(function ($query) {
                        return $query->where('alumno_id', $this->alumno_id)
                            ->where('dia', $this->dia);
                    }),
                ],
                'aula_id' => 'required|exists:aulas,id',
                'dia' => 'required|date:format:Y-m-d',
                'estado' => 'required|in:presente,ausente,tarde,justificado',
                'leccion_id' => 'required|exists:lecciones,id',
                'observaciones' => 'nullable|string',
            ];

            if ($this->hasFile('lista_imagen')) {
                $rules['lista_imagen'] = 'image|mimes:jpeg,png|max:100';
            }
        } else {
            $rules = [
                //validacion de alumno_id requerida y unica por alumno_id, aula_id y dia
                'alumno_id' => [
                    'required',
                    Rule::unique('asistencia_alumnos')->where(function ($query) {
                        return $query->where('alumno_id', $this->alumno_id)
                            ->where('dia', $this->dia);
                    })->ignore($this->route('asistencia_alumno')->id),
                ],
                'aula_id' => 'required|exists:aulas,id',
                'dia' => 'required|date:format:Y-m-d',
                'estado' => 'required|in:presente,ausente,tarde,justificado',
                'leccion_id' => 'required|exists:lecciones,id',
                'observaciones' => 'nullable|string',
            ];

            if ($this->hasFile('lista_imagen')) {
                $rules['lista_imagen'] = 'image|mimes:jpeg,png|max:100';
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'alumno_id.required' => 'El campo alumno es obligatorio.',
            'alumno_id.unique' => 'Ya existe una asistencia para este alumno en el día seleccionado.',
            'aula_id.required' => 'El campo aula es obligatorio.',
            'aula_id.exists' => 'El aula seleccionada no es válida.',
            'dia.required' => 'El campo día es obligatorio.',
            'dia.date' => 'El campo día no es una fecha válida.',
            'estado.required' => 'El campo estado es obligatorio.',
            'estado.in' => 'El estado seleccionado no es válido.',
            'leccion_id.required' => 'El campo lección es obligatorio.',
            'leccion_id.exists' => 'La lección seleccionada no es válida.',
            'observaciones.string' => 'Las observaciones deben ser una cadena de texto.',
            // Sintaxis: 'nombre_del_campo.regla' => 'Tu mensaje personalizado'
            'lista_imagen.image' => 'El archivo para la lista de imagen no es un formato de imagen aceptable.',
            'lista_imagen.mimes' => 'La imagen de la lista debe ser jpeg o png.',
            'lista_imagen.max' => 'La imagen de la lista es demasiado grande (máximo :max KB).', // Laravel reemplaza :max automáticamente
            'lista_imagen.exclude_unless' => 'Ocurrió un error con la subida del archivo.' // Aunque esta rara vez se muestra al usuario final.
        ];
    }
}
