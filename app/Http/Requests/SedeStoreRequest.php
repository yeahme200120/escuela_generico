<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SedeStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('sedes.crear');
    }

    public function rules(): array
    {
        return [
            'organizacion_id' => 'required|exists:organizaciones,id',
            'escuela_id'      => 'nullable|exists:escuelas,id',
            'nombre'          => 'required|string|max:255|unique:sedes,nombre',
            'direccion'       => 'nullable|string',
            'latitud'         => 'nullable|numeric|between:-90,90',
            'longitud'        => 'nullable|numeric|between:-180,180',
            'radio_geocerca_metros' => 'nullable|integer|min:0',
            'activo'          => 'sometimes|boolean',
        ];
    }
}