<?php

declare(strict_types=1);

namespace App\Modules\Requests\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDraftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payload' => ['required', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'payload.required' => 'El contenido del borrador es obligatorio.',
            'payload.array'    => 'El contenido del borrador debe ser un objeto.',
        ];
    }
}