<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MateriaUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('materias.editar', $this->materia);
    }

    public function rules(): array
    {
        return [
            'escuela_id'    => 'required|exists:escuelas,id',
            'clave'         => ['required', 'string', 'max:50', Rule::unique('materias')->ignore($this->materia)],
            'nombre'        => ['required', 'string', 'max:200', Rule::unique('materias')->ignore($this->materia)],
            'descripcion'   => 'nullable|string',
            'horas_semana'  => 'nullable|integer|min:0',
            'creditos'      => 'nullable|integer|min:0',
            'tipo'          => 'nullable|string|in:obligatoria,optativa,taller,extracurricular',
            'activa'        => 'sometimes|boolean',
        ];
    }
}