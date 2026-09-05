<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NivelEducativoStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('niveles_educativos.crear');
    }

    public function rules(): array
    {
        return [
            'organizacion_id' => 'required|exists:organizaciones,id',
            'nombre'          => 'required|string|max:200|unique:niveles_educativos,nombre',
            'clave'           => 'nullable|string|max:50|unique:niveles_educativos,clave',
            'orden'           => 'nullable|integer|min:0',
            'activo'          => 'sometimes|boolean',
        ];
    }
}