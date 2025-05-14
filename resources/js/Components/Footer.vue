<template>
  <footer class="bg-gradient-to-br from-gray-900 to-gray-800 text-white">
    <!-- Newsletter Section -->
    <div class="bg-blue-700 py-12">
      <div class="container mx-auto px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8">
          <div class="max-w-lg">
            <h3 class="text-2xl font-bold text-white mb-2">Subscribe to Our Newsletter</h3>
            <p class="text-blue-100">Get exclusive offers, travel tips and updates directly to your inbox.</p>
          </div>
          <div class="w-full lg:w-auto max-w-md">
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-0">
              <input type="email" placeholder="Your email address" class="px-4 py-3 bg-white border-2 border-white focus:border-blue-300 focus:ring-blue-300 focus:outline-none rounded-l-md w-full sm:w-64" />
              <button class="px-5 py-3 bg-blue-900 text-white font-semibold rounded-r-md hover:bg-blue-800 transition-colors">
                Subscribe
              </button>
            </div>
            <p class="mt-2 text-xs text-blue-200">By subscribing, you agree to our Privacy Policy and consent to receive updates.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Footer Content -->
    <div class="container mx-auto px-6 py-12 lg:py-16">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-12 mb-12">
        <!-- Column 1: About -->
        <div class="lg:col-span-2">
          <div class="flex items-center mb-6">
            <img src="/images/logo-light.svg" alt="TravelManager" class="h-10 w-auto" />
          </div>
          <p class="text-gray-300 mb-4 max-w-md">
            TravelManager provides exceptional hotel booking experiences with a wide selection of accommodations, competitive pricing, and seamless booking processes tailored to your travel needs.
          </p>
          <div class="flex space-x-4 mb-8">
            <a href="#" class="text-gray-300 hover:text-white transition-colors" aria-label="Facebook">
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M22.675 0H1.325C.593 0 0 .593 0 1.325v21.351C0 23.407.593 24 1.325 24H12.82v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116c.73 0 1.323-.593 1.323-1.325V1.325C24 .593 23.407 0 22.675 0z"></path>
              </svg>
            </a>
            <a href="#" class="text-gray-300 hover:text-white transition-colors" aria-label="Twitter">
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M23.954 4.569c-.885.389-1.83.654-2.825.775 1.014-.611 1.794-1.574 2.163-2.723-.951.555-2.005.959-3.127 1.184-.896-.959-2.173-1.559-3.591-1.559-2.717 0-4.92 2.203-4.92 4.917 0 .39.045.765.127 1.124C7.691 8.094 4.066 6.13 1.64 3.161c-.427.722-.666 1.561-.666 2.475 0 1.71.87 3.213 2.188 4.096-.807-.026-1.566-.248-2.228-.616v.061c0 2.385 1.693 4.374 3.946 4.827-.413.111-.849.171-1.296.171-.314 0-.615-.03-.916-.086.631 1.953 2.445 3.377 4.604 3.417-1.68 1.319-3.809 2.105-6.102 2.105-.39 0-.779-.023-1.17-.067 2.189 1.394 4.768 2.209 7.557 2.209 9.054 0 14-7.503 14-14.001 0-.21-.005-.429-.015-.636.961-.689 1.8-1.561 2.457-2.549l-.001-.001z"></path>
              </svg>
            </a>
            <a href="#" class="text-gray-300 hover:text-white transition-colors" aria-label="Instagram">
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"></path>
              </svg>
            </a>
            <a href="#" class="text-gray-300 hover:text-white transition-colors" aria-label="YouTube">
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"></path>
              </svg>
            </a>
          </div>
          <div>
            <h4 class="text-white font-semibold mb-4">Contact Us</h4>
            <div class="flex items-start space-x-3 mb-2">
              <svg class="w-5 h-5 text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
              <span class="text-gray-300">123 Travel Street, Destination City, 34567</span>
            </div>
            <div class="flex items-start space-x-3 mb-2">
              <svg class="w-5 h-5 text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
              </svg>
              <span class="text-gray-300">+90 212 555 1234</span>
            </div>
            <div class="flex items-start space-x-3">
              <svg class="w-5 h-5 text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
              </svg>
              <span class="text-gray-300">info@travelmanager.com</span>
            </div>
          </div>
        </div>

        <!-- Column 2: Quick Links -->
        <div>
          <h4 class="text-white text-lg font-semibold mb-6">Quick Links</h4>
          <ul class="space-y-4">
            <li v-for="(link, index) in quickLinks" :key="`quick-${index}`">
              <Link :href="link.url" class="text-gray-300 hover:text-white transition-colors">
                {{ link.title }}
              </Link>
            </li>
          </ul>
        </div>

        <!-- Column 3: Destinations -->
        <div>
          <h4 class="text-white text-lg font-semibold mb-6">Popular Destinations</h4>
          <ul class="space-y-4">
            <li v-for="(destination, index) in destinations" :key="`dest-${index}`">
              <Link :href="destination.url" class="text-gray-300 hover:text-white transition-colors">
                {{ destination.title }}
              </Link>
            </li>
          </ul>
        </div>

        <!-- Column 4: Travel Resources -->
        <div>
          <h4 class="text-white text-lg font-semibold mb-6">Travel Resources</h4>
          <ul class="space-y-4">
            <li v-for="(resource, index) in resources" :key="`res-${index}`">
              <Link :href="resource.url" class="text-gray-300 hover:text-white transition-colors">
                {{ resource.title }}
              </Link>
            </li>
          </ul>
        </div>
      </div>

      <div class="pt-8 mt-8 border-t border-gray-700">
        <div class="flex flex-col md:flex-row md:justify-between items-center">
          <div class="mb-6 md:mb-0">
            <p class="text-gray-400 text-sm">&copy; {{ new Date().getFullYear() }} TravelManager. All rights reserved.</p>
          </div>
          <div class="flex space-x-6">
            <Link v-for="(legal, index) in legalLinks" :key="`legal-${index}`" :href="legal.url" class="text-sm text-gray-400 hover:text-white">
              {{ legal.title }}
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Bar -->
    <div class="bg-black py-4">
      <div class="container mx-auto px-6 flex flex-col sm:flex-row justify-between items-center">
        <div class="text-xs text-gray-400 mb-4 sm:mb-0">
          Designed and developed with ❤️ by TravelManager Team
        </div>
        <div class="flex items-center">
          <div class="text-xs text-gray-400 mr-4">Payment Methods:</div>
          <div class="flex space-x-3">
            <span v-for="(method, index) in paymentMethods" :key="index" class="w-8 h-5 bg-white rounded flex items-center justify-center">
              <img :src="method.icon" :alt="method.name" class="h-4 w-auto" />
            </span>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';

