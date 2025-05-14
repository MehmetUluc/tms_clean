<div class="p-4 space-y-4">
    <h2 class="text-xl font-bold">API Erişim Bilgileri</h2>
    
    <div class="grid grid-cols-1 gap-4">
        <div class="border border-gray-300 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-lg font-semibold">Kimlik Bilgileri</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Bu bilgileri dış sistemlerde kullanmak için güvenli bir şekilde saklayın</p>
                </div>
            </div>
            
            <div class="mt-4 space-y-3">
                <div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Kullanıcı Adı</div>
                    <div class="mt-1 flex">
                        <code class="flex-1 p-2 bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded">
                            {{ $record->username }}
                        </code>
                        <button 
                            type="button"
                            x-data="{}"
                            x-on:click="
                                navigator.clipboard.writeText('{{ $record->username }}');
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
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">API Anahtarı</div>
                    <div class="mt-1 flex">
                        <code class="flex-1 p-2 bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded overflow-x-auto">
                            {{ $record->api_key }}
                        </code>
                        <button 
                            type="button"
                            x-data="{}"
                            x-on:click="
                                navigator.clipboard.writeText('{{ $record->api_key }}');
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
            </div>
        </div>
        
        <div class="border border-gray-300 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-lg font-semibold">Kimlik Doğrulama Örnekleri</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">API'ye istek gönderirken aşağıdaki örnekleri kullanabilirsiniz</p>
                </div>
            </div>
            
            <div class="mt-4 space-y-5">
                <div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">HTTP Basic Authentication (Tavsiye Edilen)</div>
                    <div class="mt-1">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">cURL Örneği:</div>
                        <code class="block p-2 bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded overflow-x-auto whitespace-pre">
curl -X POST {{ config('app.url') }}/api/example-endpoint \
  -H "Content-Type: application/xml" \
  -u {{ $record->username }}:{{ $record->api_key }} \
  -d '&lt;YourXMLData&gt;&lt;/YourXMLData&gt;'
                        </code>
                    </div>
                </div>
                
                <div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">API Key Header Yöntemi (Alternatif)</div>
                    <div class="mt-1">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">cURL Örneği:</div>
                        <code class="block p-2 bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded overflow-x-auto whitespace-pre">
curl -X POST {{ config('app.url') }}/api/example-endpoint \
  -H "Content-Type: application/json" \
  -H "X-API-KEY: {{ $record->api_key }}" \
  -d '{"key": "value"}'
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
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Güvenlik Uyarısı</h3>
                            <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                <p>
                                    API anahtarınızı hiçbir zaman paylaşmayın ve güvenli bir şekilde saklayın. Güvenlik endişeniz varsa, "API Anahtarını Yenile" seçeneğini kullanarak yeni bir anahtar oluşturabilirsiniz.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>