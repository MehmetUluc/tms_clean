<template>
  <Head title="Hotels" />
  <GuestLayout>
    <div class="bg-gray-50 py-10">
      <!-- Hero section with parallax effect -->
      <div class="relative h-80 mb-10 overflow-hidden">
        <div class="absolute inset-0 z-0 overflow-hidden">
          <img src="/images/hotels/hotel1.jpg" alt="Luxury hotel" class="w-full h-full object-cover filter brightness-[0.7] parallax-bg" />
        </div>
        <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-transparent z-10"></div>
        <div class="container mx-auto px-4 h-full flex items-center relative z-20">
          <div class="max-w-2xl text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Find Your Perfect Stay</h1>
            <p class="text-xl opacity-90 mb-6">Discover exceptional accommodations tailored to your desires</p>
            <div class="flex flex-wrap gap-3">
              <span class="inline-flex items-center bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full text-sm">
                <span class="mr-1">⭐</span> Premium Selection
              </span>
              <span class="inline-flex items-center bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full text-sm">
                <span class="mr-1">🔒</span> Secure Booking
              </span>
              <span class="inline-flex items-center bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full text-sm">
                <span class="mr-1">💰</span> Best Price Guarantee
              </span>
            </div>
          </div>
        </div>
      </div>
      
      <div class="container mx-auto px-4 animate-fade-in">
        <!-- Breadcrumbs -->
        <div class="text-sm text-gray-600 mb-6">
          <Link href="/" class="hover:text-blue-600">Home</Link>
          <span class="mx-2">›</span>
          <span class="text-gray-900">Hotels</span>
        </div>
        
        <!-- Active filters pills -->
        <div v-if="hasActiveFilters" class="mb-6">
          <div class="flex flex-wrap gap-2 items-center">
            <span class="text-sm text-gray-600">Active filters:</span>
            <div v-if="filters.minPrice || filters.maxPrice" 
                 class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full flex items-center">
              Price: ${{ filters.minPrice || '0' }} - ${{ filters.maxPrice || '1000+' }}
              <button @click="clearPriceFilter" class="ml-1 text-blue-800 hover:text-blue-900">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            <div v-for="rating in filters.rating" :key="'rating-'+rating" 
                 class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full flex items-center">
              {{ rating }} Stars
              <button @click="removeRatingFilter(rating)" class="ml-1 text-blue-800 hover:text-blue-900">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            <div v-for="amenity in filters.amenities" :key="'amenity-'+amenity" 
                 class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full flex items-center">
              {{ amenity.charAt(0).toUpperCase() + amenity.slice(1) }}
              <button @click="removeAmenityFilter(amenity)" class="ml-1 text-blue-800 hover:text-blue-900">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            <div v-for="type in filters.propertyType" :key="'type-'+type" 
                 class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full flex items-center">
              {{ type.charAt(0).toUpperCase() + type.slice(1) }}
              <button @click="removePropertyTypeFilter(type)" class="ml-1 text-blue-800 hover:text-blue-900">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            <button @click="resetFilters" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
              Clear all filters
            </button>
          </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
          <!-- Filters sidebar -->
          <div class="w-full lg:w-1/4 bg-white rounded-lg shadow-sm p-0 sticky top-4 self-start max-h-screen overflow-y-auto scrollbar-thin">
            <!-- Title bar -->
            <div class="bg-blue-600 px-6 py-4 text-white rounded-t-lg">
              <h3 class="font-bold text-lg">Filters</h3>
              <p class="text-blue-100 text-sm">Find your perfect accommodation</p>
            </div>
            
            <!-- Filter sections -->
            <div class="p-6">
              <div class="mb-6">
                <h3 class="font-medium text-base mb-3 text-gray-900 flex items-center">
                  <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                  Search
                </h3>
                <div class="relative">
                  <input 
                    type="text" 
                    class="w-full border-gray-300 rounded-lg pl-10 pr-4 py-2 focus:border-blue-500 focus:ring-blue-500 bg-gray-50" 
                    placeholder="Search hotels..."
                    v-model="search"
                  />
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                  </div>
                </div>
              </div>
              
              <!-- Divider -->
              <div class="border-t border-gray-100 -mx-6 my-6"></div>

              <div class="mb-6">
                <h3 class="font-medium text-lg mb-3 text-gray-900">Price Range</h3>
                <div class="mb-2">
                  <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>${{ filters.minPrice || 0 }}</span>
                    <span>${{ filters.maxPrice || 1000 }}+</span>
                  </div>
                  <div class="relative py-1">
                    <div class="h-1 bg-gray-200 rounded-full">
                      <div 
                        class="absolute h-1 bg-blue-600 rounded-full" 
                        :style="{
                          left: ((filters.minPrice || 0) / 10) + '%',
                          right: (100 - ((filters.maxPrice || 1000) / 10)) + '%'
                        }"
                      ></div>
                    </div>
                    <input 
                      type="range" 
                      min="0" 
                      max="1000" 
                      step="50"
                      v-model="filters.minPrice"
                      class="absolute w-full h-1 opacity-0 cursor-pointer z-10"
                    />
                    <input 
                      type="range" 
                      min="0" 
                      max="1000" 
                      step="50"
                      v-model="filters.maxPrice"
                      class="absolute w-full h-1 opacity-0 cursor-pointer z-10"
                    />
                  </div>
                </div>
                <div class="flex items-center space-x-3 mt-4">
                  <input 
                    type="number" 
                    class="w-full border-gray-300 rounded-md" 
                    placeholder="Min"
                    v-model="filters.minPrice"
                  />
                  <span class="text-gray-500">-</span>
                  <input 
                    type="number" 
                    class="w-full border-gray-300 rounded-md" 
                    placeholder="Max"
                    v-model="filters.maxPrice"
                  />
                </div>
              </div>

              <div class="mb-6">
                <h3 class="font-medium text-base mb-3 text-gray-900 flex items-center">
                  <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                  </svg>
                  Rating
                </h3>
                <div class="grid grid-cols-1 gap-2">
                  <label class="relative flex items-center p-3 bg-white rounded-lg border border-gray-200 cursor-pointer hover:border-blue-300 transition-all duration-200 group"
                         :class="{ 'border-blue-500 bg-blue-50': filters.rating.includes('5') }">
                    <div class="flex-shrink-0 w-5 h-5 relative">
                      <input type="checkbox" class="absolute w-5 h-5 opacity-0 cursor-pointer z-10" v-model="filters.rating" value="5" />
                      <div class="w-5 h-5 border rounded" 
                           :class="filters.rating.includes('5') ? 'bg-blue-500 border-blue-500' : 'border-gray-300'"></div>
                      <svg class="absolute top-0.5 left-0.5 w-4 h-4 text-white pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" 
                           :class="filters.rating.includes('5') ? 'opacity-100' : 'opacity-0'">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                    </div>
                    <div class="ml-3 flex items-center">
                      <div class="flex">
                        <span v-for="i in 5" :key="i" class="text-yellow-400 text-lg">★</span>
                      </div>
                      <span class="ml-2 text-sm text-gray-600 group-hover:text-gray-900 transition-colors">5 Stars</span>
                    </div>
                  </label>
                  
                  <label class="relative flex items-center p-3 bg-white rounded-lg border border-gray-200 cursor-pointer hover:border-blue-300 transition-all duration-200 group"
                         :class="{ 'border-blue-500 bg-blue-50': filters.rating.includes('4') }">
                    <div class="flex-shrink-0 w-5 h-5 relative">
                      <input type="checkbox" class="absolute w-5 h-5 opacity-0 cursor-pointer z-10" v-model="filters.rating" value="4" />
                      <div class="w-5 h-5 border rounded" 
                           :class="filters.rating.includes('4') ? 'bg-blue-500 border-blue-500' : 'border-gray-300'"></div>
                      <svg class="absolute top-0.5 left-0.5 w-4 h-4 text-white pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" 
                           :class="filters.rating.includes('4') ? 'opacity-100' : 'opacity-0'">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                    </div>
                    <div class="ml-3 flex items-center">
                      <div class="flex">
                        <span v-for="i in 4" :key="i" class="text-yellow-400 text-lg">★</span>
                        <span class="text-gray-300 text-lg">★</span>
                      </div>
                      <span class="ml-2 text-sm text-gray-600 group-hover:text-gray-900 transition-colors">4 Stars</span>
                    </div>
                  </label>
                  
                  <label class="relative flex items-center p-3 bg-white rounded-lg border border-gray-200 cursor-pointer hover:border-blue-300 transition-all duration-200 group"
                         :class="{ 'border-blue-500 bg-blue-50': filters.rating.includes('3') }">
                    <div class="flex-shrink-0 w-5 h-5 relative">
                      <input type="checkbox" class="absolute w-5 h-5 opacity-0 cursor-pointer z-10" v-model="filters.rating" value="3" />
                      <div class="w-5 h-5 border rounded" 
                           :class="filters.rating.includes('3') ? 'bg-blue-500 border-blue-500' : 'border-gray-300'"></div>
                      <svg class="absolute top-0.5 left-0.5 w-4 h-4 text-white pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" 
                           :class="filters.rating.includes('3') ? 'opacity-100' : 'opacity-0'">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                    </div>
                    <div class="ml-3 flex items-center">
                      <div class="flex">
                        <span v-for="i in 3" :key="i" class="text-yellow-400 text-lg">★</span>
                        <span v-for="i in 2" :key="i + 3" class="text-gray-300 text-lg">★</span>
                      </div>
                      <span class="ml-2 text-sm text-gray-600 group-hover:text-gray-900 transition-colors">3 Stars</span>
                    </div>
                  </label>
                  
                  <label class="relative flex items-center p-3 bg-white rounded-lg border border-gray-200 cursor-pointer hover:border-blue-300 transition-all duration-200 group"
                         :class="{ 'border-blue-500 bg-blue-50': filters.rating.includes('2') }">
                    <div class="flex-shrink-0 w-5 h-5 relative">
                      <input type="checkbox" class="absolute w-5 h-5 opacity-0 cursor-pointer z-10" v-model="filters.rating" value="2" />
                      <div class="w-5 h-5 border rounded" 
                           :class="filters.rating.includes('2') ? 'bg-blue-500 border-blue-500' : 'border-gray-300'"></div>
                      <svg class="absolute top-0.5 left-0.5 w-4 h-4 text-white pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" 
                           :class="filters.rating.includes('2') ? 'opacity-100' : 'opacity-0'">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                    </div>
                    <div class="ml-3 flex items-center">
                      <div class="flex">
                        <span v-for="i in 2" :key="i" class="text-yellow-400 text-lg">★</span>
                        <span v-for="i in 3" :key="i + 2" class="text-gray-300 text-lg">★</span>
                      </div>
                      <span class="ml-2 text-sm text-gray-600 group-hover:text-gray-900 transition-colors">2 Stars</span>
                    </div>
                  </label>
                </div>
              </div>

              <div class="mb-6">
                <h3 class="font-medium text-base mb-3 text-gray-900 flex items-center">
                  <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                  </svg>
                  Amenities
                </h3>
                <div class="grid grid-cols-2 gap-2">
                  <label class="relative flex items-center p-3 bg-white rounded-lg border border-gray-200 cursor-pointer hover:border-blue-300 transition-all duration-200 group"
                         :class="{ 'border-blue-500 bg-blue-50': filters.amenities.includes('wifi') }">
                    <div class="flex-shrink-0 w-5 h-5 relative">
                      <input type="checkbox" class="absolute w-5 h-5 opacity-0 cursor-pointer z-10" v-model="filters.amenities" value="wifi" />
                      <div class="w-5 h-5 border rounded" 
                           :class="filters.amenities.includes('wifi') ? 'bg-blue-500 border-blue-500' : 'border-gray-300'"></div>
                      <svg class="absolute top-0.5 left-0.5 w-4 h-4 text-white pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" 
                           :class="filters.amenities.includes('wifi') ? 'opacity-100' : 'opacity-0'">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                    </div>
                    <span class="ml-3 text-sm text-gray-600 group-hover:text-gray-900 transition-colors flex items-center">
                      <span class="mr-1.5 text-blue-400">📶</span> WiFi
                    </span>
                  </label>
                  
                  <label class="relative flex items-center p-3 bg-white rounded-lg border border-gray-200 cursor-pointer hover:border-blue-300 transition-all duration-200 group"
                         :class="{ 'border-blue-500 bg-blue-50': filters.amenities.includes('pool') }">
                    <div class="flex-shrink-0 w-5 h-5 relative">
                      <input type="checkbox" class="absolute w-5 h-5 opacity-0 cursor-pointer z-10" v-model="filters.amenities" value="pool" />
                      <div class="w-5 h-5 border rounded" 
                           :class="filters.amenities.includes('pool') ? 'bg-blue-500 border-blue-500' : 'border-gray-300'"></div>
                      <svg class="absolute top-0.5 left-0.5 w-4 h-4 text-white pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" 
                           :class="filters.amenities.includes('pool') ? 'opacity-100' : 'opacity-0'">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                    </div>
                    <span class="ml-3 text-sm text-gray-600 group-hover:text-gray-900 transition-colors flex items-center">
                      <span class="mr-1.5 text-blue-400">🏊</span> Pool
                    </span>
                  </label>
                  
                  <label class="relative flex items-center p-3 bg-white rounded-lg border border-gray-200 cursor-pointer hover:border-blue-300 transition-all duration-200 group"
                         :class="{ 'border-blue-500 bg-blue-50': filters.amenities.includes('spa') }">
                    <div class="flex-shrink-0 w-5 h-5 relative">
                      <input type="checkbox" class="absolute w-5 h-5 opacity-0 cursor-pointer z-10" v-model="filters.amenities" value="spa" />
                      <div class="w-5 h-5 border rounded" 
                           :class="filters.amenities.includes('spa') ? 'bg-blue-500 border-blue-500' : 'border-gray-300'"></div>
                      <svg class="absolute top-0.5 left-0.5 w-4 h-4 text-white pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" 
                           :class="filters.amenities.includes('spa') ? 'opacity-100' : 'opacity-0'">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                    </div>
                    <span class="ml-3 text-sm text-gray-600 group-hover:text-gray-900 transition-colors flex items-center">
                      <span class="mr-1.5 text-blue-400">💆</span> Spa
                    </span>
                  </label>
                  
                  <label class="relative flex items-center p-3 bg-white rounded-lg border border-gray-200 cursor-pointer hover:border-blue-300 transition-all duration-200 group"
                         :class="{ 'border-blue-500 bg-blue-50': filters.amenities.includes('restaurant') }">
                    <div class="flex-shrink-0 w-5 h-5 relative">
                      <input type="checkbox" class="absolute w-5 h-5 opacity-0 cursor-pointer z-10" v-model="filters.amenities" value="restaurant" />
                      <div class="w-5 h-5 border rounded" 
                           :class="filters.amenities.includes('restaurant') ? 'bg-blue-500 border-blue-500' : 'border-gray-300'"></div>
                      <svg class="absolute top-0.5 left-0.5 w-4 h-4 text-white pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" 
                           :class="filters.amenities.includes('restaurant') ? 'opacity-100' : 'opacity-0'">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                    </div>
                    <span class="ml-3 text-sm text-gray-600 group-hover:text-gray-900 transition-colors flex items-center">
                      <span class="mr-1.5 text-blue-400">🍽️</span> Restaurant
                    </span>
                  </label>
                  
                  <label class="relative flex items-center p-3 bg-white rounded-lg border border-gray-200 cursor-pointer hover:border-blue-300 transition-all duration-200 group"
                         :class="{ 'border-blue-500 bg-blue-50': filters.amenities.includes('gym') }">
                    <div class="flex-shrink-0 w-5 h-5 relative">
                      <input type="checkbox" class="absolute w-5 h-5 opacity-0 cursor-pointer z-10" v-model="filters.amenities" value="gym" />
                      <div class="w-5 h-5 border rounded" 
                           :class="filters.amenities.includes('gym') ? 'bg-blue-500 border-blue-500' : 'border-gray-300'"></div>
                      <svg class="absolute top-0.5 left-0.5 w-4 h-4 text-white pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" 
                           :class="filters.amenities.includes('gym') ? 'opacity-100' : 'opacity-0'">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                    </div>
                    <span class="ml-3 text-sm text-gray-600 group-hover:text-gray-900 transition-colors flex items-center">
                      <span class="mr-1.5 text-blue-400">💪</span> Fitness
                    </span>
                  </label>
                  
                  <label class="relative flex items-center p-3 bg-white rounded-lg border border-gray-200 cursor-pointer hover:border-blue-300 transition-all duration-200 group"
                         :class="{ 'border-blue-500 bg-blue-50': filters.amenities.includes('parking') }">
                    <div class="flex-shrink-0 w-5 h-5 relative">
                      <input type="checkbox" class="absolute w-5 h-5 opacity-0 cursor-pointer z-10" v-model="filters.amenities" value="parking" />
                      <div class="w-5 h-5 border rounded" 
                           :class="filters.amenities.includes('parking') ? 'bg-blue-500 border-blue-500' : 'border-gray-300'"></div>
                      <svg class="absolute top-0.5 left-0.5 w-4 h-4 text-white pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" 
                           :class="filters.amenities.includes('parking') ? 'opacity-100' : 'opacity-0'">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                    </div>
                    <span class="ml-3 text-sm text-gray-600 group-hover:text-gray-900 transition-colors flex items-center">
                      <span class="mr-1.5 text-blue-400">🅿️</span> Parking
                    </span>
                  </label>
                </div>
              </div>

              <div class="mb-6">
                <h3 class="font-medium text-base mb-3 text-gray-900 flex items-center">
                  <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                  Property Type
                </h3>
                <div class="grid grid-cols-2 gap-2">
                  <label class="relative flex items-center p-3 bg-white rounded-lg border border-gray-200 cursor-pointer hover:border-blue-300 transition-all duration-200 group"
                         :class="{ 'border-blue-500 bg-blue-50': filters.propertyType.includes('hotel') }">
                    <div class="flex-shrink-0 w-5 h-5 relative">
                      <input type="checkbox" class="absolute w-5 h-5 opacity-0 cursor-pointer z-10" v-model="filters.propertyType" value="hotel" />
                      <div class="w-5 h-5 border rounded" 
                           :class="filters.propertyType.includes('hotel') ? 'bg-blue-500 border-blue-500' : 'border-gray-300'"></div>
                      <svg class="absolute top-0.5 left-0.5 w-4 h-4 text-white pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" 
                           :class="filters.propertyType.includes('hotel') ? 'opacity-100' : 'opacity-0'">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                    </div>
                    <span class="ml-3 text-sm text-gray-600 group-hover:text-gray-900 transition-colors flex items-center">
                      <span class="mr-1.5 text-blue-400">🏨</span> Hotel
                    </span>
                  </label>
                  
                  <label class="relative flex items-center p-3 bg-white rounded-lg border border-gray-200 cursor-pointer hover:border-blue-300 transition-all duration-200 group"
                         :class="{ 'border-blue-500 bg-blue-50': filters.propertyType.includes('resort') }">
                    <div class="flex-shrink-0 w-5 h-5 relative">
                      <input type="checkbox" class="absolute w-5 h-5 opacity-0 cursor-pointer z-10" v-model="filters.propertyType" value="resort" />
                      <div class="w-5 h-5 border rounded" 
                           :class="filters.propertyType.includes('resort') ? 'bg-blue-500 border-blue-500' : 'border-gray-300'"></div>
                      <svg class="absolute top-0.5 left-0.5 w-4 h-4 text-white pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" 
                           :class="filters.propertyType.includes('resort') ? 'opacity-100' : 'opacity-0'">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                    </div>
                    <span class="ml-3 text-sm text-gray-600 group-hover:text-gray-900 transition-colors flex items-center">
                      <span class="mr-1.5 text-blue-400">🏝️</span> Resort
                    </span>
                  </label>
                  
                  <label class="relative flex items-center p-3 bg-white rounded-lg border border-gray-200 cursor-pointer hover:border-blue-300 transition-all duration-200 group"
                         :class="{ 'border-blue-500 bg-blue-50': filters.propertyType.includes('villa') }">
                    <div class="flex-shrink-0 w-5 h-5 relative">
                      <input type="checkbox" class="absolute w-5 h-5 opacity-0 cursor-pointer z-10" v-model="filters.propertyType" value="villa" />
                      <div class="w-5 h-5 border rounded" 
                           :class="filters.propertyType.includes('villa') ? 'bg-blue-500 border-blue-500' : 'border-gray-300'"></div>
                      <svg class="absolute top-0.5 left-0.5 w-4 h-4 text-white pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" 
                           :class="filters.propertyType.includes('villa') ? 'opacity-100' : 'opacity-0'">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                    </div>
                    <span class="ml-3 text-sm text-gray-600 group-hover:text-gray-900 transition-colors flex items-center">
                      <span class="mr-1.5 text-blue-400">🏡</span> Villa
                    </span>
                  </label>
                  
                  <label class="relative flex items-center p-3 bg-white rounded-lg border border-gray-200 cursor-pointer hover:border-blue-300 transition-all duration-200 group"
                         :class="{ 'border-blue-500 bg-blue-50': filters.propertyType.includes('apartment') }">
                    <div class="flex-shrink-0 w-5 h-5 relative">
                      <input type="checkbox" class="absolute w-5 h-5 opacity-0 cursor-pointer z-10" v-model="filters.propertyType" value="apartment" />
                      <div class="w-5 h-5 border rounded" 
                           :class="filters.propertyType.includes('apartment') ? 'bg-blue-500 border-blue-500' : 'border-gray-300'"></div>
                      <svg class="absolute top-0.5 left-0.5 w-4 h-4 text-white pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" 
                           :class="filters.propertyType.includes('apartment') ? 'opacity-100' : 'opacity-0'">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                    </div>
                    <span class="ml-3 text-sm text-gray-600 group-hover:text-gray-900 transition-colors flex items-center">
                      <span class="mr-1.5 text-blue-400">🏢</span> Apartment
                    </span>
                  </label>
                </div>
              </div>

              <button 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded transition duration-200 flex items-center justify-center"
                @click="applyFilters"
              >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Apply Filters
              </button>
              
              <div class="border-t border-gray-200 mt-6 pt-6">
                <h3 class="font-medium text-lg mb-3 text-gray-900">Need Help?</h3>
                <p class="text-gray-600 text-sm mb-3">Our travel experts are ready to assist you in finding your perfect stay.</p>
                <a href="#" class="text-blue-600 hover:text-blue-800 font-medium text-sm flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                  </svg>
                  Contact our support team
                </a>
              </div>
            </div>
          </div>

          <!-- Hotel listings -->
          <div class="w-full lg:w-3/4">
            <!-- Sort options -->
            <div class="bg-white p-4 rounded-lg shadow-sm mb-6 flex flex-col sm:flex-row justify-between items-center gap-3 animate-fade-in">
              <div class="text-sm text-gray-500">
                Showing <span class="font-medium text-gray-900">{{ filteredHotels.length }}</span> properties
              </div>
              <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-700">Sort by:</span>
                <select v-model="sortBy" class="border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500">
                  <option value="recommended">Recommended</option>
                  <option value="price-asc">Price: Low to High</option>
                  <option value="price-desc">Price: High to Low</option>
                  <option value="rating-desc">Rating: High to Low</option>
                  <option value="popularity">Popularity</option>
                </select>
              </div>
            </div>

            <!-- Hotel cards -->
            <div class="space-y-6">
              <!-- Skeleton loading state (would be shown while loading in a real app) -->
              <template v-if="isLoading">
                <div v-for="i in 3" :key="i" class="bg-white rounded-lg shadow-sm overflow-hidden animate-pulse">
                  <div class="flex flex-col md:flex-row">
                    <div class="w-full md:w-1/3 h-56 md:h-64 bg-gradient-to-r from-gray-200 to-gray-300 relative">
                      <div class="absolute inset-0 bg-gray-50 opacity-20"></div>
                      <div class="absolute inset-0 flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                      </div>
                    </div>
                    <div class="w-full md:w-2/3 p-6 flex flex-col">
                      <div class="h-6 bg-gray-200 rounded-lg w-3/4 mb-4"></div>
                      <div class="flex space-x-2 mb-3">
                        <div class="h-4 bg-gray-200 rounded w-16"></div>
                        <div class="h-4 bg-gray-200 rounded w-16"></div>
                      </div>
                      <div class="h-4 bg-gray-200 rounded-lg w-full mb-4"></div>
                      <div class="h-4 bg-gray-200 rounded-lg w-full mb-2"></div>
                      <div class="h-4 bg-gray-200 rounded-lg w-3/4 mb-6"></div>
                      <div class="flex flex-wrap gap-2 mb-4">
                        <div class="h-6 bg-gray-200 rounded-full w-20"></div>
                        <div class="h-6 bg-gray-200 rounded-full w-24"></div>
                        <div class="h-6 bg-gray-200 rounded-full w-16"></div>
                      </div>
                      <div class="flex justify-between items-end mt-2">
                        <div class="h-5 bg-gray-200 rounded w-1/3"></div>
                        <div class="h-8 bg-gray-200 rounded-lg w-24"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </template>
              <div 
                v-for="(hotel, index) in filteredHotels" 
                :key="hotel.id" 
                class="bg-white rounded-lg shadow-sm overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1 group relative animate-fade-in"
                :style="{'animation-delay': `${index * 100}ms`}"
              >
                <div class="flex flex-col md:flex-row">
                  <div class="w-full md:w-1/3 relative overflow-hidden h-56 md:h-full">
                    <img 
                      :src="'/images/placeholder.jpg'" 
                      :alt="hotel.name" 
                      class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                    />
                    <div v-if="hotel.discount" class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 text-xs font-bold rounded">
                      SAVE {{ hotel.discount }}%
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-b from-black/30 to-transparent opacity-70"></div>
                  </div>
                  <div class="w-full md:w-2/3 p-4 md:p-6 flex flex-col relative">
                    <div class="flex justify-between items-start mb-2">
                      <div>
                        <div class="flex items-start mb-1">
                          <h2 class="text-xl font-bold text-gray-900">{{ hotel.name }}</h2>
                          <span v-if="hotel.type === 'resort'" class="ml-2 bg-purple-100 text-purple-800 text-xs font-semibold px-2 py-0.5 rounded-full uppercase">Resort</span>
                          <span v-if="hotel.type === 'villa'" class="ml-2 bg-green-100 text-green-800 text-xs font-semibold px-2 py-0.5 rounded-full uppercase">Villa</span>
                          <span v-if="hotel.type === 'apartment'" class="ml-2 bg-orange-100 text-orange-800 text-xs font-semibold px-2 py-0.5 rounded-full uppercase">Apartment</span>
                        </div>
                        <div class="flex items-center">
                          <div class="flex">
                            <span v-for="i in Math.floor(hotel.rating)" :key="i" class="text-yellow-400">★</span>
                            <span v-for="i in (5 - Math.floor(hotel.rating))" :key="i + Math.floor(hotel.rating)" class="text-gray-300">★</span>
                          </div>
                          <span class="ml-2 text-sm text-gray-600">{{ hotel.reviewCount }} reviews</span>
                          <span class="mx-2 text-gray-300">•</span>
                          <span class="text-sm text-gray-600">{{ getRatingText(hotel.rating) }}</span>
                        </div>
                      </div>
                      <div class="text-right">
                        <div class="text-lg md:text-xl font-bold text-blue-600">${{ hotel.price }}<span class="text-sm font-normal">/night</span></div>
                        <div v-if="hotel.oldPrice" class="text-sm text-gray-500 line-through">${{ hotel.oldPrice }}</div>
                        <div v-if="hotel.discount" class="text-xs font-medium text-green-600 mt-1">Save {{ hotel.discount }}% today</div>
                      </div>
                    </div>
                    
                    <div class="text-sm text-gray-600 mb-3 flex items-center gap-1">
                      <svg class="w-4 h-4 text-gray-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      </svg>
                      {{ hotel.location }}
                    </div>
                    
                    <p class="text-gray-700 text-sm mb-4 flex-grow">{{ hotel.description }}</p>
                    
                    <div class="flex flex-wrap gap-2 mb-4">
                      <span 
                        v-for="(amenity, index) in hotel.amenities.slice(0, 5)" 
                        :key="index" 
                        class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-1 rounded flex items-center"
                      >
                        <span v-if="amenity.includes('WiFi')" class="mr-1">📶</span>
                        <span v-else-if="amenity.includes('Pool')" class="mr-1">🏊</span>
                        <span v-else-if="amenity.includes('Spa')" class="mr-1">💆</span>
                        <span v-else-if="amenity.includes('Restaurant')" class="mr-1">🍽️</span>
                        <span v-else-if="amenity.includes('Fitness')" class="mr-1">💪</span>
                        <span v-else-if="amenity.includes('Parking')" class="mr-1">🅿️</span>
                        <span v-else-if="amenity.includes('Beach')" class="mr-1">🏖️</span>
                        <span v-else-if="amenity.includes('Kitchen')" class="mr-1">🍳</span>
                        <span v-else-if="amenity.includes('Laundry')" class="mr-1">👕</span>
                        <span v-else-if="amenity.includes('Kids')" class="mr-1">👶</span>
                        {{ amenity }}
                      </span>
                      <span v-if="hotel.amenities.length > 5" class="bg-blue-50 text-blue-600 text-xs font-medium px-2.5 py-1 rounded">
                        +{{ hotel.amenities.length - 5 }} more
                      </span>
                    </div>
                    
                    <!-- Special offers badge -->
                    <div v-if="hotel.discount" class="absolute top-3 right-3 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded shadow-sm">
                      SALE
                    </div>
                    
                    <!-- Quick info badges -->
                    <div class="flex flex-wrap gap-x-4 gap-y-2 mb-4">
                      <div v-if="hotel.freeCancellation" class="flex items-center text-green-600 text-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Free cancellation
                      </div>
                      <div class="flex items-center text-gray-600 text-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        No payment needed today
                      </div>
                    </div>
                    
                    <div class="flex justify-between items-center mt-auto">
                      <div class="text-sm">
                        <span v-if="hotel.freeCancellation" class="text-green-600 font-medium flex items-center">
                          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>
                          Free cancellation
                        </span>
                        <span v-else class="text-gray-500 flex items-center">
                          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>
                          Non-refundable
                        </span>
                      </div>
                      <a 
                        :href="`/hotels/${hotel.id}`" 
                        target="_blank"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium transition duration-200 flex items-center"
                      >
                        View Details
                        <svg class="w-4 h-4 ml-1 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Empty state -->
              <div v-if="filteredHotels.length === 0" class="text-center py-10">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No hotels found</h3>
                <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
                <div class="mt-6">
                  <button 
                    @click="resetFilters" 
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                  >
                    Reset all filters
                  </button>
                </div>
              </div>
            </div>

            <!-- Pagination -->
            <div v-if="filteredHotels.length > 0" class="mt-8 flex justify-center">
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
    </div>
  </GuestLayout>
