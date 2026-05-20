<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Parcel;
use App\Models\KycSubmission;
use App\Models\SosAlert;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\ParcelOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. KPI Stats
        $totalParcels = Parcel::count();
        $deliveredParcels = Parcel::where('status', 'delivered')->count();
        $cancelledParcels = Parcel::where('status', 'cancelled')->count();

        $stats = [
            'users_count' => User::count(),
            'users_growth' => $this->calculateGrowth(User::class),
            'couriers_pending' => KycSubmission::where('status', 'pending')->count(),
            'active_parcels' => Parcel::whereIn('status', ['published', 'assigned', 'picked_up', 'in_transit'])->count(),
            'completed_parcels' => $deliveredParcels,
            'sos_alerts' => SosAlert::where('status', 'open')->count(),
            'revenue' => Parcel::where('status', 'delivered')->sum('price') * 0.15,
            'conversion_rate' => $totalParcels > 0 ? round(($deliveredParcels / $totalParcels) * 100, 1) : 0,
            'cancellation_rate' => $totalParcels > 0 ? round(($cancelledParcels / $totalParcels) * 100, 1) : 0,
            'total_wallets_balance' => Wallet::sum('balance_available'),
            'total_sequestered' => Wallet::sum('balance_sequestered'),
            'offers_pending' => ParcelOffer::where('status', 'pending')->count(),
            'transactions_today' => Transaction::whereDate('created_at', today())->count(),
        ];

        // 2. Charts Data
        $charts = [
            'parcels_by_status' => $this->getParcelsByStatus(),
            'users_registration' => $this->getUsersRegistrationHistory(),
            'revenue_history' => $this->getRevenueHistory(),
        ];

        // 3. Recent Activity
        $recent_parcels = Parcel::with(['sender', 'courier'])
            ->latest()
            ->take(5)
            ->get();

        $recent_users = User::latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'charts', 'recent_parcels', 'recent_users'));
    }

    private function calculateGrowth($model)
    {
        $lastMonth = $model::whereMonth('created_at', Carbon::now()->subMonth()->month)->count();
        $thisMonth = $model::whereMonth('created_at', Carbon::now()->month)->count();

        if ($lastMonth == 0) return 100;

        return round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1);
    }

    private function getParcelsByStatus()
    {
        return Parcel::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    private function getUsersRegistrationHistory()
    {
        return User::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();
    }

    private function getRevenueHistory()
    {
        // Assuming revenue is based on delivered parcels
        return Parcel::where('status', 'delivered')
            ->where('updated_at', '>=', Carbon::now()->subDays(30))
            ->select(DB::raw('DATE(updated_at) as date'), DB::raw('SUM(price * 0.15) as revenue'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('revenue', 'date')
            ->toArray();
    }
}
