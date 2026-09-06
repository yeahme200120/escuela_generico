<?php

namespace App\Http\Requests\Academico;

use Illuminate\Foundation\Http\FormRequest;

class DocenteRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->puedeHacer('docentes.crear'); }

    public function rules(): array
    {
        return [
            'user_id'          => ['required','exists:users,id'],
            'numero_empleado'  => ['nullable','string','max:30'],
            'especialidad'     => ['nullable','string','max:200'],
            'cedula'           => ['nullable','string','max:30'],
            'fecha_ingreso'    => ['nullable','date'],
            'tipo_contrato'    => ['required','in:base,contrato,honorarios,tiempo_parcial'],
            'estatus'          => ['required','in:activo,inactivo,baja'],
        ];
    }
}
