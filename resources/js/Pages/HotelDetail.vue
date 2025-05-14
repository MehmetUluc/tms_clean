<template>
  <Head :title="hotel.name" />
  <GuestLayout>
    <div class="bg-gray-50 py-6 md:py-10">
      <div class="container mx-auto px-4">
        <!-- Breadcrumbs -->
        <div class="text-sm text-gray-600 mb-5">
          <Link href="/" class="hover:text-blue-600">Home</Link>
          <span class="mx-2">›</span>
          <Link :href="route('hotels.index')" class="hover:text-blue-600">Hotels</Link>
          <span class="mx-2">›</span>
          <span class="text-gray-900">{{ hotel.name }}</span>
        </div>

        <!-- Hero image banner with hotel name overlay -->
        <div class="relative h-[300px] md:h-[400px] mb-8 overflow-hidden rounded-xl">
          <div class="absolute inset-0 z-0">
            <img :src="hotel?.images?.[0] || '/images/placeholder.jpg'" :alt="hotel?.name" class="w-full h-full object-cover filter brightness-[0.6]" />
          </div>
          <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent z-10"></div>
          <div class="absolute bottom-0 left-0 right-0 p-6 md:p-10 z-20">
            <div class="container mx-auto">
              <div class="max-w-2xl">
                <div class="flex items-center mb-2">
                  <div class="flex">
                    <span v-for="i in Math.floor(hotel.rating)" :key="i" class="text-yellow-400">★</span>
                    <span v-for="i in (5 - Math.floor(hotel.rating))" :key="i + Math.floor(hotel.rating)" class="text-gray-300">★</span>
                  </div>
                  <span class="ml-2 text-white">{{ hotel.rating }} ({{ hotel.reviewCount }} reviews)</span>
                </div>
                <h1 class="text-3xl md:text-5xl font-bold text-white mb-2">{{ hotel.name }}</h1>
                <div class="flex items-center text-white opacity-90">
                  <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  </svg>
                  <span>{{ hotel.location }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Hotel header section -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
          <div class="md:flex">
            <div class="md:w-2/3 p-6">
              <div class="flex justify-between items-start">
                <div>
                  <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ hotel.name }}</h1>
                  <div class="flex items-center mt-2">
                    <div class="flex">
                      <span v-for="i in Math.floor(hotel.rating)" :key="i" class="text-yellow-400">★</span>
                      <span v-for="i in (5 - Math.floor(hotel.rating))" :key="i + Math.floor(hotel.rating)" class="text-gray-300">★</span>
                    </div>
                    <span class="ml-2 text-gray-600">{{ hotel.rating }} ({{ hotel.reviewCount }} reviews)</span>
                  </div>
                  <div class="flex items-center mt-2 text-gray-600">
                    <svg class="w-5 h-5 text-gray-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>{{ hotel.location }}</span>
                  </div>
                </div>
                <div class="hidden md:block">
                  <button class="flex items-center text-blue-600 hover:text-blue-800">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    Save
                  </button>
                </div>
              </div>
            </div>
            <div class="md:w-1/3 bg-blue-50 p-6 flex flex-col justify-center">
              <div class="text-center">
                <div class="text-sm uppercase font-medium text-gray-500">Price from</div>
                <div class="text-3xl font-bold text-blue-600">${{ hotel.price }}<span class="text-lg font-normal">/night</span></div>
                <div v-if="hotel.oldPrice" class="text-gray-500 line-through">${{ hotel.oldPrice }}</div>
                <button class="mt-4 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg w-full transition duration-200">
                  Book Now
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Key highlights -->
        <div class="bg-white rounded-lg shadow-sm mb-8 overflow-hidden">
          <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-100">
            <div class="p-6 flex items-start">
              <div class="bg-blue-100 p-2 rounded-lg mr-4">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div>
                <h3 class="font-medium text-gray-900 mb-1">Fast Check-in/Check-out</h3>
                <p class="text-sm text-gray-600">Save time with our quick process and digital keys</p>
              </div>
            </div>
            <div class="p-6 flex items-start">
              <div class="bg-green-100 p-2 rounded-lg mr-4">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div>
                <h3 class="font-medium text-gray-900 mb-1">Free Cancellation</h3>
                <p class="text-sm text-gray-600">Flexible booking with 24-hour cancellation policy</p>
              </div>
            </div>
            <div class="p-6 flex items-start">
              <div class="bg-purple-100 p-2 rounded-lg mr-4">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
              </div>
              <div>
                <h3 class="font-medium text-gray-900 mb-1">Secure Booking</h3>
                <p class="text-sm text-gray-600">Encrypted payment processing for your security</p>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Hotel photos grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8 relative group">
          <div class="md:col-span-2 md:row-span-2 overflow-hidden rounded-lg">
            <img :src="hotel?.images?.[0] || '/images/placeholder.jpg'" :alt="`${hotel?.name} main`" class="w-full h-full object-cover rounded-lg transition-transform duration-700 hover:scale-110" />
          </div>
          <div v-for="(image, index) in hotel?.images?.slice(1, 5) || []" :key="index" class="hidden md:block overflow-hidden rounded-lg">
            <img :src="image || '/images/placeholder.jpg'" :alt="`Hotel image ${index + 1}`" class="w-full h-full object-cover rounded-lg transition-transform duration-700 hover:scale-110" />
          </div>
          <div class="hidden md:flex md:col-span-1 items-center justify-center bg-gray-100 rounded-lg cursor-pointer hover:bg-gray-200 transition-colors duration-300" @click="openGallery">
            <div class="text-center p-4">
              <svg class="w-8 h-8 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <span class="block mt-2 text-gray-700 font-medium">View all photos</span>
            </div>
          </div>
        </div>

        <!-- Mobile image slider (shown only on mobile) -->
        <div class="md:hidden mb-6 relative">
          <div class="flex overflow-x-auto pb-4 snap-x">
            <div v-for="(image, index) in hotel?.images || []" :key="index" class="flex-shrink-0 w-full px-2 snap-center">
              <img :src="image || '/images/placeholder.jpg'" :alt="`Hotel image ${index + 1}`" class="rounded-lg w-full h-64 object-cover" />
            </div>
          </div>
          <div class="absolute bottom-6 left-0 right-0 flex justify-center space-x-2">
            <button 
              v-for="(_, index) in hotel?.images || []" 
              :key="index" 
              class="w-2 h-2 rounded-full bg-white opacity-60"
              :class="{ 'opacity-100': index === 0 }"
            ></button>
          </div>
        </div>

        <!-- Content grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Main content -->
          <div class="lg:col-span-2">
            <!-- Photo Gallery Modal -->
        <div v-if="galleryOpen" class="fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4">
          <div class="relative w-full max-w-6xl mx-auto">
            <!-- Close button -->
            <button @click="galleryOpen = false" class="absolute top-4 right-4 text-white hover:text-gray-300 z-10">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
            
            <!-- Main image -->
            <div class="relative">
              <img :src="hotel.images[currentGalleryIndex]" :alt="`Hotel image ${currentGalleryIndex + 1}`" 
                class="w-full max-h-[80vh] object-contain" />
              
              <!-- Navigation arrows -->
              <button @click="prevImage" class="absolute left-4 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white p-2 rounded-full">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
              </button>
              <button @click="nextImage" class="absolute right-4 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white p-2 rounded-full">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </button>
            </div>
            
            <!-- Thumbnails -->
            <div class="flex justify-center mt-4 space-x-2 overflow-x-auto">
              <div 
                v-for="(image, index) in hotel.images" 
                :key="index"
                @click="currentGalleryIndex = index"
                class="w-16 h-16 flex-shrink-0 cursor-pointer rounded-md overflow-hidden transition-opacity duration-200"
                :class="{ 'ring-2 ring-blue-500': currentGalleryIndex === index, 'opacity-50 hover:opacity-100': currentGalleryIndex !== index }"
              >
                <img :src="image" :alt="`Thumbnail ${index + 1}`" class="w-full h-full object-cover" />
              </div>
            </div>
          </div>
        </div>
        
        <!-- Description -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
              <h2 class="text-xl font-bold text-gray-900 mb-4">About {{ hotel.name }}</h2>
              <div class="prose max-w-none text-gray-700">
                <p>{{ hotel.description }}</p>
                <p>{{ hotel.descriptionLong }}</p>
              </div>
            </div>

            <!-- Interactive Room Viewer -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
              <h2 class="text-xl font-bold text-gray-900 mb-4">Interactive 360° View</h2>
              <div class="aspect-w-16 aspect-h-9 bg-gray-100 rounded-lg overflow-hidden relative">
                <div class="h-64 w-full flex items-center justify-center">
                  <div class="text-center">
                    <div class="bg-blue-100 rounded-full p-4 inline-block mb-4">
                      <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                      </svg>
                    </div>
                    <h3 class="text-gray-700 font-medium mb-1">Experience the room in 360°</h3>
                    <p class="text-gray-500 text-sm mb-4">Take a virtual tour of our rooms and amenities</p>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded transition duration-200">
                      Start Virtual Tour
                    </button>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Amenities -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
              <h2 class="text-xl font-bold text-gray-900 mb-4">Amenities</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
                <div v-for="(category, categoryName) in hotel.amenitiesByCategory" :key="categoryName" class="mb-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                  <h3 class="font-medium text-gray-900 mb-2">{{ categoryName }}</h3>
                  <ul class="space-y-2">
                    <li v-for="(amenity, index) in category" :key="index" class="flex items-center text-gray-700">
                      <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                      {{ amenity }}
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- Room types -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
              <h2 class="text-xl font-bold text-gray-900 mb-4">Available Room Types</h2>
              <div class="space-y-6">
                <div v-for="(room, index) in hotel.roomTypes" :key="index" class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow duration-300">
                  <div class="md:flex">
                    <div class="md:w-1/3">
                      <img :src="room.image" :alt="room.name" class="w-full h-full object-cover" />
                    </div>
                    <div class="md:w-2/3 p-4">
                      <div class="flex justify-between items-start mb-2">
                        <h3 class="text-lg font-bold text-gray-900">{{ room.name }}</h3>
                        <div class="text-right">
                          <div class="text-lg font-bold text-blue-600">${{ room.price }}<span class="text-sm font-normal">/night</span></div>
                          <div class="text-sm text-gray-500">{{ room.occupancy }}</div>
                        </div>
                      </div>
                      <p class="text-gray-700 text-sm mb-3">{{ room.description }}</p>
                      <div class="flex flex-wrap gap-2 mb-3">
                        <span v-for="(feature, idx) in room.features" :key="idx" class="bg-gray-100 text-gray-800 text-xs px-2.5 py-1 rounded">
                          {{ feature }}
                        </span>
                      </div>
                      <div class="flex justify-between items-center mt-3">
                        <div v-if="room.freeCancellation" class="text-green-600 text-sm font-medium">Free cancellation</div>
                        <div v-else class="text-gray-500 text-sm">Non-refundable</div>
                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium transition duration-200">
                          Select Room
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Reviews -->
            <div class="bg-white rounded-lg shadow-sm p-6">
              <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-900">Guest Reviews</h2>
                <div class="flex items-center">
                  <div class="bg-blue-600 text-white font-bold rounded p-2 mr-2">{{ hotel.rating }}</div>
                  <div>
                    <div class="text-gray-900 font-medium">{{ getRatingText(hotel.rating) }}</div>
                    <div class="text-sm text-gray-600">{{ hotel.reviewCount }} reviews</div>
                  </div>
                </div>
              </div>
              
              <!-- Review filters -->
              <div class="flex flex-wrap gap-2 mb-4">
                <button class="bg-blue-600 text-white px-3 py-1 rounded-full text-sm">All</button>
                <button v-for="category in ['Excellent', 'Very Good', 'Good', 'Average', 'Poor']" :key="category" 
                  class="border border-gray-300 px-3 py-1 rounded-full text-sm text-gray-700 hover:bg-gray-50">
                  {{ category }}
                </button>
              </div>
              
              <!-- Review list -->
              <div class="space-y-6">
                <div v-for="(review, index) in hotel.reviews" :key="index" class="border-b border-gray-200 pb-6 last:border-b-0 last:pb-0">
                  <div class="flex justify-between items-start mb-2">
                    <div class="flex items-center">
                      <div class="bg-blue-100 rounded-full w-10 h-10 flex items-center justify-center text-blue-600 font-medium mr-3">
                        {{ review.author.charAt(0) }}
                      </div>
                      <div>
                        <div class="font-medium text-gray-900">{{ review.author }}</div>
                        <div class="text-sm text-gray-600">{{ review.date }}</div>
                      </div>
                    </div>
                    <div class="bg-blue-600 text-white font-bold rounded p-1 px-2 text-sm">{{ review.rating }}</div>
                  </div>
                  <p class="text-gray-700">{{ review.comment }}</p>
                </div>
              </div>
              
              <!-- Show more button -->
              <div class="text-center mt-6">
                <button class="border border-gray-300 rounded-md px-4 py-2 text-gray-700 hover:bg-gray-50 font-medium hover:text-blue-600 transition-colors duration-200">
                  Show more reviews
                </button>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="lg:col-span-1">
            <!-- Booking widget -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6 sticky top-4 border-t-4 border-blue-600">
              <h2 class="text-lg font-bold text-gray-900 mb-4">Book Your Stay</h2>
              
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Check-in / Check-out</label>
                  <div class="border border-gray-300 rounded-md p-2 flex">
                    <div class="flex-1 text-center">
                      <input type="date" class="w-full border-0 focus:ring-0" />
                      <div class="text-xs text-gray-500 mt-1">Check-in</div>
                    </div>
                    <div class="border-l border-gray-300 mx-2"></div>
                    <div class="flex-1 text-center">
                      <input type="date" class="w-full border-0 focus:ring-0" />
                      <div class="text-xs text-gray-500 mt-1">Check-out</div>
                    </div>
                  </div>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Guests</label>
                  <div class="border border-gray-300 rounded-md p-2">
                    <select class="w-full border-0 focus:ring-0">
                      <option>1 adult</option>
                      <option>2 adults</option>
                      <option>2 adults, 1 child</option>
                      <option>2 adults, 2 children</option>
                      <option>3 adults</option>
                      <option>4 adults</option>
                    </select>
                  </div>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Rooms</label>
                  <div class="border border-gray-300 rounded-md p-2">
                    <select class="w-full border-0 focus:ring-0">
                      <option>1 room</option>
                      <option>2 rooms</option>
                      <option>3 rooms</option>
                      <option>4 rooms</option>
                    </select>
                  </div>
                </div>
              </div>
              
              <div class="mt-6 space-y-4">
                <div class="flex justify-between items-center text-gray-700">
                  <div>Average nightly rate</div>
                  <div class="font-semibold">${{ hotel.price }}</div>
                </div>
                
                <div class="flex justify-between items-center text-gray-700">
                  <div>Tax & fees</div>
                  <div class="font-semibold">${{ Math.round(hotel.price * 0.15) }}</div>
                </div>
                
                <div class="border-t border-gray-200 pt-4 flex justify-between items-center">
                  <div class="font-semibold text-gray-900">Total</div>
                  <div class="font-bold text-lg text-blue-600">${{ Math.round(hotel.price * 1.15) }}</div>
                </div>
              </div>
              
              <button class="mt-6 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg w-full transition duration-200 transform hover:scale-105">
                Book Now
              </button>
              
              <p class="text-green-600 text-sm mt-3 flex items-center justify-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                No payment required today
              </p>
            </div>
            
            <!-- Map widget -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
              <h2 class="text-lg font-bold text-gray-900 mb-4">Location</h2>
              <div class="aspect-w-16 aspect-h-9 mb-3">
                <div class="w-full h-48 bg-gray-200 rounded-lg mb-2 flex items-center justify-center">
                  <div class="text-gray-400">Map placeholder</div>
                </div>
              </div>
              <div class="text-gray-700">
                <p class="mb-2">{{ hotel.address }}</p>
                <ul class="space-y-1 text-sm">
                  <li v-for="(point, index) in hotel.pointsOfInterest" :key="index" class="flex items-start">
                    <svg class="w-4 h-4 text-gray-500 mr-1 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <circle cx="12" cy="12" r="10" stroke-width="2" />
                      <path d="M12 8v4l3 3" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    {{ point.name }} - {{ point.distance }}
                  </li>
                </ul>
              </div>
            </div>
            
            <!-- Policy info -->
            <div class="bg-white rounded-lg shadow-sm p-6">
              <h2 class="text-lg font-bold text-gray-900 mb-4">Hotel Policies</h2>
              <div class="space-y-4 text-gray-700">
                <div>
                  <h3 class="font-medium mb-1">Check-in & Check-out</h3>
                  <p class="text-sm">Check-in: 3:00 PM - 12:00 AM<br>Check-out: 7:00 AM - 11:00 AM</p>
                </div>
                
                <div>
                  <h3 class="font-medium mb-1">Cancellation Policy</h3>
                  <p class="text-sm">Free cancellation up to 24 hours before check-in. After that, cancellation will incur a fee equivalent to the first night's stay.</p>
                </div>
                
                <div>
                  <h3 class="font-medium mb-1">Children & Extra Beds</h3>
                  <p class="text-sm">Children of all ages are welcome. Children 12 and above are considered adults at this property.</p>
                </div>
                
                <div>
                  <h3 class="font-medium mb-1">Pets</h3>
                  <p class="text-sm">Pets are not allowed.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </GuestLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