</template>

<script setup>
// Add global CSS for animations
const style = document.createElement('style');
style.textContent = `
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
  }
  
  .animate-fade-in {
    animation: fadeIn 0.5s ease-out forwards;
    opacity: 0;
  }
  
  .scrollbar-thin {
    scrollbar-width: thin;
  }
  
  .scrollbar-thin::-webkit-scrollbar {
    width: 6px;
  }
  
  .scrollbar-thin::-webkit-scrollbar-track {
    background: #f1f1f1;
  }
  
  .scrollbar-thin::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 3px;
  }
`;
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import route from '@/route.js';

// Sample hotel data
const hotels = ref([
  {
    id: 1,
    name: 'Grand Resort & Spa',
    location: 'Antalya, Turkey',
    description: 'Luxurious beachfront resort with spectacular views of the Mediterranean Sea, featuring elegant rooms and world-class dining.',
    rating: 4.8,
    reviewCount: 642,
    price: 189,
    oldPrice: 229,
    discount: 18,
    image: '/images/hotels/hotel1.jpg',
    type: 'resort',
    amenities: ['Free WiFi', 'Swimming Pool', 'Spa', 'Restaurant', 'Fitness Center'],
    freeCancellation: true
  },
  {
    id: 2,
    name: 'Azure Bay Hotel',
    location: 'Bodrum, Turkey',
    description: 'Modern boutique hotel nestled in a quiet bay, offering stylish accommodations with private balconies overlooking the Aegean Sea.',
    rating: 4.6,
    reviewCount: 428,
    price: 159,
    image: '/images/hotels/hotel2.jpg',
    type: 'hotel',
    amenities: ['Free WiFi', 'Swimming Pool', 'Restaurant', 'Beach Access'],
    freeCancellation: true
  },
  {
    id: 3,
    name: 'Mountain View Lodge',
    location: 'Cappadocia, Turkey',
    description: 'Charming lodge with stunning views of the unique Cappadocian landscape, featuring cave-inspired rooms and authentic Turkish hospitality.',
    rating: 4.4,
    reviewCount: 356,
    price: 129,
    oldPrice: 149,
    discount: 13,
    image: '/images/hotels/hotel3.jpg',
    type: 'hotel',
    amenities: ['Free WiFi', 'Restaurant', 'Free Parking', 'Airport Shuttle'],
    freeCancellation: false
  },
  {
    id: 4,
    name: 'Sunset Beach Villas',
    location: 'Fethiye, Turkey',
    description: 'Exclusive beachfront villas with private pools, perfect for families seeking privacy and direct beach access in a serene environment.',
    rating: 4.9,
    reviewCount: 287,
    price: 349,
    image: '/images/hotels/hotel4.jpg',
    type: 'villa',
    amenities: ['Free WiFi', 'Private Pool', 'Beach Access', 'Kitchen', 'Free Parking'],
    freeCancellation: true
  },
  {
    id: 5,
    name: 'Urban Deluxe Suites',
    location: 'Istanbul, Turkey',
    description: 'Contemporary apartment-style accommodations in the heart of Istanbul, offering spacious living areas and panoramic city views.',
    rating: 4.3,
    reviewCount: 512,
    price: 139,
    oldPrice: 179,
    discount: 22,
    image: '/images/hotels/hotel5.jpg',
    type: 'apartment',
    amenities: ['Free WiFi', 'Kitchen', 'Fitness Center', 'Laundry Service'],
    freeCancellation: false
  },
  {
    id: 6,
    name: 'Historic Palace Hotel',
    location: 'Istanbul, Turkey',
    description: 'Elegant hotel housed in a restored Ottoman palace, combining historical architecture with modern luxury amenities.',
    rating: 4.7,
    reviewCount: 723,
    price: 229,
    image: '/images/hotels/hotel6.jpg',
    type: 'hotel',
    amenities: ['Free WiFi', 'Swimming Pool', 'Spa', 'Restaurant', 'Fitness Center'],
    freeCancellation: true
  },
  {
    id: 7,
    name: 'Aegean Breeze Resort',
    location: 'Izmir, Turkey',
    description: 'Family-friendly beachfront resort with extensive recreational facilities, including water parks and kids\' clubs.',
    rating: 4.5,
    reviewCount: 489,
    price: 169,
    oldPrice: 199,
    discount: 15,
    image: '/images/hotels/hotel7.jpg',
    type: 'resort',
    amenities: ['Free WiFi', 'Swimming Pool', 'Kids Club', 'Restaurant', 'Beach Access'],
    freeCancellation: true
  },
  {
    id: 8,
    name: 'Pine Forest Cabins',
    location: 'Bursa, Turkey',
    description: 'Cozy wooden cabins nestled in a pine forest, offering a tranquil retreat with mountain views and outdoor activities.',
    rating: 4.2,
    reviewCount: 231,
    price: 99,
    image: '/images/hotels/hotel8.jpg',
    type: 'villa',
    amenities: ['Free WiFi', 'Fireplace', 'Free Parking', 'Nature Trails'],
    freeCancellation: false
  },
]);

