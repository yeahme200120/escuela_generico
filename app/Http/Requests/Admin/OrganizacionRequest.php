<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class OrganizacionRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->esSuperadmin(); }

    public function rules(): array
    {
        return [
            'nombre'        => ['required','string','max:200'],
            'razon_social'  => ['nullable','string','max:250'],
            'rfc'           => ['nullable','string','max:20'],
            'email'         => ['nullable','email','max:200'],
            'telefono'      => ['nullable','string','max:30'],
            'sitio_web'     => ['nullable','url','max:200'],
            'ciudad'        => ['nullable','string','max:100'],
            'estado'        => ['nullable','string','max:100'],
            'pais'          => ['nullable','string','max:100'],
            'codigo_postal' => ['nullable','string','max:10'],
            'latitud'       => ['nullable','numeric','between:-90,90'],
            'longitud'      => ['nullable','numeric','between:-180,180'],
            'activa'        => ['boolean'],
        ];
    }
}
