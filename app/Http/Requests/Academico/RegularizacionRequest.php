<?php

namespace App\Http\Requests\Academico;

use Illuminate\Foundation\Http\FormRequest;

class RegularizacionRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->puedeHacer('calificaciones.registrar'); }

    public function rules(): array
    {
        return [
            'alumno_id'                   => ['required','exists:alumnos,id'],
            'materia_id'                  => ['required','exists:materias,id'],
            'ciclo_escolar_id'            => ['required','exists:ciclos_escolares,id'],
            'calificacion_original'       => ['nullable','numeric','between:0,10'],
            'calificacion_regularizacion' => ['nullable','numeric','between:0,10'],
            'fecha'                       => ['nullable','date'],
            'resultado'                   => ['required','in:aprobado,reprobado,pendiente'],
            'observaciones'               => ['nullable','string','max:300'],
        ];
    }
}
