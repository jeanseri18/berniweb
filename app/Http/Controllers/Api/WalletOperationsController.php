<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WalletOperationsController extends Controller
{
    public function deposit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:500|max:5000000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $amount = round((float) $request->amount, 2);
        $user = $request->user();
        $wallet = $user->wallet ?? $user->wallet()->create([
            'balance_available' => 0,
            'balance_sequestered' => 0,
        ]);

        DB::transaction(function () use ($wallet, $amount, $user) {
            $wallet->increment('balance_available', $amount);
            Transaction::create([
                'wallet_id' => $wallet->id,
                'amount' => $amount,
                'type' => 'deposit',
                'description' => 'Recharge portefeuille (Mobile Money / carte)',
            ]);
        });

        $wallet->refresh();

        return response()->json([
            'message' => 'Fonds ajoutés avec succès.',
            'available' => (int) round((float) $wallet->balance_available),
            'sequestered' => (int) round((float) $wallet->balance_sequestered),
        ]);
    }

    public function withdraw(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:500|max:5000000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $amount = round((float) $request->amount, 2);
        $user = $request->user();
        $wallet = $user->wallet;

        if (! $wallet) {
            return response()->json(['message' => 'Portefeuille introuvable'], 404);
        }

        if ((float) $wallet->balance_available < $amount) {
            return response()->json([
                'message' => 'Solde disponible insuffisant pour ce retrait.',
            ], 400);
        }

        DB::transaction(function () use ($wallet, $amount) {
            $wallet->decrement('balance_available', $amount);
            Transaction::create([
                'wallet_id' => $wallet->id,
                'amount' => $amount,
                'type' => 'withdrawal',
                'description' => 'Retrait vers Mobile Money',
            ]);
        });

        $wallet->refresh();

        return response()->json([
            'message' => 'Retrait enregistré. Versement sous 24–48 h (démo).',
            'available' => (int) round((float) $wallet->balance_available),
            'sequestered' => (int) round((float) $wallet->balance_sequestered),
        ]);
    }
}
