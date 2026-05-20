@extends('layouts.admin')

@section('title', 'Validations KYC')

@section('content')
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <x-admin-filters :reset-url="route('admin.kyc.list')">
            <div>
                <label class="text-xs text-gray-500 font-medium block mb-1">Recherche</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Nom, email, téléphone…" class="rounded-lg border-gray-200 text-sm w-48 md:w-56">
            </div>
            <div>
                <label class="text-xs text-gray-500 font-medium block mb-1">Statut</label>
                <select name="status" class="rounded-lg border-gray-200 text-sm">
                    <option value="pending" {{ request('status', 'pending') === 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approuvé</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Refusé</option>
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

        @if($submissions->isEmpty())
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-check-circle text-4xl mb-4 text-gray-300"></i>
                <p>Aucun dossier KYC pour ces critères.</p>
            </div>
        @else
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 font-medium text-gray-500">Utilisateur</th>
                        <th class="px-6 py-4 font-medium text-gray-500">Email</th>
                        <th class="px-6 py-4 font-medium text-gray-500">Transport</th>
                        <th class="px-6 py-4 font-medium text-gray-500">Statut</th>
                        <th class="px-6 py-4 font-medium text-gray-500">Date</th>
                        <th class="px-6 py-4 font-medium text-gray-500 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($submissions as $sub)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-gray-800 font-medium">{{ $sub->user->name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $sub->user->email }}</td>
                            <td class="px-6 py-4 text-gray-600">
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs uppercase font-bold">{{ $sub->transport_type }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded text-xs font-bold uppercase
                                    {{ $sub->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $sub->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $sub->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                                ">{{ $sub->status }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-sm">{{ $sub->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.kyc.show', $sub->id) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition text-sm">
                                    Examiner
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <x-admin-list-footer :paginator="$submissions" />
        @endif
    </div>
@endsection
