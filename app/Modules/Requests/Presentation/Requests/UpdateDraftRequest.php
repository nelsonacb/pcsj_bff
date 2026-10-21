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
            'payload.required' => 'The draft content is required.',
            'payload.array'    => 'The draft content must be an object.',
        ];
    }
}