<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NivelEducativoUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('niveles_educativos.editar', $this->nivel_educativo);
    }

    public function rules(): array
    {
        return [
            'organizacion_id' => 'required|exists:organizaciones,id',
            'nombre'          => ['required', 'string', 'max:200', Rule::unique('niveles_educativos')->ignore($this->nivel_educativo)],
            'clave'           => ['nullable', 'string', 'max:50', Rule::unique('niveles_educativos')->ignore($this->nivel_educativo)],
            'orden'           => 'nullable|integer|min:0',
            'activo'          => 'sometimes|boolean',
        ];
    }
}