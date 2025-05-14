@php
    // Extract props, handling closures if provided
    $mappingData = is_callable($mappingData) ? $mappingData() : $mappingData;
    $mappingName = is_callable($mappingName) ? $mappingName() : $mappingName;
    $channelName = is_callable($channelName) ? $channelName() : $channelName;
    $operationType = is_callable($operationType) ? $operationType() : $operationType;
    $formatType = is_callable($formatType) ? $formatType() : $formatType;
    $mappingEntity = is_callable($mappingEntity) ? $mappingEntity() : $mappingEntity;
    $totalMappings = is_callable($totalMappings) ? $totalMappings() : $totalMappings;
    $transformationCount = is_callable($transformationCount) ? $transformationCount() : $transformationCount;
    $simpleMappingCount = is_callable($simpleMappingCount) ? $simpleMappingCount() : $simpleMappingCount;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium mb-2 text-gray-900 dark:text-white">Basic Information</h3>
        <div class="space-y-2">
            <div class="flex">
                <span class="font-semibold text-gray-700 dark:text-gray-300 w-1/3">Mapping Name:</span>
                <span class="text-gray-700 dark:text-gray-300 w-2/3">{{ $mappingName }}</span>
            </div>
            <div class="flex">
                <span class="font-semibold text-gray-700 dark:text-gray-300 w-1/3">OTA Channel:</span>
                <span class="text-gray-700 dark:text-gray-300 w-2/3">{{ $channelName }}</span>
            </div>
            <div class="flex">
                <span class="font-semibold text-gray-700 dark:text-gray-300 w-1/3">Operation:</span>
                <span class="text-gray-700 dark:text-gray-300 w-2/3">{{ $operationType }}</span>
            </div>
            <div class="flex">
                <span class="font-semibold text-gray-700 dark:text-gray-300 w-1/3">Format:</span>
                <span class="text-gray-700 dark:text-gray-300 w-2/3">{{ strtoupper($formatType) }}</span>
            </div>
            <div class="flex">
                <span class="font-semibold text-gray-700 dark:text-gray-300 w-1/3">Entity:</span>
                <span class="text-gray-700 dark:text-gray-300 w-2/3">{{ $mappingEntity }}</span>
            </div>
        </div>
    </div>
    
    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium mb-2 text-gray-900 dark:text-white">Mapping Statistics</h3>
        <div class="space-y-2">
            <div class="flex">
                <span class="font-semibold text-gray-700 dark:text-gray-300 w-1/2">Total Mappings:</span>
                <span class="text-gray-700 dark:text-gray-300 w-1/2">{{ $totalMappings }}</span>
            </div>
            <div class="flex">
                <span class="font-semibold text-gray-700 dark:text-gray-300 w-1/2">With Transformations:</span>
                <span class="text-gray-700 dark:text-gray-300 w-1/2">{{ $transformationCount }}</span>
            </div>
            <div class="flex">
                <span class="font-semibold text-gray-700 dark:text-gray-300 w-1/2">Simple Mappings:</span>
                <span class="text-gray-700 dark:text-gray-300 w-1/2">{{ $simpleMappingCount }}</span>
            </div>
        </div>
    </div>
    
    <div class="col-span-1 md:col-span-2 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium mb-2 text-gray-900 dark:text-white">Mapping Details</h3>
        <div class="overflow-auto max-h-96">
            <div class="filament-tables-container rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800">
                <table class="filament-tables-table w-full text-start divide-y table-auto dark:divide-gray-700">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700">
                            <th class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-600 dark:text-gray-300">
                                <span>Source Path</span>
                            </th>
                            <th class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-600 dark:text-gray-300">
                                <span>Target Field</span>
                            </th>
                            <th class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-600 dark:text-gray-300">
                                <span>Transformation</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-gray-700">
                        @forelse ($mappingData as $sourcePath => $target)
                            <tr class="@if($loop->even) bg-gray-50 dark:bg-gray-800/50 @endif">
                                <td class="px-4 py-2 align-middle text-sm text-gray-900 dark:text-white">
                                    {{ $sourcePath }}
                                </td>
                                
                                @if(is_array($target))
                                    <td class="px-4 py-2 align-middle text-sm text-gray-900 dark:text-white">
                                        {{ $target['target'] ?? '' }}
                                    </td>
                                    <td class="px-4 py-2 align-middle text-sm text-gray-600 dark:text-gray-400">
                                        @if(isset($target['transformations']))
                                            @foreach($target['transformations'] as $type => $params)
                                                @if($type == 'date_format')
                                                    <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-medium rounded bg-blue-100 text-blue-800 dark:bg-blue-800/30 dark:text-blue-400">
                                                        Date Format: {{ $params['from'] ?? '' }} → {{ $params['to'] ?? '' }}
                                                    </span>
                                                @elseif($type == 'number_format')
                                                    <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-400">
                                                        Number Format: {{ $params['decimals'] ?? 2 }} decimals
                                                    </span>
                                                @elseif($type == 'boolean')
                                                    <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-medium rounded bg-purple-100 text-purple-800 dark:bg-purple-800/30 dark:text-purple-400">
                                                        Boolean: {{ $params['output'] ?? 'boolean' }}
                                                    </span>
                                                @elseif($type == 'fixed')
                                                    <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-medium rounded bg-purple-100 text-purple-800 dark:bg-purple-800/30 dark:text-purple-400">
                                                        Fixed Value: {{ $params }}
                                                    </span>
                                                @elseif($type == 'custom')
                                                    <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-medium rounded bg-amber-100 text-amber-800 dark:bg-amber-800/30 dark:text-amber-400">
                                                        Custom Transformation
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-800 dark:bg-gray-800/30 dark:text-gray-400">
                                                        {{ ucfirst($type) }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        @else
                                            <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-800 dark:bg-gray-800/30 dark:text-gray-400">
                                                Unknown Transformation
                                            </span>
                                        @endif
                                    </td>
                                @else
                                    <td class="px-4 py-2 align-middle text-sm text-gray-900 dark:text-white">
                                        {{ $target }}
                                    </td>
                                    <td class="px-4 py-2 align-middle text-sm text-gray-500 dark:text-gray-400">
                                        <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-600 dark:bg-gray-800/30 dark:text-gray-400">
                                            -
                                        </span>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-center text-gray-500 dark:text-gray-400">
                                    No mappings found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>