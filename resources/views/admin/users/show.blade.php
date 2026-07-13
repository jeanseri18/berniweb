@extends('layouts.admin')

@section('title', 'Détail Utilisateur')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ url()->previous() }}" class="text-gray-500 hover:text-gray-700 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Retour
        </a>
        
        @if($user->is_courier && $user->courier_status === 'approved')
            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-bold">
                <i class="fas fa-check-circle mr-1"></i> Relais Certifié
            </span>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar Info -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border-t-4 border-berrni-500">
                <div class="text-center mb-6">
                    <div class="w-24 h-24 bg-berrni-100 rounded-full flex items-center justify-center text-berrni-600 text-3xl font-bold mx-auto mb-4">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $user->name }}</h2>
                    <p class="text-gray-500 text-sm">Membre depuis {{ $user->created_at->format('d/m/Y') }}</p>
                </div>

                <div class="space-y-4 border-t border-gray-100 pt-4">
                    <div>
                        <label class="text-xs text-gray-400 uppercase font-semibold">Email</label>
                        <p class="text-gray-700 font-medium">{{ $user->email }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-400 uppercase font-semibold">Téléphone</label>
                        <div class="flex items-center justify-between">
                            <p class="text-gray-700 font-medium">{{ $user->phone ?? 'Non renseigné' }}</p>
                            @if($user->is_verified)
                                <span class="text-green-600 font-medium text-sm"><i class="fas fa-check-circle mr-1"></i> Vérifié</span>
                            @else
                                <span class="text-orange-500 font-medium text-sm"><i class="fas fa-clock mr-1"></i> Non vérifié</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="text-xs text-gray-400 uppercase font-semibold">Moyen de paiement</label>
                        @php
                            $pk = $user->payment_kind ?? 'wallet';
                            $pkLabel = $pk === 'momo' ? 'Mobile Money' : 'Portefeuille BERRNI';
                        @endphp
                        <p class="text-gray-700 font-medium">{{ $pkLabel }}</p>
                        @if($pk === 'momo' && $user->momo_number)
                            <p class="text-sm text-gray-500">{{ $user->momo_number }}</p>
                        @endif
                    </div>
                    <div>
                        <label class="text-xs text-gray-400 uppercase font-semibold">Portefeuille</label>
                        <p class="text-lg font-bold text-berrni-600">{{ format_fcfa($user->wallet->balance_available ?? 0) }}</p>
                        <p class="text-xs text-gray-500">Séquestré : {{ format_fcfa($user->wallet->balance_sequestered ?? 0) }}</p>
                    </div>
                </div>
            </div>

            {{-- Ajustement portefeuille --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Ajustement portefeuille</h3>
                <form method="POST" action="{{ route('admin.users.wallet_adjust', $user->id) }}" class="space-y-3" onsubmit="return confirm('Confirmer cet ajustement ?')">
                    @csrf
                    <div>
                        <label class="text-xs text-gray-500 font-medium">Type</label>
                        <select name="direction" class="w-full mt-1 rounded-lg border-gray-200 text-sm">
                            <option value="credit">Créditer (+)</option>
                            <option value="debit">Débiter (−)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 font-medium">Montant (FCFA)</label>
                        <input type="number" name="amount" min="1" max="5000000" required class="w-full mt-1 rounded-lg border-gray-200 text-sm" placeholder="5000">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 font-medium">Note (optionnel)</label>
                        <input type="text" name="note" maxlength="255" class="w-full mt-1 rounded-lg border-gray-200 text-sm" placeholder="Motif de l’ajustement">
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-berrni-600 text-white rounded-lg hover:bg-berrni-700 text-sm font-medium">
                        Appliquer l’ajustement
                    </button>
                </form>
            </div>

            @if(!$user->is_verified)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Vérification Téléphone</h3>
                <form method="POST" action="{{ route('admin.users.verify_phone', $user->id) }}">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 bg-berrni-600 text-white rounded-lg hover:bg-berrni-700">
                        Marquer comme vérifié
                    </button>
                </form>
                <p class="text-xs text-gray-500 mt-3">
                    À utiliser si l’utilisateur est bloqué sur la vérification SMS.
                </p>
            </div>
            @endif

            @if($user->is_courier || $user->courier_status !== 'none')
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Informations Relais</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Statut</span>
                        <span class="font-medium capitalize {{ $user->courier_status === 'approved' ? 'text-green-600' : 'text-orange-500' }}">
                            {{ $user->courier_status }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Colis Livrés</span>
                        <span class="font-medium">{{ $user->deliveredParcels->count() }}</span>
                    </div>
                    <div class="pt-2">
                        <a href="{{ route('admin.kyc.list') }}" class="block w-full text-center px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">
                            Voir dossier KYC
                        </a>
                    </div>
                </div>
            </div>
            @endif

            {{-- Suspension --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Gestion du compte</h3>
                @if($user->is_suspended ?? false)
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-3">
                        <p class="text-red-700 font-medium text-sm"><i class="fas fa-ban mr-1"></i> Compte suspendu</p>
                    </div>
                    <form method="POST" action="{{ route('admin.users.reactivate', $user->id) }}">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                            <i class="fas fa-check mr-1"></i> Réactiver le compte
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.users.suspend', $user->id) }}" onsubmit="return confirm('Suspendre cet utilisateur ?')">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-red-50 text-red-600 border border-red-200 rounded-lg hover:bg-red-100 text-sm font-medium">
                            <i class="fas fa-ban mr-1"></i> Suspendre le compte
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Parcels History -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-700">Historique des Colis</h3>
                    <div class="flex space-x-2 text-sm">
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded">Envoyés: {{ $user->sentParcels->count() }}</span>
                        @if($user->is_courier)
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded">Livrés: {{ $user->deliveredParcels->count() }}</span>
                        @endif
                    </div>
                </div>
                
                @php
                    $allParcels = $user->sentParcels->merge($user->deliveredParcels)->sortByDesc('created_at')->take(10);
                @endphp

                @if($allParcels->isEmpty())
                    <div class="p-8 text-center text-gray-500">
                        <p>Aucun historique de colis.</p>
                    </div>
                @else
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Rôle</th>
                                <th class="px-4 py-3">Trajet</th>
                                <th class="px-4 py-3">Statut</th>
                                <th class="px-4 py-3 text-right">Montant</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach($allParcels as $parcel)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-500">{{ $parcel->created_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">
                                    @if($parcel->sender_id === $user->id)
                                        <span class="text-blue-600 font-medium">Expéditeur</span>
                                    @else
                                        <span class="text-green-600 font-medium">Livreur</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-gray-800">{{ $parcel->departure_address }}</span>
                                        <span class="text-gray-400 text-xs">-> {{ $parcel->destination_address }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded text-xs font-bold bg-gray-100 text-gray-600">
                                        {{ $parcel->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-medium">{{ format_fcfa($parcel->price) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection
