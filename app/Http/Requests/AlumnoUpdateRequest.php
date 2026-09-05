<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AlumnoUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('alumnos.editar', $this->alumno);
    }

    public function rules(): array
    {
        $alumno = $this->route('alumno');

        return [
            'organizacion_id'   => 'required|exists:organizaciones,id',
            'sede_actual_id'    => 'nullable|exists:sedes,id',

            'matricula'         => ['required', 'string', 'max:50', Rule::unique('alumnos')->ignore($alumno->id)],
            'curp'              => ['nullable', 'string', 'max:18', Rule::unique('alumnos')->ignore($alumno->id)],
            'nombres'           => 'required|string|max:100',
            'apellido_paterno'  => 'required|string|max:100',
            'apellido_materno'  => 'nullable|string|max:100',
            'fecha_nacimiento'  => 'required|date|before:today',
            'sexo'              => 'nullable|string|max:10|in:Masculino,Femenino,Otro',

            'email'             => 'nullable|email|max:255',
            'telefono'          => 'nullable|string|max:30',
            'celular'           => 'nullable|string|max:30',

            'direccion'         => 'nullable|string',
            'ciudad'            => 'nullable|string|max:100',
            'estado'            => 'nullable|string|max:100',
            'pais'              => 'nullable|string|max:100',
            'codigo_postal'     => 'nullable|string|max:10',

            'fecha_ingreso'     => 'nullable|date',
            'estatus'           => ['nullable', 'string', Rule::in(['activo', 'baja_temporal', 'baja_definitiva', 'egresado'])],
            'situacion_academica' => ['nullable', 'string', Rule::in(['regular', 'irregular', 'reprobado', 'en_regularizacion', 'condicionado'])],
            'activo'            => 'sometimes|boolean',
            'configuracion'     => 'nullable|json',
        ];
    }
}