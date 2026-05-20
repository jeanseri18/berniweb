@extends('layouts.admin')

@section('title', 'Gestion des Colis')

@section('content')
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <x-admin-filters :reset-url="route('admin.parcels')">
            <div>
                <label class="text-xs text-gray-500 font-medium block mb-1">Recherche</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="ID, trajet, expéditeur…" class="rounded-lg border-gray-200 text-sm w-48 md:w-56">
            </div>
            <div>
                <label class="text-xs text-gray-500 font-medium block mb-1">Statut</label>
                <select name="status" class="rounded-lg border-gray-200 text-sm">
                    <option value="">Tous</option>
                    @foreach(['published', 'assigned', 'picked_up', 'in_transit', 'delivered', 'completed', 'cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
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
                    <th class="px-6 py-4 font-medium text-gray-500">ID</th>
                    <th class="px-6 py-4 font-medium text-gray-500">Expéditeur</th>
                    <th class="px-6 py-4 font-medium text-gray-500">Relais</th>
                    <th class="px-6 py-4 font-medium text-gray-500">Trajet</th>
                    <th class="px-6 py-4 font-medium text-gray-500">Prix</th>
                    <th class="px-6 py-4 font-medium text-gray-500">Statut</th>
                    <th class="px-6 py-4 font-medium text-gray-500 text-right">Date</th>
                    <th class="px-6 py-4 font-medium text-gray-500 text-right"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($parcels as $parcel)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-gray-500">#{{ $parcel->id }}</td>
                        <td class="px-6 py-4 font-medium">{{ $parcel->sender->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $parcel->courier ? $parcel->courier->name : '—' }}</td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col text-sm">
                                <span class="text-gray-800">{{ $parcel->departure_address }}</span>
                                <span class="text-gray-400 text-xs">vers</span>
                                <span class="text-gray-800">{{ $parcel->destination_address }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-800">{{ format_fcfa($parcel->price) }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded text-xs font-bold uppercase
                                {{ $parcel->status === 'published' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $parcel->status === 'delivered' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $parcel->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}
                                {{ in_array($parcel->status, ['assigned', 'picked_up', 'in_transit']) ? 'bg-orange-100 text-orange-700' : '' }}
                            ">{{ $parcel->status }}</span>
                        </td>
                        <td class="px-6 py-4 text-right text-gray-500 text-sm">{{ $parcel->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.parcels.show', $parcel->id) }}" class="text-berrni-600 hover:underline text-sm font-medium">Détails →</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-6 py-8 text-center text-gray-400">Aucun colis trouvé.</td></tr>
                @endforelse
            </tbody>
        </table>
        <x-admin-list-footer :paginator="$parcels" />
    </div>
@endsection
