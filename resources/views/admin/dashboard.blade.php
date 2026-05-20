@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('content')
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Users -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-berrni-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">Utilisateurs Total</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['users_count'] }}</h3>
                    <p class="text-xs {{ $stats['users_growth'] >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1 font-semibold">
                        <i class="fas fa-arrow-{{ $stats['users_growth'] >= 0 ? 'up' : 'down' }}"></i> {{ abs($stats['users_growth']) }}% ce mois
                    </p>
                </div>
                <div class="p-3 bg-berrni-50 rounded-lg text-berrni-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Revenue -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">Revenu estimé</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ format_fcfa($stats['revenue']) }}</h3>
                    <p class="text-xs text-gray-400 mt-1">Commission BERRNI (15 %)</p>
                </div>
                <div class="p-3 bg-blue-50 rounded-lg text-blue-600">
                    <i class="fas fa-coins text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Parcels -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">Colis Actifs</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['active_parcels'] }}</h3>
                    <p class="text-xs text-gray-400 mt-1">{{ $stats['completed_parcels'] }} livrés au total</p>
                </div>
                <div class="p-3 bg-purple-50 rounded-lg text-purple-600">
                    <i class="fas fa-box-open text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-red-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">SOS Alertes</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['sos_alerts'] }}</h3>
                    <p class="text-xs text-red-600 mt-1 font-bold">Action requise</p>
                </div>
                <div class="p-3 bg-red-50 rounded-lg text-red-600">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary KPIs -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-4 text-center">
            <p class="text-xs text-gray-400 uppercase font-semibold">Taux livraison</p>
            <p class="text-xl font-bold text-berrni-600 mt-1">{{ $stats['conversion_rate'] }}%</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 text-center">
            <p class="text-xs text-gray-400 uppercase font-semibold">Taux annulation</p>
            <p class="text-xl font-bold text-red-500 mt-1">{{ $stats['cancellation_rate'] }}%</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 text-center">
            <p class="text-xs text-gray-400 uppercase font-semibold">Offres en attente</p>
            <p class="text-xl font-bold text-orange-600 mt-1">{{ $stats['offers_pending'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 text-center">
            <p class="text-xs text-gray-400 uppercase font-semibold">Transactions aujourd'hui</p>
            <p class="text-xl font-bold text-gray-800 mt-1">{{ $stats['transactions_today'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 text-center">
            <p class="text-xs text-gray-400 uppercase font-semibold">Soldes portefeuilles (disponible)</p>
            <p class="text-xl font-bold text-berrni-600 mt-1">{{ format_fcfa($stats['total_wallets_balance']) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 text-center">
            <p class="text-xs text-gray-400 uppercase font-semibold">Fonds séquestrés</p>
            <p class="text-xl font-bold text-orange-600 mt-1">{{ format_fcfa($stats['total_sequestered']) }}</p>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Chart 1: Growth -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-4 text-lg">Inscriptions & Croissance</h3>
            <div class="h-64">
                <canvas id="usersChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Parcels Status -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-4 text-lg">État des Colis</h3>
            <div class="h-64 flex justify-center">
                <canvas id="parcelsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activity Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Parcels -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">Derniers Colis</h3>
                <a href="{{ route('admin.parcels') }}" class="text-sm text-berrni-600 font-medium hover:underline">Voir tout</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-5 py-3">Expéditeur</th>
                            <th class="px-5 py-3">Trajet</th>
                            <th class="px-5 py-3">Statut</th>
                            <th class="px-5 py-3 text-right">Prix</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @foreach($recent_parcels as $parcel)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-3 font-medium">{{ $parcel->sender->name }}</td>
                            <td class="px-5 py-3">
                                <div class="flex flex-col">
                                    <span class="text-gray-800">{{ $parcel->departure_address }}</span>
                                    <span class="text-gray-400 text-xs">vers {{ $parcel->destination_address }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-bold 
                                    {{ $parcel->status === 'published' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $parcel->status === 'delivered' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ in_array($parcel->status, ['assigned', 'in_transit']) ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $parcel->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right font-bold">{{ format_fcfa($parcel->price) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">Nouveaux Inscrits</h3>
                <a href="{{ route('admin.users.index') }}" class="text-sm text-berrni-600 font-medium hover:underline">Voir tout</a>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($recent_users as $user)
                <div class="p-4 flex items-center space-x-3 hover:bg-gray-50 transition">
                    <div class="w-10 h-10 rounded-full bg-berrni-100 flex items-center justify-center text-berrni-600 font-bold shrink-0">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $user->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                    </div>
                    <span class="text-xs text-gray-400">{{ $user->created_at->diffForHumans(null, true) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Chart.js Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Users Chart
        const usersCtx = document.getElementById('usersChart').getContext('2d');
        new Chart(usersCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_keys($charts['users_registration'])) !!},
                datasets: [{
                    label: 'Nouveaux Utilisateurs',
                    data: {!! json_encode(array_values($charts['users_registration'])) !!},
                    borderColor: '#25ce28',
                    backgroundColor: 'rgba(37, 206, 40, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [2, 4] } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Parcels Status Chart
        const parcelsCtx = document.getElementById('parcelsChart').getContext('2d');
        new Chart(parcelsCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode(array_keys($charts['parcels_by_status'])) !!},
                datasets: [{
                    data: {!! json_encode(array_values($charts['parcels_by_status'])) !!},
                    backgroundColor: [
                        '#3b82f6', // blue (published)
                        '#25ce28', // green (delivered)
                        '#f97316', // orange (in transit)
                        '#ef4444', // red (cancelled)
                        '#a855f7', // purple (assigned)
                        '#64748b'  // gray (other)
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { position: 'right' }
                }
            }
        });
    </script>
@endsection
