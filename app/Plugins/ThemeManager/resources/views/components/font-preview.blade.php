@props([
    'fontFamily' => 'Inter, sans-serif',
    'sampleText' => 'Bu bir yazı tipi örneğidir',
    'sizes' => [
        'text-lg' => 'Normal Boyut',
    ]
])

<div class="font-preview-component rounded-lg border border-gray-200 dark:border-gray-700 p-4 mt-2 bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900">
    <style>
        .custom-font-sample {
            font-family: {{ $fontFamily }};
        }
        .font-preview-header {
            border-bottom: 1px solid rgba(209, 213, 219, 0.5);
            margin-bottom: 0.75rem;
            padding-bottom: 0.5rem;
        }
        .font-weight-controls {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        .font-weight-btn {
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            cursor: pointer;
            border: 1px solid #e5e7eb;
            background: white;
        }
        .font-weight-btn:hover {
            background: #f9fafb;
        }
        .dark .font-weight-btn {
            border-color: #374151;
            background: #1f2937;
        }
        .dark .font-weight-btn:hover {
            background: #111827;
        }
        .font-weight-btn.active {
            background: #3b82f6;
            color: white;
            border-color: #2563eb;
        }
        .dark .font-weight-btn.active {
            background: #2563eb;
            border-color: #1d4ed8;
        }
        .font-sample-controls {
            margin-top: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .font-preview-card {
            transition: all 0.2s ease;
        }
        .font-preview-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
    </style>
    
    <div class="font-preview-header">
        <div class="flex justify-between items-center">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $fontFamily }}</span>
            <span class="text-xs text-gray-500 dark:text-gray-400 px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded">{{ count($sizes) }} boyut</span>
        </div>
    </div>

    <div class="font-weight-controls">
        <button type="button" class="font-weight-btn active" onclick="updateFontWeight(this, 'normal')" data-weight="normal">Normal</button>
        <button type="button" class="font-weight-btn" onclick="updateFontWeight(this, 'bold')" data-weight="bold">Kalın</button>
        <button type="button" class="font-weight-btn" onclick="updateFontWeight(this, 'italic')" data-weight="italic">İtalik</button>
    </div>
    
    <div class="space-y-3">
        @foreach ($sizes as $sizeClass => $sizeLabel)
            <div class="font-preview-card bg-white dark:bg-gray-800 p-3 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $sizeLabel }}</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ $sizeClass }}</span>
                </div>
                <div class="custom-font-sample font-sample {{ $sizeClass }} p-2">
                    {{ $sampleText }}
                </div>
            </div>
        @endforeach
    </div>
    
    <div class="font-sample-controls">
        <button type="button" class="text-xs text-blue-600 dark:text-blue-400 hover:underline" onclick="updateSample(this, 'Bu bir yazı tipi örneğidir')">Kısa Metin</button>
        <button type="button" class="text-xs text-blue-600 dark:text-blue-400 hover:underline" onclick="updateSample(this, 'Şu yaylanın eteğinde bir köy kurulmuştur. Evler beyaz badanalı, pencereleri çiçekli ve bacalarından dumanlar tüten küçük bir köy.')">Uzun Metin</button>
        <button type="button" class="text-xs text-blue-600 dark:text-blue-400 hover:underline" onclick="updateSample(this, 'ABCÇDEFGĞHIJKLMNOÖPRSŞTUÜVYZ abcçdefgğhijklmnoöprsştuüvyz 0123456789')">Tüm Karakterler</button>
    </div>

    <script>
        function updateFontWeight(button, weight) {
            // Remove active class from all buttons
            button.parentNode.querySelectorAll('.font-weight-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            // Add active class to clicked button
            button.classList.add('active');
            
            // Update all samples
            document.querySelectorAll('.font-sample').forEach(sample => {
                sample.style.fontStyle = 'normal';
                sample.style.fontWeight = 'normal';
                
                if (weight === 'bold') {
                    sample.style.fontWeight = 'bold';
                } else if (weight === 'italic') {
                    sample.style.fontStyle = 'italic';
                }
            });
        }
        
        function updateSample(button, text) {
            document.querySelectorAll('.font-sample').forEach(sample => {
                sample.textContent = text;
            });
        }
    </script>
</div>