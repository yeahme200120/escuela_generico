<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EscuelaStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('escuelas.crear');
    }

    public function rules(): array
    {
        return [
            'organizacion_id' => 'required|exists:organizaciones,id',
            'nombre'          => 'required|string|max:200|unique:escuelas,nombre',
            'clave'           => 'nullable|string|max:50|unique:escuelas,clave',
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