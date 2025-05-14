<template>
    <Head title="Home - Find Your Perfect Hotel in Turkey" />
    <GuestLayout>
        <!-- Combined Hero + Destinations Section -->
        <section class="relative overflow-hidden pb-48">
            <!-- Animated background with parallax -->
            <div class="absolute w-full h-full top-0 left-0">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-900/80 to-indigo-900/80 z-10"></div>

                <!-- Main background image with parallax -->
                <div
                    class="absolute inset-0 w-full h-full bg-cover bg-center transition-transform duration-500 transform scale-110"
                    :style="{
                        'background-image': `url('${heroBackgrounds[currentBgIndex].image}')`,
                        'transform': `scale(1.1) translateY(${scrollY * 0.15}px)`
                    }"
                    :class="{'animate-ken-burns': animateBackground}"
                ></div>
            </div>

            <!-- Background image indicators -->
            <div class="absolute top-[650px] left-0 right-0 z-20 flex justify-center">
                <div class="flex space-x-2">
                    <button
                        v-for="(bg, index) in heroBackgrounds"
                        :key="index"
                        @click="changeBackground(index)"
                        class="w-2 h-2 rounded-full bg-white transition-opacity"
                        :class="currentBgIndex === index ? 'opacity-100' : 'opacity-40'"
                    ></button>
                </div>
            </div>

            <!-- Hero content -->
            <div class="container relative z-20 mx-auto pt-24 pb-32 px-6 lg:px-8 min-h-[600px]">
                <div class="max-w-4xl mx-auto relative">
                    <div class="text-center mb-12">
                        <div class="inline-block rounded-full bg-blue-500/20 px-4 py-1.5 mb-5 backdrop-blur-sm text-white text-sm font-medium">
                            Incredible hotel experiences in Turkey
                        </div>
                        <h1 class="text-4xl md:text-6xl font-extrabold mb-6 text-white text-shadow-lg">
                            {{ heroBackgrounds[currentBgIndex].title }}
                        </h1>
                        <p class="text-xl md:text-2xl mb-10 text-white/90 font-light max-w-3xl mx-auto leading-relaxed">
                            {{ heroBackgrounds[currentBgIndex].subtitle }}
                        </p>
                    </div>

                    <!-- Search form - no special wrapper needed anymore -->
                    <div>
                        <SearchForm />
                    </div>
                </div>
            </div>

            <!-- Popular Destinations - with white background but still part of same section -->
            <div class="relative z-10 mt-32 py-16 bg-white rounded-t-3xl">
                <div class="container mx-auto px-6 md:px-0">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl font-bold mb-2 text-gray-900">Popular Destinations</h2>
                        <p class="text-gray-600">Discover our most sought-after travel destinations</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div v-for="destination in destinations" :key="destination.id" class="group">
                            <div class="relative rounded-xl overflow-hidden shadow-lg h-72">
                                <img :src="destination.image" :alt="destination.name" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-300">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                                <div class="absolute bottom-0 left-0 p-6">
                                    <h3 class="text-xl font-bold text-white mb-1">{{ destination.name }}</h3>
                                    <p class="text-white/80">{{ destination.count }} Properties</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-10">
                        <a href="/regions" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium">
                            View All Destinations
                            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Hotels -->
        <section class="py-16">
            <div class="container mx-auto px-6 md:px-0">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold mb-2">Featured Hotels</h2>
                    <p class="text-gray-600">Handpicked hotels for your perfect stay</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div v-for="hotel in featuredHotels" :key="hotel.id" class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="relative h-48">
                            <img :src="hotel.image" :alt="hotel.name" class="w-full h-full object-cover">
                            <div v-if="hotel.promo" class="absolute top-4 left-4 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">{{ hotel.promo }}</div>
                        </div>
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-lg font-bold">{{ hotel.name }}</h3>
                                <div class="flex">
                                    <span v-for="i in 5" :key="i" class="text-yellow-400">
                                        {{ i <= hotel.stars ? '★' : '☆' }}
                                    </span>
                                </div>
                            </div>
                            <p class="text-gray-500 text-sm mb-4">{{ hotel.location }}</p>
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-lg font-bold">${{ hotel.price }}</span>
                                    <span class="text-gray-500 text-sm">/night</span>
                                </div>
                                <a :href="'/hotels/' + hotel.slug" class="inline-flex items-center px-4 py-2 border border-primary-600 text-primary-600 rounded hover:bg-primary-600 hover:text-white transition-colors">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-10">
                    <a href="/hotels" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium">
                        View All Hotels
                        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </section>

        <!-- Why Choose Us -->
        <section class="py-16 bg-gray-50">
            <div class="container mx-auto px-6 md:px-0">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold mb-2">Why Choose Us</h2>
                    <p class="text-gray-600">The best reasons to book with TravelManager</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="text-center p-6">
                        <div class="inline-block p-4 bg-primary-100 text-primary-600 rounded-full mb-4">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2">Best Price Guarantee</h3>
                        <p class="text-gray-600">Find a lower price? We'll match it and give you an additional 10% off.</p>
                    </div>
                    
                    <div class="text-center p-6">
                        <div class="inline-block p-4 bg-primary-100 text-primary-600 rounded-full mb-4">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2">Secure Booking</h3>
                        <p class="text-gray-600">Your booking and personal information are safe with our industry-leading security.</p>
                    </div>
                    
                    <div class="text-center p-6">
                        <div class="inline-block p-4 bg-primary-100 text-primary-600 rounded-full mb-4">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2">24/7 Support</h3>
                        <p class="text-gray-600">Need help? Our customer support team is available 24/7 to assist you.</p>
                    </div>
                    
                    <div class="text-center p-6">
                        <div class="inline-block p-4 bg-primary-100 text-primary-600 rounded-full mb-4">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905a3.61 3.61 0 01-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2">No Booking Fees</h3>
                        <p class="text-gray-600">Book without any hidden charges or booking fees. What you see is what you pay.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Newsletter Section -->
        <section class="py-16 bg-primary-600 text-white">
            <div class="container mx-auto px-6 md:px-0">
                <div class="max-w-3xl mx-auto text-center">
                    <h2 class="text-3xl font-bold mb-3">Subscribe to Our Newsletter</h2>
                    <p class="mb-6">Stay updated with our latest offers, deals, and travel inspiration.</p>
                    
                    <div class="flex flex-col sm:flex-row gap-2">
                        <input type="email" class="flex-grow px-4 py-3 rounded-md focus:outline-none text-gray-800" placeholder="Your email address">
                        <button class="px-6 py-3 bg-white text-primary-600 font-bold rounded-md hover:bg-gray-100 transition-colors">Subscribe</button>
                    </div>
                    
                    <p class="mt-4 text-sm text-white/80">By subscribing, you agree to our Privacy Policy and consent to receive updates from us.</p>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import SearchForm from '@/Components/SearchForm.vue';
