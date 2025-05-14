<div class="p-4 space-y-4">
    <h2 class="text-xl font-bold">API Endpoint Bilgileri</h2>
    
    <div class="grid grid-cols-1 gap-4">
        <div class="border border-gray-300 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-lg font-semibold">HTTP Bilgileri</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Dış sistemin API'nize gönderim yaparken kullanacağı bilgiler</p>
                </div>
            </div>
            
            <div class="mt-4 space-y-3">
                <div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Endpoint URL</div>
                    <div class="mt-1 flex">
                        <code class="flex-1 p-2 bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded">
                            {{ config('app.url') . $record->endpoint_path }}
                        </code>
                        <button 
                            type="button"
                            x-data="{}"
                            x-on:click="
                                navigator.clipboard.writeText('{{ config('app.url') . $record->endpoint_path }}');
                                $tooltip('Kopyalandı!');
                            "
                            class="ml-2 p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" />
                                <path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">HTTP Metod</div>
                    <div class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-700 dark:text-indigo-100">
                            POST
                        </span>
                    </div>
                </div>
                
                <div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Content-Type</div>
                    <div class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100">
                            {{ $record->source_type === 'json' ? 'application/json' : ($record->source_type === 'xml' ? 'application/xml' : 'text/plain') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="border border-gray-300 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-lg font-semibold">Kimlik Doğrulama</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Kullanıcı adı ve API anahtarı ile gönderim yapılmalıdır</p>
                </div>
            </div>
            
            <div class="mt-4 space-y-3">
                <div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Kimlik Doğrulama Tipi</div>
                    <div class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                            HTTP Basic Authentication
                        </span>
                    </div>
                </div>
                
                <div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Örnek HTTP Başlığı</div>
                    <div class="mt-1">
                        <code class="block p-2 bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded">
                            Authorization: Basic {base64_encoded_credentials}
                        </code>
                    </div>
                </div>
                
                <div class="p-3 bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-800 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Dikkat!</h3>
                            <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                <p>
                                    API anahtarınızı hiçbir zaman paylaşmayın ve güvenli bir şekilde saklayın. Sisteme zarar verici data gönderimi durumunda API anahtarınız devre dışı bırakılacaktır.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="border border-gray-300 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-lg font-semibold">Veri Formatı</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ strtoupper($record->source_type) }} verilerinizi bu formata uygun olarak göndermelisiniz</p>
                </div>
            </div>
            
            <div class="mt-4 space-y-3">
                @if($record->schema)
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Şema Yapısı</div>
                        <div class="mt-1">
                            <code class="block whitespace-pre overflow-x-auto p-2 bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded">
                                {{ json_encode($record->schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
                            </code>
                        </div>
                    </div>
                @endif
                
                @if($record->test_data)
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Örnek Veri</div>
                        <div class="mt-1">
                            <code class="block whitespace-pre overflow-x-auto p-2 bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded">
                                @if($record->source_type === 'json')
                                    {{ json_encode(json_decode($record->test_data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
                                @else
                                    {{ $record->test_data }}
                                @endif
                            </code>
                        </div>
                    </div>
                @endif
                
                <div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Alan Eşleştirmeleri</div>
                    <div class="mt-1 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-100 dark:bg-gray-800">
                                <tr>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Kaynak Alan ({{ strtoupper($record->source_type) }})
                                    </th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Hedef Alan ({{ class_basename($record->target_model) }})
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                                @foreach($record->field_mappings as $sourceField => $targetField)
                                    <tr>
                                        <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $sourceField }}
                                        </td>
                                        <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">
                                            @if(is_array($targetField) && isset($targetField['field']))
                                                {{ $targetField['field'] }}
                                                @if(isset($targetField['transform']))
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                        dönüşüm var
                                                    </span>
                                                @endif
                                            @else
                                                {{ $targetField }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>