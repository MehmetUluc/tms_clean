<template>
  <header class="relative z-50">
    <!-- Top Bar -->
    <div class="bg-gradient-to-r from-blue-700 to-indigo-800 text-white py-2 px-6">
      <div class="container mx-auto flex justify-between items-center">
        <div class="flex items-center text-sm space-x-6">
          <div class="flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
            </svg>
            <span>+90 212 555 1234</span>
          </div>
          <div class="flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <span>info@travelmanager.com</span>
          </div>
        </div>
        <div class="flex items-center space-x-4">
          <div class="flex space-x-2">
            <a href="#" class="hover:text-blue-200 transition-colors" aria-label="Facebook">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M22.675 0H1.325C.593 0 0 .593 0 1.325v21.351C0 23.407.593 24 1.325 24H12.82v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116c.73 0 1.323-.593 1.323-1.325V1.325C24 .593 23.407 0 22.675 0z" />
              </svg>
            </a>
            <a href="#" class="hover:text-blue-200 transition-colors" aria-label="Twitter">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M23.954 4.569c-.885.389-1.83.654-2.825.775 1.014-.611 1.794-1.574 2.163-2.723-.951.555-2.005.959-3.127 1.184-.896-.959-2.173-1.559-3.591-1.559-2.717 0-4.92 2.203-4.92 4.917 0 .39.045.765.127 1.124C7.691 8.094 4.066 6.13 1.64 3.161c-.427.722-.666 1.561-.666 2.475 0 1.71.87 3.213 2.188 4.096-.807-.026-1.566-.248-2.228-.616v.061c0 2.385 1.693 4.374 3.946 4.827-.413.111-.849.171-1.296.171-.314 0-.615-.03-.916-.086.631 1.953 2.445 3.377 4.604 3.417-1.68 1.319-3.809 2.105-6.102 2.105-.39 0-.779-.023-1.17-.067 2.189 1.394 4.768 2.209 7.557 2.209 9.054 0 14-7.503 14-14.001 0-.21-.005-.429-.015-.636.961-.689 1.8-1.561 2.457-2.549l-.001-.001z" />
              </svg>
            </a>
            <a href="#" class="hover:text-blue-200 transition-colors" aria-label="Instagram">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" />
              </svg>
            </a>
          </div>
          <div class="text-sm border-l border-blue-500 pl-4">
            <a href="/login" class="hover:text-blue-200 transition-colors mr-3">Login</a>
            <a href="/register" class="bg-white text-blue-700 px-3 py-1 rounded-full hover:bg-blue-50 transition-colors">Sign Up</a>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Main Navigation -->
    <div class="bg-white shadow">
      <div class="container mx-auto px-6 relative">
        <nav class="flex justify-between items-center py-4">
          <!-- Logo -->
          <div class="flex-shrink-0">
            <Link href="/" class="flex items-center">
              <img src="/images/logo-dark.svg" alt="Travel Manager" class="h-10 w-auto mr-2" />
              <span class="sr-only">Travel Manager</span>
            </Link>
          </div>
          
          <!-- Desktop Main Menu -->
          <div class="hidden lg:flex items-center justify-center space-x-1 flex-1 mx-8">
            <div v-for="(item, index) in navigationItems" :key="index" 
                 class="relative group"
                 :class="{'mega-menu-parent': item.isMegaMenu}">
              <!-- Regular Menu Link -->
              <Link :href="item.url" 
                    class="block px-4 py-3 text-gray-700 font-medium rounded-md hover:bg-gray-50 hover:text-blue-600 transition-colors">
                {{ item.title }}
                <svg v-if="item.children?.length || item.isMegaMenu" class="w-4 h-4 ml-1 inline-block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
              </Link>
              
              <!-- Mega Menu -->
              <div v-if="item.isMegaMenu" 
                   class="mega-menu absolute left-0 transform translate-y-2 w-full bg-white shadow-xl border-t border-gray-100 py-8 px-6 hidden group-hover:block transition-all duration-300 z-50 rounded-b-lg"
                   :class="item.megaMenuClasses || ''">
                <div class="grid grid-cols-4 gap-8">
                  <!-- Mega Menu Content -->
                  <div v-for="(section, sIdx) in item.megaMenuSections" :key="sIdx" 
                       :class="section.colSpan ? `col-span-${section.colSpan}` : ''">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-3">{{ section.title }}</h3>
                    
                    <!-- Links Section -->
                    <ul v-if="section.type === 'links'" class="space-y-2">
                      <li v-for="(link, lIdx) in section.links" :key="lIdx">
                        <Link :href="link.url" 
                              class="text-gray-700 hover:text-blue-600 transition-colors" 
                              :class="{'font-semibold text-blue-600': link.featured}">
                          {{ link.title }}
                        </Link>
                      </li>
                    </ul>
                    
                    <!-- Featured Section -->
                    <div v-else-if="section.type === 'featured'" class="bg-gray-50 rounded-lg p-4">
                      <div v-if="section.image" class="mb-3 rounded overflow-hidden">
                        <img :src="section.image" :alt="section.title" class="w-full h-32 object-cover">
                      </div>
                      <h4 class="font-bold text-gray-800 mb-1">{{ section.title }}</h4>
                      <p v-if="section.description" class="text-sm text-gray-600 mb-2">{{ section.description }}</p>
                      <Link v-if="section.url" :href="section.url" class="text-sm font-medium text-blue-600 hover:underline">
                        Learn More <span aria-hidden="true">→</span>
                      </Link>
                    </div>
                    
                    <!-- HTML Content -->
                    <div v-else-if="section.type === 'html'" v-html="section.content">
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Regular Dropdown Menu -->
              <div v-else-if="item.children?.length" 
                   class="absolute z-50 mt-1 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden group-hover:block transition-all duration-300">
                <div class="py-1" role="menu" aria-orientation="vertical">
                  <Link v-for="(child, cIdx) in item.children" :key="cIdx" 
                        :href="child.url" 
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-600 transition-colors" 
                        role="menuitem">
                    {{ child.title }}
                  </Link>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Desktop Right Actions -->
          <div class="hidden lg:flex items-center space-x-4">
            <a href="/search" class="flex items-center p-2 rounded hover:bg-gray-100 transition-colors" title="Search">
              <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </a>
            <Link href="/hotels" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded transition-colors">
              Book Now
            </Link>
          </div>
          
          <!-- Mobile Menu Button -->
          <div class="lg:hidden">
            <button @click="toggleMobileMenu" class="p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none">
              <svg v-if="!mobileMenuOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
              <svg v-else class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </nav>
      </div>
    </div>
    
    <!-- Mobile Menu -->
    <div v-show="mobileMenuOpen" class="lg:hidden">
      <div class="bg-white shadow-xl border-t border-gray-100 py-2 px-4 space-y-1">
        <div v-for="(item, index) in navigationItems" :key="index"
             class="py-1">
          <div class="flex items-center justify-between py-2 px-3 rounded-md hover:bg-gray-50 hover:text-blue-600">
            <Link :href="item.url" class="flex-1 font-medium">
              {{ item.title }}
            </Link>
            <button v-if="item.children?.length || item.isMegaMenu" 
                    @click="toggleMobileSubmenu(index)" 
                    class="p-1">
              <svg class="w-5 h-5 text-gray-500" :class="{'transform rotate-180': openMobileSubmenus.includes(index)}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
          </div>
          
          <!-- Mobile Mega Menu / Dropdown -->
          <div v-if="(item.isMegaMenu || item.children?.length) && openMobileSubmenus.includes(index)" 
               class="pl-4 py-2 border-l-2 border-gray-100 ml-4 space-y-2">
            <!-- Mega Menu Sections -->
            <template v-if="item.isMegaMenu">
              <div v-for="(section, sIdx) in item.megaMenuSections" :key="sIdx" class="mb-4">
                <h3 class="font-bold text-sm text-gray-500 uppercase tracking-wide mb-2">{{ section.title }}</h3>
                
                <!-- Links Section -->
                <ul v-if="section.type === 'links'" class="space-y-2">
                  <li v-for="(link, lIdx) in section.links" :key="lIdx">
                    <Link :href="link.url" 
                          class="block py-1 text-gray-700 hover:text-blue-600" 
                          :class="{'font-medium text-blue-600': link.featured}">
                      {{ link.title }}
                    </Link>
                  </li>
                </ul>
                
                <!-- Featured Section (simplified for mobile) -->
                <div v-else-if="section.type === 'featured'">
                  <Link :href="section.url" class="flex items-center py-1 text-blue-600 font-medium">
                    {{ section.title }} <span class="ml-1">→</span>
                  </Link>
                </div>
                
                <!-- HTML (simplified for mobile) -->
                <div v-else-if="section.type === 'html' && section.mobileContent" 
                     v-html="section.mobileContent">
                </div>
              </div>
            </template>
            
            <!-- Regular Dropdown Items -->
            <template v-else>
              <Link v-for="(child, cIdx) in item.children" :key="cIdx" 
                    :href="child.url" 
                    class="block py-2 text-gray-700 hover:text-blue-600">
                {{ child.title }}
              </Link>
            </template>
          </div>
        </div>
        
        <!-- Mobile Actions -->
        <div class="pt-2 pb-3 border-t border-gray-100 mt-2">
          <div class="flex items-center justify-between">
            <a href="/search" class="flex items-center px-3 py-2 text-gray-700 hover:text-blue-600">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
              Search
            </a>
            <Link href="/hotels" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded transition-colors">
              Book Now
            </Link>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';

