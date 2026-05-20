@extends('layouts.admin')

@section('title', 'Transactions')

@section('content')
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Historique des transactions</h3>
        </div>

        <x-admin-filters :reset-url="route('admin.transactions')">
            <div>
                <label class="text-xs text-gray-500 font-medium block mb-1">Recherche</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Description, utilisateur…" class="rounded-lg border-gray-200 text-sm w-48">
            </div>
            <div>
                <label class="text-xs text-gray-500 font-medium block mb-1">Type</label>
                <select name="type" class="rounded-lg border-gray-200 text-sm">
                    <option value="">Tous</option>
                    @foreach(['credit', 'debit', 'deposit', 'withdrawal', 'escrow', 'release', 'admin_credit', 'admin_debit'] as $t)
                        <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs text-gray-500 font-medium block mb-1">Colis #</label>
                <input type="number" name="parcel_id" value="{{ request('parcel_id') }}" class="rounded-lg border-gray-200 text-sm w-24">
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
                    <th class="px-6 py-3 font-medium text-gray-500 text-sm">ID</th>
                    <th class="px-6 py-3 font-medium text-gray-500 text-sm">Utilisateur</th>
                    <th class="px-6 py-3 font-medium text-gray-500 text-sm">Type</th>
                    <th class="px-6 py-3 font-medium text-gray-500 text-sm">Montant</th>
                    <th class="px-6 py-3 font-medium text-gray-500 text-sm">Colis</th>
                    <th class="px-6 py-3 font-medium text-gray-500 text-sm">Description</th>
                    <th class="px-6 py-3 font-medium text-gray-500 text-sm text-right">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($transactions as $tx)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-3 text-gray-500 text-sm">#{{ $tx->id }}</td>
                        <td class="px-6 py-3">
                            @if($tx->wallet && $tx->wallet->user)
                                <a href="{{ route('admin.users.show', $tx->wallet->user_id) }}" class="text-berrni-600 hover:underline font-medium">{{ $tx->wallet->user->name }}</a>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-0.5 rounded text-xs font-bold uppercase bg-gray-100 text-gray-700">{{ $tx->type }}</span>
                        </td>
                        <td class="px-6 py-3 font-bold {{ in_array($tx->type, ['credit', 'release', 'deposit', 'admin_credit'], true) ? 'text-green-700' : 'text-red-600' }}">
                            {{ in_array($tx->type, ['credit', 'release', 'deposit', 'admin_credit'], true) ? '+' : '−' }}{{ format_fcfa(abs($tx->amount), false) }}
                        </td>
                        <td class="px-6 py-3">
                            @if($tx->parcel_id)
                                <a href="{{ route('admin.parcels.show', $tx->parcel_id) }}" class="text-berrni-600 hover:underline">#{{ $tx->parcel_id }}</a>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-gray-600 text-sm">{{ Str::limit($tx->description, 40) }}</td>
                        <td class="px-6 py-3 text-right text-gray-500 text-sm">{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-8 text-center text-gray-400">Aucune transaction.</td></tr>
                @endforelse
            </tbody>
        </table>
        <x-admin-list-footer :paginator="$transactions" />
    </div>
@endsection
