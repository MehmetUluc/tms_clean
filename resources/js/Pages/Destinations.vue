<template>
  <Head title="Destinations" />
  <GuestLayout>
    <div class="bg-gray-50 py-10">
      <div class="container mx-auto px-4">
        <!-- Page header -->
        <div class="text-center mb-12">
          <h1 class="text-3xl font-bold text-gray-900 md:text-4xl mb-3">Explore Destinations</h1>
          <p class="text-gray-600 max-w-2xl mx-auto">Discover amazing places to stay and experience authentic Turkish hospitality. From historic cities to seaside resorts, find your perfect destination.</p>
        </div>

        <!-- Search and filter -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-8">
          <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-grow">
              <div class="relative">
                <input
                  type="text"
                  class="w-full border-gray-300 rounded-md pl-10 pr-4 py-2 focus:border-blue-500 focus:ring-blue-500"
                  placeholder="Search destinations..."
                  v-model="search"
                />
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </div>
              </div>
            </div>
            <div class="flex-shrink-0 w-full md:w-auto">
              <select v-model="regionFilter" class="w-full border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500">
                <option value="all">All regions</option>
                <option value="aegean">Aegean Coast</option>
                <option value="mediterranean">Mediterranean Coast</option>
                <option value="blacksea">Black Sea Coast</option>
                <option value="central">Central Anatolia</option>
                <option value="marmara">Marmara Region</option>
                <option value="eastern">Eastern Anatolia</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Featured destinations -->
        <div class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-6">Featured Destinations</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div 
              v-for="destination in featuredDestinations" 
              :key="destination.id"
              class="relative group rounded-lg overflow-hidden shadow-sm transition-transform duration-300 hover:-translate-y-1 hover:shadow-md"
            >
              <Link :href="route('destinations.show', destination.id)" class="block">
                <div class="aspect-w-16 aspect-h-9">
                  <img :src="destination.image" :alt="destination.name" class="w-full h-full object-cover" />
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
                  <h3 class="text-xl font-bold mb-1">{{ destination.name }}</h3>
                  <div class="flex items-center text-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    {{ destination.hotelCount }} hotels
                  </div>
                </div>
                <div class="absolute top-3 right-3 bg-white/90 text-blue-600 font-bold px-2 py-1 rounded text-sm">
                  {{ destination.region }}
                </div>
              </Link>
            </div>
          </div>
        </div>

        <!-- All destinations grid -->
        <div>
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">All Destinations</h2>
            <div class="flex items-center space-x-2">
              <span class="text-sm text-gray-700">View:</span>
              <button 
                @click="viewMode = 'grid'" 
                class="p-1 rounded-md"
                :class="viewMode === 'grid' ? 'bg-gray-200' : 'hover:bg-gray-100'"
              >
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
              </button>
              <button 
                @click="viewMode = 'list'" 
                class="p-1 rounded-md"
                :class="viewMode === 'list' ? 'bg-gray-200' : 'hover:bg-gray-100'"
              >
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
              </button>
            </div>
          </div>

          <!-- Grid view -->
          <div v-if="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
            <Link 
              v-for="destination in filteredDestinations" 
              :key="destination.id"
              :href="route('destinations.show', destination.id)"
              class="block bg-white rounded-lg overflow-hidden shadow-sm transition-transform duration-300 hover:-translate-y-1 hover:shadow-md"
            >
              <div class="aspect-w-3 aspect-h-2">
                <img :src="destination.image" :alt="destination.name" class="w-full h-full object-cover" />
              </div>
              <div class="p-4">
                <h3 class="font-bold text-gray-900">{{ destination.name }}</h3>
                <p class="text-gray-600 text-sm mb-2">{{ destination.region }}</p>
                <div class="flex items-center text-xs text-gray-500">
                  <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                  {{ destination.hotelCount }} hotels
                </div>
              </div>
            </Link>
          </div>

          <!-- List view -->
          <div v-else class="space-y-4 mb-8">
            <Link 
              v-for="destination in filteredDestinations" 
              :key="destination.id"
              :href="route('destinations.show', destination.id)"
              class="flex bg-white rounded-lg overflow-hidden shadow-sm transition-transform duration-300 hover:-translate-y-1 hover:shadow-md"
            >
              <div class="w-1/4 h-32">
                <img :src="destination.image" :alt="destination.name" class="w-full h-full object-cover" />
              </div>
              <div class="w-3/4 p-4 flex flex-col">
                <h3 class="font-bold text-gray-900 mb-1">{{ destination.name }}</h3>
                <p class="text-gray-600 text-sm mb-2">{{ destination.region }}</p>
                <p class="text-gray-700 text-sm mb-auto line-clamp-2">{{ destination.description }}</p>
                <div class="flex items-center justify-between mt-2">
                  <div class="text-sm text-gray-500">
                    <span class="font-medium">{{ destination.hotelCount }}</span> hotels
                  </div>
                  <div class="text-blue-600 text-sm font-medium">
                    Explore
                    <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                  </div>
                </div>
              </div>
            </Link>
          </div>

          <!-- Pagination -->
          <div class="flex justify-center">
            <nav class="flex items-center">
              <button 
                class="px-2 py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                :disabled="currentPage === 1"
                @click="currentPage--"
              >
                Previous
              </button>
              <div class="flex mx-2">
                <button 
                  v-for="page in totalPages" 
                  :key="page" 
                  @click="currentPage = page" 
                  :class="[
                    'px-3 py-1 mx-1 rounded-md',
                    currentPage === page ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-50 border border-gray-300'
                  ]"
                >
                  {{ page }}
                </button>
              </div>
              <button 
                class="px-2 py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                :disabled="currentPage === totalPages"
                @click="currentPage++"
              >
                Next
              </button>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </GuestLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const search = ref('');
