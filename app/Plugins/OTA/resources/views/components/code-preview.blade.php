@php
    // If $code is not passed directly, try to use $sampleData from parent context
    $code = $code ?? ($sampleData ?? '');
    $codeValue = is_callable($code) ? $code() : $code;
    $language = is_callable($language) ? $language() : ($language ?? 'text');
    $copyable = is_callable($copyable) ? $copyable() : ($copyable ?? false);
@endphp

<div class="relative">
    <div class="border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 rounded-lg overflow-auto max-h-96">
        <pre class="p-4 text-sm text-gray-800 dark:text-gray-200 font-mono">{{ $codeValue }}</pre>
    </div>
    
    @if($copyable)
    <button 
        type="button" 
        onclick="navigator.clipboard.writeText(`{{ $codeValue }}`).then(() => { this.querySelector('span').innerText = 'Kopyalandı!'; setTimeout(() => { this.querySelector('span').innerText = 'Kopyala'; }, 2000); })"
        class="absolute top-2 right-2 inline-flex items-center justify-center h-8 w-8 rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 bg-white dark:bg-gray-800 shadow-sm border border-gray-300 dark:border-gray-700"
    >
        <span class="sr-only">Kopyala</span>
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path d="M8 2a1 1 0 000 2h2a1 1 0 100-2H8z" />
            <path d="M3 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v6h-4.586l1.293-1.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L10.414 13H15v3a2 2 0 01-2 2H5a2 2 0 01-2-2V5zM15 11h2a1 1 0 110 2h-2v-2z" />
        </svg>
    </button>
    @endif
</div>