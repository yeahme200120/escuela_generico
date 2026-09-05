<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GrupoUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('grupos.editar', $this->grupo);
    }

    public function rules(): array
    {
        return [
            'sede_id'           => 'required|exists:sedes,id',
            'ciclo_escolar_id'  => 'required|exists:ciclos_escolares,id',
            'grado_id'          => 'required|exists:grados,id',
            'nombre'            => ['required', 'string', 'max:200', Rule::unique('grupos')->ignore($this->grupo)],
            'turno'             => 'nullable|string|max:50',
            'capacidad'         => 'nullable|integer|min:0',
            'aula_principal_id' => 'nullable|exists:aulas,id',
            'docente_tutor_id'  => 'nullable|exists:docentes,id',
            'activo'            => 'sometimes|boolean',
        ];
    }
}