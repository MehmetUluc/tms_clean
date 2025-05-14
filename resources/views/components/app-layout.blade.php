@props(['header' => null])

<x-layouts.app>
    @if($header)
        <x-slot:header>
            {{ $header }}
        </x-slot>
    @endif
    
    {{ $slot }}
</x-layouts.app>