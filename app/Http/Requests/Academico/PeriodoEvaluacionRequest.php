<?php

namespace App\Http\Requests\Academico;

use Illuminate\Foundation\Http\FormRequest;

class PeriodoEvaluacionRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->puedeHacer('calificaciones.cerrar'); }

    public function rules(): array
    {
        return [
            'ciclo_escolar_id' => ['required','exists:ciclos_escolares,id'],
            'nombre'           => ['required','string','max:100'],
            'numero'           => ['required','integer','between:1,10'],
            'fecha_inicio'     => ['required','date'],
            'fecha_fin'        => ['required','date','after:fecha_inicio'],
            'fecha_cierre'     => ['nullable','date'],
        ];
    }
}
