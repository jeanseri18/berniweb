@extends('layouts.admin')

@section('title', 'Offres & Négociations')

@section('content')
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Toutes les offres</h3>
        </div>

        <x-admin-filters :reset-url="route('admin.offers')">
            <div>
                <label class="text-xs text-gray-500 font-medium block mb-1">Recherche</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Colis #, relais, expéditeur…" class="rounded-lg border-gray-200 text-sm w-48">
            </div>
            <div>
                <label class="text-xs text-gray-500 font-medium block mb-1">Colis #</label>
                <input type="number" name="parcel_id" value="{{ request('parcel_id') }}" class="rounded-lg border-gray-200 text-sm w-24">
            </div>
            <div>
                <label class="text-xs text-gray-500 font-medium block mb-1">Statut</label>
                <select name="status" class="rounded-lg border-gray-200 text-sm">
                    <option value="">Tous</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Acceptée</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Refusée</option>
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
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[900px]">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 font-medium text-gray-500 text-sm">ID</th>
                        <th class="px-4 py-3 font-medium text-gray-500 text-sm">Colis</th>
                        <th class="px-4 py-3 font-medium text-gray-500 text-sm">Relais</th>
                        <th class="px-4 py-3 font-medium text-gray-500 text-sm">Montant</th>
                        <th class="px-4 py-3 font-medium text-gray-500 text-sm">Négociation</th>
                        <th class="px-4 py-3 font-medium text-gray-500 text-sm">Statut</th>
                        <th class="px-4 py-3 font-medium text-gray-500 text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($offers as $offer)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-500 text-sm">#{{ $offer->id }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.parcels.show', $offer->parcel_id) }}" class="text-berrni-600 hover:underline font-medium">#{{ $offer->parcel_id }}</a>
                                <p class="text-xs text-gray-400">{{ $offer->parcel->sender->name ?? '—' }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.users.show', $offer->courier_id) }}" class="text-berrni-600 hover:underline">{{ $offer->courier->name ?? '—' }}</a>
                            </td>
                            <td class="px-4 py-3 font-bold text-gray-800">{{ format_fcfa($offer->amount) }}</td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                Tours: {{ $offer->turns_used ?? 0 }}<br>
                                Dernier: {{ $offer->last_counter_by ?? 'courier' }}<br>
                                @if($offer->courier_amount) Relais: {{ format_fcfa($offer->courier_amount, false) }}<br>@endif
                                @if($offer->sender_amount) Exp.: {{ format_fcfa($offer->sender_amount, false) }}@endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-xs font-bold uppercase
                                    {{ $offer->status === 'accepted' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $offer->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $offer->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                                ">{{ $offer->status }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($offer->status === 'pending')
                                    <div class="flex flex-col gap-1">
                                        <form method="POST" action="{{ route('admin.offers.accept', $offer->id) }}" onsubmit="return confirm('Accepter cette offre pour le colis #{{ $offer->parcel_id }} ?')">
                                            @csrf
                                            <button type="submit" class="text-xs px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700">Accepter</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.offers.reject', $offer->id) }}">
                                            @csrf
                                            <button type="submit" class="text-xs px-2 py-1 bg-red-50 text-red-600 border border-red-200 rounded">Refuser</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.offers.reset', $offer->id) }}" onsubmit="return confirm('Réinitialiser la négociation ?')">
                                            @csrf
                                            <button type="submit" class="text-xs px-2 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded">Réinit. négociation</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Aucune offre.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-admin-list-footer :paginator="$offers" />
    </div>
@endsection
