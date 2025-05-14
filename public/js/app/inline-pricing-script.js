// Directly add pricing button to hotel rows - independent of asset loading
document.addEventListener('DOMContentLoaded', function() {
    // Simple script to add a button to each hotel row
    function addPricingButtons() {
        // Find tables
        const tables = document.querySelectorAll('table');
        
        tables.forEach(function(table) {
            // Find rows
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(function(row) {
                // Skip if already processed
                if (row.hasAttribute('data-pricing-processed')) return;
                
                // Mark as processed
                row.setAttribute('data-pricing-processed', 'true');
                
                // Get last cell (action cell)
                const lastCell = row.querySelector('td:last-child');
                if (!lastCell) return;
                
                // Try to find an edit link to extract hotel ID
                const links = lastCell.querySelectorAll('a[href*="/hotels/"]');
                if (links.length === 0) return;
                
                // Find hotel ID from first matching link
                let hotelId = null;
                for (let link of links) {
                    const match = link.href.match(/\/hotels\/(\d+)/);
                    if (match && match[1]) {
                        hotelId = match[1];
                        break;
                    }
                }
                
                if (!hotelId) return;
                
                // Create a "Pricing V2" button
                const button = document.createElement('a');
                button.href = '/admin/hotels/' + hotelId + '/pricing-v2';
                button.className = 'inline-flex items-center justify-center gap-1 font-medium text-primary-600 hover:text-primary-500 ml-2';
                button.innerHTML = 'Pricing V2';
                button.target = '_blank';
                
                // Add to action cell
                const actionGroup = lastCell.querySelector('.flex') || lastCell;
                actionGroup.appendChild(button);
            });
        });
    }
    
    // Run initially and periodically
    addPricingButtons();
    setInterval(addPricingButtons, 1000);
});