// Search and filters
const search = ref('');
const sortBy = ref('recommended');
const currentPage = ref(1);
const itemsPerPage = 5;
const filters = ref({
  minPrice: null,
  maxPrice: null,
  rating: [],
  amenities: [],
  propertyType: []
});

// Reset filters
const resetFilters = () => {
  search.value = '';
  filters.value = {
    minPrice: null,
    maxPrice: null,
    rating: [],
    amenities: [],
    propertyType: []
  };
  applyFilters();
};

// Clear specific filters
const clearPriceFilter = () => {
  filters.value.minPrice = null;
  filters.value.maxPrice = null;
  applyFilters();
};

const removeRatingFilter = (rating) => {
  filters.value.rating = filters.value.rating.filter(r => r !== rating);
  applyFilters();
};

const removeAmenityFilter = (amenity) => {
  filters.value.amenities = filters.value.amenities.filter(a => a !== amenity);
  applyFilters();
};

const removePropertyTypeFilter = (type) => {
  filters.value.propertyType = filters.value.propertyType.filter(t => t !== type);
  applyFilters();
};

// Apply filters
const applyFilters = () => {
  // In a real app, this might trigger an API call
  // For now, we'll just reset the page
  currentPage.value = 1;
};

// Check if any filters are active
const hasActiveFilters = computed(() => {
  return (
    (filters.value.minPrice !== null && filters.value.minPrice !== 0) ||
    (filters.value.maxPrice !== null && filters.value.maxPrice !== 1000) ||
    filters.value.rating.length > 0 ||
    filters.value.amenities.length > 0 ||
    filters.value.propertyType.length > 0
  );
});

