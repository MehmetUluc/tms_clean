@props([
    'statePath' => null,
])

<div class="fi-fo-file-upload-files-list gap-2">
    <ul
        data-file-upload-files-container
        class="gap-2"
        wire:key="{{ $statePath }}.{{ \Filament\Support\Facades\FilamentView::getRenderKey() }}.files-list"
    >
        @if (count($getState()))
            @foreach ($getState() as $uuid => $file)
                <li
                    wire:key="{{ $statePath }}.{{ \Filament\Support\Facades\FilamentView::getRenderKey() }}.files-list.{{ $uuid }}"
                    data-file-upload-file
                    @class([
                        'relative flex items-center justify-between space-x-2 rounded-lg border border-gray-300 bg-white p-2 text-sm shadow-sm',
                        'dark:border-white/10 dark:bg-white/5 dark:text-white' => config('forms.dark_mode'),
                    ])
                >
                    <div class="flex items-center gap-x-2 truncate">
                        <div class="shrink-0">
                            <x-filament-forms::partials.file-upload.file-icon
                                :upload="$file"
                                @class([
                                    'h-8 w-8',
                                    'text-gray-400 dark:text-gray-400' => config('forms.dark_mode'),
                                ])
                            />
                        </div>

                        <div class="truncate">
                            <p
                                class="truncate"
                                @if ($shouldDisplayFileSize($file))
                                    title="{{ $getUploadName($file) }} ({{ $getUploadSize($file) }})"
                                @else
                                    title="{{ $getUploadName($file) }}"
                                @endif
                            >
                                {{ $getUploadName($file) }}
                            </p>

                            @if ($shouldDisplayFileSize($file))
                                <p class="text-xs text-gray-500">
                                    {{ $getUploadSize($file) }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-x-1">
                        @foreach ($getActions() as $action)
                            {{ $action(['file' => $file, 'uuid' => $uuid]) }}
                        @endforeach

                        @if ($isDeletable())
                            <x-filament-forms::partials.file-upload.actions.delete-file
                                :statePath="$statePath"
                                :uuid="$uuid"
                            />
                        @endif
                    </div>
                </li>
            @endforeach
        @endif
    </ul>
</div>