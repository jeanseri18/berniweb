@extends('layouts.admin')

@section('title', 'Paiement CinetPay #' . $payment->id)

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.payments') }}" class="text-berrni-600 hover:underline text-sm"><i class="fas fa-arrow-left mr-1"></i> Retour aux paiements</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-xl font-serif font-bold text-gray-800 mb-4">Paiement #{{ $payment->id }}</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between border-b border-gray-100 pb-2">
                    <span class="text-gray-500">Statut</span>
                    <span class="font-bold uppercase">{{ $payment->status }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-100 pb-2">
                    <span class="text-gray-500">Montant</span>
                    <span class="font-bold text-berrni-600">{{ format_fcfa($payment->amount) }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-100 pb-2">
                    <span class="text-gray-500">Provider</span>
                    <span>{{ $payment->provider }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-100 pb-2">
                    <span class="text-gray-500">Référence</span>
                    <span class="font-mono text-xs">{{ $payment->provider_payment_id ?? '—' }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-100 pb-2">
                    <span class="text-gray-500">Payé le</span>
                    <span>{{ $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i') : '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Créé le</span>
                    <span>{{ $payment->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
            @if($payment->checkout_url)
                <a href="{{ $payment->checkout_url }}" target="_blank" rel="noopener" class="mt-4 inline-block text-sm text-berrni-600 hover:underline">URL de checkout →</a>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-bold text-gray-800 mb-3">Utilisateur</h4>
                <p><a href="{{ route('admin.users.show', $payment->user_id) }}" class="text-berrni-600 font-semibold hover:underline">{{ $payment->user->name }}</a></p>
                <p class="text-sm text-gray-500">{{ $payment->user->phone }} · {{ $payment->user->email }}</p>
            </div>

            @if($payment->parcel)
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h4 class="font-bold text-gray-800 mb-3">Colis lié #{{ $payment->parcel_id }}</h4>
                    <p class="text-sm text-gray-600">{{ $payment->parcel->departure_address }} → {{ $payment->parcel->destination_address }}</p>
                    <p class="text-sm mt-2">Statut colis : <strong>{{ $payment->parcel->status }}</strong></p>
                    <a href="{{ route('admin.parcels.show', $payment->parcel_id) }}" class="mt-3 inline-block text-sm text-berrni-600 hover:underline">Voir le colis →</a>
                </div>
            @endif

            @if($payment->provider_payload)
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h4 class="font-bold text-gray-800 mb-2">Payload provider</h4>
                    <pre class="text-xs bg-gray-50 p-3 rounded-lg overflow-x-auto max-h-64">{{ json_encode($payment->provider_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            @endif
        </div>
    </div>
@endsection
