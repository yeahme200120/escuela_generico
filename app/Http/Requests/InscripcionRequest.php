<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InscripcionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('control_escolar.inscribir');
    }

    public function rules(): array
    {
        return [
            'alumno_id'           => 'required|exists:alumnos,id',
            'sede_id'             => 'required|exists:sedes,id',
            'ciclo_escolar_id'    => 'required|exists:ciclos_escolares,id',
            'nivel_educativo_id'  => 'required|exists:niveles_educativos,id',
            'grado_id'            => 'required|exists:grados,id',
            'grupo_id'            => 'required|exists:grupos,id',
            'fecha_inscripcion'   => 'nullable|date',
            'observaciones'       => 'nullable|string',
            'generar_cargos'      => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'alumno_id.required' => 'Selecciona un alumno.',
            'sede_id.required' => 'Selecciona una sede.',
            'ciclo_escolar_id.required' => 'Selecciona un ciclo escolar.',
            'nivel_educativo_id.required' => 'Selecciona un nivel educativo.',
            'grado_id.required' => 'Selecciona un grado.',
            'grupo_id.required' => 'Selecciona un grupo.',
        ];
    }
}