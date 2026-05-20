@extends('layouts.admin')

@section('title', 'Détail Validation KYC')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.kyc.list') }}" class="text-gray-500 hover:text-gray-700 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- User Info -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border-t-4 border-berrni-500">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-user-circle mr-2 text-berrni-500"></i> Candidat Relais
                </h3>
                
                <div class="space-y-4">
                    <div class="pb-3 border-b border-gray-100">
                        <label class="text-xs text-gray-400 uppercase font-semibold">Identité</label>
                        <p class="font-medium text-gray-800 text-lg">{{ $submission->user->name }}</p>
                        <p class="text-sm text-gray-500">ID: #{{ $submission->user->id }}</p>
                    </div>
                    
                    <div class="pb-3 border-b border-gray-100">
                        <label class="text-xs text-gray-400 uppercase font-semibold">Contact</label>
                        <div class="mt-1 flex items-center text-gray-700">
                            <i class="fas fa-envelope w-5 text-gray-400"></i>
                            <span>{{ $submission->user->email }}</span>
                        </div>
                        <div class="mt-1 flex items-center text-gray-700">
                            <i class="fas fa-phone w-5 text-gray-400"></i>
                            <span>{{ $submission->user->phone ?? 'Non renseigné' }}</span>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs text-gray-400 uppercase font-semibold">Transport</label>
                        <div class="mt-2 inline-flex items-center px-3 py-1 bg-blue-50 text-blue-700 rounded-lg font-medium">
                            <i class="fas fa-shuttle-van mr-2"></i> {{ $submission->transport_type }}
                        </div>
                    </div>

                    <div class="pt-2">
                        <label class="text-xs text-gray-400 uppercase font-semibold">Date de soumission</label>
                        <p class="text-sm text-gray-600">{{ $submission->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>
            </div>

            @if($submission->status === 'pending')
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Décision</h3>
                <form action="{{ route('admin.kyc.approve', $submission->id) }}" method="POST" class="mb-4">
                    @csrf
                    <button type="submit" class="w-full py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold transition shadow-md flex justify-center items-center">
                        <i class="fas fa-check-circle mr-2"></i> Valider le dossier
                    </button>
                    <p class="text-xs text-gray-500 mt-2 text-center">Le candidat recevra une notification immédiate.</p>
                </form>
                
                <hr class="border-gray-100 my-4">

                <form action="{{ route('admin.kyc.reject', $submission->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Motif du refus (Obligatoire)</label>
                        <textarea name="reason" rows="3" required class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 text-sm p-2 border" placeholder="Ex: Photo de la CNI floue, selfie non conforme..."></textarea>
                    </div>
                    <button type="submit" class="w-full py-2 bg-white border-2 border-red-100 text-red-600 hover:bg-red-50 hover:border-red-200 rounded-lg font-medium transition">
                        Refuser le dossier
                    </button>
                </form>
            </div>
            @else
            <div class="bg-white rounded-xl shadow-sm p-6 text-center">
                <h3 class="text-lg font-bold text-gray-800 mb-2">Statut du dossier</h3>
                @if($submission->status === 'approved')
                    <div class="inline-block p-3 bg-green-100 rounded-full text-green-600 mb-2">
                        <i class="fas fa-check text-2xl"></i>
                    </div>
                    <p class="text-green-700 font-bold">Dossier Validé</p>
                @else
                    <div class="inline-block p-3 bg-red-100 rounded-full text-red-600 mb-2">
                        <i class="fas fa-times text-2xl"></i>
                    </div>
                    <p class="text-red-700 font-bold">Dossier Refusé</p>
                    <p class="text-sm text-gray-500 mt-2 bg-gray-50 p-2 rounded">"{{ $submission->admin_notes }}"</p>
                @endif
            </div>
            @endif
        </div>

        <!-- Documents -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-file-alt mr-2 text-berrni-500"></i> Pièces Justificatives
                </h3>
                
                <div class="grid grid-cols-1 gap-8">
                    <!-- ID Card Section -->
                    <div>
                        <h4 class="font-semibold text-gray-700 mb-4 border-l-4 border-blue-500 pl-3">Carte Nationale d'Identité</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <p class="text-sm text-gray-500">Recto</p>
                                <div class="aspect-video bg-gray-100 rounded-lg overflow-hidden border border-gray-200 group relative">
                                    <img src="{{ asset($submission->id_card_front) }}" alt="ID Front" class="w-full h-full object-contain">
                                    <a href="{{ asset($submission->id_card_front) }}" target="_blank" class="absolute inset-0 bg-black/50 hidden group-hover:flex items-center justify-center text-white font-medium">
                                        <i class="fas fa-search-plus mr-2"></i> Agrandir
                                    </a>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <p class="text-sm text-gray-500">Verso</p>
                                <div class="aspect-video bg-gray-100 rounded-lg overflow-hidden border border-gray-200 group relative">
                                    <img src="{{ asset($submission->id_card_back) }}" alt="ID Back" class="w-full h-full object-contain">
                                    <a href="{{ asset($submission->id_card_back) }}" target="_blank" class="absolute inset-0 bg-black/50 hidden group-hover:flex items-center justify-center text-white font-medium">
                                        <i class="fas fa-search-plus mr-2"></i> Agrandir
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-100">

                    <!-- Selfie Section -->
                    <div>
                        <h4 class="font-semibold text-gray-700 mb-4 border-l-4 border-purple-500 pl-3">Vérification Visuelle</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <p class="text-sm text-gray-500">Selfie Simple</p>
                                <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden border border-gray-200 max-w-sm mx-auto group relative">
                                    <img src="{{ asset($submission->selfie) }}" alt="Selfie" class="w-full h-full object-cover">
                                    <a href="{{ asset($submission->selfie) }}" target="_blank" class="absolute inset-0 bg-black/50 hidden group-hover:flex items-center justify-center text-white font-medium">
                                        <i class="fas fa-search-plus mr-2"></i> Agrandir
                                    </a>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <p class="text-sm text-gray-500">Selfie avec Pièce d'Identité</p>
                                <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden border border-gray-200 max-w-sm mx-auto group relative">
                                    <img src="{{ asset($submission->selfie_with_id) }}" alt="Selfie ID" class="w-full h-full object-cover">
                                    <a href="{{ asset($submission->selfie_with_id) }}" target="_blank" class="absolute inset-0 bg-black/50 hidden group-hover:flex items-center justify-center text-white font-medium">
                                        <i class="fas fa-search-plus mr-2"></i> Agrandir
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($submission->transport_photo)
                    <hr class="border-gray-100">

                    <div>
                        <h4 class="font-semibold text-gray-700 mb-4 border-l-4 border-green-500 pl-3">Photo du véhicule / moyen</h4>
                        <div class="aspect-video bg-gray-100 rounded-lg overflow-hidden border border-gray-200 max-w-lg group relative">
                            <img src="{{ asset($submission->transport_photo) }}" alt="Transport" class="w-full h-full object-contain">
                            <a href="{{ asset($submission->transport_photo) }}" target="_blank" class="absolute inset-0 bg-black/50 hidden group-hover:flex items-center justify-center text-white font-medium">
                                <i class="fas fa-search-plus mr-2"></i> Agrandir
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
