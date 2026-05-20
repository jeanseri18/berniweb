<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Parcel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConversationController extends Controller
{
    public function index(Request $request)
    {
        // Messagerie contextuelle : uniquement après accord (relais assigné).
        $parcels = Parcel::with(['sender:id,name', 'courier:id,name'])
            ->where(function ($q) use ($request) {
                $q->where('sender_id', $request->user()->id)
                  ->orWhere('courier_id', $request->user()->id);
            })
            ->whereNotNull('courier_id')
            ->latest()
            ->get();

        $items = $parcels->map(function (Parcel $p) use ($request) {
            $other = $p->sender_id === $request->user()->id ? $p->courier : $p->sender;
            $last = $p->messages()->latest()->first();
            $lastUser = $p->messages()->where('is_system_message', false)->latest()->first();

            $preview = $last?->content;
            if ($preview === null || trim($preview) === '') {
                $preview = 'La messagerie est ouverte pour cette course.';
            }

            $lastAt = $last?->created_at ?? $p->updated_at ?? $p->created_at;
            $terminated = in_array($p->status, ['delivered', 'completed', 'cancelled'], true);

            return [
                'id' => 'parcel_'.$p->id,
                'parcel_id' => $p->id,
                'other_party_name' => $this->otherPartyName($other),
                'route_label' => $this->routeLabel($p),
                'status' => $terminated ? 'terminee' : 'enCours',
                'last_message_preview' => $preview,
                'last_message_at' => $lastAt?->toIso8601String(),
                'is_active_course' => ! $terminated,
                'needs_attention' => $this->needsAttention($p, $lastUser),
                'messaging_unlocked' => true,
            ];
        })->values();

        $hasUnlocked = $items->isNotEmpty();

        return response()->json([
            'is_locked' => ! $hasUnlocked,
            'conversations' => $items,
            'pending_without_agreement' => $this->countPendingWithoutAgreement($request),
        ]);
    }

    public function messages(Request $request, $conversationId)
    {
        $parcel = $this->findUnlockedParcel($request, $conversationId);
        if ($parcel instanceof \Illuminate\Http\JsonResponse) {
            return $parcel;
        }

        $messages = $parcel->messages()->latest()->take(200)->get()->reverse()->values();

        return response()->json($messages->map(function (Message $m) use ($request) {
            return [
                'id' => (string) $m->id,
                'body' => $m->content,
                'is_mine' => $m->sender_id === $request->user()->id,
                'is_system' => (bool) $m->is_system_message,
                'sent_at' => $m->created_at?->toIso8601String(),
            ];
        }));
    }

    public function send(Request $request, $conversationId)
    {
        $validator = Validator::make($request->all(), [
            'body' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $parcel = $this->findUnlockedParcel($request, $conversationId);
        if ($parcel instanceof \Illuminate\Http\JsonResponse) {
            return $parcel;
        }

        $message = $parcel->messages()->create([
            'sender_id' => $request->user()->id,
            'content' => $request->body,
            'is_system_message' => false,
        ]);

        return response()->json([
            'id' => (string) $message->id,
            'body' => $message->content,
            'is_mine' => true,
            'is_system' => false,
            'sent_at' => $message->created_at?->toIso8601String(),
        ], 201);
    }

    /**
     * @return Parcel|\Illuminate\Http\JsonResponse
     */
    private function findUnlockedParcel(Request $request, $conversationId)
    {
        $parcelId = (int) str_replace('parcel_', '', (string) $conversationId);
        $parcel = Parcel::findOrFail($parcelId);

        if ($parcel->sender_id !== $request->user()->id
            && $parcel->courier_id !== $request->user()->id
            && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($parcel->courier_id === null) {
            return response()->json([
                'message' => 'La messagerie s\'ouvre uniquement après acceptation d\'une proposition.',
            ], 403);
        }

        return $parcel;
    }

    private function countPendingWithoutAgreement(Request $request): int
    {
        return Parcel::where('sender_id', $request->user()->id)
            ->whereNull('courier_id')
            ->where('status', 'published')
            ->count();
    }

    private function needsAttention(Parcel $parcel, ?Message $lastUserMessage): bool
    {
        if (in_array($parcel->status, ['delivered', 'completed', 'cancelled'], true)) {
            return false;
        }

        $silenceHours = in_array($parcel->status, ['picked_up', 'in_transit'], true) ? 12 : 24;
        $since = $lastUserMessage?->created_at ?? $parcel->updated_at ?? $parcel->created_at;

        if ($since === null) {
            return false;
        }

        return $since->lt(now()->subHours($silenceHours));
    }

    private function otherPartyName(?User $other): string
    {
        $name = trim((string) ($other?->name ?? ''));
        if ($name !== '') {
            return $name;
        }

        return 'Interlocuteur';
    }

    private function routeLabel(Parcel $parcel): string
    {
        $from = $this->shortAddress($parcel->departure_address);
        $to = $this->shortAddress($parcel->destination_address);
        if ($from === '' && $to === '') {
            return '';
        }
        if ($from === '') {
            return $to;
        }
        if ($to === '') {
            return $from;
        }

        return $from.' → '.$to;
    }

    private function shortAddress(?string $address): string
    {
        if ($address === null) {
            return '';
        }
        $s = trim($address);
        if ($s === '') {
            return '';
        }
        if (str_contains($s, ',')) {
            $s = trim(explode(',', $s, 2)[0]);
        }

        return mb_strlen($s) > 28 ? mb_substr($s, 0, 25).'…' : $s;
    }
}
