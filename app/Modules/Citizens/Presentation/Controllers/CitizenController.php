<?php

declare(strict_types=1);

namespace App\Modules\Citizens\Presentation\Controllers;

use App\Modules\Citizens\Domain\CitizenStatus;
use App\Modules\Citizens\Infrastructure\Models\Citizen;
use App\Modules\Citizens\Presentation\Requests\RegisterCitizenRequest;
use App\Modules\Citizens\Presentation\Resources\CitizenResource;
use Illuminate\Http\JsonResponse;

class CitizenController
{
    /**
     * POST /api/v1/citizens/register
     */
    public function register(RegisterCitizenRequest $request): JsonResponse
    {
        $data = $request->validated();

        $citizen = new Citizen();
        $citizen->identity_number = $data['identity_number'];
        $citizen->email           = $data['email'];
        $citizen->full_name       = $data['full_name'];
        $citizen->phone           = $data['phone'] ?? null;
        $citizen->status          = CitizenStatus::Unverified;
        $citizen->setPassword($data['password']);
        $citizen->save();

        return (new CitizenResource($citizen))
            ->response()
            ->setStatusCode(201);
    }
}