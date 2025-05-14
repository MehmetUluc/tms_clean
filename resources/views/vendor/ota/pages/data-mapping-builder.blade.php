@php
    $pageClass = 'data-mapping-builder';
@endphp

<x-filament::page>
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Data Mapping Builder: {{ $record->name }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Channel: {{ $record->channel->name }} | 
                    Entity: {{ ucfirst($record->mapping_entity) }} | 
                    Format: {{ strtoupper($record->format_type) }} |
                    Operation: {{ $record->isImport() ? 'Import' : 'Export' }}
                </p>
            </div>
            <div class="flex space-x-2">
                <x-filament::button 
                    wire:click="saveMapping" 
                    color="success"
                >
                    Save Mapping
                </x-filament::button>
                <x-filament::button 
                    tag="a" 
                    :href="route('filament.admin.resources.data-mappings.edit', ['record' => $record])" 
                    color="gray"
                >
                    Cancel
                </x-filament::button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-5 space-y-8">
            <!-- Left side - Sample Data Section -->
            {{ $this->form }}

            @if(!empty($sampleData))
                <div class="mt-4">
                    <h3 class="text-lg font-medium mb-2">Sample Data</h3>
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 overflow-auto max-h-60">
                        <pre class="text-xs"><code>{{ $sampleData }}</code></pre>
                    </div>
                </div>
            @endif

            <div class="mt-4 flex space-x-2">
                <x-filament::button 
                    wire:click="analyzeSampleData" 
                    wire:loading.attr="disabled"
                    wire:target="analyzeSampleData"
                    color="primary"
                >
                    <span wire:loading.remove wire:target="analyzeSampleData">Analyze Sample Data</span>
                    <span wire:loading wire:target="analyzeSampleData">Analyzing...</span>
                </x-filament::button>
                
                <x-filament::button 
                    wire:click="generateMapping" 
                    wire:loading.attr="disabled"
                    wire:target="generateMapping"
                    color="warning"
                    :disabled="empty($parsedPaths)"
                >
                    <span wire:loading.remove wire:target="generateMapping">Generate Mapping Template</span>
                    <span wire:loading wire:target="generateMapping">Generating...</span>
                </x-filament::button>
            </div>
        </div>

        <div class="lg:col-span-7 space-y-8">
            <!-- Right side - Data Paths and Field Mapping -->
            <div>
                <div class="mb-4 flex items-end justify-between">
                    <div>
                        <h3 class="text-lg font-medium">Available {{ strtoupper($record->format_type) }} Paths</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Paths found in the sample data
                        </p>
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ count($parsedPaths) }} paths found
                    </div>
                </div>

                @if(count($parsedPaths) > 0)
                    <div class="overflow-hidden border border-gray-300 dark:border-gray-700 rounded-xl">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left rtl:text-right divide-y table-auto">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-800">
                                        <th class="px-3 py-2 text-xs font-medium text-gray-500 dark:text-gray-400">Path</th>
                                        <th class="px-3 py-2 text-xs font-medium text-gray-500 dark:text-gray-400">Type</th>
                                        <th class="px-3 py-2 text-xs font-medium text-gray-500 dark:text-gray-400">Example Value</th>
                                        <th class="px-3 py-2 text-xs font-medium text-gray-500 dark:text-gray-400">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @foreach($parsedPaths as $path)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                            <td class="px-3 py-2 text-xs font-medium">
                                                {{ $path['path'] }}
                                            </td>
                                            <td class="px-3 py-2 text-xs">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                                    {{ $path['type'] === 'element' ? 'bg-blue-50 text-blue-600 dark:bg-blue-600/10 dark:text-blue-400' : 'bg-yellow-50 text-yellow-600 dark:bg-yellow-600/10 dark:text-yellow-400' }}">
                                                    {{ $path['type'] }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-2 text-xs font-mono">
                                                {{ Str::limit($path['example'] ?? '', 30) }}
                                            </td>
                                            <td class="px-3 py-2 text-xs">
                                                <button wire:click="mapPath('{{ $path['path'] }}')" class="text-primary-600 dark:text-primary-400 hover:underline">
                                                    Map
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="rounded-lg border border-gray-300 dark:border-gray-700 p-4 text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">No paths found. Please analyze sample data first.</p>
                    </div>
                @endif
            </div>

            <div>
                <div class="mb-4 flex items-end justify-between">
                    <div>
                        <h3 class="text-lg font-medium">System Fields</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Available fields for {{ ucfirst($record->mapping_entity) }}
                        </p>
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ count($systemFields) }} fields available
                    </div>
                </div>

                @if(count($systemFields) > 0)
                    <div class="overflow-hidden border border-gray-300 dark:border-gray-700 rounded-xl">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left rtl:text-right divide-y table-auto">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-800">
                                        <th class="px-3 py-2 text-xs font-medium text-gray-500 dark:text-gray-400">Field</th>
                                        <th class="px-3 py-2 text-xs font-medium text-gray-500 dark:text-gray-400">Type</th>
                                        <th class="px-3 py-2 text-xs font-medium text-gray-500 dark:text-gray-400">Description</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @foreach($systemFields as $field)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                            <td class="px-3 py-2 text-xs font-medium">
                                                {{ $field['name'] }}
                                            </td>
                                            <td class="px-3 py-2 text-xs">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-600 dark:bg-gray-600/10 dark:text-gray-400">
                                                    {{ $field['type'] }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-2 text-xs">
                                                {{ $field['description'] }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="rounded-lg border border-gray-300 dark:border-gray-700 p-4 text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">No system fields found for this entity.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-filament::page>