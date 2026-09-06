<?php

namespace App\Http\Requests\Finanzas;

use Illuminate\Foundation\Http\FormRequest;

class CargoRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->puedeHacer('pagos.registrar'); }

    public function rules(): array
    {
        return [
            'alumno_id'         => ['required','exists:alumnos,id'],
            'ciclo_escolar_id'  => ['required','exists:ciclos_escolares,id'],
            'concepto_id'       => ['required','exists:conceptos_pago,id'],
            'sede_id'           => ['required','exists:sedes,id'],
            'importe'           => ['required','numeric','min:0'],
            'descuento'         => ['nullable','numeric','min:0'],
            'recargo'           => ['nullable','numeric','min:0'],
            'fecha_vencimiento' => ['nullable','date'],
            'referencia'        => ['nullable','string','max:100'],
        ];
    }
}
