<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CicloEscolarUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('ciclos_escolares.editar', $this->ciclo_escolar);
    }

    public function rules(): array
    {
        return [
            'organizacion_id' => 'required|exists:organizaciones,id',
            'nombre'          => ['required', 'string', 'max:200', Rule::unique('ciclos_escolares')->ignore($this->ciclo_escolar)],
            'clave'           => ['nullable', 'string', 'max:50', Rule::unique('ciclos_escolares')->ignore($this->ciclo_escolar)],
            'fecha_inicio'    => 'required|date',
            'fecha_fin'       => 'required|date|after:fecha_inicio',
            'activo'          => 'sometimes|boolean',
            'es_actual'       => 'sometimes|boolean',
        ];
    }
}