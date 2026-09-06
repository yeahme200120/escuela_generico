<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DocumentoRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->puedeHacer('documentos.generar'); }

    public function rules(): array
    {
        return [
            'alumno_id'         => ['required','exists:alumnos,id'],
            'tipo_documento_id' => ['required','exists:tipos_documento,id'],
            'sede_id'           => ['required','exists:sedes,id'],
        ];
    }
}