const regionFilter = ref('all');
const viewMode = ref('grid');
const currentPage = ref(1);
const itemsPerPage = 16;

// Sample featured destinations
const featuredDestinations = [
  {
    id: 1,
    name: 'Istanbul',
    region: 'Marmara Region',
    image: '/images/destinations/istanbul.jpg',
    hotelCount: 1245
  },
  {
    id: 2,
    name: 'Antalya',
    region: 'Mediterranean Coast',
    image: '/images/destinations/antalya.jpg',
    hotelCount: 873
  },
  {
    id: 3,
    name: 'Cappadocia',
    region: 'Central Anatolia',
    image: '/images/destinations/cappadocia.jpg',
    hotelCount: 342
  }
];

// Sample all destinations
const allDestinations = ref([
  {
    id: 1,
    name: 'Istanbul',
    region: 'Marmara Region',
    regionKey: 'marmara',
    image: '/images/destinations/istanbul.jpg',
    description: 'Explore the fascinating cultural fusion of East and West in Istanbul, a city straddling two continents with a rich historical heritage.',
    hotelCount: 1245
  },
  {
    id: 2,
    name: 'Antalya',
    region: 'Mediterranean Coast',
    regionKey: 'mediterranean',
    image: '/images/destinations/antalya.jpg',
    description: 'Enjoy the stunning beaches and ancient ruins of Antalya, a beautiful coastal city on the Mediterranean Turkish Riviera.',
    hotelCount: 873
  },
  {
    id: 3,
    name: 'Cappadocia',
    region: 'Central Anatolia',
    regionKey: 'central',
    image: '/images/destinations/cappadocia.jpg',
    description: 'Discover the otherworldly landscapes of Cappadocia with its famous fairy chimneys, hot air balloon rides, and cave dwellings.',
    hotelCount: 342
  },
  {
    id: 4,
    name: 'Bodrum',
    region: 'Aegean Coast',
    regionKey: 'aegean',
    image: '/images/destinations/bodrum.jpg',
    description: 'Experience the vibrant nightlife and beautiful beaches of Bodrum, a popular resort town on the Aegean Sea.',
    hotelCount: 521
  },
  {
    id: 5,
    name: 'Izmir',
    region: 'Aegean Coast',
    regionKey: 'aegean',
    image: '/images/destinations/izmir.jpg',
    description: 'Visit Izmir, Turkey\'s third-largest city, known for its modern atmosphere, seaside promenade, and proximity to ancient Ephesus.',
    hotelCount: 456
  },
  {
    id: 6,
    name: 'Fethiye',
    region: 'Mediterranean Coast',
    regionKey: 'mediterranean',
    image: '/images/destinations/fethiye.jpg',
    description: 'Relax in Fethiye with its marina, beautiful beaches, and the famous Blue Lagoon at nearby Ölüdeniz.',
    hotelCount: 328
  },
  {
    id: 7,
    name: 'Trabzon',
    region: 'Black Sea Coast',
    regionKey: 'blacksea',
    image: '/images/destinations/trabzon.jpg',
    description: 'Discover the lush green landscapes of Trabzon on the Black Sea coast, home to the historic Sumela Monastery.',
    hotelCount: 187
  },
  {
    id: 8,
    name: 'Konya',
    region: 'Central Anatolia',
    regionKey: 'central',
    image: '/images/destinations/konya.jpg',
    description: 'Experience the spiritual heritage of Konya, the city of the whirling dervishes and Rumi\'s final resting place.',
    hotelCount: 132
  },
  {
    id: 9,
    name: 'Bursa',
    region: 'Marmara Region',
    regionKey: 'marmara',
    image: '/images/destinations/bursa.jpg',
    description: 'Visit Bursa, the first capital of the Ottoman Empire, known for its historic architecture and thermal springs.',
    hotelCount: 245
  },
  {
    id: 10,
    name: 'Marmaris',
    region: 'Aegean Coast',
    regionKey: 'aegean',
    image: '/images/destinations/marmaris.jpg',
    description: 'Enjoy the lively atmosphere of Marmaris, a popular resort town with beautiful beaches and a vibrant marina.',
    hotelCount: 387
  },
  {
    id: 11,
    name: 'Alanya',
    region: 'Mediterranean Coast',
    regionKey: 'mediterranean',
    image: '/images/destinations/alanya.jpg',
    description: 'Discover Alanya with its iconic castle, beautiful beaches, and vibrant nightlife on the Mediterranean coast.',
    hotelCount: 421
  },
  {
    id: 12,
    name: 'Ankara',
    region: 'Central Anatolia',
    regionKey: 'central',
    image: '/images/destinations/ankara.jpg',
    description: 'Explore Turkey\'s capital city Ankara, home to important museums, government buildings, and Atatürk\'s mausoleum.',
    hotelCount: 312
  },
  {
    id: 13,
    name: 'Erzurum',
    region: 'Eastern Anatolia',
    regionKey: 'eastern',
    image: '/images/destinations/erzurum.jpg',
    description: 'Visit Erzurum, an important center in Eastern Turkey known for winter sports and historic architecture.',
    hotelCount: 98
  },
  {
    id: 14,
    name: 'Kayseri',
    region: 'Central Anatolia',
    regionKey: 'central',
    image: '/images/destinations/kayseri.jpg',
    description: 'Discover Kayseri, a city at the base of Mount Erciyes, known for its historic sites and as a gateway to Cappadocia.',
    hotelCount: 143
  },
  {
    id: 15,
    name: 'Canakkale',
    region: 'Marmara Region',
    regionKey: 'marmara',
    image: '/images/destinations/canakkale.jpg',
    description: 'Explore Canakkale, the gateway to the ancient city of Troy and the Gallipoli battlefields.',
    hotelCount: 156
  },
  {
    id: 16,
    name: 'Datca',
    region: 'Aegean Coast',
    regionKey: 'aegean',
    image: '/images/destinations/datca.jpg',
    description: 'Relax in the unspoiled beauty of Datca Peninsula with its pristine beaches and traditional villages.',
    hotelCount: 87
  },
  {
    id: 17,
    name: 'Rize',
    region: 'Black Sea Coast',
    regionKey: 'blacksea',
    image: '/images/destinations/rize.jpg',
    description: 'Visit Rize, famous for its tea plantations and beautiful green landscapes on the Black Sea coast.',
    hotelCount: 76
  },
  {
    id: 18,
    name: 'Cesme',
    region: 'Aegean Coast',
    regionKey: 'aegean',
    image: '/images/destinations/cesme.jpg',
    description: 'Enjoy the thermal springs and beautiful beaches of Cesme, a popular resort town near Izmir.',
    hotelCount: 213
  },
  {
    id: 19,
    name: 'Gaziantep',
    region: 'Eastern Anatolia',
    regionKey: 'eastern',
    image: '/images/destinations/gaziantep.jpg',
    description: 'Discover Gaziantep, known for its amazing cuisine, especially baklava, and rich cultural heritage.',
    hotelCount: 124
  },
  {
    id: 20,
    name: 'Kusadasi',
    region: 'Aegean Coast',
    regionKey: 'aegean',
    image: '/images/destinations/kusadasi.jpg',
    description: 'Visit Kusadasi, a popular cruise port and beach resort town close to the ancient city of Ephesus.',
    hotelCount: 287
  }
]);

// Filtered destinations based on search and region filter
const filteredDestinations = computed(() => {
  let results = [...allDestinations.value];
  
  if (search.value) {
    const searchLower = search.value.toLowerCase();
    results = results.filter(destination => 
      destination.name.toLowerCase().includes(searchLower) ||
      destination.region.toLowerCase().includes(searchLower) ||
      destination.description.toLowerCase().includes(searchLower)
    );
  }
  
  if (regionFilter.value !== 'all') {
    results = results.filter(destination => destination.regionKey === regionFilter.value);
  }
  
  return results;
});

// Pagination
const totalPages = computed(() => {
  return Math.ceil(filteredDestinations.value.length / itemsPerPage);
});

const paginatedDestinations = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return filteredDestinations.value.slice(start, end);
});

// Mock route function (would be provided by Inertia in a real app)
const route = (name, params) => {
  if (name === 'destinations.show') {
    return `/destinations/${params}`;
  }
  return '/';
};
</script>