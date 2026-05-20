@extends('layouts.admin')

@section('title', 'Paiements CinetPay')

@section('content')
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800"><i class="fas fa-credit-card mr-2 text-berrni-500"></i>Paiements CinetPay</h3>
        </div>

        <x-admin-filters :reset-url="route('admin.payments')">
            <div>
                <label class="text-xs text-gray-500 font-medium block mb-1">Recherche</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Réf., utilisateur…" class="rounded-lg border-gray-200 text-sm w-48">
            </div>
            <div>
                <label class="text-xs text-gray-500 font-medium block mb-1">Statut</label>
                <select name="status" class="rounded-lg border-gray-200 text-sm">
                    <option value="">Tous</option>
                    @foreach(['initiated', 'pending', 'paid', 'failed', 'cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ $s }}</option>
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
                    <th class="px-6 py-3 font-medium text-gray-500 text-sm">Colis</th>
                    <th class="px-6 py-3 font-medium text-gray-500 text-sm">Montant</th>
                    <th class="px-6 py-3 font-medium text-gray-500 text-sm">Statut</th>
                    <th class="px-6 py-3 font-medium text-gray-500 text-sm">Réf. provider</th>
                    <th class="px-6 py-3 font-medium text-gray-500 text-sm text-right">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-3">
                            <a href="{{ route('admin.payments.show', $payment->id) }}" class="text-berrni-600 font-medium hover:underline">#{{ $payment->id }}</a>
                        </td>
                        <td class="px-6 py-3">
                            <a href="{{ route('admin.users.show', $payment->user_id) }}" class="text-gray-700 hover:underline">{{ $payment->user->name ?? '—' }}</a>
                            <p class="text-xs text-gray-400">{{ $payment->user->phone ?? '' }}</p>
                        </td>
                        <td class="px-6 py-3">
                            <a href="{{ route('admin.parcels.show', $payment->parcel_id) }}" class="text-berrni-600 hover:underline font-medium">#{{ $payment->parcel_id }}</a>
                            @if($payment->parcel)
                                <p class="text-xs text-gray-400 truncate max-w-xs">{{ $payment->parcel->departure_address }} → {{ $payment->parcel->destination_address }}</p>
                                <span class="text-xs text-gray-500">({{ $payment->parcel->status }})</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 font-bold text-gray-800">{{ format_fcfa($payment->amount) }}</td>
                        <td class="px-6 py-3">
                            @php
                                $statusColors = [
                                    'paid' => 'bg-green-100 text-green-700',
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'initiated' => 'bg-blue-100 text-blue-700',
                                    'failed' => 'bg-red-100 text-red-700',
                                    'cancelled' => 'bg-gray-100 text-gray-600',
                                ];
                            @endphp
                            <span class="px-2 py-0.5 rounded text-xs font-bold uppercase {{ $statusColors[$payment->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $payment->status }}</span>
                        </td>
                        <td class="px-6 py-3 text-xs font-mono text-gray-500">{{ Str::limit($payment->provider_payment_id ?? '—', 24) }}</td>
                        <td class="px-6 py-3 text-right text-gray-500 text-sm">{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-400">Aucun paiement CinetPay.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <x-admin-list-footer :paginator="$payments" />
    </div>
@endsection
