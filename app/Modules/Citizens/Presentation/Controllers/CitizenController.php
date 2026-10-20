<?php

declare(strict_types=1);

namespace App\Modules\Citizens\Presentation\Controllers;

use App\Modules\Citizens\Domain\CitizenStatus;
use App\Modules\Citizens\Infrastructure\Models\Citizen;
use App\Modules\Citizens\Presentation\Requests\RegisterCitizenRequest;
use App\Modules\Citizens\Presentation\Requests\LoginCitizenRequest;
use App\Modules\Citizens\Presentation\Resources\CitizenResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    public function login(LoginCitizenRequest $request): JsonResponse
{
    $data = $request->validated();

    $citizen = Citizen::where('email', $data['identifier'])
        ->orWhere('identity_number', $data['identifier'])
        ->first();

  
    if (!$citizen || !$citizen->verifyPassword($data['password'])) {
        if ($citizen) {
            $citizen->failed_login_attempts = ($citizen->failed_login_attempts ?? 0) + 1;
            if ($citizen->failed_login_attempts >= 5) {
                $citizen->locked_until = now()->addMinutes(15);
            }
            $citizen->save();
        }

        return response()->json(
            ['message' => 'Credenciales incorrectas.'],
            401
        );
    }

    
    if ($citizen->locked_until && $citizen->locked_until->isFuture()) {
        return response()->json([
            'message'      => 'Cuenta bloqueada por intentos fallidos. Intente más tarde.',
            'locked_until' => $citizen->locked_until->toIso8601String(),
        ], 423);
    }

    
    $citizen->failed_login_attempts = 0;
    $citizen->locked_until = null;
    $citizen->save();

    $token = $citizen->createToken('citizen', ['citizen'])->plainTextToken;

    return response()->json([
        'data' => [
            'token'   => $token,
            'citizen' => new CitizenResource($citizen),
        ],
    ]);
}

public function me(Request $request): JsonResponse
{
    // $request->user() devuelve el Citizen autenticado (gracias al guard)
    return (new CitizenResource($request->user()))->response();
}

public function logout(Request $request): JsonResponse
{
    // Revoca SOLO el token actual (no todas las sesiones del ciudadano)
    $request->user()->currentAccessToken()->delete();

    return response()->json(['message' => 'Sesión cerrada.']);
}
}