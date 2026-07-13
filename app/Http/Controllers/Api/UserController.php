<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20|unique:users,phone,'.$request->user()->id,
            'email' => 'sometimes|nullable|string|email|max:255|unique:users,email,'.$request->user()->id,
            'is_sender' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = $request->user();
        $fields = $request->only(['name', 'phone', 'email', 'is_sender']);

        if (array_key_exists('is_sender', $fields) && $fields['is_sender'] === false
            && !$user->is_courier) {
            return response()->json([
                'message' => 'Activez au moins un rôle (expéditeur ou relais).',
            ], 422);
        }

        $user->forceFill($fields)->save();

        return response()->json([
            'message' => 'Profil mis à jour',
            'user' => $request->user()->fresh()->load('wallet'),
        ]);
    }

    public function updatePaymentMethod(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_kind' => 'required|string|in:wallet,momo',
            'momo_number' => 'required_if:payment_kind,momo|nullable|string|max:30',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $kind = $request->input('payment_kind');
        $momo = $kind === 'momo' ? trim((string) $request->input('momo_number', '')) : null;

        if ($kind === 'momo' && ($momo === null || $momo === '')) {
            return response()->json(['message' => 'Indiquez votre numéro Mobile Money.'], 422);
        }

        $user = $request->user();
        $user->update([
            'payment_kind' => $kind,
            'momo_number' => $momo,
        ]);

        $latestKyc = $user->kycSubmissions()->latest()->first();
        if ($latestKyc) {
            $latestKyc->update([
                'payment_kind' => $kind,
                'momo_number' => $momo,
            ]);
        }

        return response()->json([
            'message' => 'Moyen de paiement enregistré.',
            'user' => $user->fresh()->load('wallet'),
        ]);
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Mot de passe actuel incorrect.'], 403);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Mot de passe mis à jour.']);
    }

    public function deleteAccount(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        $user->update(['is_suspended' => true]);

        return response()->json(['message' => 'Compte supprimé.']);
    }

    public function reviews(Request $request)
    {
        // If a Review model exists, use it; otherwise return empty
        if (class_exists(\App\Models\Review::class)) {
            $reviews = \App\Models\Review::where('reviewed_id', $request->user()->id)
                ->with('reviewer:id,name')
                ->latest()
                ->get()
                ->map(fn ($r) => [
                    'id' => $r->id,
                    'reviewer_name' => $r->reviewer->name ?? null,
                    'rating' => $r->rating,
                    'comment' => $r->comment,
                    'created_at' => $r->created_at?->toIso8601String(),
                ]);
            return response()->json(['reviews' => $reviews]);
        }

        return response()->json(['reviews' => []]);
    }
}
