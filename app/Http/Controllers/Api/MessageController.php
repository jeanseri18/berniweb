<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Parcel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $parcelId)
    {
        $parcel = Parcel::findOrFail($parcelId);
        
        if ($parcel->sender_id !== $request->user()->id && $parcel->courier_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($parcel->messages()->with('sender:id,name')->latest()->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $parcelId)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $parcel = Parcel::findOrFail($parcelId);

        if ($parcel->sender_id !== $request->user()->id && $parcel->courier_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $message = $parcel->messages()->create([
            'sender_id' => $request->user()->id,
            'content' => $request->content,
        ]);

        return response()->json($message->load('sender:id,name'), 201);
    }
}
