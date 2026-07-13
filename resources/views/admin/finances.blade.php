@extends('layouts.admin')

@section('title', 'Finances — Portefeuilles')

@section('content')
    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-xs text-gray-400 uppercase font-semibold">Solde total disponible</p>
            <p class="text-2xl font-bold text-berrni-600 mt-1">{{ format_fcfa($totals['available']) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-xs text-gray-400 uppercase font-semibold">Fonds séquestrés</p>
            <p class="text-2xl font-bold text-orange-600 mt-1">{{ format_fcfa($totals['sequestered']) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-xs text-gray-400 uppercase font-semibold">Transactions totales</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totals['transactions_count']) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Portefeuilles utilisateurs</h3>
            <a href="{{ route('admin.transactions') }}" class="text-sm text-berrni-600 hover:underline">Voir les transactions →</a>
        </div>

        <x-admin-filters :reset-url="route('admin.finances')">
            <div>
                <label class="text-xs text-gray-500 font-medium block mb-1">Utilisateur</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Nom, email, téléphone…" class="rounded-lg border-gray-200 text-sm w-48 md:w-56">
            </div>
            <div>
                <label class="text-xs text-gray-500 font-medium block mb-1">Solde min. (FCFA)</label>
                <input type="number" name="min_balance" value="{{ request('min_balance') }}" min="0" placeholder="0" class="rounded-lg border-gray-200 text-sm w-32">
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
                    <th class="px-6 py-3 font-medium text-gray-500 text-sm">Utilisateur</th>
                    <th class="px-6 py-3 font-medium text-gray-500 text-sm">Disponible</th>
                    <th class="px-6 py-3 font-medium text-gray-500 text-sm">Séquestré</th>
                    <th class="px-6 py-3 font-medium text-gray-500 text-sm text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($wallets as $wallet)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-3">
                            <a href="{{ route('admin.users.show', $wallet->user_id) }}" class="text-berrni-600 font-medium hover:underline">
                                {{ $wallet->user->name ?? 'Utilisateur #'.$wallet->user_id }}
                            </a>
                        </td>
                        <td class="px-6 py-3 font-semibold text-green-700">{{ format_fcfa($wallet->balance_available) }}</td>
                        <td class="px-6 py-3 text-orange-600">{{ format_fcfa($wallet->balance_sequestered) }}</td>
                        <td class="px-6 py-3 text-right font-bold text-gray-800">{{ format_fcfa($wallet->balance_available + $wallet->balance_sequestered) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400">Aucun portefeuille trouvé.</td></tr>
                @endforelse
            </tbody>
        </table>
        <x-admin-list-footer :paginator="$wallets" />
    </div>
@endsection
