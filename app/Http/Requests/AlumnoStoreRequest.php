<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AlumnoStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('alumnos.crear');
    }

    public function rules(): array
    {
        return [
            // Organización y sede
            'organizacion_id'   => 'required|exists:organizaciones,id',
            'sede_actual_id'    => 'nullable|exists:sedes,id',

            // Datos personales
            'matricula'         => 'required|string|max:50|unique:alumnos,matricula',
            'curp'              => 'nullable|string|max:18|unique:alumnos,curp',
            'nombres'           => 'required|string|max:100',
            'apellido_paterno'  => 'required|string|max:100',
            'apellido_materno'  => 'nullable|string|max:100',
            'fecha_nacimiento'  => 'required|date|before:today',
            'sexo'              => 'nullable|string|max:10|in:Masculino,Femenino,Otro',

            // Contacto
            'email'             => 'nullable|email|max:255',
            'telefono'          => 'nullable|string|max:30',
            'celular'           => 'nullable|string|max:30',

            // Dirección
            'direccion'         => 'nullable|string',
            'ciudad'            => 'nullable|string|max:100',
            'estado'            => 'nullable|string|max:100',
            'pais'              => 'nullable|string|max:100',
            'codigo_postal'     => 'nullable|string|max:10',

            // Estatus académico
            'fecha_ingreso'     => 'nullable|date',
            'estatus'           => ['nullable', 'string', Rule::in(['activo', 'baja_temporal', 'baja_definitiva', 'egresado'])],
            'situacion_academica' => ['nullable', 'string', Rule::in(['regular', 'irregular', 'reprobado', 'en_regularizacion', 'condicionado'])],
            'activo'            => 'sometimes|boolean',
            'configuracion'     => 'nullable|json',
        ];
    }

    public function messages(): array
    {
        return [
            'matricula.unique' => 'Esta matrícula ya está registrada.',
            'curp.unique' => 'Este CURP ya está registrado.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
        ];
    }
}