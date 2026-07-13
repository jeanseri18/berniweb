<?php

namespace App\Http\Controllers;

use App\Models\CinetpayPayment;
use App\Models\Message;
use App\Models\Parcel;
use App\Models\ParcelOffer;
use App\Models\SosAlert;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\AdminBroadcastNotification;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class AdminOperationsController extends Controller
{
    private function perPage(Request $request): int
    {
        $n = (int) $request->input('per_page', 20);

        return max(10, min(50, $n));
    }

    // ─── PARCEL DETAIL ────────────────────────────────────────────

    public function parcelShow($id)
    {
        $parcel = Parcel::with(['sender', 'courier', 'offers.courier', 'messages.sender', 'sosAlerts', 'transactions'])->findOrFail($id);

        return view('admin.parcels_show', compact('parcel'));
    }

    public function parcelUpdateStatus(Request $request, $id)
    {
        $parcel = Parcel::findOrFail($id);
        $request->validate([
            'status' => 'required|in:published,assigned,picked_up,in_transit,delivered,completed,cancelled',
        ]);
        $parcel->update(['status' => $request->input('status')]);

        return redirect()->route('admin.parcels.show', $id)->with('success', 'Statut mis à jour.');
    }

    public function parcelCancel($id)
    {
        $parcel = Parcel::findOrFail($id);
        $parcel->update(['status' => 'cancelled']);

        return redirect()->route('admin.parcels.show', $id)->with('success', 'Colis annulé.');
    }

    // ─── FINANCES ─────────────────────────────────────────────────

    public function finances(Request $request)
    {
        $query = Wallet::with('user')->orderByDesc('balance_available');

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->whereHas('user', function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        if ($request->filled('min_balance')) {
            $query->where('balance_available', '>=', (float) $request->input('min_balance'));
        }

        $wallets = $query->paginate($this->perPage($request))->appends($request->query());
        $totals = [
            'available' => Wallet::sum('balance_available'),
            'sequestered' => Wallet::sum('balance_sequestered'),
            'transactions_count' => Transaction::count(),
        ];

        return view('admin.finances', compact('wallets', 'totals'));
    }

    public function transactions(Request $request)
    {
        $query = Transaction::with(['wallet.user', 'parcel'])->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('parcel_id')) {
            $query->where('parcel_id', $request->input('parcel_id'));
        }

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($w) use ($q) {
                $w->where('description', 'like', "%{$q}%")
                    ->orWhereHas('wallet.user', function ($u) use ($q) {
                        $u->where('name', 'like', "%{$q}%");
                    });
            });
        }

        $transactions = $query->paginate($this->perPage($request))->appends($request->query());

        return view('admin.transactions', compact('transactions'));
    }

    public function walletAdjust(Request $request, $userId)
    {
        $request->validate([
            'direction' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:1|max:5000000',
            'note' => 'nullable|string|max:255',
        ]);

        $user = User::findOrFail($userId);
        $wallet = $user->wallet ?? $user->wallet()->create([
            'balance_available' => 0,
            'balance_sequestered' => 0,
        ]);

        $amount = round((float) $request->amount, 2);
        $direction = $request->input('direction');

        if ($direction === 'debit' && (float) $wallet->balance_available < $amount) {
            return redirect()
                ->route('admin.users.show', $userId)
                ->with('error', 'Solde disponible insuffisant pour ce débit.');
        }

        DB::transaction(function () use ($wallet, $amount, $direction, $request) {
            if ($direction === 'credit') {
                $wallet->increment('balance_available', $amount);
                $type = 'admin_credit';
            } else {
                $wallet->decrement('balance_available', $amount);
                $type = 'admin_debit';
            }

            Transaction::create([
                'wallet_id' => $wallet->id,
                'amount' => $amount,
                'type' => $type,
                'description' => $request->input('note')
                    ?: ('Ajustement manuel admin (' . $direction . ')'),
            ]);
        });

        return redirect()
            ->route('admin.users.show', $userId)
            ->with('success', 'Portefeuille ajusté avec succès.');
    }

    // ─── CINETPAY ─────────────────────────────────────────────────

    public function payments(Request $request)
    {
        $query = CinetpayPayment::with(['user:id,name,phone', 'parcel:id,departure_address,destination_address,status'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('parcel_id')) {
            $query->where('parcel_id', $request->input('parcel_id'));
        }

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($w) use ($q) {
                $w->where('provider_payment_id', 'like', "%{$q}%")
                    ->orWhereHas('user', function ($u) use ($q) {
                        $u->where('name', 'like', "%{$q}%")
                            ->orWhere('phone', 'like', "%{$q}%");
                    });
            });
        }

        $payments = $query->paginate($this->perPage($request))->appends($request->query());

        return view('admin.payments', compact('payments'));
    }

    public function paymentShow($id)
    {
        $payment = CinetpayPayment::with(['user', 'parcel.sender', 'parcel.courier'])->findOrFail($id);

        return view('admin.payments_show', compact('payment'));
    }

    // ─── SOS RESOLUTION ───────────────────────────────────────────

    public function sosResolve(Request $request, $id)
    {
        $alert = SosAlert::findOrFail($id);
        $alert->update([
            'status' => 'resolved',
            'resolution_notes' => $request->input('notes', 'Résolu par admin.'),
        ]);

        return redirect()->route('admin.sos')->with('success', 'Alerte #' . $id . ' résolue.');
    }

    public function sosClose($id)
    {
        $alert = SosAlert::findOrFail($id);
        $alert->update(['status' => 'investigating']);

        return redirect()->route('admin.sos')->with('success', 'Alerte #' . $id . ' passée en investigation.');
    }

    // ─── OFFERS ───────────────────────────────────────────────────

    public function offers(Request $request)
    {
        $query = ParcelOffer::with(['parcel.sender', 'courier'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('parcel_id')) {
            $query->where('parcel_id', $request->input('parcel_id'));
        }

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($w) use ($q) {
                $w->where('parcel_id', $q)
                    ->orWhereHas('courier', fn ($c) => $c->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('parcel.sender', fn ($s) => $s->where('name', 'like', "%{$q}%"));
            });
        }

        $offers = $query->paginate($this->perPage($request))->appends($request->query());

        return view('admin.offers', compact('offers'));
    }

    public function offerAccept($id)
    {
        $offer = ParcelOffer::with('parcel')->findOrFail($id);
        $parcel = $offer->parcel;

        if ($offer->status !== 'pending') {
            return redirect()->route('admin.offers')->with('error', 'Proposition déjà traitée.');
        }

        if (! in_array($parcel->status, ['published', 'assigned'], true)) {
            return redirect()->route('admin.offers')->with('error', 'Colis non éligible pour acceptation.');
        }

        $offer->update(['status' => 'accepted']);
        $parcel->update([
            'courier_id' => $offer->courier_id,
            'status' => 'assigned',
            'price' => $offer->amount,
        ]);

        ParcelOffer::where('parcel_id', $parcel->id)
            ->where('id', '!=', $offer->id)
            ->where('status', 'pending')
            ->update(['status' => 'rejected']);

        $parcel->messages()->create([
            'sender_id' => $offer->courier_id,
            'content' => 'Proposition acceptée par l’administration — messagerie ouverte.',
            'is_system_message' => true,
        ]);

        return redirect()->route('admin.offers')->with('success', 'Offre #' . $id . ' acceptée (colis #' . $parcel->id . ' assigné).');
    }

    public function offerReject($id)
    {
        $offer = ParcelOffer::findOrFail($id);

        if ($offer->status !== 'pending') {
            return redirect()->route('admin.offers')->with('error', 'Proposition déjà traitée.');
        }

        $offer->update(['status' => 'rejected']);

        return redirect()->route('admin.offers')->with('success', 'Offre #' . $id . ' refusée.');
    }

    public function offerResetNegotiation($id)
    {
        $offer = ParcelOffer::findOrFail($id);

        if ($offer->status !== 'pending') {
            return redirect()->route('admin.offers')->with('error', 'Seules les offres en attente peuvent être réinitialisées.');
        }

        $baseAmount = $offer->courier_amount ?? $offer->amount;

        $offer->update([
            'amount' => $baseAmount,
            'courier_amount' => $baseAmount,
            'sender_amount' => null,
            'last_counter_by' => 'courier',
            'turns_used' => 0,
        ]);

        return redirect()->route('admin.offers')->with('success', 'Négociation #' . $id . ' réinitialisée (tour relais).');
    }

    // ─── USER ACTIONS ─────────────────────────────────────────────

    public function userSuspend($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_suspended' => true]);
        $user->tokens()->delete();

        return redirect()->route('admin.users.show', $id)->with('success', 'Utilisateur suspendu (sessions mobile révoquées).');
    }

    public function userReactivate($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_suspended' => false]);

        return redirect()->route('admin.users.show', $id)->with('success', 'Utilisateur réactivé.');
    }

    // ─── MESSAGING MODERATION ─────────────────────────────────────

    public function messages(Request $request)
    {
        $query = Message::with(['parcel', 'sender'])->latest();

        if ($request->filled('parcel_id')) {
            $query->where('parcel_id', $request->input('parcel_id'));
        }

        if ($request->filled('kind')) {
            if ($request->input('kind') === 'system') {
                $query->where('is_system_message', true);
            } elseif ($request->input('kind') === 'user') {
                $query->where('is_system_message', false);
            }
        }

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where('content', 'like', "%{$q}%");
        }

        $messages = $query->paginate($this->perPage($request))->appends($request->query());

        return view('admin.messages', compact('messages'));
    }

    public function messageDelete($id)
    {
        Message::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Message supprimé.');
    }

    // ─── REVIEWS ──────────────────────────────────────────────────

    public function reviews()
    {
        if (class_exists(\App\Models\Review::class)) {
            $reviews = \App\Models\Review::with(['reviewer', 'reviewed'])->latest()->paginate(30);
        } else {
            $reviews = collect();
        }

        return view('admin.reviews', compact('reviews'));
    }

    // ─── NOTIFICATIONS ────────────────────────────────────────────

    public function notificationsSend(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'body' => 'required|string|max:500',
            'target' => 'required|in:all,couriers,senders',
        ]);

        $query = User::query()->where('is_suspended', false);

        if ($request->input('target') === 'couriers') {
            $query->where('is_courier', true);
        } elseif ($request->input('target') === 'senders') {
            $query->where('is_sender', true);
        }

        $users = $query->get();
        $notification = new AdminBroadcastNotification(
            $request->input('title'),
            $request->input('body'),
        );

        Notification::send($users, $notification);

        return redirect()->back()->with('success', 'Notification envoyée à ' . $users->count() . ' utilisateur(s).');
    }

    public function notificationsPage()
    {
        $recentNotifs = DatabaseNotification::query()
            ->where('type', AdminBroadcastNotification::class)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return view('admin.notifications', compact('recentNotifs'));
    }
}
