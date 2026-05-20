<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Parcel;
use App\Models\KycSubmission;
use App\Models\SosAlert;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    private function perPage(Request $request): int
    {
        $n = (int) $request->input('per_page', 20);

        return max(10, min(50, $n));
    }

    public function index()
    {
        $stats = [
            'users' => User::count(),
            'couriers_pending' => KycSubmission::where('status', 'pending')->count(),
            'active_parcels' => Parcel::whereIn('status', ['published', 'assigned', 'picked_up', 'in_transit'])->count(),
            'sos_alerts' => SosAlert::where('status', 'open')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function kycList(Request $request)
    {
        $query = KycSubmission::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        } else {
            $query->where('status', 'pending');
        }

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->whereHas('user', function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        $submissions = $query->paginate($this->perPage($request))->appends($request->query());

        return view('admin.kyc_list', compact('submissions'));
    }

    public function kycShow($id)
    {
        $submission = KycSubmission::with('user')->findOrFail($id);

        return view('admin.kyc_show', compact('submission'));
    }

    public function kycApprove($id)
    {
        $submission = KycSubmission::findOrFail($id);
        $submission->update(['status' => 'approved']);

        $user = $submission->user;
        $user->update(['is_courier' => true, 'courier_status' => 'approved']);

        return redirect()->route('admin.kyc.list')->with('success', 'Relais validé avec succès.');
    }

    public function kycReject(Request $request, $id)
    {
        $submission = KycSubmission::findOrFail($id);
        $submission->update([
            'status' => 'rejected',
            'admin_notes' => $request->input('reason'),
        ]);

        $user = $submission->user;
        $user->update(['courier_status' => 'rejected']);

        return redirect()->route('admin.kyc.list')->with('success', 'Relais refusé.');
    }

    public function parcels(Request $request)
    {
        $query = Parcel::with(['sender', 'courier'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($w) use ($q) {
                $w->where('id', $q)
                    ->orWhere('departure_address', 'like', "%{$q}%")
                    ->orWhere('destination_address', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhereHas('sender', function ($s) use ($q) {
                        $s->where('name', 'like', "%{$q}%");
                    });
            });
        }

        $parcels = $query->paginate($this->perPage($request))->appends($request->query());

        return view('admin.parcels', compact('parcels'));
    }

    public function sos(Request $request)
    {
        $query = SosAlert::with(['user', 'parcel'])->latest();

        $status = $request->input('status', 'active');
        if ($status === 'active') {
            $query->whereIn('status', ['open', 'investigating']);
        } elseif ($status !== '' && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($request->filled('parcel_id')) {
            $query->where('parcel_id', $request->input('parcel_id'));
        }

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($w) use ($q) {
                $w->where('reason', 'like', "%{$q}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$q}%"));
            });
        }

        $alerts = $query->paginate($this->perPage($request))->appends($request->query());

        return view('admin.sos', compact('alerts'));
    }

    public function users(Request $request)
    {
        $query = User::latest();

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        if ($request->filled('role')) {
            if ($request->input('role') === 'admin') {
                $query->where('role', 'admin');
            } elseif ($request->input('role') === 'courier') {
                $query->where('is_courier', true);
            } elseif ($request->input('role') === 'sender') {
                $query->where('is_sender', true);
            }
        }

        if ($request->filled('suspended')) {
            $query->where('is_suspended', $request->input('suspended') === '1');
        }

        $users = $query->paginate($this->perPage($request))->appends($request->query());

        return view('admin.users.index', compact('users'));
    }

    public function couriers(Request $request)
    {
        $query = User::query()
            ->where(function ($w) {
                $w->where('is_courier', true)
                    ->orWhere('courier_status', '!=', 'none');
            })
            ->with('wallet')
            ->withCount('deliveredParcels')
            ->latest();

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        if ($request->filled('courier_status')) {
            $query->where('courier_status', $request->input('courier_status'));
        }

        $users = $query->paginate($this->perPage($request))->appends($request->query());

        return view('admin.users.couriers', compact('users'));
    }

    public function senders(Request $request)
    {
        $query = User::query()
            ->where('is_courier', false)
            ->whereHas('sentParcels')
            ->withCount('sentParcels')
            ->withSum('sentParcels as total_spent', 'price')
            ->withMax('sentParcels as last_parcel_at', 'created_at')
            ->latest();

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        $users = $query->paginate($this->perPage($request))->appends($request->query());

        return view('admin.users.senders', compact('users'));
    }
}
