// IMPORTANT: This script adds PricingV2 buttons to the hotel listing table
(function() {
    // Create a timestamp to track last run time
    let lastRunTime = 0;
    let isProcessing = false;
    
    // Run once DOM is ready or when called
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

        // Check if we're on a page that might have hotel listings
        const pageUrl = window.location.pathname;
        const isHotelPage = pageUrl.includes('/hotels') || pageUrl.includes('/admin');

        if (!isHotelPage) {
            isProcessing = false;
            return;
        }

        // Find all tables in the document
        const tables = document.querySelectorAll('table');

        tables.forEach(table => {
            // Skip tables that don't look like hotel tables
            const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.toLowerCase());
            const hasRelevantColumn = headers.some(text =>
                text.includes('otel') || text.includes('hotel') ||
                text.includes('star') || text.includes('yıldız')
            );

            if (!hasRelevantColumn) {
                return;
            }

            // Check rows for data
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => {
                // Skip if we already added our button
                if (row.querySelector('.pricing-v2-button')) {
                    return;
                }

                // Get the cell with actions
                const actionCell = row.querySelector('td:last-child');
                if (!actionCell) {
                    return;
                }

                // Look for a hotel ID in this row
                let hotelId = null;

                // Method 1: Try from wire:key
                if (row.hasAttribute('wire:key')) {
                    const wireKey = row.getAttribute('wire:key');

                    // Try different patterns for the ID
                    const patterns = [
                        /hotels\.(\d+)/i,
                        /hotel-(\d+)/i,
                        /record-(\d+)/i,
                        /(\d+)$/
                    ];

                    for (const pattern of patterns) {
                        const match = wireKey.match(pattern);
                        if (match && match[1]) {
                            hotelId = match[1];
                            break;
                        }
                    }
                }

                // Method 2: Look in action links
                if (!hotelId) {
                    const links = actionCell.querySelectorAll('a[href*="/hotels/"]');

                    for (const link of links) {
                        const match = link.href.match(/\/hotels\/(\d+)/);
                        if (match && match[1]) {
                            hotelId = match[1];
                            break;
                        }
                    }
                }

                // Method 3: Try from data attributes
                if (!hotelId && row.hasAttribute('id')) {
                    const rowId = row.getAttribute('id');
                    const match = rowId.match(/(\d+)$/);
                    if (match && match[1]) {
                        hotelId = match[1];
                    }
                }

                // Method 4: Try from any element with a record ID
                if (!hotelId) {
                    const elementsWithId = row.querySelectorAll('[id*="record-"]');
                    for (const el of elementsWithId) {
                        const match = el.id.match(/record-(\d+)/);
                        if (match && match[1]) {
                            hotelId = match[1];
                            break;
                        }
                    }
                }

                // Method 5: Last resort, try to find any number in the row text
                if (!hotelId) {
                    const firstCell = row.querySelector('td:first-child');
                    if (firstCell && firstCell.textContent.trim().match(/^\d+$/)) {
                        hotelId = firstCell.textContent.trim();
                    }
                }

                if (!hotelId) {
                    return;
                }

                // Now create the button (try both styling approaches)
                const pricingButton = document.createElement('a');
                pricingButton.href = '/admin/hotels/' + hotelId + '/pricing-v2';
                pricingButton.className = 'pricing-v2-button filament-link inline-flex items-center justify-center gap-1 font-medium outline-none hover:underline focus:underline filament-tables-link-action text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400 ml-4';
                pricingButton.innerHTML = 'Pricing V2';
                pricingButton.target = '_blank';

                // Find the container and add the button
                const containers = [
                    actionCell.querySelector('.filament-tables-actions-container'),
                    actionCell.querySelector('.flex'),
                    actionCell.querySelector('.filament-tables-actions'),
                    actionCell
                ];

                // Use the first valid container
                for (const container of containers) {
                    if (container) {
                        container.appendChild(pricingButton);
                        break;
                    }
                }
            });
        });
        
        isProcessing = false;
    }

    // Add the buttons on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(addPricingV2Buttons, 500);
        });
    } else {
        setTimeout(addPricingV2Buttons, 500);
    }

    // Watch for changes to the DOM (for Livewire updates)
    const observer = new MutationObserver(function(mutations) {
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
            setTimeout(addPricingV2Buttons, 200);
        }
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    // Use a more reasonable interval (5 seconds instead of 2)
    setInterval(addPricingV2Buttons, 5000);
})();