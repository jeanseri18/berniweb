<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CinetpayPayment;
use App\Models\Parcel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    /**
     * Init payment for a parcel (sender only).
     * This is a stub ready to be wired to CinetPay.
     */
    public function init(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'parcel_id' => 'required|integer|exists:parcels,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $parcel = Parcel::findOrFail($request->parcel_id);

        if ($parcel->sender_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!in_array($parcel->payment_status, ['pending', 'refunded'], true)) {
            return response()->json(['message' => 'Paiement déjà démarré ou finalisé'], 400);
        }

        $payment = CinetpayPayment::create([
            'user_id' => $request->user()->id,
            'parcel_id' => $parcel->id,
            'amount' => $parcel->price,
            'currency' => 'XOF',
            'status' => 'initiated',
            'provider' => 'cinetpay',
        ]);

        // Keep parcel state in sync with "payment started"
        $parcel->update([
            'payment_status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Paiement initialisé (à connecter à CinetPay)',
            'payment' => $payment,
            'checkout_url' => $payment->checkout_url, // null until wired
        ], 201);
    }

    public function status(Request $request, $id)
    {
        $payment = CinetpayPayment::with('parcel')->findOrFail($id);

        if ($payment->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($payment);
    }

    /**
     * Provider notify/webhook endpoint (no auth).
     * You will validate signature and call CinetPay verification here.
     */
    public function notify(Request $request)
    {
        // Save raw payload for debugging / reconciliation.
        // When you wire CinetPay: locate payment by provider_payment_id and update status.
        return response()->json(['ok' => true]);
    }

    /**
     * Provider return URL endpoint (no auth).
     * Mobile app can open it in a WebView; you can redirect to deep-link later.
     */
    public function callback(Request $request)
    {
        return response()->json(['ok' => true]);
    }
}

