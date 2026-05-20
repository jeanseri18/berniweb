@extends('layouts.admin')

@section('title', 'Gestion des Expéditeurs')

@section('content')
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-blue-50">
            <h3 class="font-bold text-blue-800"><i class="fas fa-paper-plane mr-2"></i> Expéditeurs Actifs</h3>
        </div>

        <x-admin-filters :reset-url="route('admin.users.senders')">
            <div>
                <label class="text-xs text-gray-500 font-medium block mb-1">Recherche</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Nom, email, téléphone…" class="rounded-lg border-gray-200 text-sm w-48 md:w-56">
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
                    <th class="px-6 py-4 font-medium text-gray-500">Colis Envoyés</th>
                    <th class="px-6 py-4 font-medium text-gray-500">Dépenses Totales</th>
                    <th class="px-6 py-4 font-medium text-gray-500 text-right">Dernier envoi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition cursor-pointer" onclick="window.location='{{ route('admin.users.show', $user->id) }}'">
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $user->phone }}<br>{{ $user->email }}</td>
                        <td class="px-6 py-4 text-gray-700 font-medium">{{ $user->sent_parcels_count ?? 0 }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ format_fcfa($user->total_spent ?? 0) }}</td>
                        <td class="px-6 py-4 text-right text-sm text-gray-500">
                            {{ $user->last_parcel_at ? \Carbon\Carbon::parse($user->last_parcel_at)->format('d/m/Y') : '—' }}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400">Aucun expéditeur trouvé.</td></tr>
                @endforelse
            </tbody>
        </table>
        <x-admin-list-footer :paginator="$users" />
    </div>
@endsection
