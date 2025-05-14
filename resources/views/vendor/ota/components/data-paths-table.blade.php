@php
    $paths = is_callable($dataPaths) ? $dataPaths() : $dataPaths;
    $formatType = is_callable($formatType) ? $formatType() : ($formatType ?? 'xml');
@endphp

<div class="overflow-auto max-h-96 p-2">
    <div class="filament-tables-container rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800">
        <table class="filament-tables-table w-full text-start divide-y table-auto dark:divide-gray-700">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700">
                    <th class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-600 dark:text-gray-300">
                        <span>{{ strtoupper($formatType) }} Path</span>
                    </th>
                    <th class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-600 dark:text-gray-300">
                        <span>Type</span>
                    </th>
                    <th class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-600 dark:text-gray-300">
                        <span>Example Value</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y dark:divide-gray-700">
                @forelse ($paths as $path)
                    <tr class="@if($loop->even) bg-gray-50 dark:bg-gray-800/50 @endif">
                        <td class="px-4 py-2 align-middle whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $path['path'] }}
                        </td>
                        <td class="px-4 py-2 align-middle whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                {{ $path['type'] === 'element' ? 'bg-blue-50 text-blue-600 dark:bg-blue-600/10 dark:text-blue-400' : 'bg-yellow-50 text-yellow-600 dark:bg-yellow-600/10 dark:text-yellow-400' }}">
                                {{ ucfirst($path['type']) }}
                            </span>
                        </td>
                        <td class="px-4 py-2 align-middle text-sm text-gray-600 dark:text-gray-400 max-w-md truncate">
                            {{ $path['example'] ?? '' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-2 text-center text-gray-500 dark:text-gray-400">
                            No data paths found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>