@extends('layouts.admin')

@section('title', 'SOS Alertes & Litiges')

@section('content')
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <x-admin-filters :reset-url="route('admin.sos')">
            <div>
                <label class="text-xs text-gray-500 font-medium block mb-1">Recherche</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Motif, utilisateur…" class="rounded-lg border-gray-200 text-sm w-48">
            </div>
            <div>
                <label class="text-xs text-gray-500 font-medium block mb-1">Statut</label>
                <select name="status" class="rounded-lg border-gray-200 text-sm">
                    <option value="active" {{ request('status', 'active') === 'active' ? 'selected' : '' }}>Actives (ouvert + investigation)</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Ouvert</option>
                    <option value="investigating" {{ request('status') === 'investigating' ? 'selected' : '' }}>En investigation</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Résolu</option>
                    <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Toutes</option>
                </select>
            </div>
            <div>
                <label class="text-xs text-gray-500 font-medium block mb-1">Colis #</label>
                <input type="number" name="parcel_id" value="{{ request('parcel_id') }}" placeholder="ID" class="rounded-lg border-gray-200 text-sm w-24">
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

        @if($alerts->isEmpty())
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-shield-alt text-4xl mb-4 text-gray-300"></i>
                <p>Aucune alerte pour ces critères.</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4 p-4">
                @foreach($alerts as $alert)
                    <div class="border border-red-200 rounded-lg p-5 bg-red-50/70 flex flex-col md:flex-row justify-between items-start">
                        <div class="mb-4 md:mb-0 flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="px-2 py-0.5 bg-red-200 text-red-800 text-xs font-bold rounded uppercase">SOS #{{ $alert->id }}</span>
                                <span class="px-2 py-0.5 bg-gray-200 text-gray-700 text-xs rounded">{{ $alert->status }}</span>
                                <span class="text-gray-500 text-sm">{{ $alert->created_at->diffForHumans() }}</span>
                            </div>
                            <h4 class="font-bold text-red-800 text-lg mb-1">{{ Str::limit($alert->reason, 80) }}</h4>
                            <p class="text-gray-700 text-sm mb-2">
                                Signalé par <a href="{{ route('admin.users.show', $alert->user_id) }}" class="font-medium text-berrni-600 hover:underline">{{ $alert->user->name }}</a>
                                pour le colis <a href="{{ route('admin.parcels.show', $alert->parcel_id) }}" class="font-medium text-berrni-600 hover:underline">#{{ $alert->parcel_id }}</a>
                            </p>
                            @if($alert->parcel)
                                <div class="text-sm text-gray-600 bg-white p-3 rounded border border-red-100">
                                    <p class="font-medium text-xs text-gray-400 uppercase mb-1">Détails du colis</p>
                                    {{ $alert->parcel->description }} ({{ $alert->parcel->departure_address }} → {{ $alert->parcel->destination_address }})
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-col space-y-2 ml-0 md:ml-4">
                            @if($alert->status !== 'resolved')
                            <form action="{{ route('admin.sos.resolve', $alert->id) }}" method="POST">
                                @csrf
                                <input type="text" name="notes" placeholder="Notes de résolution..." class="w-full mb-2 rounded border-gray-200 text-sm px-3 py-1.5">
                                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm shadow-sm font-medium">
                                    <i class="fas fa-check mr-1"></i> Résoudre
                                </button>
                            </form>
                            @endif
                            @if($alert->status === 'open')
                            <form action="{{ route('admin.sos.close', $alert->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 text-sm">
                                    <i class="fas fa-search mr-1"></i> Passer en investigation
                                </button>
                            </form>
                            @elseif($alert->status === 'investigating')
                                <span class="text-xs text-gray-500 text-center block">En investigation</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <x-admin-list-footer :paginator="$alerts" />
        @endif
    </div>
@endsection
