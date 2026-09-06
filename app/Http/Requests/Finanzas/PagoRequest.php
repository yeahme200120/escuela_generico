<?php

namespace App\Http\Requests\Finanzas;

use Illuminate\Foundation\Http\FormRequest;

class PagoRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->puedeHacer('pagos.registrar'); }

    public function rules(): array
    {
        return [
            'alumno_id'       => ['required','exists:alumnos,id'],
            'sede_id'         => ['required','exists:sedes,id'],
            'importe'         => ['required','numeric','min:0.01'],
            'fecha_pago'      => ['required','date'],
            'metodo_pago_id'  => ['required','exists:metodos_pago,id'],
            'caja_id'         => ['nullable','exists:cajas,id'],
            'referencia'      => ['nullable','string','max:100'],
            'cargos'          => ['required','array','min:1'],
            'cargos.*.cargo_id'         => ['required','exists:cargos,id'],
            'cargos.*.importe_aplicado' => ['required','numeric','min:0.01'],
            'cargos.*.parcialidad_id'   => ['nullable','exists:parcialidades,id'],
        ];
    }
}
