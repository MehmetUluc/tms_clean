<script>
    // Directly add pricing button to hotel rows - runs as soon as possible
    (function() {
        function addPricingButtons() {
            // Only run on hotel pages
            if (!window.location.pathname.includes('/hotels') && !window.location.pathname.includes('/admin')) {
                return;
            }
            
            // Find tables
            var tables = document.querySelectorAll('table');
            if (tables.length === 0) return;
            
            // Process each table
            for (var i = 0; i < tables.length; i++) {
                var table = tables[i];
                
                // Check table headers
                var headers = table.querySelectorAll('thead th');
                var isHotelTable = false;
                
                for (var j = 0; j < headers.length; j++) {
                    var headerText = headers[j].textContent.toLowerCase();
                    if (headerText.includes('otel') || headerText.includes('hotel')) {
                        isHotelTable = true;
                        break;
                    }
                }
                
                if (!isHotelTable) continue;
                
                // Process rows
                var rows = table.querySelectorAll('tbody tr');
                for (var k = 0; k < rows.length; k++) {
                    var row = rows[k];
                    
                    // Skip processed rows
                    if (row.querySelector('.pricing-v2-btn')) continue;
                    
                    // Get action cell
                    var actionCell = row.querySelector('td:last-child');
                    if (!actionCell) continue;
                    
                    // Find links to extract hotel ID
                    var links = actionCell.querySelectorAll('a[href*="/hotels/"]');
                    if (links.length === 0) continue;
                    
                    // Get hotel ID
                    var hotelId = null;
                    for (var l = 0; l < links.length; l++) {
                        var match = links[l].href.match(/\/hotels\/(\d+)/);
                        if (match && match[1]) {
                            hotelId = match[1];
                            break;
                        }
                    }
                    
                    if (!hotelId) continue;
                    
                    // Create button
                    var button = document.createElement('a');
                    button.href = '/admin/hotels/' + hotelId + '/pricing-v2';
                    button.className = 'pricing-v2-btn filament-button filament-button-size-sm inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors text-sm text-white bg-primary-600 hover:bg-primary-500 ml-2';
                    button.textContent = 'Pricing V2';
                    button.target = '_blank';
                    
                    // Add to action container
                    var actionContainer = actionCell.querySelector('.flex') || actionCell;
                    actionContainer.appendChild(button);
                }
            }
        }
        
        // Run when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(addPricingButtons, 500);
            });
        } else {
            setTimeout(addPricingButtons, 500);
        }
        
        // Also run periodically
        setInterval(addPricingButtons, 1000);
    })();
</script>