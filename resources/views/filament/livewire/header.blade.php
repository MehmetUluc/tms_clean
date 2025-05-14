<style>
    /* Başlık için özel stil */
    .rate-plan-header {
        background: linear-gradient(to right, #f9fafb, #f3f4f6);
        border-bottom: 1px solid #e5e7eb;
        padding: 1rem 1.25rem;
    }

    .rate-plan-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
    }

    .rate-plan-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 9999px;
        padding: 0.125rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 500;
        margin-left: 0.75rem;
    }

    .refundable-badge {
        background-color: #d1fae5;
        color: #065f46;
    }

    .non-refundable-badge {
        background-color: #fef3c7;
        color: #92400e;
    }

    .pricing-method-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 0.375rem;
        padding: 0.125rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 500;
        background-color: #dbeafe;
        color: #1e40af;
    }
</style>

<div class="rate-plan-header">
    <div class="rate-plan-title">
        {{ $roomName }} - {{ $boardTypeName }}
        
        @if(!empty($refundType))
            <span class="rate-plan-badge {{ $refundType == 'İade Edilebilir' ? 'refundable-badge' : 'non-refundable-badge' }}">
                {{ $refundType }}
            </span>
        @endif
    </div>
    
    <div class="mt-2 flex items-center flex-wrap gap-2">
        <span class="pricing-method-badge">
            {{ $pricingMethod }}
        </span>
        
        <div class="flex items-center text-sm text-gray-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 mr-1" viewBox="0 0 20 20" fill="currentColor">
                <path d="M5 4a1 1 0 00-2 0v7.268a2 2 0 000 3.464V16a1 1 0 102 0v-1.268a2 2 0 000-3.464V4zM11 4a1 1 0 10-2 0v1.268a2 2 0 000 3.464V16a1 1 0 102 0V8.732a2 2 0 000-3.464V4zM16 3a1 1 0 011 1v7.268a2 2 0 010 3.464V16a1 1 0 11-2 0v-1.268a2 2 0 010-3.464V4a1 1 0 011-1z" />
            </svg>
            Fiyat Kontrolü
        </div>
    </div>
</div>