import { useWindowScroll } from '@vueuse/core';

// Hero backgrounds with rotating content
const heroBackgrounds = [
    {
        image: '/images/destinations/istanbul.jpg',
        title: 'Discover Your Perfect Turkish Getaway',
        subtitle: 'From vibrant cities to pristine beaches, find accommodations that perfectly match your travel style and preferences.'
    },
    {
        image: '/images/destinations/antalya.jpg',
        title: 'Experience Luxury on the Mediterranean',
        subtitle: 'Book your stay at premium resorts along Turkey\'s stunning coastline and indulge in world-class amenities and services.'
    },
    {
        image: '/images/destinations/cappadocia.jpg',
        title: 'Unforgettable Adventures Await',
        subtitle: 'Explore Turkey\'s most breathtaking landscapes and immerse yourself in rich cultural experiences with our curated selection of hotels.'
    }
];

// Parallax and background rotation functionality
const currentBgIndex = ref(0);
const animateBackground = ref(true);
const { y: scrollY } = useWindowScroll();

const changeBackground = (index) => {
    if (index === currentBgIndex.value) return;
    
    animateBackground.value = false;
    currentBgIndex.value = index;
    setTimeout(() => {
        animateBackground.value = true;
    }, 50);
};

// Auto-rotate backgrounds
let rotationInterval;

onMounted(() => {
    // Start background rotation
    rotationInterval = setInterval(() => {
        currentBgIndex.value = (currentBgIndex.value + 1) % heroBackgrounds.length;
    }, 8000);
});

onUnmounted(() => {
    // Clear interval on component unmount
    clearInterval(rotationInterval);
});

// Sample data
const destinations = [
    { id: 1, name: 'Istanbul', count: 124, image: '/images/destinations/istanbul.jpg' },
    { id: 2, name: 'Antalya', count: 78, image: '/images/destinations/antalya.jpg' },
    { id: 3, name: 'Bodrum', count: 56, image: '/images/destinations/bodrum.jpg' },
];

const featuredHotels = [
    { 
        id: 1, 
        name: 'Grand Oasis Resort', 
        slug: 'grand-oasis-resort',
        location: 'Antalya, Turkey', 
        stars: 5, 
        price: 199, 
        promo: '20% OFF', 
        image: '/images/hotels/hotel1.jpg'
    },
    { 
        id: 2, 
        name: 'Blue Palace Spa', 
        slug: 'blue-palace-spa',
        location: 'Bodrum, Turkey', 
        stars: 4, 
        price: 149, 
        promo: null, 
        image: '/images/hotels/hotel2.jpg'
    },
    { 
        id: 3, 
        name: 'Sunset Beach Hotel', 
        slug: 'sunset-beach-hotel',
        location: 'Istanbul, Turkey', 
        stars: 5, 
        price: 259, 
        promo: 'HOT DEAL', 
        image: '/images/hotels/hotel3.jpg'
    },
];
</script>

<style scoped>
.text-shadow-lg {
  text-shadow: 0 2px 10px rgba(0, 0, 0, 0.25);
}

@keyframes kenBurnsEffect {
  0% {
    transform: scale(1.1) translateY(var(--scroll-offset, 0));
  }
  100% {
    transform: scale(1.2) translateY(var(--scroll-offset, 0));
  }
}

.animate-ken-burns {
  animation: kenBurnsEffect 20s ease-in-out infinite alternate;
}

/* Search form styling removed - no longer needed as sections are combined */
</style>