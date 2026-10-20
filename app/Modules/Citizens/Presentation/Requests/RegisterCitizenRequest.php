<?php

declare(strict_types=1);

namespace App\Modules\Citizens\Presentation\Requests;

use App\Modules\Shared\Domain\CubanIdentityNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterCitizenRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'identity_number' => [
                'required',
                'string',
                'size:11',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (!CubanIdentityNumber::isValid((string) $value)) {
                        $fail('El número de identidad cubano no es válido.');
                    }
                },
                Rule::unique('citizens', 'identity_number'),
            ],
            'email' => [
                'required',
                'string',
                'email:rfc',
                'max:255',
                Rule::unique('citizens', 'email'),
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(12)->mixedCase()->numbers()->symbols(),
            ],
            'full_name' => ['required', 'string', 'max:255'],
            'phone'     => ['nullable', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'identity_number.required' => 'El número de identidad es obligatorio.',
            'identity_number.size'     => 'El número de identidad debe tener 11 dígitos.',
            'identity_number.unique'   => 'Ya existe una cuenta con ese número de identidad.',
            'email.required'           => 'El correo electrónico es obligatorio.',
            'email.email'              => 'El correo electrónico no es válido.',
            'email.unique'             => 'Ya existe una cuenta con ese correo electrónico.',
            'password.required'        => 'La contraseña es obligatoria.',
            'password.confirmed'       => 'Las contraseñas no coinciden.',
            'password.min'             => 'La contraseña debe tener al menos 12 caracteres.',
            'full_name.required'       => 'El nombre completo es obligatorio.',
        ];
    }
}