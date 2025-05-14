@props(['src'])

<div class="p-2 bg-gray-50 rounded-lg">
    @if ($src)
        <div class="single-image">
            <img src="{{ $src }}" alt="Kapak Görsel" class="single-image__image" />
        </div>
        <div class="mt-2 text-xs text-gray-500 text-center">
            Kapak Görsel
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-6 text-gray-400">
            <svg class="h-10 w-10 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p>Henüz kapak görsel yüklenmedi</p>
        </div>
    @endif
</div>