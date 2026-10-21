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
                        $fail('The Cuban identity number is not valid.');
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
            'identity_number.required' => 'The identity number is required.',
            'identity_number.size'     => 'The identity number must be 11 digits.',
            'identity_number.unique'   => 'An account with that identity number already exists.',
            'email.required'           => 'The email address is required.',
            'email.email'              => 'The email address is not valid.',
            'email.unique'             => 'An account with that email address already exists.',
            'password.required'        => 'The password is required.',
            'password.confirmed'       => 'The passwords do not match.',
            'password.min'             => 'The password must be at least 12 characters long.',
            'full_name.required'       => 'The full name is required.',
        ];
    }
}