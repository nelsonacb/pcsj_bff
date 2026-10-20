<?php

declare(strict_types=1);

namespace App\Modules\Citizens\Presentation\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CitizenResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'identity_number' => $this->identity_number,
            'full_name'       => $this->full_name,
            'email'           => $this->email,
            'phone'           => $this->phone,
            'person_id_sgp'   => $this->person_id_sgp,
            'status'          => $this->status->value,
            'email_verified'  => $this->email_verified_at !== null,
            'created_at'      => $this->created_at?->toIso8601String(),
        ];
    }
}