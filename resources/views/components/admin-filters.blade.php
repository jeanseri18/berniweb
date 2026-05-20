@props(['resetUrl' => null])

<div class="px-6 py-4 border-b border-gray-100 bg-gray-50/40">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        {{ $slot }}
        <button type="submit" class="px-4 py-2 bg-berrni-500 text-white rounded-lg text-sm font-medium hover:bg-berrni-600 shrink-0">
            <i class="fas fa-filter mr-1"></i> Filtrer
        </button>
        @if($resetUrl)
            <a href="{{ $resetUrl }}" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-lg text-sm hover:bg-white shrink-0">
                Réinitialiser
            </a>
        @endif
    </form>
</div>
