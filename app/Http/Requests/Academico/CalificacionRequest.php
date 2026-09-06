<?php

namespace App\Http\Requests\Academico;

use Illuminate\Foundation\Http\FormRequest;

class CalificacionRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->puedeHacer('calificaciones.registrar'); }

    public function rules(): array
    {
        return [
            'alumno_id'              => ['required','exists:alumnos,id'],
            'materia_id'             => ['required','exists:materias,id'],
            'grupo_id'               => ['required','exists:grupos,id'],
            'periodo_evaluacion_id'  => ['required','exists:periodos_evaluacion,id'],
            'ciclo_escolar_id'       => ['required','exists:ciclos_escolares,id'],
            'calificacion'           => ['nullable','numeric','between:0,10'],
            'observaciones'          => ['nullable','string','max:300'],
        ];
    }

    public function messages(): array
    {
        return [
            'calificacion.between' => 'La calificación debe estar entre 0 y 10.',
        ];
    }
}
