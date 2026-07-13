@extends('layouts.admin')

@section('title', 'Modération Messagerie')

@section('content')
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Messages récents</h3>
        </div>

        <x-admin-filters :reset-url="route('admin.messages')">
            <div>
                <label class="text-xs text-gray-500 font-medium block mb-1">Contenu</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Mot-clé dans le message…" class="rounded-lg border-gray-200 text-sm w-48">
            </div>
            <div>
                <label class="text-xs text-gray-500 font-medium block mb-1">Colis #</label>
                <input type="number" name="parcel_id" value="{{ request('parcel_id') }}" class="rounded-lg border-gray-200 text-sm w-24">
            </div>
            <div>
                <label class="text-xs text-gray-500 font-medium block mb-1">Type</label>
                <select name="kind" class="rounded-lg border-gray-200 text-sm">
                    <option value="">Tous</option>
                    <option value="user" {{ request('kind') === 'user' ? 'selected' : '' }}>Utilisateur</option>
                    <option value="system" {{ request('kind') === 'system' ? 'selected' : '' }}>Système</option>
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
                    <th class="px-6 py-3 font-medium text-gray-500 text-sm">Colis</th>
                    <th class="px-6 py-3 font-medium text-gray-500 text-sm">Expéditeur</th>
                    <th class="px-6 py-3 font-medium text-gray-500 text-sm">Message</th>
                    <th class="px-6 py-3 font-medium text-gray-500 text-sm">Type</th>
                    <th class="px-6 py-3 font-medium text-gray-500 text-sm">Date</th>
                    <th class="px-6 py-3 font-medium text-gray-500 text-sm text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($messages as $msg)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-3">
                            <a href="{{ route('admin.parcels.show', $msg->parcel_id) }}" class="text-berrni-600 hover:underline">#{{ $msg->parcel_id }}</a>
                        </td>
                        <td class="px-6 py-3 text-gray-700 font-medium">{{ $msg->sender->name ?? 'Système' }}</td>
                        <td class="px-6 py-3 text-gray-600 text-sm">{{ Str::limit($msg->content, 60) }}</td>
                        <td class="px-6 py-3">
                            @if($msg->is_system_message)
                                <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded text-xs font-bold">Système</span>
                            @else
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs font-bold">Utilisateur</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-gray-500 text-sm">{{ $msg->created_at->format('d/m H:i') }}</td>
                        <td class="px-6 py-3 text-right">
                            <form action="{{ route('admin.messages.delete', $msg->id) }}" method="POST" onsubmit="return confirm('Supprimer ce message ?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-8 text-center text-gray-400">Aucun message.</td></tr>
                @endforelse
            </tbody>
        </table>
        <x-admin-list-footer :paginator="$messages" />
    </div>
@endsection
