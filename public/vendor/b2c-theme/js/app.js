// B2C Theme JavaScript

/**
 * Toggle Mobile Menu
 */
function initMobileMenu() {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', () => {
            // Toggle mobile menu visibility
            mobileMenu.classList.toggle('hidden');
        });
    }
}

/**
 * Color Mode Switcher
 */
function initColorModeSwitcher() {
    const colorModeSwitch = document.getElementById('color-mode-switch');
    
    if (colorModeSwitch) {
        colorModeSwitch.addEventListener('click', () => {
            // Toggle dark/light mode
            document.documentElement.classList.toggle('dark');
            
            // Save preference to cookies
            const isDark = document.documentElement.classList.contains('dark');
            document.cookie = `color_mode=${isDark ? 'dark' : 'light'}; path=/; max-age=${60*60*24*365}`;
        });
    }
    
    // Initialize based on saved preference
    const colorMode = document.cookie.match(/color_mode=(light|dark)/)?.[1] || 'light';
    if (colorMode === 'dark') {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
}

/**
 * Sticky Header
 */
function initStickyHeader() {
    const header = document.querySelector('header');
    const headerOffset = header ? header.offsetTop : 0;
    
    function toggleStickyHeader() {
        if (window.scrollY > headerOffset) {
            header.classList.add('sticky', 'top-0', 'z-50', 'shadow-md', 'animate-fade-in');
        } else {
            header.classList.remove('sticky', 'top-0', 'z-50', 'shadow-md', 'animate-fade-in');
        }
    }
    
    if (header) {
        window.addEventListener('scroll', toggleStickyHeader);
    }
}

/**
 * Image Gallery Lightbox
 */
function initImageGallery() {
    const galleryItems = document.querySelectorAll('.gallery-item');
    const body = document.body;
    
    galleryItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            
            const imageUrl = item.getAttribute('data-full-src');
            const caption = item.getAttribute('data-caption');
            
            // Create lightbox elements
            const lightbox = document.createElement('div');
            lightbox.className = 'fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-90';
            
            lightbox.innerHTML = `
                <div class="relative max-w-4xl max-h-full">
                    <img src="${imageUrl}" alt="${caption}" class="max-w-full max-h-90vh object-contain">
                    <div class="absolute bottom-0 left-0 right-0 p-4 text-white text-center bg-black bg-opacity-60">${caption}</div>
                    <button class="absolute top-4 right-4 text-white text-2xl">&times;</button>
                </div>
            `;
            
            // Close lightbox when clicking anywhere
            lightbox.addEventListener('click', () => {
                lightbox.remove();
                body.classList.remove('overflow-hidden');
            });
            
            body.appendChild(lightbox);
            body.classList.add('overflow-hidden');
        });
    });
}

/**
 * Initialize Datepickers
 */
function initDatepickers() {
    // This is just a placeholder for date picker initialization
    // In a real app, you'd initialize a date picker library like flatpickr here
    console.log('Date pickers initialized');
}

/**
 * Initialize the application
 */
document.addEventListener('DOMContentLoaded', () => {
    initMobileMenu();
    initColorModeSwitcher();
    initStickyHeader();
    initImageGallery();
    initDatepickers();
    
    // Initialize any other components
});

// Additional initialization for Livewire
document.addEventListener('livewire:load', () => {
    // Livewire-specific initializations
});