<?php

namespace App\Http\Requests\Academico;

use Illuminate\Foundation\Http\FormRequest;

class BajaRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->puedeHacer('control_escolar.bajas'); }

    public function rules(): array
    {
        return [
            'alumno_id'       => ['required','exists:alumnos,id'],
            'tipo'            => ['required','in:temporal,definitiva,desercion,traslado,egreso'],
            'fecha_solicitud' => ['required','date'],
            'fecha_inicio'    => ['nullable','date'],
            'fecha_fin_estimada' => ['nullable','date','after_or_equal:fecha_inicio'],
            'motivo'          => ['required','string','min:10','max:500'],
            'motivo_desercion'=> ['nullable','in:abandono,inasistencia_prolongada,problemas_economicos,problemas_familiares,cambio_ciudad,cambio_escuela,bajo_aprovechamiento,motivo_personal,otro'],
            'documento'       => ['nullable','file','mimes:pdf,jpg,png','max:5120'],
            'observaciones'   => ['nullable','string','max:1000'],
        ];
    }
}
