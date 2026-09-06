<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->puedeHacer('usuarios.crear'); }

    public function rules(): array
    {
        return [
            'nombres'           => ['required','string','max:100'],
            'apellido_paterno'  => ['required','string','max:100'],
            'apellido_materno'  => ['nullable','string','max:100'],
            'email'             => ['required','email','max:200','unique:users,email'],
            'username'          => ['nullable','string','max:60','unique:users,username'],
            'telefono'          => ['nullable','string','max:30'],
            'password'          => ['required','string','min:8','confirmed'],
            'roles'             => ['nullable','array'],
            'roles.*'           => ['exists:roles,id'],
            'sede_ids'          => ['nullable','array'],
            'sede_ids.*'        => ['exists:sedes,id'],
            'activo'            => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'    => 'El correo ya está registrado.',
            'username.unique' => 'El nombre de usuario ya está en uso.',
            'password.min'    => 'La contraseña debe tener al menos 8 caracteres.',
        ];
    }
}
