@props([
    'hotel' => null
])

<div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4 mt-4">
    <h4 class="font-medium text-amber-900 dark:text-amber-100 mb-2 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        Cancellation Policy
    </h4>
    
    @if($hotel && $hotel->refund_policy_type)
        <div class="text-sm text-amber-700 dark:text-amber-300 space-y-2">
            @switch($hotel->refund_policy_type)
                @case('flexible')
                    <p>✓ Free cancellation up to 24 hours before check-in</p>
                    <p>✓ Full refund if cancelled within the free cancellation period</p>
                    @break
                @case('moderate')
                    <p>⚠ Free cancellation up to 7 days before check-in</p>
                    <p>⚠ 50% refund if cancelled 3-7 days before check-in</p>
                    <p>⚠ No refund if cancelled less than 3 days before check-in</p>
                    @break
                @case('strict')
                    <p>❌ Free cancellation up to 14 days before check-in</p>
                    <p>❌ 50% refund if cancelled 7-14 days before check-in</p>
                    <p>❌ No refund if cancelled less than 7 days before check-in</p>
                    @break
                @case('non_refundable')
                    <p>❌ This is a non-refundable booking</p>
                    <p>❌ No refund will be provided for cancellations</p>
                    <p class="font-medium">💰 Save {{ $hotel->non_refundable_discount ?? 10 }}% with this non-refundable rate</p>
                    @break
            @endswitch
        </div>
    @else
        <p class="text-sm text-amber-700 dark:text-amber-300">Standard cancellation policy applies. Please contact the hotel for details.</p>
    @endif
</div>