// Add PricingV2 button to hotel listing tables
document.addEventListener('DOMContentLoaded', function() {
    // Create a timestamp to track last run time
    let lastRunTime = 0;
    let isProcessing = false;

    // Function to add buttons
    function addPricingV2Buttons() {
        // Prevent multiple concurrent executions
        if (isProcessing) return;
        isProcessing = true;

        // Throttle executions - don't run more than once every 500ms
        const now = Date.now();
        if (now - lastRunTime < 500) {
            isProcessing = false;
            return;
        }
        lastRunTime = now;

        // Process all tables on the page
        const tables = document.querySelectorAll('table');
        const tableCount = tables.length;
        let hotelTableCount = 0;

        tables.forEach(function(table) {
            // If this is a hotel table, it likely has columns with hotel names
            let tableHeaders = table.querySelectorAll('thead th');
            let isHotelTable = false;

            // Check if this is a hotel table by looking at column headers
            for (let i = 0; i < tableHeaders.length; i++) {
                let headerText = tableHeaders[i].textContent.toLowerCase();
                if (headerText.includes('otel') || headerText.includes('hotel') ||
                    headerText.includes('star') || headerText.includes('yıldız')) {
                    isHotelTable = true;
                    break;
                }
            }

            if (!isHotelTable) return;
            hotelTableCount++;

            // Process each row in the table
            let rows = table.querySelectorAll('tbody tr');
            rows.forEach(function(row) {
                // Skip if already processed
                if (row.querySelector('.pricing-v2-btn')) return;

                // Get the last cell which usually contains action buttons
                let actionsCell = row.querySelector('td:last-child');
                if (!actionsCell) return;

                // Try to find the hotel ID
                let hotelId = null;

                // Try from row attributes first
                if (row.hasAttribute('wire:key')) {
                    let key = row.getAttribute('wire:key');
                    let match = key.match(/hotel-(\d+)/i);
                    if (match) hotelId = match[1];
                }

                // Try from edit button URL
                if (!hotelId) {
                    let editLinks = actionsCell.querySelectorAll('a[href*="/hotels/"]');
                    for (let i = 0; i < editLinks.length; i++) {
                        let match = editLinks[i].href.match(/\/hotels\/(\d+)/i);
                        if (match) {
                            hotelId = match[1];
                            break;
                        }
                    }
                }

                // Try from row ID
                if (!hotelId && row.id) {
                    let match = row.id.match(/record-(\d+)/i);
                    if (match) hotelId = match[1];
                }

                if (!hotelId) return;

                // Create the button
                let button = document.createElement('a');
                button.href = '/admin/hotels/' + hotelId + '/pricing-v2';
                button.className = 'pricing-v2-btn filament-button filament-button-size-sm inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2rem] px-3 text-sm text-white shadow focus:ring-white border-primary-600 bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 ml-2';
                button.target = '_blank';
                button.innerHTML = '<svg class="w-5 h-5 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 10.818v2.614A3.13 3.13 0 0011.888 13c.482-.315.612-.648.612-.875 0-.227-.13-.56-.612-.875a3.13 3.13 0 00-1.138-.432zM8.33 8.62c.053.055.115.11.184.164.208.16.46.284.736.363V6.603a2.45 2.45 0 00-.35.13c-.14.065-.27.143-.386.233-.377.292-.514.627-.514.909 0 .184.058.39.202.592.037.051.08.102.128.152z" /><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-6a.75.75 0 01.75.75v.316a3.78 3.78 0 011.653.713c.426.33.744.74.925 1.2a.75.75 0 01-1.395.55 1.35 1.35 0 00-.447-.563 2.187 2.187 0 00-.736-.363V9.3c.698.093 1.383.32 1.959.696.787.514 1.29 1.27 1.29 2.13 0 .86-.504 1.616-1.29 2.13-.576.377-1.261.603-1.96.696v.299a.75.75 0 11-1.5 0v-.3c-.697-.092-1.382-.318-1.958-.695-.482-.315-.857-.717-1.078-1.188a.75.75 0 111.359-.636c.08.173.245.376.54.569.313.205.706.353 1.138.432v-2.748a3.782 3.782 0 01-1.653-.713C6.9 9.433 6.5 8.681 6.5 7.875c0-.805.4-1.558 1.097-2.096a3.78 3.78 0 011.653-.713V4.75A.75.75 0 0110 4z" clip-rule="evenodd" /></svg> Pricing V2';

                // Find the actions container and add the button
                let actionsContainer = actionsCell.querySelector('.flex, .filament-tables-actions-container, .filament-tables-actions');
                if (actionsContainer) {
                    actionsContainer.appendChild(button);
                } else {
                    actionsCell.appendChild(button);
                }
            });
        });

        // Log only once after processing is complete
        isProcessing = false;
    }

    // Run immediately
    addPricingV2Buttons();

    // Set up mutation observer for dynamic content
    let observer = new MutationObserver(function(mutations) {
        // Check if any mutations affected tables or their contents
        let shouldProcess = false;
        for (let i = 0; i < mutations.length; i++) {
            const mutation = mutations[i];

            // Check if added nodes contain tables
            if (mutation.addedNodes.length) {
                for (let j = 0; j < mutation.addedNodes.length; j++) {
                    const node = mutation.addedNodes[j];
                    if (node.nodeName === 'TABLE' ||
                        (node.nodeType === 1 && node.querySelector('table'))) {
                        shouldProcess = true;
                        break;
                    }
                }
            }

            // Also check if the mutation target is a table or contains tables
            if (!shouldProcess &&
                (mutation.target.nodeName === 'TABLE' ||
                 (mutation.target.nodeType === 1 && mutation.target.querySelector('table')))) {
                shouldProcess = true;
            }

            if (shouldProcess) break;
        }

        if (shouldProcess) {
            addPricingV2Buttons();
        }
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    // Use a more reasonable interval for Livewire updates (3 seconds instead of 1)
    setInterval(addPricingV2Buttons, 3000);
});