// Footer menu structure
const quickLinks = ref([
  { title: 'Home', url: '/' },
  { title: 'About Us', url: '/about' },
  { title: 'Hotels', url: '/hotels' },
  { title: 'Special Offers', url: '/special-offers' },
  { title: 'Contact Us', url: '/contact' }
]);

const destinations = ref([
  { title: 'Istanbul', url: '/destinations/istanbul' },
  { title: 'Antalya', url: '/destinations/antalya' },
  { title: 'Bodrum', url: '/destinations/bodrum' },
  { title: 'Cappadocia', url: '/destinations/cappadocia' },
  { title: 'Izmir', url: '/destinations/izmir' }
]);

const resources = ref([
  { title: 'Travel Tips', url: '/travel-tips' },
  { title: 'FAQs', url: '/faq' },
  { title: 'Travel Blog', url: '/blog' },
  { title: 'Booking Guide', url: '/booking-guide' },
  { title: 'Payment Options', url: '/payment-options' }
]);

const legalLinks = ref([
  { title: 'Privacy Policy', url: '/privacy' },
  { title: 'Terms of Service', url: '/terms' },
  { title: 'Cookie Policy', url: '/cookies' },
  { title: 'Refund Policy', url: '/refunds' }
]);

const paymentMethods = ref([
  { name: 'Visa', icon: '/images/payment/visa.svg' },
  { name: 'Mastercard', icon: '/images/payment/mastercard.svg' },
  { name: 'PayPal', icon: '/images/payment/paypal.svg' },
  { name: 'Apple Pay', icon: '/images/payment/apple-pay.svg' }
]);

// Function to fetch dynamic footer data
const fetchFooterData = async () => {
  try {
    // Skip API call for now and use static data
    console.log('Using static footer data instead of API');
    return;
    
    // Original API fetch code - disabled for now
    /*
    const response = await fetch('/api/menu/footer');
    console.log('Footer menu API URL:', '/api/menu/footer');
    const data = await response.json();
    */
    
    if (data.success && data.menu && data.menu.items) {
      const footerMenus = data.menu.items;
      
      // Parse footer menus into the appropriate sections
      footerMenus.forEach(menu => {
        if (menu.title.toLowerCase().includes('quick') || menu.slug === 'quick-links') {
          quickLinks.value = parseMenuLinks(menu.children || []);
        } else if (menu.title.toLowerCase().includes('destination')) {
          destinations.value = parseMenuLinks(menu.children || []);
        } else if (menu.title.toLowerCase().includes('resource')) {
          resources.value = parseMenuLinks(menu.children || []);
        } else if (menu.title.toLowerCase().includes('legal')) {
          legalLinks.value = parseMenuLinks(menu.children || []);
        }
      });
    }
  } catch (error) {
    console.error('Error fetching footer menu data:', error);
    // Fallback to static data is already in place
  }
};

// Helper function to parse menu items to the format we need
const parseMenuLinks = (items) => {
  return items.map(item => ({
    title: item.title,
    url: safeUrl(item.url || item.link || '#')
  }));
};

// Helper function to safely parse URLs
const safeUrl = (url) => {
  if (!url || url === '#') return '#';
  if (!url.match(/^(https?:)?\/\//) && !url.startsWith('/')) {
    return '/' + url;
  }
  return url;
};

onMounted(() => {
  // Fetch dynamic footer data on mount
  fetchFooterData();
});
</script>

<style scoped>
/* Any footer specific styles can go here */
</style>