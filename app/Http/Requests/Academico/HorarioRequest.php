<?php

namespace App\Http\Requests\Academico;

use Illuminate\Foundation\Http\FormRequest;

class HorarioRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->puedeHacer('horarios.crear'); }

    public function rules(): array
    {
        return [
            'grupo_id'          => ['required','exists:grupos,id'],
            'materia_id'        => ['required','exists:materias,id'],
            'docente_id'        => ['required','exists:docentes,id'],
            'aula_id'           => ['nullable','exists:aulas,id'],
            'ciclo_escolar_id'  => ['required','exists:ciclos_escolares,id'],
            'dia_semana'        => ['required','integer','between:1,5'],
            'hora_inicio'       => ['required','date_format:H:i'],
            'hora_fin'          => ['required','date_format:H:i','after:hora_inicio'],
        ];
    }

    public function messages(): array
    {
        return [
            'hora_fin.after'        => 'La hora de fin debe ser posterior a la hora de inicio.',
            'dia_semana.between'    => 'El día debe ser entre 1 (lunes) y 5 (viernes).',
        ];
    }
}
