<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Parcel;
use Illuminate\Http\Request;

class WalletSnapshotController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $wallet = $user->wallet;

        // FCFA in UI is integer; we round decimals from DB.
        $available = $wallet ? (int) round((float) $wallet->balance_available) : 0;
        $pending = $wallet ? (int) round((float) $wallet->balance_sequestered) : 0;

        $pendingEscrow = null;
        $escrowParcel = Parcel::where('sender_id', $user->id)
            ->where('payment_status', 'sequestered')
            ->latest()
            ->first();

        if ($escrowParcel) {
            $pendingEscrow = [
                'course_ref' => 'BRN-'.$escrowParcel->id,
                'amount_fcfa' => (int) round((float) $escrowParcel->price),
                'summary_line' => 'Libération après confirmation du destinataire',
            ];
        }

        // Payment follow-ups: sender parcels with payment status
        $paymentFollowUps = Parcel::where('sender_id', $user->id)
            ->whereIn('payment_status', ['pending', 'sequestered', 'released', 'refunded'])
            ->latest()
            ->take(20)
            ->get()
            ->map(function (Parcel $p) {
                $statusLabel = match ($p->payment_status) {
                    'pending' => 'En attente',
                    'sequestered' => 'Séquestre actif',
                    'released' => 'Libéré',
                    'refunded' => 'Remboursé',
                    default => $p->payment_status,
                };

                return [
                    'id' => 'p'.$p->id,
                    'course_ref' => 'BRN-'.$p->id,
                    'route_label' => $p->departure_address.' → '.$p->destination_address,
                    'status_label' => $statusLabel,
                    'amount_fcfa' => (int) round((float) $p->price),
                    'updated_label' => 'Mis à jour '.$p->updated_at->diffForHumans(),
                    'hint' => $p->payment_status === 'sequestered'
                        ? 'Le montant sera libéré une fois le colis réceptionné.'
                        : null,
                ];
            })
            ->values();

        // Relay gains follow-ups: courier parcels delivered but not yet "completed"
        $relayGainFollowUps = Parcel::where('courier_id', $user->id)
            ->whereIn('status', ['assigned', 'picked_up', 'in_transit', 'delivered', 'completed'])
            ->latest()
            ->take(20)
            ->get()
            ->map(function (Parcel $p) {
                $credited = in_array($p->payment_status, ['released'], true);
                $statusLabel = $credited ? 'Versé sur le portefeuille' : 'En cours de versement';

                return [
                    'id' => 'r'.$p->id,
                    'course_ref' => 'BRN-'.$p->id,
                    'route_label' => $p->departure_address.' → '.$p->destination_address,
                    'status_label' => $statusLabel,
                    'amount_fcfa' => (int) round((float) $p->price),
                    'updated_label' => 'Mis à jour '.$p->updated_at->diffForHumans(),
                    'credited' => $credited,
                ];
            })
            ->values();

        $history = $wallet
            ? $wallet->transactions()->latest()->take(30)->get()->map(function ($tx) {
                $amountFcfa = (int) round((float) $tx->amount);
                $signed = in_array($tx->type, ['withdrawal', 'sequester', 'commission'], true) ? -$amountFcfa : $amountFcfa;

                return [
                    'id' => (string) $tx->id,
                    'amount_fcfa' => $signed,
                    'label' => $tx->description ?? $tx->type,
                    'date_label' => $tx->created_at?->diffForHumans(),
                ];
            })->values()
            : collect();

        // Cas 1 pédagogique : pas encore de mouvement réel sur le portefeuille.
        $hasFinancialActivity = $available > 0
            || $pending > 0
            || $history->isNotEmpty()
            || $pendingEscrow !== null;

        return response()->json([
            'is_pedagogique_mode' => ! $hasFinancialActivity,
            'available_fcfa' => $available,
            'pending_fcfa' => $pending,
            'pending_escrow' => $pendingEscrow,
            'history' => $history,
            'payment_follow_ups' => $paymentFollowUps,
            'relay_gain_follow_ups' => $relayGainFollowUps,
        ]);
    }
}

