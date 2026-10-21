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
            'type.required' => 'El tipo de borrador es obligatorio.',
            'type.enum'     => 'El tipo debe ser retirement_request o entity_request.',
            'payload.array' => 'El contenido del borrador debe ser un objeto.',
        ];
    }
}