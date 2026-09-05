<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GradoStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('grados.crear');
    }

    public function rules(): array
    {
        return [
            'organizacion_id'     => 'required|exists:organizaciones,id',
            'nivel_educativo_id'  => 'required|exists:niveles_educativos,id',
            'nombre'              => 'required|string|max:200|unique:grados,nombre',
            'clave'               => 'nullable|string|max:50|unique:grados,clave',
            'orden'               => 'nullable|integer|min:0',
            'activo'              => 'sometimes|boolean',
        ];
    }
}