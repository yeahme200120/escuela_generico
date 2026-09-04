<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'string', 'max:200'],
            'password' => ['required', 'string', 'min:1'],
            // Geo — opcionales, nunca bloquean el login
            'geo_latitude'  => ['nullable', 'numeric', 'between:-90,90'],
            'geo_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'geo_accuracy'  => ['nullable', 'numeric', 'min:0'],
            'geo_altitude'  => ['nullable', 'numeric'],
            'geo_source'    => ['nullable', 'string', 'max:30'],
            // Dispositivo
            'device_id'     => ['nullable', 'string', 'max:100'],
            'device_info'   => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => 'El correo o usuario es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
        ];
    }
}
