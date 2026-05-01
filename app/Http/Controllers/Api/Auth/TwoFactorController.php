<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    public function __construct(private Google2FA $google2fa) {}

    public function setup(Request $request): JsonResponse
    {
        $user = $request->user();
        $secret = $this->google2fa->generateSecretKey();

        $user->update(['two_factor_secret' => $secret]);

        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        return response()->json([
            'success' => true,
            'data' => [
                'secret' => $secret,
                'qr_code_url' => $qrCodeUrl,
            ],
        ]);
    }

    public function enable(Request $request): JsonResponse
    {
        $request->validate(['otp' => 'required|string|size:6']);

        $user = $request->user();

        if (!$user->two_factor_secret) {
            return response()->json([
                'success' => false,
                'message' => 'Run 2FA setup first',
            ], 400);
        }

        $valid = $this->google2fa->verifyKey($user->two_factor_secret, $request->otp);

        if (!$valid) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP code',
            ], 422);
        }

        $user->update([
            'two_factor_enabled' => true,
            'two_factor_confirmed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => '2FA enabled successfully',
            'data' => new UserResource($user),
        ]);
    }

    public function disable(Request $request): JsonResponse
    {
        $request->validate(['otp' => 'required|string|size:6']);

        $user = $request->user();
        $valid = $this->google2fa->verifyKey($user->two_factor_secret, $request->otp);

        if (!$valid) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP code',
            ], 422);
        }

        $user->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => '2FA disabled successfully',
        ]);
    }

    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $user = $request->user();

        // tmp_token hanya punya scope 2fa-verify
        if (!$request->user()->tokenCan('2fa-verify')) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token for 2FA verification',
            ], 401);
        }

        $valid = $this->google2fa->verifyKey($user->two_factor_secret, $request->otp);

        if (!$valid) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP',
            ], 401);
        }

        // Revoke tmp token, issue full token
        $user->token()->revoke();
        $token = $user->createToken('access_token')->accessToken;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => new UserResource($user),
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => config('passport.tokens_expire_in')->totalSeconds,
            ],
        ]);
    }
}