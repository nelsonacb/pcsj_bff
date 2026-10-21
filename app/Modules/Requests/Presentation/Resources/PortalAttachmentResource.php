<?php

declare(strict_types=1);

namespace App\Modules\Requests\Presentation\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PortalAttachmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'draft_id'      => $this->draft_id,
            'original_name' => $this->original_name,
            'mime_type'     => $this->mime_type,
            'size'          => $this->size,
            'size_human'    => $this->humanSize(),
            'created_at'    => $this->created_at?->toIso8601String(),
        ];
    }
}