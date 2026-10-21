<?php

declare(strict_types=1);

namespace App\Modules\Requests\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttachmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file'     => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:10240', // 10240 KB = 10 MB
            ],
            'draft_id' => [
                'required',
                'integer',
                'exists:portal_drafts,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Debe adjuntar un archivo.',
            'file.file'     => 'El archivo no es válido.',
            'file.mimes'    => 'Solo se permiten archivos PDF, JPG o PNG.',
            'file.max'      => 'El archivo no puede superar los 10 MB.',
            'draft_id.required' => 'Debe indicar el borrador al que pertenece el archivo.',
            'draft_id.exists'   => 'El borrador indicado no existe.',
        ];
    }
}