// Dynamic navigation data
const navigationItems = ref([]);

// Fetch menu data from the server
const fetchMenuData = async () => {
  try {
    // Use hard-coded fallback for now
    console.log('Using fallback menu data instead of API');
    useStaticFallbackMenu();
    return;
    
    // Original API fetch code - disabled for now
    /*
    const response = await fetch('/api/menu/header');
    console.log('Header menu API URL:', '/api/menu/header');
    const data = await response.json();
    */
    
    // Transform the menu data for our component
    if (data && data.menu && data.menu.items) {
      navigationItems.value = parseMenuItems(data.menu.items);
    } else {
      console.error('Menu data format is not as expected');
      useStaticFallbackMenu();
    }
  } catch (error) {
    console.error('Error fetching menu data:', error);
    useStaticFallbackMenu();
  }
};

// Parse menu items from the server response
const parseMenuItems = (items) => {
  return items.map(item => {
    // Secure URL handling - ensure URLs are properly formatted
    let url = item.url || '#';
    // If URL is not external (doesn't start with http:// or https://) and it doesn't start with a slash,
    // add a slash to make it a relative URL
    if (!url.match(/^(https?:)?\/\//) && !url.startsWith('/') && url !== '#') {
      url = '/' + url;
    }
    
    const parsedItem = {
      title: item.title,
      url: url,
    };
    
    // Check if item has children
    if (item.children && item.children.length > 0) {
      parsedItem.children = parseMenuItems(item.children);
    }
    
    // Check if item is a mega menu
    if (item.is_mega_menu) {
      parsedItem.isMegaMenu = true;
      
      // Parse mega menu content if available
      if (item.mega_menu_content) {
        const megaContent = typeof item.mega_menu_content === 'string' 
          ? JSON.parse(item.mega_menu_content) 
          : item.mega_menu_content;
          
        parsedItem.megaMenuSections = megaContent.map(section => {
          // Transform each section based on its type
          const parsedSection = {
            title: section.title || '',
            type: section.content_type || 'links',
            colSpan: parseInt(section.width?.replace('col-span-', '') || '1')
          };
          
          if (section.content_type === 'links' && section.links) {
            parsedSection.links = section.links.map(link => {
              // Secure URL formatting for links
              let linkUrl = link.url || '#';
              if (!linkUrl.match(/^(https?:)?\/\//) && !linkUrl.startsWith('/') && linkUrl !== '#') {
                linkUrl = '/' + linkUrl;
              }
              
              return {
                title: link.title,
                url: linkUrl,
                featured: link.featured || false
              };
            });
          } else if (section.content_type === 'featured') {
            parsedSection.title = section.featured_title || section.title;
            parsedSection.description = section.featured_description || '';
            
            // Secure URL for featured section
            let featuredUrl = section.featured_url || '#';
            if (!featuredUrl.match(/^(https?:)?\/\//) && !featuredUrl.startsWith('/') && featuredUrl !== '#') {
              featuredUrl = '/' + featuredUrl;
            }
            parsedSection.url = featuredUrl;
            
            // Secure image path for featured image
            parsedSection.image = section.featured_image 
              ? (section.featured_image.startsWith('/') || section.featured_image.match(/^(https?:)?\/\//) 
                 ? section.featured_image 
                 : `/${section.featured_image}`)
              : null;
          } else if (section.content_type === 'html') {
            parsedSection.content = section.html_content || '';
            parsedSection.mobileContent = section.mobile_html_content || section.html_content || '';
          }
          
          return parsedSection;
        });
      }
    }
    
    return parsedItem;
  });
};

// Use a static fallback menu if the API fails
const useStaticFallbackMenu = () => {
  navigationItems.value = [
    {
      title: 'Home',
      url: '/'
    },
    {
      title: 'Hotels',
      url: '/hotels',
      isMegaMenu: true,
      megaMenuSections: [
        {
          title: 'Hotel Types',
          type: 'links',
          colSpan: 1,
          links: [
            { title: 'Luxury Hotels', url: '/hotels?type=luxury' },
            { title: 'Boutique Hotels', url: '/hotels?type=boutique' },
            { title: 'Resorts', url: '/hotels?type=resort' },
            { title: 'Villas', url: '/hotels?type=villa' },
            { title: 'Apartments', url: '/hotels?type=apartment' }
          ]
        },
        {
          title: 'Top Destinations',
          type: 'links',
          colSpan: 1,
          links: [
            { title: 'Istanbul Hotels', url: '/hotels?location=istanbul' },
            { title: 'Antalya Hotels', url: '/hotels?location=antalya' },
            { title: 'Bodrum Hotels', url: '/hotels?location=bodrum' },
            { title: 'Cappadocia Hotels', url: '/hotels?location=cappadocia' },
            { title: 'Izmir Hotels', url: '/hotels?location=izmir' }
          ]
        },
        {
          title: 'Special Offers',
          type: 'links',
          colSpan: 1,
          links: [
            { title: 'Last Minute Deals', url: '/hotels?offer=last-minute', featured: true },
            { title: 'Summer Specials', url: '/hotels?offer=summer' },
            { title: 'Weekend Getaways', url: '/hotels?offer=weekend' },
            { title: 'Family Packages', url: '/hotels?offer=family' },
            { title: 'Honeymoon Suites', url: '/hotels?offer=honeymoon' }
          ]
        },
        {
          title: 'Featured Hotel',
          type: 'featured',
          colSpan: 1,
          title: 'Grand Resort & Spa',
          description: 'Luxury beachfront resort with spectacular views of the Mediterranean Sea',
          url: '/hotels/grand-resort-spa',
          image: '/images/hotels/hotel1.jpg'
        }
      ]
    },
    {
      title: 'Destinations',
      url: '/destinations',
      isMegaMenu: true,
      megaMenuSections: [
        {
          title: 'Regions',
          type: 'links',
          colSpan: 1,
          links: [
            { title: 'Aegean Coast', url: '/destinations?region=aegean' },
            { title: 'Mediterranean Coast', url: '/destinations?region=mediterranean' },
            { title: 'Black Sea Coast', url: '/destinations?region=blacksea' },
            { title: 'Marmara Region', url: '/destinations?region=marmara' },
            { title: 'Central Anatolia', url: '/destinations?region=central' }
          ]
        },
        {
          title: 'Popular Cities',
          type: 'links',
          colSpan: 1,
          links: [
            { title: 'Istanbul', url: '/destinations/istanbul' },
            { title: 'Antalya', url: '/destinations/antalya' },
            { title: 'Bodrum', url: '/destinations/bodrum' },
            { title: 'Izmir', url: '/destinations/izmir' },
            { title: 'Cappadocia', url: '/destinations/cappadocia' }
          ]
        },
        {
          title: 'Explore By Type',
          type: 'links',
          colSpan: 1,
          links: [
            { title: 'Beach Destinations', url: '/destinations?feature=beach' },
            { title: 'Historical Sites', url: '/destinations?feature=historical' },
            { title: 'Natural Wonders', url: '/destinations?feature=natural' },
            { title: 'Cultural Experiences', url: '/destinations?feature=cultural' },
            { title: 'Adventure Destinations', url: '/destinations?feature=adventure' }
          ]
        },
        {
          title: 'Featured Destination',
          type: 'featured',
          colSpan: 1,
          title: 'Magical Cappadocia',
          description: 'Experience the unique landscape of fairy chimneys and hot air balloon rides',
          url: '/destinations/cappadocia',
          image: '/images/destinations/cappadocia.jpg'
        }
      ]
    },
    {
      title: 'About',
      url: '/about',
      children: [
        { title: 'Our Story', url: '/about/story' },
        { title: 'Our Team', url: '/about/team' },
        { title: 'Careers', url: '/about/careers' }
      ]
    },
    {
      title: 'Contact',
      url: '/contact'
    }
  ];
};

// Mobile menu state
const mobileMenuOpen = ref(false);
const openMobileSubmenus = ref([]);

const toggleMobileMenu = () => {
  mobileMenuOpen.value = !mobileMenuOpen.value;
  if (!mobileMenuOpen.value) {
    // Clear submenus when closing the main menu
    openMobileSubmenus.value = [];
  }
};

const toggleMobileSubmenu = (index) => {
  if (openMobileSubmenus.value.includes(index)) {
    openMobileSubmenus.value = openMobileSubmenus.value.filter(i => i !== index);
  } else {
    openMobileSubmenus.value.push(index);
  }
};

// Close menu on wider screens
watch(() => window.innerWidth, (width) => {
  if (width >= 1024 && mobileMenuOpen.value) {
    mobileMenuOpen.value = false;
    openMobileSubmenus.value = [];
  }
});

// Function to load actual menu data from the server
// This would typically be done by fetching it via the parent component
// But for now, we'll use the static data above

// Initialize menu data and setup event listeners
onMounted(() => {
  // Fetch menu data from API
  fetchMenuData();
  
  // Close menu when clicking outside
  document.addEventListener('click', (event) => {
    const header = document.querySelector('header');
    if (header && !header.contains(event.target) && mobileMenuOpen.value) {
      mobileMenuOpen.value = false;
      openMobileSubmenus.value = [];
    }
  });
});
</script>

<style scoped>
/* Mega Menu Styling */
.mega-menu-parent {
  position: static !important;
}

.mega-menu {
  opacity: 0;
  visibility: hidden;
  transition: all 0.2s ease-in-out;
}

.group:hover .mega-menu {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

/* Custom transitions for dropdown menus */
@keyframes fadeDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.group-hover\:block {
  animation: fadeDown 0.2s ease-out;
}
</style>