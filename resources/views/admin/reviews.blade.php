@extends('layouts.admin')

@section('title', 'Avis & Réputation')

@section('content')
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Avis utilisateurs</h3>
            <p class="text-sm text-gray-500 mt-1">Modération des avis laissés entre membres de la communauté.</p>
        </div>

        @if($reviews instanceof \Illuminate\Pagination\LengthAwarePaginator && $reviews->isNotEmpty())
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 font-medium text-gray-500 text-sm">De</th>
                        <th class="px-6 py-3 font-medium text-gray-500 text-sm">Pour</th>
                        <th class="px-6 py-3 font-medium text-gray-500 text-sm">Note</th>
                        <th class="px-6 py-3 font-medium text-gray-500 text-sm">Commentaire</th>
                        <th class="px-6 py-3 font-medium text-gray-500 text-sm text-right">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($reviews as $review)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-medium">{{ $review->reviewer->name ?? '—' }}</td>
                            <td class="px-6 py-3">{{ $review->reviewed->name ?? '—' }}</td>
                            <td class="px-6 py-3">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star text-sm {{ $i <= ($review->rating ?? 0) ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                                @endfor
                            </td>
                            <td class="px-6 py-3 text-gray-600 text-sm">{{ Str::limit($review->comment, 50) }}</td>
                            <td class="px-6 py-3 text-right text-gray-500 text-sm">{{ $review->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-4">{{ $reviews->links() }}</div>
        @else
            <div class="p-8 text-center text-gray-400">
                <i class="fas fa-star text-4xl mb-3 text-gray-200"></i>
                <p>Aucun avis pour le moment.</p>
                <p class="text-xs mt-2">Les avis apparaîtront ici dès que les utilisateurs en laisseront depuis l'application mobile.</p>
            </div>
        @endif
    </div>
@endsection
