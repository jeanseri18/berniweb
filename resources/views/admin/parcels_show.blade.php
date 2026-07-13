@extends('layouts.admin')

@section('title', 'Colis #' . $parcel->id)

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.parcels') }}" class="text-berrni-600 hover:underline text-sm"><i class="fas fa-arrow-left mr-1"></i> Retour aux colis</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main info --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-2xl font-serif font-bold text-gray-800">Colis #{{ $parcel->id }}</h3>
                        <p class="text-gray-500 mt-1">{{ $parcel->description ?? 'Aucune description' }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-lg text-xs font-bold uppercase
                        {{ $parcel->status === 'published' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $parcel->status === 'delivered' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $parcel->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}
                        {{ in_array($parcel->status, ['assigned', 'picked_up', 'in_transit']) ? 'bg-orange-100 text-orange-700' : '' }}
                    ">{{ $parcel->status }}</span>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-400 font-medium text-xs uppercase">Départ</p>
                        <p class="font-semibold text-gray-800">{{ $parcel->departure_address }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 font-medium text-xs uppercase">Destination</p>
                        <p class="font-semibold text-gray-800">{{ $parcel->destination_address }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 font-medium text-xs uppercase">Prix</p>
                        <p class="font-bold text-lg text-berrni-600">{{ format_fcfa($parcel->price) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 font-medium text-xs uppercase">Date de départ</p>
                        <p class="font-semibold text-gray-800">{{ $parcel->departure_date ? $parcel->departure_date->format('d/m/Y H:i') : '—' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 font-medium text-xs uppercase">Destinataire</p>
                        <p class="font-semibold text-gray-800">{{ $parcel->recipient_name ?? '—' }} {{ $parcel->recipient_phone ? '('.$parcel->recipient_phone.')' : '' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 font-medium text-xs uppercase">Code vérification</p>
                        <p class="font-mono font-bold text-gray-800 text-lg">{{ $parcel->verification_code ?? '—' }}</p>
                    </div>
                </div>
            </div>

            {{-- Offers --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-bold text-gray-800 mb-3"><i class="fas fa-handshake mr-2 text-berrni-500"></i>Offres ({{ $parcel->offers->count() }})</h4>
                @if($parcel->offers->isEmpty())
                    <p class="text-gray-400 text-sm">Aucune offre reçue.</p>
                @else
                    <div class="space-y-2">
                        @foreach($parcel->offers as $offer)
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <span class="font-medium">{{ $offer->courier->name ?? 'Relais inconnu' }}</span>
                                    <span class="text-gray-500 text-sm ml-2">{{ format_fcfa($offer->amount) }}</span>
                                </div>
                                <span class="px-2 py-0.5 rounded text-xs font-bold
                                    {{ $offer->status === 'accepted' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $offer->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $offer->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                                ">{{ $offer->status }}</span>
                                @if($offer->status === 'pending')
                                    <span class="text-xs text-gray-400 block mt-1">tour {{ $offer->last_counter_by ?? 'courier' }} · {{ $offer->turns_used ?? 0 }} tours</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Messages --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-bold text-gray-800 mb-3"><i class="fas fa-comments mr-2 text-berrni-500"></i>Messages ({{ $parcel->messages->count() }})</h4>
                @if($parcel->messages->isEmpty())
                    <p class="text-gray-400 text-sm">Aucun message.</p>
                @else
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @foreach($parcel->messages->sortByDesc('created_at')->take(20) as $msg)
                            <div class="p-3 rounded-lg {{ $msg->is_system_message ? 'bg-yellow-50 border border-yellow-100' : 'bg-gray-50' }}">
                                <div class="flex justify-between text-xs text-gray-400 mb-1">
                                    <span class="font-medium {{ $msg->is_system_message ? 'text-yellow-700' : 'text-gray-600' }}">
                                        {{ $msg->is_system_message ? 'Système' : ($msg->sender->name ?? '?') }}
                                    </span>
                                    <span>{{ $msg->created_at->format('d/m H:i') }}</span>
                                </div>
                                <p class="text-sm text-gray-700">{{ $msg->content }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Actors --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-bold text-gray-800 mb-3">Acteurs</h4>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-medium">Expéditeur</p>
                        <a href="{{ route('admin.users.show', $parcel->sender_id) }}" class="text-berrni-600 font-semibold hover:underline">{{ $parcel->sender->name }}</a>
                    </div>
                    @if($parcel->courier)
                        <div>
                            <p class="text-xs text-gray-400 uppercase font-medium">Relais</p>
                            <a href="{{ route('admin.users.show', $parcel->courier_id) }}" class="text-berrni-600 font-semibold hover:underline">{{ $parcel->courier->name }}</a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-bold text-gray-800 mb-3">Actions</h4>
                <form action="{{ route('admin.parcels.update_status', $parcel->id) }}" method="POST" class="mb-3">
                    @csrf
                    <label class="text-xs text-gray-500 font-medium">Changer le statut</label>
                    <div class="flex mt-1 gap-2">
                        <select name="status" class="flex-1 rounded-lg border-gray-200 text-sm">
                            @foreach(['published', 'assigned', 'picked_up', 'in_transit', 'delivered', 'completed', 'cancelled'] as $s)
                                <option value="{{ $s }}" {{ $parcel->status === $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-3 py-2 bg-berrni-500 text-white rounded-lg text-sm font-medium hover:bg-berrni-600">OK</button>
                    </div>
                </form>

                @if($parcel->status !== 'cancelled')
                    <form action="{{ route('admin.parcels.cancel', $parcel->id) }}" method="POST" onsubmit="return confirm('Annuler ce colis ?')">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-red-50 text-red-600 border border-red-200 rounded-lg text-sm font-medium hover:bg-red-100">
                            <i class="fas fa-ban mr-1"></i> Annuler le colis
                        </button>
                    </form>
                @endif
            </div>

            {{-- SOS --}}
            @if($parcel->sosAlerts->isNotEmpty())
                <div class="bg-red-50 rounded-xl shadow-sm p-6 border border-red-200">
                    <h4 class="font-bold text-red-800 mb-2"><i class="fas fa-exclamation-triangle mr-1"></i> Alertes SOS</h4>
                    @foreach($parcel->sosAlerts as $sos)
                        <div class="text-sm text-red-700 mb-1">
                            #{{ $sos->id }} — {{ Str::limit($sos->reason, 50) }} <span class="text-xs text-red-400">({{ $sos->status }})</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
