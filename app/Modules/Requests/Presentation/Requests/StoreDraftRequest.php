<?php

declare(strict_types=1);

namespace App\Modules\Requests\Presentation\Requests;

use App\Modules\Requests\Domain\DraftType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDraftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'    => ['required', Rule::enum(DraftType::class)],
            'payload' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'The draft type is required.',
            'type.enum'     => 'The type must be retirement_request or entity_request.',
            'payload.array' => 'The draft content must be an object.',
        ];
    }
}