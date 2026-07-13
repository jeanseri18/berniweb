@extends('layouts.admin')

@section('title', 'Gestion des Utilisateurs')

@section('content')
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <x-admin-filters :reset-url="route('admin.users.index')">
            <div>
                <label class="text-xs text-gray-500 font-medium block mb-1">Recherche</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Nom, email, téléphone…" class="rounded-lg border-gray-200 text-sm w-48 md:w-56">
            </div>
            <div>
                <label class="text-xs text-gray-500 font-medium block mb-1">Rôle</label>
                <select name="role" class="rounded-lg border-gray-200 text-sm">
                    <option value="">Tous</option>
                    <option value="courier" {{ request('role') === 'courier' ? 'selected' : '' }}>Relais</option>
                    <option value="sender" {{ request('role') === 'sender' ? 'selected' : '' }}>Expéditeur</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
            <div>
                <label class="text-xs text-gray-500 font-medium block mb-1">Compte</label>
                <select name="suspended" class="rounded-lg border-gray-200 text-sm">
                    <option value="">Tous</option>
                    <option value="0" {{ request('suspended') === '0' ? 'selected' : '' }}>Actifs</option>
                    <option value="1" {{ request('suspended') === '1' ? 'selected' : '' }}>Suspendus</option>
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
                    <th class="px-6 py-4 font-medium text-gray-500">ID</th>
                    <th class="px-6 py-4 font-medium text-gray-500">Nom</th>
                    <th class="px-6 py-4 font-medium text-gray-500">Email / Téléphone</th>
                    <th class="px-6 py-4 font-medium text-gray-500">Vérifié</th>
                    <th class="px-6 py-4 font-medium text-gray-500">Rôle</th>
                    <th class="px-6 py-4 font-medium text-gray-500">Statut Relais</th>
                    <th class="px-6 py-4 font-medium text-gray-500 text-right">Inscrit le</th>
                    <th class="px-6 py-4 font-medium text-gray-500 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($users as $user)
                    <tr class="hover:bg-gray-50 transition cursor-pointer" onclick="window.location='{{ route('admin.users.show', $user->id) }}'">
                        <td class="px-6 py-4 text-gray-500">#{{ $user->id }}</td>
                        <td class="px-6 py-4 font-medium text-gray-800">
                            {{ $user->name }}
                            @if($user->role === 'admin')
                                <span class="ml-2 px-2 py-0.5 bg-purple-100 text-purple-700 text-xs rounded-full">Admin</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col text-sm">
                                <span class="text-gray-800">{{ $user->email }}</span>
                                <span class="text-gray-500">{{ $user->phone }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($user->is_verified)
                                <span class="text-green-600 font-medium text-sm"><i class="fas fa-check-circle mr-1"></i> Oui</span>
                            @else
                                <span class="text-orange-500 font-medium text-sm"><i class="fas fa-clock mr-1"></i> Non</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($user->is_courier)
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold uppercase">Relais</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs font-bold uppercase">Utilisateur</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                             @if($user->courier_status === 'approved')
                                <span class="text-green-600 font-medium text-sm"><i class="fas fa-check-circle mr-1"></i> Validé</span>
                             @elseif($user->courier_status === 'pending')
                                <span class="text-orange-500 font-medium text-sm"><i class="fas fa-clock mr-1"></i> En attente</span>
                             @elseif($user->courier_status === 'rejected')
                                <span class="text-red-500 font-medium text-sm"><i class="fas fa-times-circle mr-1"></i> Refusé</span>
                             @else
                                <span class="text-gray-400 text-sm">-</span>
                             @endif
                        </td>
                        <td class="px-6 py-4 text-right text-gray-500 text-sm">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-right">
                             <a href="{{ route('admin.users.show', $user->id) }}" class="text-berrni-600 hover:text-berrni-800">
                                <i class="fas fa-chevron-right"></i>
                             </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <x-admin-list-footer :paginator="$users" />
    </div>
@endsection
