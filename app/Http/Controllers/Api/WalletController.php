<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function show(Request $request)
    {
        $wallet = $request->user()->wallet;
        
        if (!$wallet) {
            // Create if missing (should be created on register, but just in case)
            $wallet = $request->user()->wallet()->create([
                'balance_available' => 0,
                'balance_sequestered' => 0,
            ]);
        }

        return response()->json([
            'available' => $wallet->balance_available,
            'sequestered' => $wallet->balance_sequestered,
            'transactions' => $wallet->transactions()->latest()->take(10)->get()
        ]);
    }
}
