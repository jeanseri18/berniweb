<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Parcel;
use App\Notifications\ParcelPublishedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ParcelController extends Controller
{
    public function mine(Request $request)
    {
        $query = Parcel::with(['sender', 'courier'])
            ->withCount([
                'offers',
                'offers as pending_offers_count' => function ($q) {
                    $q->where('status', 'pending');
                },
            ])
            ->where(function ($q) use ($request) {
                $q->where('sender_id', $request->user()->id)
                  ->orWhere('courier_id', $request->user()->id);
            });

        if ($request->filled('role')) {
            if ($request->role === 'sender') {
                $query->where('sender_id', $request->user()->id);
            } elseif ($request->role === 'courier') {
                $query->where('courier_id', $request->user()->id);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->latest()->paginate(10));
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Parcel::with(['sender', 'courier']);

        $forMarket = $request->boolean('market') || $request->boolean('for_relay');

        if ($forMarket) {
            // Marché relais : annonces ouvertes, sans relais assigné.
            $query->where('status', 'published')->whereNull('courier_id');
            if ($request->user()) {
                $query->where('sender_id', '!=', $request->user()->id);
            }
        } elseif ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'published');
        }

        if ($request->has('departure')) {
            $query->where('departure_address', 'like', '%' . $request->departure . '%');
        }
        if ($request->has('destination')) {
            $query->where('destination_address', 'like', '%' . $request->destination . '%');
        }

        $query->orderByDesc('created_at');

        return response()->json($query->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->user()->is_sender === false) {
            return response()->json([
                'message' => 'Le rôle expéditeur est désactivé. Réactivez-le dans votre profil.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'description' => 'required|string',
            'category' => 'nullable|string|max:50',
            'fragile' => 'nullable|boolean',
            'departure_address' => 'required|string',
            'destination_address' => 'required|string',
            'departure_date' => 'required|date',
            'recipient_name' => 'required|string',
            'recipient_phone' => 'required|string',
            'recipient_note' => 'nullable|string|max:2000',
            'price' => 'required|numeric|min:0',
            'weight' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $parcel = Parcel::create([
            'sender_id' => $request->user()->id,
            'description' => $request->description,
            'category' => $request->input('category'),
            'departure_address' => $request->departure_address,
            'destination_address' => $request->destination_address,
            'departure_date' => $request->departure_date,
            'recipient_name' => $request->recipient_name,
            'recipient_phone' => $request->recipient_phone,
            'recipient_note' => $request->input('recipient_note'),
            'price' => $request->price,
            'weight' => $request->weight,
            'fragile' => $request->input('fragile', true),
            'status' => 'published',
            'verification_code' => (string) random_int(100000, 999999),
            'payment_status' => 'pending', // In real app, prompt payment here
        ]);

        $request->user()->notify(new ParcelPublishedNotification($parcel));

        return response()->json($parcel, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $parcel = Parcel::with(['sender', 'courier', 'messages'])
            ->withCount([
                'offers',
                'offers as pending_offers_count' => function ($q) {
                    $q->where('status', 'pending');
                },
            ])
            ->findOrFail($id);
        return response()->json($parcel);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $parcel = Parcel::findOrFail($id);

        if ($parcel->sender_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($parcel->status !== 'published') {
            return response()->json([
                'message' => 'Ce colis ne peut plus être modifié (déjà pris en charge ou terminé).',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'description' => 'sometimes|required|string',
            'category' => 'nullable|string|max:50',
            'fragile' => 'nullable|boolean',
            'departure_address' => 'sometimes|required|string',
            'destination_address' => 'sometimes|required|string',
            'departure_date' => 'sometimes|required|date',
            'recipient_name' => 'sometimes|required|string',
            'recipient_phone' => 'sometimes|required|string',
            'recipient_note' => 'nullable|string|max:2000',
            'price' => 'sometimes|required|numeric|min:0',
            'weight' => 'sometimes|required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $parcel->update($validator->validated());

        return response()->json($parcel->fresh());
    }

    /**
     * Courier accepts a parcel
     */
    public function accept(Request $request, $id)
    {
        $parcel = Parcel::findOrFail($id);

        if ($parcel->status !== 'published') {
            return response()->json(['message' => 'Colis déjà assigné ou indisponible'], 400);
        }

        if (!$request->user()->is_courier) {
             return response()->json(['message' => 'Vous devez être un relais validé'], 403);
        }

        $parcel->update([
            'courier_id' => $request->user()->id,
            'status' => 'assigned',
        ]);

        return response()->json(['message' => 'Colis accepté avec succès', 'parcel' => $parcel]);
    }

    public function pickup(Request $request, $id)
    {
        $parcel = Parcel::findOrFail($id);

        if ($parcel->courier_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($parcel->status !== 'assigned') {
            return response()->json(['message' => 'Transition invalide'], 400);
        }

        $parcel->update(['status' => 'picked_up']);

        return response()->json(['message' => 'Colis récupéré', 'parcel' => $parcel]);
    }

    public function inTransit(Request $request, $id)
    {
        $parcel = Parcel::findOrFail($id);

        if ($parcel->courier_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!in_array($parcel->status, ['assigned', 'picked_up'], true)) {
            return response()->json(['message' => 'Transition invalide'], 400);
        }

        $parcel->update(['status' => 'in_transit']);

        return response()->json(['message' => 'Colis en transit', 'parcel' => $parcel]);
    }

    public function delivered(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'verification_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $parcel = Parcel::findOrFail($id);

        if ($parcel->courier_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($parcel->status !== 'in_transit') {
            return response()->json(['message' => 'Transition invalide'], 400);
        }

        if ($parcel->verification_code !== $request->verification_code) {
            return response()->json(['message' => 'Code de vérification invalide'], 400);
        }

        $parcel->update([
            'status' => 'delivered',
            'payment_status' => $parcel->payment_status === 'sequestered' ? 'released' : $parcel->payment_status,
        ]);

        return response()->json(['message' => 'Colis livré', 'parcel' => $parcel]);
    }

    public function cancel(Request $request, $id)
    {
        $parcel = Parcel::findOrFail($id);

        if ($parcel->sender_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!in_array($parcel->status, ['published', 'assigned'], true)) {
            return response()->json(['message' => 'Annulation impossible à ce stade'], 400);
        }

        $parcel->update([
            'status' => 'cancelled',
            'payment_status' => in_array($parcel->payment_status, ['sequestered'], true) ? 'refunded' : $parcel->payment_status,
        ]);

        return response()->json(['message' => 'Colis annulé', 'parcel' => $parcel]);
    }
}
