@extends('layouts.admin')

@section('title', 'Notifications')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-4"><i class="fas fa-paper-plane mr-2 text-berrni-500"></i>Envoyer une notification</h3>
            <form action="{{ route('admin.notifications.send') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-sm font-medium text-gray-600">Cible</label>
                    <select name="target" class="w-full mt-1 rounded-lg border-gray-200">
                        <option value="all">Tous les utilisateurs (non suspendus)</option>
                        <option value="couriers">Relais uniquement</option>
                        <option value="senders">Expéditeurs uniquement</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600">Titre</label>
                    <input type="text" name="title" required maxlength="100" class="w-full mt-1 rounded-lg border-gray-200" placeholder="Ex: Maintenance programmée">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600">Message</label>
                    <textarea name="body" required maxlength="500" rows="4" class="w-full mt-1 rounded-lg border-gray-200" placeholder="Contenu de la notification..."></textarea>
                </div>
                <button type="submit" class="w-full px-4 py-3 bg-berrni-500 text-white rounded-lg font-semibold hover:bg-berrni-600 transition">
                    <i class="fas fa-bell mr-2"></i>Envoyer
                </button>
            </form>
            <p class="text-xs text-gray-400 mt-3">Format compatible avec l’app mobile (notifications Laravel en base).</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-4"><i class="fas fa-history mr-2 text-gray-400"></i>Derniers envois</h3>
            @if($recentNotifs->isEmpty())
                <p class="text-gray-400 text-sm">Aucune notification envoyée.</p>
            @else
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($recentNotifs as $n)
                        @php $data = is_array($n->data) ? $n->data : (array) $n->data; @endphp
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <div class="flex justify-between text-xs text-gray-400 mb-1">
                                <span class="font-bold text-gray-600">{{ $data['title'] ?? 'Sans titre' }}</span>
                                <span>{{ $n->created_at->format('d/m H:i') }}</span>
                            </div>
                            <p class="text-sm text-gray-700">{{ Str::limit($data['message'] ?? $data['body'] ?? '', 120) }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
