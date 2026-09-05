<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GradoUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('grados.editar', $this->grado);
    }

    public function rules(): array
    {
        return [
            'organizacion_id'     => 'required|exists:organizaciones,id',
            'nivel_educativo_id'  => 'required|exists:niveles_educativos,id',
            'nombre'              => ['required', 'string', 'max:200', Rule::unique('grados')->ignore($this->grado)],
            'clave'               => ['nullable', 'string', 'max:50', Rule::unique('grados')->ignore($this->grado)],
            'orden'               => 'nullable|integer|min:0',
            'activo'              => 'sometimes|boolean',
        ];
    }
}