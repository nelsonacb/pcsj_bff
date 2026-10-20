<?php

declare(strict_types=1);

namespace App\Modules\Citizens\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginCitizenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'identifier' => ['required', 'string', 'max:255'],
            'password'   => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'identifier.required' => 'Debe indicar su correo o número de identidad.',
            'password.required'   => 'La contraseña es obligatoria.',
        ];
    }
}