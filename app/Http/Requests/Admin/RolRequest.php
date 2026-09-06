<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RolRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->esSuperadmin(); }

    public function rules(): array
    {
        $rolId = $this->route('rol') ?? $this->route('role');
        $id = $rolId instanceof \App\Models\Role ? $rolId->id : $rolId;

        return [
            'nombre'      => ['required','string','max:60'],
            'slug'        => ['required','string','max:60', Rule::unique('roles','slug')->ignore($id)],
            'descripcion' => ['nullable','string','max:255'],
            'nivel'       => ['required','integer','between:1,100'],
            'activo'      => ['boolean'],
            'permisos'    => ['nullable','array'],
            'permisos.*'  => ['exists:permissions,id'],
        ];
    }
}
