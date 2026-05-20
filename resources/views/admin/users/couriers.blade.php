@extends('layouts.admin')

@section('title', 'Gestion des Livreurs (Relais)')

@section('content')
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-green-50">
            <h3 class="font-bold text-green-800"><i class="fas fa-truck mr-2"></i> Relais de Confiance</h3>
        </div>

        <x-admin-filters :reset-url="route('admin.users.couriers')">
            <div>
                <label class="text-xs text-gray-500 font-medium block mb-1">Recherche</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Nom, email, téléphone…" class="rounded-lg border-gray-200 text-sm w-48 md:w-56">
            </div>
            <div>
                <label class="text-xs text-gray-500 font-medium block mb-1">Statut relais</label>
                <select name="courier_status" class="rounded-lg border-gray-200 text-sm">
                    <option value="">Tous</option>
                    <option value="approved" {{ request('courier_status') === 'approved' ? 'selected' : '' }}>Validé</option>
                    <option value="pending" {{ request('courier_status') === 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="rejected" {{ request('courier_status') === 'rejected' ? 'selected' : '' }}>Refusé</option>
                    <option value="none" {{ request('courier_status') === 'none' ? 'selected' : '' }}>Aucun</option>
                </select>
            </div>
            <div>
                <label class="text-xs text-gray-500 font-medium block mb-1">Par page</label>
                <select name="per_page" class="rounded-lg border-gray-200 text-sm">
                    @foreach([10, 20, 30, 50] as $n)
                        <option value="{{ $n }}" {{ (int) request('per_page', 20) === $n ? 'selected' : '' }}>{{ $n }}</option>
                    @endforeach
                </select>
            </div>
        </x-admin-filters>

        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 font-medium text-gray-500">Nom</th>
                    <th class="px-6 py-4 font-medium text-gray-500">Contact</th>
                    <th class="px-6 py-4 font-medium text-gray-500">Statut</th>
                    <th class="px-6 py-4 font-medium text-gray-500">Colis Livrés</th>
                    <th class="px-6 py-4 font-medium text-gray-500">Gain (Wallet)</th>
                    <th class="px-6 py-4 font-medium text-gray-500 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition cursor-pointer" onclick="window.location='{{ route('admin.users.show', $user->id) }}'">
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $user->phone }}<br>{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            @if($user->courier_status === 'approved')
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold uppercase">Validé</span>
                            @elseif($user->courier_status === 'pending')
                                <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded text-xs font-bold uppercase">En attente</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-bold uppercase">{{ $user->courier_status }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-700 font-medium">{{ $user->delivered_parcels_count ?? 0 }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $user->wallet ? format_fcfa($user->wallet->balance_available) : format_fcfa(0) }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.users.show', $user->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-semibold" onclick="event.stopPropagation()">
                                <i class="fas fa-eye mr-1"></i> Détails
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-8 text-center text-gray-400">Aucun relais trouvé.</td></tr>
                @endforelse
            </tbody>
        </table>
        <x-admin-list-footer :paginator="$users" />
    </div>
@endsection