// Computed hotels with filtering applied
const filteredHotels = computed(() => {
  let result = [...hotels.value];
  
  // Search filter
  if (search.value) {
    const searchLower = search.value.toLowerCase();
    result = result.filter(hotel => 
      hotel.name.toLowerCase().includes(searchLower) ||
      hotel.location.toLowerCase().includes(searchLower) ||
      hotel.description.toLowerCase().includes(searchLower)
    );
  }
  
  // Price range filter
  if (filters.value.minPrice) {
    result = result.filter(hotel => hotel.price >= filters.value.minPrice);
  }
  if (filters.value.maxPrice) {
    result = result.filter(hotel => hotel.price <= filters.value.maxPrice);
  }
  
  // Rating filter
  if (filters.value.rating.length > 0) {
    result = result.filter(hotel => {
      return filters.value.rating.some(r => {
        const ratingValue = parseInt(r);
        return Math.floor(hotel.rating) === ratingValue;
      });
    });
  }
  
  // Amenities filter
  if (filters.value.amenities.length > 0) {
    result = result.filter(hotel => {
      return filters.value.amenities.every(amenity => {
        const amenityName = amenity.charAt(0).toUpperCase() + amenity.slice(1);
        return hotel.amenities.some(a => a.toLowerCase().includes(amenity));
      });
    });
  }
  
  // Property type filter
  if (filters.value.propertyType.length > 0) {
    result = result.filter(hotel => 
      filters.value.propertyType.includes(hotel.type)
    );
  }
  
  // Sorting
  switch (sortBy.value) {
    case 'price-asc':
      result.sort((a, b) => a.price - b.price);
      break;
    case 'price-desc':
      result.sort((a, b) => b.price - a.price);
      break;
    case 'rating-desc':
      result.sort((a, b) => b.rating - a.rating);
      break;
    case 'popularity':
      result.sort((a, b) => b.reviewCount - a.reviewCount);
      break;
    // For 'recommended', we use the default order
  }
  
  return result;
});

