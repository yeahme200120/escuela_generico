<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->puedeHacer('usuarios.editar'); }

    public function rules(): array
    {
        $userId = $this->route('user') instanceof \App\Models\User
            ? $this->route('user')->id
            : $this->route('user');

        return [
            'nombres'          => ['required','string','max:100'],
            'apellido_paterno' => ['required','string','max:100'],
            'apellido_materno' => ['nullable','string','max:100'],
            'email'            => ['required','email','max:200', Rule::unique('users','email')->ignore($userId)],
            'username'         => ['nullable','string','max:60', Rule::unique('users','username')->ignore($userId)],
            'telefono'         => ['nullable','string','max:30'],
            'password'         => ['nullable','string','min:8','confirmed'],
            'activo'           => ['boolean'],
        ];
    }
}
