<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MateriaStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('materias.crear');
    }

    public function rules(): array
    {
        return [
            'escuela_id'    => 'required|exists:escuelas,id',
            'clave'         => 'required|string|max:50|unique:materias,clave',
            'nombre'        => 'required|string|max:200|unique:materias,nombre',
            'descripcion'   => 'nullable|string',
            'horas_semana'  => 'nullable|integer|min:0',
            'creditos'      => 'nullable|integer|min:0',
            'tipo'          => 'nullable|string|in:obligatoria,optativa,taller,extracurricular',
            'activa'        => 'sometimes|boolean',
        ];
    }
}