// Pagination
const totalPages = computed(() => {
  return Math.ceil(filteredHotels.value.length / itemsPerPage);
});

const paginatedHotels = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return filteredHotels.value.slice(start, end);
});

// Helper function to get rating text based on score
const getRatingText = (rating) => {
  if (rating >= 4.5) return 'Exceptional';
  if (rating >= 4.0) return 'Excellent';
  if (rating >= 3.5) return 'Very Good';
  if (rating >= 3.0) return 'Good';
  if (rating >= 2.0) return 'Average';
  return 'Poor';
};

// Animation for fade-in effect
const isLoading = ref(false); // In a real app, this would be true during data fetch

// Register scroll event for parallax effect
const handleScroll = () => {
  const scrollY = window.scrollY;
  const heroImage = document.querySelector('.parallax-bg');
  if (heroImage) {
    // Move the background image at a slower rate than the scroll
    heroImage.style.transform = `translateY(${scrollY * 0.4}px)`;
  }
};

onMounted(() => {
  // In a real app, this is where you might fetch hotel data from the server
  console.log('Hotels page mounted');
  
  // Add scroll event for parallax effect
  window.addEventListener('scroll', handleScroll);

  // Add the style to the document head
  document.head.appendChild(style);
});

// Remove event listener when component is unmounted
onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});
</script>