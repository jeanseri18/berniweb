<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->wallet?->transactions()->latest();

        if (!$query) {
            return response()->json([
                'data' => [],
                'message' => 'Aucune transaction',
            ]);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('parcel_id')) {
            $query->where('parcel_id', $request->parcel_id);
        }

        return response()->json($query->paginate(20));
    }
}

