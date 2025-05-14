<div class="overflow-auto max-h-96 p-2">
    <div class="filament-tables-container rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800">
        <table class="filament-tables-table w-full text-start divide-y table-auto dark:divide-gray-700">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700">
                    <th class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-600 dark:text-gray-300">
                        <span>XML Alanı</span>
                    </th>
                    <th class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-600 dark:text-gray-300">
                        <span>Tür</span>
                    </th>
                    <th class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-600 dark:text-gray-300">
                        <span>Örnek Değer</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y dark:divide-gray-700">
                @php
                    $paths = is_callable($xmlPaths) ? $xmlPaths() : $xmlPaths;
                @endphp
                @forelse ($paths as $path)
                    <tr class="@if($loop->even) bg-gray-50 dark:bg-gray-800/50 @endif">
                        <td class="px-4 py-2 align-middle whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $path['path'] }}
                        </td>
                        <td class="px-4 py-2 align-middle whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            {{ ucfirst($path['type']) }}
                        </td>
                        <td class="px-4 py-2 align-middle whitespace-nowrap text-sm text-gray-600 dark:text-gray-400 max-w-md truncate">
                            {{ $path['example'] ?? '' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-2 text-center text-gray-500 dark:text-gray-400">
                            Hiç XML alanı bulunamadı
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>