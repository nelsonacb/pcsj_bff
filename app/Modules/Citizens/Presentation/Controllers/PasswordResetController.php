<?php

declare(strict_types=1);

namespace App\Modules\Citizens\Presentation\Controllers;

use App\Modules\Citizens\Domain\Exceptions\InvalidPasswordException;
use App\Modules\Citizens\Domain\PasswordPolicy;
use App\Modules\Citizens\Infrastructure\Models\Citizen;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;

class PasswordResetController
{

    public function sendResetLink(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:citizens,email'],
        ]);

        $status = Password::broker('citizens')->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'If the email exists, you will receive a link to reset your password.',
            ]);
        }

        return response()->json([
            'message' => 'The link could not be sent. Please try again.',
        ], 500);
    }


    public function reset(Request $request): JsonResponse
    {
        $request->validate([
            'token'    => ['required', 'string'],
            'email'    => ['required', 'email', 'exists:citizens,email'],
            'password' => [
                'required',
                'string',
                'confirmed',
                PasswordRule::min(12)->mixedCase()->numbers()->symbols(),
            ],
        ]);

        $status = Password::broker('citizens')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Citizen $citizen, string $password) {
                $citizen->password_hash = Hash::make($password);
                $citizen->save();

                $citizen->tokens()->delete();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Password reset successfully. You can now log in.',
            ]);
        }

        return response()->json([
            'message' => 'The reset link is invalid or has expired.',
            'errors'  => ['token' => [__($status)]],
        ], 422);
    }
}