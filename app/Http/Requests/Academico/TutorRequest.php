<?php

namespace App\Http\Requests\Academico;

use Illuminate\Foundation\Http\FormRequest;

class TutorRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->puedeHacer('alumnos.crear'); }

    public function rules(): array
    {
        return [
            'nombres'           => ['required','string','max:100'],
            'apellido_paterno'  => ['required','string','max:100'],
            'apellido_materno'  => ['nullable','string','max:100'],
            'parentesco'        => ['nullable','string','max:50'],
            'email'             => ['nullable','email','max:200'],
            'telefono'          => ['nullable','string','max:30'],
            'telefono_trabajo'  => ['nullable','string','max:30'],
            'ocupacion'         => ['nullable','string','max:100'],
            'alumno_id'         => ['nullable','exists:alumnos,id'],
            'es_principal'      => ['boolean'],
            'autorizado_recoger'=> ['boolean'],
        ];
    }
}
