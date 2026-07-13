<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KycSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KycController extends Controller
{
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_card_front' => 'required|file|image|max:5120',
            'id_card_back' => 'required|file|image|max:5120',
            'selfie' => 'required|file|image|max:5120',
            'selfie_with_id' => 'required|file|image|max:5120',
            'transport_type' => 'required|string',
            'transport_mode' => 'nullable|string|in:voiture,train,avion',
            'transport_model' => 'nullable|string|max:255',
            'transport_plate' => 'nullable|string|max:255',
            'transport_photo' => 'nullable|file|image|max:5120',
            'zone_hint' => 'nullable|string|max:255',
            'availability_hint' => 'nullable|string|max:255',
            'payment_kind' => 'nullable|string|in:wallet,momo',
            'momo_number' => 'nullable|string|max:30',

            'full_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',

            'payment_method' => 'nullable|string|max:255',
            'payment_account' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Store files
        $paths = [];
        foreach (['id_card_front', 'id_card_back', 'selfie', 'selfie_with_id', 'transport_photo'] as $field) {
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store('kyc-documents', 'public');
                $paths[$field] = 'storage/' . $path;
            }
        }

        $submission = KycSubmission::create([
            'user_id' => $request->user()->id,
            'transport_type' => $request->transport_type,
            'transport_mode' => $request->input('transport_mode'),
            'transport_model' => $request->input('transport_model'),
            'transport_plate' => $request->input('transport_plate'),
            'transport_photo' => $paths['transport_photo'] ?? null,
            'zone_hint' => $request->input('zone_hint'),
            'availability_hint' => $request->input('availability_hint'),
            'payment_kind' => $request->input('payment_kind'),
            'momo_number' => $request->input('momo_number'),
            'status' => 'pending',
            'id_card_front' => $paths['id_card_front'],
            'id_card_back' => $paths['id_card_back'],
            'selfie' => $paths['selfie'],
            'selfie_with_id' => $paths['selfie_with_id'],

            'full_name' => $request->input('full_name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),

            'payment_method' => $request->input('payment_method'),
            'payment_account' => $request->input('payment_account'),
        ]);

        $request->user()->update(['courier_status' => 'pending']);

        return response()->json(['message' => 'Dossier soumis avec succès', 'submission' => $submission], 201);
    }

    public function status(Request $request)
    {
        $submission = KycSubmission::where('user_id', $request->user()->id)->latest()->first();
        return response()->json(['status' => $request->user()->courier_status, 'submission' => $submission]);
    }

    public function get(Request $request)
    {
        $submission = KycSubmission::where('user_id', $request->user()->id)
            ->latest()
            ->first();

        if (!$submission) {
            return response()->json([
                'message' => 'Aucun dossier KYC trouvé.'
            ], 404);
        }

        return response()->json([
            'submission' => $submission
        ], 200);
    }
}
