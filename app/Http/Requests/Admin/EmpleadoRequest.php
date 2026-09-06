<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class EmpleadoRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->puedeHacer('rh.gestionar'); }

    public function rules(): array
    {
        return [
            'user_id'          => ['required','exists:users,id'],
            'numero_empleado'  => ['nullable','string','max:30'],
            'puesto'           => ['nullable','string','max:150'],
            'departamento'     => ['nullable','string','max:100'],
            'fecha_ingreso'    => ['nullable','date'],
            'tipo_contrato'    => ['required','in:base,contrato,honorarios,tiempo_parcial'],
            'salario'          => ['nullable','numeric','min:0'],
            'estatus'          => ['required','in:activo,baja,suspendido'],
        ];
    }
}
