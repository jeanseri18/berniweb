<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Parcel;
use App\Models\SosAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SosAlertController extends Controller
{
    public function index(Request $request)
    {
        $alerts = SosAlert::with(['parcel:id,departure_address,destination_address,status', 'user:id,name'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return response()->json($alerts);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'parcel_id' => 'required|integer|exists:parcels,id',
            'reason' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $parcel = Parcel::findOrFail($request->parcel_id);
        $userId = $request->user()->id;

        // Only sender or courier can open an SOS on the parcel (admin handled via web UI)
        if ($parcel->sender_id !== $userId && $parcel->courier_id !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $alert = SosAlert::create([
            'user_id' => $userId,
            'parcel_id' => $parcel->id,
            'reason' => $request->reason,
            'status' => 'open',
        ]);

        return response()->json(['message' => 'Alerte SOS créée', 'alert' => $alert], 201);
    }

    public function show(Request $request, $id)
    {
        $alert = SosAlert::with(['parcel', 'user:id,name'])
            ->findOrFail($id);

        if ($alert->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($alert);
    }
}

