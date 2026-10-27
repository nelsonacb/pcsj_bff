<?php

declare(strict_types=1);

namespace App\Modules\Citizens\Presentation\Controllers;

use App\Modules\Citizens\Infrastructure\Models\Citizen;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailVerificationController
{

    public function resend(Request $request): JsonResponse
    {
        $citizen = $request->user();

        if ($citizen->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'The email is already verified.',
            ], 400);
        }

        $citizen->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Verification link sent. Check your email.',
        ]);
    }

    public function verify(Request $request, int $id, string $hash): JsonResponse
    {
        $citizen = Citizen::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($citizen->getEmailForVerification()))) {
            return response()->json([
                'message' => 'The verification link is invalid.',
            ], 403);
        }

        if ($citizen->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'The email was already verified.',
            ]);
        }

        if ($citizen->markEmailAsVerified()) {
            event(new \Illuminate\Auth\Events\Verified($citizen));
        }

        return response()->json([
            'message' => 'Email verified successfully. You can now use all features.',
        ]);
    }
}