// Gallery state
const galleryOpen = ref(false);
const currentGalleryIndex = ref(0);
const hotel = ref();

// Gallery navigation
const openGallery = () => {
  galleryOpen.value = true;
  document.body.classList.add('overflow-hidden'); // Prevent scrolling when gallery is open
};

const closeGallery = () => {
  galleryOpen.value = false;
  document.body.classList.remove('overflow-hidden');
};

const nextImage = () => {
  if (currentGalleryIndex.value < (hotel.value?.images?.length || 0) - 1) {
    currentGalleryIndex.value++;
  } else {
    currentGalleryIndex.value = 0; // Loop back to the first image
  }
};

const prevImage = () => {
  if (currentGalleryIndex.value > 0) {
    currentGalleryIndex.value--;
  } else {
    currentGalleryIndex.value = (hotel.value?.images?.length || 0) - 1; // Loop to the last image
  }
};

// Process hotel data received from Inertia props
const props = defineProps({
  hotel: Object,
  similarHotels: Array,
  metadata: Object
});

// When component is mounted
onMounted(() => {
  // Set hotel data from props
  hotel.value = props.hotel;
  
  // Prepare images array if it doesn't exist
  if (!hotel.value.images) {
    hotel.value.images = ['/images/placeholder.jpg'];
  }
  
  // Set up image gallery navigation
  const handleKeyDown = (e) => {
    if (e.key === 'Escape' && galleryOpen.value) {
      closeGallery();
    } else if (e.key === 'ArrowRight' && galleryOpen.value) {
      nextImage();
    } else if (e.key === 'ArrowLeft' && galleryOpen.value) {
      prevImage();
    }
  };
  
  window.addEventListener('keydown', handleKeyDown);
  
  onUnmounted(() => {
    window.removeEventListener('keydown', handleKeyDown);
    if (galleryOpen.value) {
      document.body.classList.remove('overflow-hidden');
    }
  });
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

// Import route function from route.js
import route from '@/route.js';
</script>