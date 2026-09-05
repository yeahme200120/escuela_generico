<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EscuelaUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('escuelas.editar', $this->escuela);
    }

    public function rules(): array
    {
        return [
            'organizacion_id' => 'required|exists:organizaciones,id',
            'nombre'          => ['required', 'string', 'max:200', Rule::unique('escuelas')->ignore($this->escuela)],
            'clave'           => ['nullable', 'string', 'max:50', Rule::unique('escuelas')->ignore($this->escuela)],
            'email'           => 'nullable|email|max:255',
            'telefono'        => 'nullable|string|max:30',
            'direccion'       => 'nullable|string',
            'ciudad'          => 'nullable|string|max:100',
            'estado'          => 'nullable|string|max:100',
            'pais'            => 'nullable|string|max:100',
            'codigo_postal'   => 'nullable|string|max:10',
            'activa'          => 'sometimes|boolean',
        ];
    }
}