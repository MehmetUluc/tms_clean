@php
    $refundable = $hotel->refund_type ?? 'refundable';
    $cancellationDeadline = $hotel->cancellation_deadline ?? 24;
    $nonRefundableDiscount = $hotel->non_refundable_discount ?? 10;
@endphp

<div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
    <h4 class="font-semibold text-amber-900 dark:text-amber-100 mb-3 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        Cancellation Policy
    </h4>
    
    <div class="space-y-3 text-sm">
        @if($refundable === 'refundable')
            <div class="flex items-start">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="font-medium text-gray-900 dark:text-gray-100">Free Cancellation</p>
                    <p class="text-gray-600 dark:text-gray-400">
                        Cancel up to {{ $cancellationDeadline }} hours before check-in for a full refund
                    </p>
                </div>
            </div>
            <div class="flex items-start">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="font-medium text-gray-900 dark:text-gray-100">Late Cancellation</p>
                    <p class="text-gray-600 dark:text-gray-400">
                        Cancellations made within {{ $cancellationDeadline }} hours will incur a one night charge
                    </p>
                </div>
            </div>
        @elseif($refundable === 'non_refundable')
            <div class="flex items-start">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="font-medium text-gray-900 dark:text-gray-100">Non-Refundable Rate</p>
                    <p class="text-gray-600 dark:text-gray-400">
                        This booking cannot be cancelled or modified. Save {{ $nonRefundableDiscount }}% compared to flexible rates
                    </p>
                </div>
            </div>
        @else
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="font-medium text-gray-900 dark:text-gray-100">Mixed Cancellation Policy</p>
                    <p class="text-gray-600 dark:text-gray-400">
                        Different rates have different cancellation policies. Please review each rate's terms
                    </p>
                </div>
            </div>
        @endif
        
        <div class="flex items-start">
            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            <div>
                <p class="font-medium text-gray-900 dark:text-gray-100">No-Show Policy</p>
                <p class="text-gray-600 dark:text-gray-400">
                    No-shows will be charged the full amount of the reservation
                </p>
            </div>
        </div>
    </div>
    
    <div class="mt-4 pt-3 border-t border-amber-200 dark:border-amber-700">
        <p class="text-xs text-gray-600 dark:text-gray-400">
            By completing this booking, you acknowledge that you have read and agree to the cancellation policy
        </p>
    </div>
</div>