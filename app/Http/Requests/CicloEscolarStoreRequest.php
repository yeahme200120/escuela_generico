<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CicloEscolarStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('ciclos_escolares.crear');
    }

    public function rules(): array
    {
        return [
            'organizacion_id' => 'required|exists:organizaciones,id',
            'nombre'          => 'required|string|max:200|unique:ciclos_escolares,nombre',
            'clave'           => 'nullable|string|max:50|unique:ciclos_escolares,clave',
            'fecha_inicio'    => 'required|date',
            'fecha_fin'       => 'required|date|after:fecha_inicio',
            'activo'          => 'sometimes|boolean',
            'es_actual'       => 'sometimes|boolean',
        ];
    }
}