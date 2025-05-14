<x-filament::page>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow">
        <h2 class="text-2xl font-bold tracking-tight mb-4">Sistem Yapılandırma</h2>
        
        <div class="mb-6">
            <div class="px-4 py-2 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-100 dark:border-yellow-800/30 rounded-lg mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-400">Dikkat</h3>
                        <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                            <p>Bu sayfadaki ayarlar sistem çalışmasını doğrudan etkiler. Değişiklik yapmadan önce yedek almanız önerilir.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="space-y-6">
            <div>
                <h3 class="text-lg font-medium mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">Veritabanı</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                        <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-3">Bağlantı Bilgileri</h4>
                        <div class="space-y-3">
                            <div>
                                <label for="db_connection" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Veritabanı Türü</label>
                                <select id="db_connection" name="db_connection" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm">
                                    <option value="mysql" selected>MySQL</option>
                                    <option value="pgsql">PostgreSQL</option>
                                    <option value="sqlite">SQLite</option>
                                    <option value="sqlsrv">SQL Server</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="db_host" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sunucu Adresi</label>
                                <input type="text" id="db_host" name="db_host" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm" value="127.0.0.1">
                            </div>
                            
                            <div>
                                <label for="db_port" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Port</label>
                                <input type="number" id="db_port" name="db_port" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm" value="3306">
                            </div>
                            
                            <div>
                                <label for="db_database" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Veritabanı Adı</label>
                                <input type="text" id="db_database" name="db_database" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm" value="filament">
                            </div>
                            
                            <div>
                                <label for="db_username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kullanıcı Adı</label>
                                <input type="text" id="db_username" name="db_username" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm" value="root">
                            </div>
                            
                            <div>
                                <label for="db_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Şifre</label>
                                <input type="password" id="db_password" name="db_password" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm" value="••••••••">
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                        <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-3">Yedekleme Ayarları</h4>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input id="auto_backup" name="auto_backup" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500" checked>
                                <label for="auto_backup" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Otomatik Yedekleme</label>
                            </div>
                            
                            <div>
                                <label for="backup_frequency" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Yedekleme Sıklığı</label>
                                <select id="backup_frequency" name="backup_frequency" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm">
                                    <option value="daily">Günlük</option>
                                    <option value="weekly" selected>Haftalık</option>
                                    <option value="monthly">Aylık</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="backup_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Yedekleme Saati</label>
                                <input type="time" id="backup_time" name="backup_time" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm" value="03:00">
                            </div>
                            
                            <div>
                                <label for="backup_retention" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Yedekleme Saklama Süresi (gün)</label>
                                <input type="number" id="backup_retention" name="backup_retention" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm" value="30">
                            </div>
                            
                            <div>
                                <label for="backup_storage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Yedekleme Depolama</label>
                                <select id="backup_storage" name="backup_storage" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm">
                                    <option value="local" selected>Yerel Disk</option>
                                    <option value="s3">Amazon S3</option>
                                    <option value="sftp">SFTP</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <h3 class="text-lg font-medium mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">Entegrasyonlar</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                        <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-3">E-posta</h4>
                        <div class="space-y-3">
                            <div>
                                <label for="mail_mailer" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mail Sürücüsü</label>
                                <select id="mail_mailer" name="mail_mailer" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm">
                                    <option value="smtp" selected>SMTP</option>
                                    <option value="sendmail">Sendmail</option>
                                    <option value="mailgun">Mailgun</option>
                                    <option value="ses">Amazon SES</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="mail_host" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SMTP Sunucu</label>
                                <input type="text" id="mail_host" name="mail_host" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm" value="smtp.example.com">
                            </div>
                            
                            <div>
                                <label for="mail_port" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SMTP Port</label>
                                <input type="number" id="mail_port" name="mail_port" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm" value="587">
                            </div>
                            
                            <div>
                                <label for="mail_username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kullanıcı Adı</label>
                                <input type="email" id="mail_username" name="mail_username" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm" value="info@example.com">
                            </div>
                            
                            <div>
                                <label for="mail_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Şifre</label>
                                <input type="password" id="mail_password" name="mail_password" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm" value="••••••••">
                            </div>
                            
                            <div>
                                <label for="mail_encryption" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Şifreleme</label>
                                <select id="mail_encryption" name="mail_encryption" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm">
                                    <option value="null">Hiçbiri</option>
                                    <option value="tls" selected>TLS</option>
                                    <option value="ssl">SSL</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="mail_from_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gönderen E-posta</label>
                                <input type="email" id="mail_from_address" name="mail_from_address" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm" value="no-reply@example.com">
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                        <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-3">SMS Servisi</h4>
                        <div class="space-y-3">
                            <div>
                                <label for="sms_provider" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SMS Sağlayıcı</label>
                                <select id="sms_provider" name="sms_provider" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm">
                                    <option value="null">Hiçbiri</option>
                                    <option value="twilio" selected>Twilio</option>
                                    <option value="netgsm">Netgsm</option>
                                    <option value="clickatell">Clickatell</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="sms_account_sid" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hesap SID</label>
                                <input type="text" id="sms_account_sid" name="sms_account_sid" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm" value="ACxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                            </div>
                            
                            <div>
                                <label for="sms_auth_token" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Erişim Anahtarı</label>
                                <input type="password" id="sms_auth_token" name="sms_auth_token" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm" value="••••••••">
                            </div>
                            
                            <div>
                                <label for="sms_from_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gönderen Numarası</label>
                                <input type="text" id="sms_from_number" name="sms_from_number" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm" value="+905321234567">
                            </div>
                            
                            <div class="flex items-center">
                                <input id="sms_enabled" name="sms_enabled" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500" checked>
                                <label for="sms_enabled" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">SMS Bildirimlerini Etkinleştir</label>
                            </div>
                            
                            <button type="button" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                Test SMS Gönder
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <h3 class="text-lg font-medium mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">Sistem Performansı</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                        <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-3">Önbellek</h4>
                        <div class="space-y-3">
                            <div>
                                <label for="cache_driver" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Önbellek Sürücüsü</label>
                                <select id="cache_driver" name="cache_driver" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm">
                                    <option value="file">Dosya</option>
                                    <option value="redis" selected>Redis</option>
                                    <option value="memcached">Memcached</option>
                                    <option value="database">Veritabanı</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="cache_prefix" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Önbellek Öneki</label>
                                <input type="text" id="cache_prefix" name="cache_prefix" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm" value="filament_cache_">
                            </div>
                            
                            <div class="flex items-center">
                                <input id="cache_routes" name="cache_routes" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500" checked>
                                <label for="cache_routes" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Route Önbelleğini Etkinleştir</label>
                            </div>
                            
                            <div class="flex items-center">
                                <input id="cache_views" name="cache_views" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500" checked>
                                <label for="cache_views" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">View Önbelleğini Etkinleştir</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                        <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-3">Kuyruk</h4>
                        <div class="space-y-3">
                            <div>
                                <label for="queue_connection" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kuyruk Bağlantısı</label>
                                <select id="queue_connection" name="queue_connection" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm">
                                    <option value="sync">Senkron</option>
                                    <option value="database">Veritabanı</option>
                                    <option value="redis" selected>Redis</option>
                                    <option value="sqs">Amazon SQS</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="queue_worker_processes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Çalışan İşlem Sayısı</label>
                                <input type="number" id="queue_worker_processes" name="queue_worker_processes" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm" value="2">
                            </div>
                            
                            <div>
                                <label for="queue_default" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Varsayılan Kuyruk</label>
                                <input type="text" id="queue_default" name="queue_default" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm" value="default">
                            </div>
                            
                            <div>
                                <label for="queue_retry_after" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tekrar Deneme Süresi (saniye)</label>
                                <input type="number" id="queue_retry_after" name="queue_retry_after" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm" value="90">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex justify-between">
                    <div>
                        <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                            </svg>
                            Önbelleği Temizle
                        </button>
                    </div>
                    <div class="flex space-x-3">
                        <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Varsayılanlara Sıfırla
                        </button>
                        <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Değişiklikleri Kaydet
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament::page>