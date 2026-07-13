<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Parcel;
use App\Models\ParcelOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OfferController extends Controller
{
    private function userId(Request $request): int
    {
        return (int) $request->user()->id;
    }

    private function userIsParcelSender(Request $request, Parcel $parcel): bool
    {
        return (int) $parcel->sender_id === $this->userId($request);
    }

    private function userIsAdmin(Request $request): bool
    {
        return $request->user()->role === 'admin';
    }

    /**
     * Propositions de l'utilisateur : envoyées (relais) ou reçues (expéditeur).
     */
    public function mine(Request $request)
    {
        $role = $request->query('role', 'sent');
        if (! in_array($role, ['sent', 'received'], true)) {
            return response()->json(['message' => 'Rôle invalide (sent ou received)'], 422);
        }

        $query = ParcelOffer::query()
            ->with([
                'courier:id,name,courier_status,is_courier',
                'parcel' => function ($q) {
                    $q->with(['sender:id,name', 'courier:id,name']);
                },
            ])
            ->latest();

        $userId = $this->userId($request);

        if ($role === 'sent') {
            $query->where('courier_id', $userId);
        } else {
            $query->whereHas('parcel', function ($q) use ($userId) {
                $q->where('sender_id', $userId);
            });
        }

        if ($request->filled('parcel_id')) {
            $query->where('parcel_id', (int) $request->query('parcel_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        return response()->json($query->paginate(20));
    }

    public function index(Request $request, $parcelId)
    {
        $parcel = Parcel::findOrFail($parcelId);

        if (! $this->userIsParcelSender($request, $parcel) && ! $this->userIsAdmin($request)) {
            return response()->json([
                'message' => 'Vous n’êtes pas l’expéditeur de ce colis.',
            ], 403);
        }

        return response()->json(
            $parcel->offers()
                ->with('courier:id,name,courier_status,is_courier')
                ->latest()
                ->get()
        );
    }

    public function store(Request $request, $parcelId)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $parcel = Parcel::findOrFail($parcelId);

        if ($parcel->status !== 'published') {
            return response()->json(['message' => 'Course indisponible'], 400);
        }

        if (!$request->user()->is_courier) {
            return response()->json(['message' => 'Vous devez être un relais validé'], 403);
        }

        $amount = $request->amount;
        $offer = ParcelOffer::updateOrCreate(
            ['parcel_id' => $parcel->id, 'courier_id' => $request->user()->id],
            [
                'amount' => $amount,
                'courier_amount' => $amount,
                'sender_amount' => null,
                'last_counter_by' => 'courier',
                'status' => 'pending',
                'turns_used' => 0,
            ]
        );

        return response()->json(['message' => 'Proposition envoyée', 'offer' => $offer], 201);
    }

    public function counter(Request $request, $offerId)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $offer = ParcelOffer::with('parcel')->findOrFail($offerId);
        $parcel = $offer->parcel;

        // Allow sender or courier to counter as long as still pending.
        $userId = $request->user()->id;
        $isSender = (int) $parcel->sender_id === (int) $userId;
        $isCourier = (int) $offer->courier_id === (int) $userId;

        if (! $isSender && ! $isCourier && ! $this->userIsAdmin($request)) {
            return response()->json(['message' => 'Accès refusé à cette proposition.'], 403);
        }

        if ($offer->status !== 'pending') {
            return response()->json(['message' => 'Proposition non modifiable'], 400);
        }

        if ($offer->turns_used >= 3) {
            return response()->json(['message' => 'Nombre de négociations atteint'], 400);
        }

        $lastBy = $offer->last_counter_by ?? (($offer->turns_used % 2 === 0) ? 'courier' : 'sender');
        if ($isSender && $lastBy === 'sender') {
            return response()->json([
                'message' => 'Attendez la réponse du relais avant une nouvelle offre.',
            ], 400);
        }
        if ($isCourier && $lastBy === 'courier' && $offer->turns_used > 0) {
            return response()->json([
                'message' => 'Attendez la réponse de l’expéditeur.',
            ], 400);
        }

        $amount = $request->amount;
        $payload = [
            'amount' => $amount,
            'turns_used' => $offer->turns_used + 1,
        ];

        if ($isSender) {
            $payload['sender_amount'] = $amount;
            $payload['last_counter_by'] = 'sender';
        } else {
            $payload['courier_amount'] = $amount;
            $payload['last_counter_by'] = 'courier';
        }

        $offer->update($payload);

        return response()->json(['message' => 'Contre-proposition envoyée', 'offer' => $offer->fresh()]);
    }

    public function accept(Request $request, $offerId)
    {
        $offer = ParcelOffer::with('parcel')->findOrFail($offerId);
        $parcel = $offer->parcel;

        if (! $this->userIsParcelSender($request, $parcel) && ! $this->userIsAdmin($request)) {
            return response()->json(['message' => 'Vous n’êtes pas l’expéditeur de ce colis.'], 403);
        }

        if ($offer->status !== 'pending') {
            return response()->json(['message' => 'Proposition déjà traitée'], 400);
        }

        if ($parcel->status !== 'published') {
            return response()->json(['message' => 'Course indisponible'], 400);
        }

        $lastBy = $offer->last_counter_by ?? (($offer->turns_used % 2 === 0) ? 'courier' : 'sender');
        if ($lastBy !== 'courier') {
            return response()->json([
                'message' => 'Vous ne pouvez pas accepter votre propre contre-proposition. Attendez la réponse du relais.',
            ], 400);
        }

        $offer->update(['status' => 'accepted']);
        $parcel->update([
            'courier_id' => $offer->courier_id,
            'status' => 'assigned',
            'price' => $offer->amount,
        ]);

        // Reject other offers
        ParcelOffer::where('parcel_id', $parcel->id)
            ->where('id', '!=', $offer->id)
            ->where('status', 'pending')
            ->update(['status' => 'rejected']);

        $parcel->messages()->create([
            'sender_id' => $offer->courier_id,
            'content' => 'Proposition acceptée — la messagerie est ouverte pour organiser la course.',
            'is_system_message' => true,
        ]);

        return response()->json(['message' => 'Proposition acceptée', 'parcel' => $parcel, 'offer' => $offer]);
    }

    public function reject(Request $request, $offerId)
    {
        $offer = ParcelOffer::with('parcel')->findOrFail($offerId);
        $parcel = $offer->parcel;

        if (! $this->userIsParcelSender($request, $parcel) && ! $this->userIsAdmin($request)) {
            return response()->json(['message' => 'Vous n’êtes pas l’expéditeur de ce colis.'], 403);
        }

        if ($offer->status !== 'pending') {
            return response()->json(['message' => 'Proposition déjà traitée'], 400);
        }

        $offer->update(['status' => 'rejected']);

        return response()->json(['message' => 'Proposition refusée', 'offer' => $offer]);
    }
}

