@props([
    'statePath' => null,
    'uuid' => null,
])

<button
    type="button"
    {{
        $attributes
            ->merge([
                'dusk' => "filament.forms.{$statePath}.actions.delete-file.{$uuid}",
                'title' => __('filament-forms::components.file_upload.actions.delete_file.label'),
            ], escape: false)
            ->merge([
                'data-file-upload-state-path' => $statePath,
                'data-file-upload-uuid' => $uuid,
                'wire:click' => "deleteUploadedFile('{$statePath}', '{$uuid}')",
                'wire:loading.attr' => 'disabled',
                'wire:target' => "deleteUploadedFile('{$statePath}', '{$uuid}')",
            ])
            ->class(['fi-fo-file-upload-action-delete-file fi-icon-btn relative text-danger-600 transition hover:text-danger-500 focus:outline-none disabled:pointer-events-none disabled:opacity-70 dark:text-danger-500 dark:hover:text-danger-400'])
    }}
>
    <span class="sr-only">
        {{ __('filament-forms::components.file_upload.actions.delete_file.label') }}
    </span>

    <x-filament::icon
        alias="forms::components.file-upload.actions.delete-file"
        icon="heroicon-m-x-mark"
        class="h-5 w-5"
    />
</button>