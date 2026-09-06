<?php

namespace App\Http\Requests\Academico;

use Illuminate\Foundation\Http\FormRequest;

class PlanEstudioRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->puedeHacer('sedes.editar'); }

    public function rules(): array
    {
        return [
            'escuela_id'    => ['required','exists:escuelas,id'],
            'nombre'        => ['required','string','max:200'],
            'clave'         => ['nullable','string','max:50'],
            'descripcion'   => ['nullable','string','max:500'],
            'activo'        => ['boolean'],
            'materias'      => ['nullable','array'],
            'materias.*.materia_id'  => ['required','exists:materias,id'],
            'materias.*.grado_id'    => ['required','exists:grados,id'],
            'materias.*.obligatoria' => ['boolean'],
            'materias.*.orden'       => ['integer','min:0'],
        ];
    }
}
