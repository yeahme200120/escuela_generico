<?php

namespace App\Http\Requests\Academico;

use Illuminate\Foundation\Http\FormRequest;

class AsistenciaRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->puedeHacer('asistencias.registrar'); }

    public function rules(): array
    {
        return [
            'grupo_id'    => ['required','exists:grupos,id'],
            'materia_id'  => ['nullable','exists:materias,id'],
            'ciclo_id'    => ['required','exists:ciclos_escolares,id'],
            'fecha'       => ['required','date'],
            'lista'       => ['required','array','min:1'],
            'lista.*.alumno_id'    => ['required','exists:alumnos,id'],
            'lista.*.estado'       => ['required','in:presente,falta,retardo,justificada'],
            'lista.*.observacion'  => ['nullable','string','max:300'],
        ];
    }
}
