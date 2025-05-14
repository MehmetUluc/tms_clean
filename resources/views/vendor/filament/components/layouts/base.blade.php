@props([
    'livewire' => null,
])

<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ __('filament::layout.direction') ?? 'ltr' }}"
    @class([
        'filament',
        'filament-' . str(config('filament.layout.id'))->camel()->kebab(),
        'scroll-smooth',
    ])
>
    <head>
        {{ \Filament\Support\Facades\FilamentView::renderHook('head.start') }}

        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        @foreach (\Filament\Facades\Filament::getMeta() as $tag)
            {{ $tag }}
        @endforeach

        @if ($favicon = config('filament.favicon'))
            <link rel="icon" href="{{ $favicon }}" />
        @endif

        <title>
            {{ filled($title = $livewire?->getTitle() ?? config('filament.brand')) ? "{$title} - " : null }}
            {{ config('app.name') }}
        </title>

        {{ \Filament\Support\Facades\FilamentView::renderHook('styles.start') }}

        <style>
            [x-cloak=''],
            [x-cloak='x-cloak'],
            [x-cloak='1'] {
                display: none !important;
            }

            @media not all and (min-width: 640px) {
                [x-cloak='-sm'] {
                    display: none !important;
                }
            }

            @media (min-width: 640px) {
                [x-cloak='sm'] {
                    display: none !important;
                }
            }

            @media not all and (min-width: 768px) {
                [x-cloak='-md'] {
                    display: none !important;
                }
            }

            @media (min-width: 768px) {
                [x-cloak='md'] {
                    display: none !important;
                }
            }

            @media not all and (min-width: 1024px) {
                [x-cloak='-lg'] {
                    display: none !important;
                }
            }

            @media (min-width: 1024px) {
                [x-cloak='lg'] {
                    display: none !important;
                }
            }

            @media not all and (min-width: 1280px) {
                [x-cloak='-xl'] {
                    display: none !important;
                }
            }

            @media (min-width: 1280px) {
                [x-cloak='xl'] {
                    display: none !important;
                }
            }

            @media not all and (min-width: 1536px) {
                [x-cloak='-2xl'] {
                    display: none !important;
                }
            }

            @media (min-width: 1536px) {
                [x-cloak='2xl'] {
                    display: none !important;
                }
            }
        </style>

        @filamentStyles

        {{ \Filament\Support\Facades\FilamentView::renderHook('styles.end') }}

        <style>
            :root {
                --font-family: {!! config('filament.layout.font_family') !!};
                --sidebar-width: {{ config('filament.layout.sidebar.width') ?? '20rem' }};
                --collapsed-sidebar-width: {{ config('filament.layout.sidebar.collapsed_width') ?? '5.4rem' }};
                --main-content-padding: {{ config('filament.layout.main_content.padding') ?? '1rem' }};
            }

            body {
                --sidebar-width: {{ config('filament.layout.sidebar.width') ?? '20rem' }};
            }

            @media (min-width: 1024px) {
                :root {
                    --main-content-padding: {{ config('filament.layout.main_content.padding.lg') ?? config('filament.layout.main_content.padding') ?? '2rem' }};
                }
            }

            @if (config('filament.layout.sidebar.is_collapsible_on_desktop'))
                [x-data="{ isCollapsed: localStorage.getItem('collapsedSidebar-{{ config('filament.layout.id') }}') === 'true' }"] {
                    @if (! config('filament.layout.sidebar.is_full_height'))
                        body:not(.fi-layout-sidebar-open) & {
                            --sidebar-width: var(--collapsed-sidebar-width);
                        }
                    @endif

                    &[data-sidebar-collapsed="true"] {
                        --sidebar-width: var(--collapsed-sidebar-width);

                        &:not(:focus-within) {
                            --sidebar-width: var(--collapsed-sidebar-width);
                        }
                    }
                }
            @endif
        </style>

        @vite('resources/css/app.css')
        @livewireStyles

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Function to add pricing buttons
                function addPricingV2Buttons() {
                    // Find tables in the page
                    document.querySelectorAll('table').forEach(function(table) {
                        // Check if there are actions (usually in the last column)
                        var rows = table.querySelectorAll('tbody tr');
                        
                        rows.forEach(function(row) {
                            // Skip if already has our button
                            if (row.querySelector('.pricing-v2-btn')) return;
                            
                            // Find action buttons container
                            var actionCell = row.querySelector('td:last-child');
                            if (!actionCell) return;
                            
                            // Try to find the hotel ID
                            var hotelId = null;
                            
                            // Method 1: From data attributes
                            if (row.hasAttribute('wire:key')) {
                                var key = row.getAttribute('wire:key');
                                var match = key.match(/hotel-(\d+)/i);
                                if (match) hotelId = match[1];
                            }
                            
                            // Method 2: From edit button URL
                            if (!hotelId) {
                                var editLink = actionCell.querySelector('a[href*="/hotels/"]');
                                if (editLink) {
                                    var match = editLink.href.match(/\/hotels\/(\d+)/i);
                                    if (match) hotelId = match[1];
                                }
                            }
                            
                            // Skip if we can't find the hotel ID
                            if (!hotelId) return;
                            
                            // Create our pricing button
                            var button = document.createElement('a');
                            button.href = '/admin/hotels/' + hotelId + '/pricing-v2';
                            button.className = 'pricing-v2-btn filament-button inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2rem] px-3 text-sm text-white shadow focus:ring-white border-primary-600 bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 filament-page-button-action ml-2';
                            button.target = '_blank';
                            button.innerHTML = '<svg class="w-5 h-5 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 10.818v2.614A3.13 3.13 0 0011.888 13c.482-.315.612-.648.612-.875 0-.227-.13-.56-.612-.875a3.13 3.13 0 00-1.138-.432zM8.33 8.62c.053.055.115.11.184.164.208.16.46.284.736.363V6.603a2.45 2.45 0 00-.35.13c-.14.065-.27.143-.386.233-.377.292-.514.627-.514.909 0 .184.058.39.202.592.037.051.08.102.128.152z" /><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-6a.75.75 0 01.75.75v.316a3.78 3.78 0 011.653.713c.426.33.744.74.925 1.2a.75.75 0 01-1.395.55 1.35 1.35 0 00-.447-.563 2.187 2.187 0 00-.736-.363V9.3c.698.093 1.383.32 1.959.696.787.514 1.29 1.27 1.29 2.13 0 .86-.504 1.616-1.29 2.13-.576.377-1.261.603-1.96.696v.299a.75.75 0 11-1.5 0v-.3c-.697-.092-1.382-.318-1.958-.695-.482-.315-.857-.717-1.078-1.188a.75.75 0 111.359-.636c.08.173.245.376.54.569.313.205.706.353 1.138.432v-2.748a3.782 3.782 0 01-1.653-.713C6.9 9.433 6.5 8.681 6.5 7.875c0-.805.4-1.558 1.097-2.096a3.78 3.78 0 011.653-.713V4.75A.75.75 0 0110 4z" clip-rule="evenodd" /></svg> Pricing V2';
                            
                            // Find the actions container
                            var actionsContainer = actionCell.querySelector('.flex, .filament-tables-actions-container');
                            if (actionsContainer) {
                                actionsContainer.appendChild(button);
                            } else {
                                actionCell.appendChild(button);
                            }
                        });
                    });
                }
                
                // Run immediately and also observe for changes
                addPricingV2Buttons();
                
                // Set up mutation observer for dynamic content
                var observer = new MutationObserver(function(mutations) {
                    addPricingV2Buttons();
                });
                
                observer.observe(document.body, {
                    childList: true,
                    subtree: true
                });
                
                // Also run periodically as a fallback
                setInterval(addPricingV2Buttons, 1000);
            });
        </script>

        @include('vendor.filament.components.layouts.head-script')
        {{ \Filament\Support\Facades\FilamentView::renderHook('head.end') }}
    </head>

    <body
        @class([
            'fi-body',
            'fi-layout',
            'min-h-screen bg-gray-50 font-normal text-gray-950 antialiased dark:bg-gray-950 dark:text-white',
        ])
    >
        {{ \Filament\Support\Facades\FilamentView::renderHook('body.start') }}

        {{ $slot }}

        @livewireScripts

        @filamentScripts(withCore: true)

        @if (config('app.debug'))
            @vite('resources/js/app.js')
        @else
            <script src="{{ asset('build/assets/app-4ed993c7.js') }}"></script>
        @endif

        @stack('scripts')

        {{ \Filament\Support\Facades\FilamentView::renderHook('body.end') }}
    </body>
</html>