@props(['paginator'])

@if($paginator->total() > 0)
    <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row flex-wrap items-center justify-between gap-3 bg-gray-50/50">
        <p class="text-sm text-gray-500">
            Affichage {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} sur {{ number_format($paginator->total()) }} résultat{{ $paginator->total() > 1 ? 's' : '' }}
        </p>
        @if($paginator->hasPages())
            <div>{{ $paginator->appends(request()->except('page'))->links() }}</div>
        @endif
    </div>
@endif
