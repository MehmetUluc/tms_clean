/**
 * Mega Menu JavaScript for enhancing the functionality and interactions
 */
document.addEventListener('DOMContentLoaded', function() {
    // Select all mega menu elements
    const megaMenus = document.querySelectorAll('.menu-mega');
    
    // Skip if no mega menus found
    if (!megaMenus.length) return;
    
    // For each mega menu
    megaMenus.forEach(menu => {
        // Get all menu items with mega menu functionality
        const megaMenuItems = menu.querySelectorAll('.has-mega-menu');
        
        // For each mega menu item
        megaMenuItems.forEach(item => {
            // Handle hover enter
            item.addEventListener('mouseenter', function() {
                const megaMenuPanel = this.querySelector('.mega-menu');
                if (megaMenuPanel) {
                    megaMenuPanel.classList.remove('hidden');
                    megaMenuPanel.classList.add('block');
                }
            });
            
            // Handle hover leave
            item.addEventListener('mouseleave', function() {
                const megaMenuPanel = this.querySelector('.mega-menu');
                if (megaMenuPanel) {
                    megaMenuPanel.classList.add('hidden');
                    megaMenuPanel.classList.remove('block');
                }
            });
            
            // For touch devices
            item.addEventListener('click', function(e) {
                // Check if the clicked element is directly the main link
                if (e.target === this.querySelector('a') || e.target.closest('a') === this.querySelector('a')) {
                    const megaMenuPanel = this.querySelector('.mega-menu');
                    if (megaMenuPanel && window.getComputedStyle(megaMenuPanel).display === 'none') {
                        e.preventDefault();
                        megaMenuPanel.classList.toggle('hidden');
                        
                        // Close other open mega menus
                        megaMenuItems.forEach(otherItem => {
                            if (otherItem !== item) {
                                const otherPanel = otherItem.querySelector('.mega-menu');
                                if (otherPanel) {
                                    otherPanel.classList.add('hidden');
                                }
                            }
                        });
                    }
                }
            });
        });
    });
    
    // Handle responsive behavior
    function handleResponsiveMegaMenu() {
        const isMobile = window.innerWidth < 1024;
        
        megaMenus.forEach(menu => {
            if (isMobile) {
                menu.classList.add('menu-mega-mobile');
            } else {
                menu.classList.remove('menu-mega-mobile');
            }
        });
    }
    
    // Initial call
    handleResponsiveMegaMenu();
    
    // Update on resize
    window.addEventListener('resize', handleResponsiveMegaMenu);
});