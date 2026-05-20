<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('phone', $request->phone)->first();
        if (!$user) {
            return response()->json(['message' => 'Utilisateur introuvable'], 404);
        }

        $otp = (string) random_int(1000, 9999);
        $user->forceFill([
            'otp_code' => Hash::make($otp),
            'otp_expires_at' => now()->addMinutes(10),
        ])->save();

        $payload = ['message' => 'Code renvoyé par SMS', 'phone' => $user->phone];
        if (app()->environment('local')) {
            $payload['debug_otp'] = $otp;
        }

        return response()->json($payload);
    }

    public function requestPasswordReset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('phone', $request->phone)->first();
        if (!$user) {
            return response()->json(['message' => 'Utilisateur introuvable'], 404);
        }

        $otp = (string) random_int(1000, 9999);
        $user->forceFill([
            'otp_code' => Hash::make($otp),
            'otp_expires_at' => now()->addMinutes(10),
        ])->save();

        $payload = ['message' => 'Code de réinitialisation envoyé par SMS', 'phone' => $user->phone];
        if (app()->environment('local')) {
            $payload['debug_otp'] = $otp;
        }

        return response()->json($payload);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:20',
            'code' => 'required|string|size:4',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('phone', $request->phone)->first();
        if (!$user || !$user->otp_code) {
            return response()->json(['message' => 'Code invalide'], 400);
        }

        if ($user->otp_expires_at && now()->greaterThan($user->otp_expires_at)) {
            return response()->json(['message' => 'Code expiré'], 400);
        }

        if (!Hash::check($request->code, $user->otp_code)) {
            return response()->json(['message' => 'Code invalide'], 400);
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
            'otp_code' => null,
            'otp_expires_at' => null,
        ])->save();

        return response()->json(['message' => 'Mot de passe réinitialisé avec succès']);
    }

    public function requestOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|string|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $otp = (string) random_int(1000, 9999);

        $user = User::updateOrCreate(
            ['phone' => $request->phone],
            [
                'name' => $request->name,
                'email' => $request->input('email'),
                // Temporary password (user sets their password later)
                'password' => Hash::make(Str::random(40)),
                'role' => 'user',
                'otp_code' => Hash::make($otp),
                'otp_expires_at' => now()->addMinutes(10),
                'is_verified' => false,
            ]
        );

        // TODO: Integrate SMS provider. For now we do not return the OTP in production.
        $payload = ['message' => 'Code envoyé par SMS', 'phone' => $user->phone];
        if (app()->environment('local')) {
            $payload['debug_otp'] = $otp;
        }

        return response()->json($payload);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:20',
            'code' => 'required|string|size:4',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('phone', $request->phone)->first();
        if (!$user || !$user->otp_code) {
            return response()->json(['message' => 'Code invalide'], 400);
        }

        if ($user->otp_expires_at && now()->greaterThan($user->otp_expires_at)) {
            return response()->json(['message' => 'Code expiré'], 400);
        }

        if (!Hash::check($request->code, $user->otp_code)) {
            return response()->json(['message' => 'Code invalide'], 400);
        }

        $user->forceFill([
            'is_verified' => true,
            'otp_code' => null,
            'otp_expires_at' => null,
        ])->save();

        return response()->json(['message' => 'Numéro vérifié avec succès']);
    }

    public function completeRegistration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'name' => 'sometimes|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('phone', $request->phone)->firstOrFail();

        if ($user->is_suspended) {
            return response()->json([
                'message' => 'Votre compte a été suspendu. Contactez le support BERRNI.',
            ], 403);
        }

        if (!$user->is_verified) {
            return response()->json(['message' => 'Numéro non vérifié'], 403);
        }

        $user->forceFill([
            'name' => $request->input('name', $user->name),
            'email' => $request->input('email', $user->email),
            'password' => Hash::make($request->password),
        ])->save();

        Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance_available' => 0, 'balance_sequestered' => 0]
        );

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Compte créé avec succès',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 201);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $existing = User::where('phone', $request->phone)->first();
        if ($existing?->is_suspended) {
            return response()->json([
                'message' => 'Ce compte est suspendu. Contactez le support BERRNI.',
            ], 403);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        // Create Wallet
        Wallet::create([
            'user_id' => $user->id,
            'balance_available' => 0,
            'balance_sequestered' => 0,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        $loginField = $request->filled('phone') ? 'phone' : 'email';
        if (!auth()->attempt($request->only($loginField, 'password'))) {
            return response()->json(['message' => 'Identifiants invalides'], 401);
        }

        $user = User::where($loginField, $request->input($loginField))->firstOrFail();

        if ($user->is_suspended) {
            return response()->json([
                'message' => 'Votre compte a été suspendu. Contactez le support BERRNI.',
            ], 403);
        }

        if (!$user->is_verified) {
            return response()->json(['message' => 'Numéro non vérifié'], 403);
        }
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json($request->user()->load('wallet'));
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Déconnexion réussie']);
    }
}
