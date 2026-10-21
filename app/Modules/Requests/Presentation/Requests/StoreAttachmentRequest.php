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
            'file.required' => 'You must attach a file.',
            'file.file'     => 'The file is not valid.',
            'file.mimes'    => 'Only PDF, JPG or PNG files are allowed.',
            'file.max'      => 'The file may not exceed 10 MB.',
            'draft_id.required' => 'You must specify the draft the file belongs to.',
            'draft_id.exists'   => 'The specified draft does not exist.',
        ];
    }
}