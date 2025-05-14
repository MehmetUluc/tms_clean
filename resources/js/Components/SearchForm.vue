<template>
  <div ref="searchFormRef" class="bg-white rounded-xl shadow-2xl p-6 text-gray-800 relative z-[9999]">
    <h2 class="text-xl font-semibold mb-6 text-gray-900">Find Your Dream Getaway</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <!-- Destination -->
      <div class="relative z-[99999]">
        <label class="block text-sm font-medium text-gray-700 mb-1">Destination</label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <MapPinIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
          </div>
          <div ref="destinationRef" @click="openDestinationDropdown" class="cursor-pointer relative">
            <input
              type="text"
              class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Where are you going?"
              readonly
              :value="destination ? destination.name : ''"
            >
            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
              <ChevronDownIcon
                class="h-5 w-5 text-gray-400 transform transition-transform"
                :class="{'rotate-180': isDestinationOpen}"
                aria-hidden="true"
              />
            </div>
          </div>
          <!-- Destination Dropdown moved here -->
          <div v-if="isDestinationOpen"
               ref="destinationDropdownRef"
               class="absolute z-[999999] bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden w-full mt-1"
               style="top: 100%">
            <div class="p-2">
              <input
                type="text"
                class="w-full p-2 border border-gray-300 rounded-md"
                placeholder="Search destinations..."
                v-model="destinationSearch"
                @input="filterDestinations"
              >
            </div>
            <div class="max-h-60 overflow-y-auto py-2">
              <div
                v-for="option in filteredDestinations"
                :key="option.id"
                class="px-4 py-2 hover:bg-blue-50 cursor-pointer flex items-center"
                @click="selectDestination(option)"
              >
                <MapPinIcon class="h-5 w-5 text-blue-500 mr-2" />
                <div>
                  <div>{{ option.name }}</div>
                  <div class="text-xs text-gray-500">{{ option.region }}</div>
                </div>
              </div>
              <div v-if="filteredDestinations.length === 0" class="px-4 py-2 text-gray-500 text-center">
                No destinations found
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Date Picker -->
      <div class="relative z-[99999]">
        <label class="block text-sm font-medium text-gray-700 mb-1">Check-in / Check-out</label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <CalendarIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
          </div>
          <div ref="datePickerRef" @click="openDatePickerDropdown" class="cursor-pointer">
            <input
              type="text"
              class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Add dates"
              readonly
              :value="formatDateRange"
            >
            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
              <ChevronDownIcon
                class="h-5 w-5 text-gray-400 transform transition-transform"
                :class="{'rotate-180': isDatePickerOpen}"
                aria-hidden="true"
              />
            </div>
          </div>
          <!-- Date Picker Dropdown moved here -->
          <div v-if="isDatePickerOpen"
               ref="datePickerDropdownRef"
               class="absolute z-[999999] bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden"
               style="top: 100%; left: 0">
            <DatePicker
              v-model.range="dateRange"
              :min-date="new Date()"
              is-range
              color="blue"
              :columns="isMobile ? 1 : 2"
              :rows="1"
              :initial-page="{ month: new Date().getMonth() + 1, year: new Date().getFullYear() }"
              :masks="{
                input: ['YYYY-MM-DD', 'YYYY-MM-DD']
              }"
              :attributes="holidayAttributes"
              mode="date"
              @dayclick="onDayClick"
            >
              <template v-slot:footer-left>
                <div class="calendar-legend">
                  <div v-if="isLoadingHolidays" class="flex items-center gap-1">
                    <span class="inline-block w-4 h-4 border-2 border-gray-300 border-t-blue-500 rounded-full animate-spin"></span>
                    <span>Loading holidays...</span>
                  </div>
                  <template v-else>
                    <div class="flex items-center">
                      <span class="inline-block w-2 h-2 rounded-full bg-red-600 mr-1"></span>
                      <span>National Holiday</span>
                    </div>
                    <div class="flex items-center">
                      <span class="inline-block w-2 h-2 rounded-full bg-green-600 mr-1"></span>
                      <span>Religious Holiday</span>
                    </div>
                    <div class="flex items-center">
                      <span class="inline-block w-2 h-2 rounded-full bg-orange-200 mr-1"></span>
                      <span>High Season</span>
                    </div>
                  </template>
                </div>
              </template>
              <template v-slot:footer>
                <div class="p-2 border-t text-right">
                  <button
                    @click="clearDates"
                    class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800"
                  >
                    Clear
                  </button>
                  <button
                    @click="isDatePickerOpen = false"
                    class="ml-2 px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700"
                  >
                    Apply
                  </button>
                </div>
              </template>
            </DatePicker>
          </div>
        </div>
      </div>
      
      <!-- Guests -->
      <div class="relative z-[99999]">
        <label class="block text-sm font-medium text-gray-700 mb-1">Guests</label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <UserGroupIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
          </div>
          <div ref="guestsRef" @click="openGuestsDropdown" class="cursor-pointer">
            <input
              type="text"
              class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Add guests"
              readonly
              :value="guestsText"
            >
            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
              <ChevronDownIcon
                class="h-5 w-5 text-gray-400 transform transition-transform"
                :class="{'rotate-180': isGuestsOpen}"
                aria-hidden="true"
              />
            </div>
          </div>
          <!-- Guests Dropdown moved here -->
          <div v-if="isGuestsOpen"
               ref="guestsDropdownRef"
               class="absolute z-[999999] bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden right-0"
               style="top: 100%; width: 320px">
            <div class="p-4 w-[320px]">
              <!-- Adults -->
              <div class="flex items-center justify-between mb-4">
                <div>
                  <div class="font-medium">Adults</div>
                  <div class="text-sm text-gray-500">Ages 13+</div>
                </div>
                <div class="flex items-center">
                  <button
                    @click="guests.adults = Math.max(1, guests.adults - 1)"
                    class="p-1 w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:border-blue-500 hover:text-blue-500 transition-colors"
                    :class="{ 'opacity-50 cursor-not-allowed': guests.adults <= 1 }"
                    :disabled="guests.adults <= 1"
                  >
                    <MinusIcon class="h-4 w-4" />
                  </button>
                  <span class="w-10 text-center">{{ guests.adults }}</span>
                  <button
                    @click="guests.adults = Math.min(10, guests.adults + 1)"
                    class="p-1 w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:border-blue-500 hover:text-blue-500 transition-colors"
                    :class="{ 'opacity-50 cursor-not-allowed': guests.adults >= 10 }"
                    :disabled="guests.adults >= 10"
                  >
                    <PlusIcon class="h-4 w-4" />
                  </button>
                </div>
              </div>

              <!-- Children -->
              <div class="flex items-center justify-between mb-4">
                <div>
                  <div class="font-medium">Children</div>
                  <div class="text-sm text-gray-500">Ages 2-12</div>
                </div>
                <div class="flex items-center">
                  <button
                    @click="removeChild"
                    class="p-1 w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:border-blue-500 hover:text-blue-500 transition-colors"
                    :class="{ 'opacity-50 cursor-not-allowed': guests.children.length === 0 }"
                    :disabled="guests.children.length === 0"
                  >
                    <MinusIcon class="h-4 w-4" />
                  </button>
                  <span class="w-10 text-center">{{ guests.children.length }}</span>
                  <button
                    @click="addChild"
                    class="p-1 w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:border-blue-500 hover:text-blue-500 transition-colors"
                    :class="{ 'opacity-50 cursor-not-allowed': guests.children.length >= 6 }"
                    :disabled="guests.children.length >= 6"
                  >
                    <PlusIcon class="h-4 w-4" />
                  </button>
                </div>
              </div>

              <!-- Child Ages -->
              <div v-if="guests.children.length > 0" class="mb-4">
                <div class="mb-2 font-medium text-sm">Child Ages</div>
                <div class="max-h-40 overflow-y-auto pr-2 custom-scrollbar">
                  <div class="grid grid-cols-3 gap-2">
                    <div v-for="(child, index) in guests.children" :key="index" class="flex flex-col">
                      <label class="text-xs text-gray-500 mb-1">Child {{ index + 1 }}</label>
                      <select
                        v-model="guests.children[index].age"
                        class="border border-gray-300 rounded-md text-sm p-1"
                      >
                        <option v-for="age in 11" :key="age" :value="age + 1">
                          {{ age + 1 }} {{ age === 0 ? 'year' : 'years' }}
                        </option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Rooms -->
              <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200">
                <div>
                  <div class="font-medium">Rooms</div>
                </div>
                <div class="flex items-center">
                  <button
                    @click="guests.rooms = Math.max(1, guests.rooms - 1)"
                    class="p-1 w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:border-blue-500 hover:text-blue-500 transition-colors"
                    :class="{ 'opacity-50 cursor-not-allowed': guests.rooms <= 1 }"
                    :disabled="guests.rooms <= 1"
                  >
                    <MinusIcon class="h-4 w-4" />
                  </button>
                  <span class="w-10 text-center">{{ guests.rooms }}</span>
                  <button
                    @click="guests.rooms = Math.min(5, guests.rooms + 1)"
                    class="p-1 w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:border-blue-500 hover:text-blue-500 transition-colors"
                    :class="{ 'opacity-50 cursor-not-allowed': guests.rooms >= 5 }"
                    :disabled="guests.rooms >= 5"
                  >
                    <PlusIcon class="h-4 w-4" />
                  </button>
                </div>
              </div>

              <button
                @click="isGuestsOpen = false"
                class="mt-4 w-full py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                Apply
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Search Button -->
      <div class="flex items-end">
        <button 
          @click="search" 
          class="w-full py-3 px-4 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 flex items-center justify-center transition-colors"
        >
          <MagnifyingGlassIcon class="h-5 w-5 mr-2" />
          Search Hotels
        </button>
      </div>
    </div>

    <!-- Mobile actions - Advanced Options -->
    <div class="mt-4 border-t pt-4 md:hidden">
      <div class="flex items-center justify-between">
        <button class="flex items-center text-sm text-blue-600 font-medium" @click="showAdvancedOptions = !showAdvancedOptions">
          <AdjustmentsHorizontalIcon class="h-4 w-4 mr-1" />
          {{ showAdvancedOptions ? 'Hide advanced options' : 'Show advanced options' }}
        </button>
        <button class="text-sm text-gray-600" @click="clearForm">Clear all</button>
      </div>
    </div>

    <!-- Advanced options section -->
    <div v-if="showAdvancedOptions" class="mt-4 pt-2 border-t border-gray-200">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Star Rating Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Star Rating</label>
          <div class="flex flex-wrap gap-2">
            <button 
              v-for="star in 5" 
              :key="star"
              @click="toggleStarFilter(star)"
              class="px-3 py-1 border rounded-full text-sm transition-colors"
              :class="starFilter.includes(star) 
                ? 'bg-blue-600 text-white border-blue-600' 
                : 'border-gray-300 text-gray-700 hover:border-blue-500'"
            >
              {{ star }}★
            </button>
          </div>
        </div>
        
        <!-- Price Range -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
          <div class="grid grid-cols-2 gap-2">
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-500">$</span>
              </div>
              <input 
                type="number"
                v-model.number="priceRange.min"
                placeholder="Min"
                class="block w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md"
                min="0"
              />
            </div>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-500">$</span>
              </div>
              <input 
                type="number"
                v-model.number="priceRange.max"
                placeholder="Max"
                class="block w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md"
                min="0"
              />
            </div>
          </div>
        </div>
        
        <!-- Amenities Quick Filters -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Amenities</label>
          <div class="flex flex-wrap gap-2">
            <button 
              v-for="amenity in popularAmenities"
              :key="amenity.id"
              @click="toggleAmenity(amenity.id)"
              class="px-3 py-1 border rounded-full text-sm transition-colors"
              :class="selectedAmenities.includes(amenity.id) 
                ? 'bg-blue-600 text-white border-blue-600' 
                : 'border-gray-300 text-gray-700 hover:border-blue-500'"
            >
              {{ amenity.name }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue';
import { DatePicker } from 'v-calendar';
import 'v-calendar/style.css';
import { 
  MapPinIcon, 
  CalendarIcon, 
  ChevronDownIcon, 
  UserGroupIcon, 
  PlusIcon, 
  MinusIcon,
  MagnifyingGlassIcon,
  AdjustmentsHorizontalIcon
} from '@heroicons/vue/24/outline';
import { useBreakpoints } from '@vueuse/core';

// Destinations data
const destinationsData = [
  { id: 1, name: 'Istanbul', region: 'Marmara Region', country: 'Turkey' },
  { id: 2, name: 'Antalya', region: 'Mediterranean Coast', country: 'Turkey' },
  { id: 3, name: 'Bodrum', region: 'Aegean Coast', country: 'Turkey' },
  { id: 4, name: 'Cappadocia', region: 'Central Anatolia', country: 'Turkey' },
  { id: 5, name: 'Izmir', region: 'Aegean Coast', country: 'Turkey' },
  { id: 6, name: 'Fethiye', region: 'Mediterranean Coast', country: 'Turkey' },
  { id: 7, name: 'Alanya', region: 'Mediterranean Coast', country: 'Turkey' },
  { id: 8, name: 'Marmaris', region: 'Aegean Coast', country: 'Turkey' },
  { id: 9, name: 'Ankara', region: 'Central Anatolia', country: 'Turkey' },
  { id: 10, name: 'Trabzon', region: 'Black Sea Coast', country: 'Turkey' },
];

const popularAmenities = [
  { id: 'wifi', name: 'Free WiFi' },
  { id: 'pool', name: 'Pool' },
  { id: 'breakfast', name: 'Breakfast' },
  { id: 'parking', name: 'Free Parking' },
  { id: 'beach', name: 'Beach Access' },
];

// Form state
const isDestinationOpen = ref(false);
const isDatePickerOpen = ref(false);
const isGuestsOpen = ref(false);
const showAdvancedOptions = ref(false);

const destination = ref(null);
const destinationSearch = ref('');
const filteredDestinations = ref([...destinationsData]);

const dateRange = ref({
  start: null,
  end: null
});

// Turkish public holidays - will be fetched from API
const turkishHolidays = ref([]);
const isLoadingHolidays = ref(true);
const highSeason = ref({
  start: '2025-06-01',
  end: '2025-09-15',
  name: 'High Season',
  class: 'season-high'
});

// Fetch holidays from the Nager.date API - free public holiday API
const fetchTurkishHolidays = async () => {
  isLoadingHolidays.value = true;
  
  try {
    // Get current year for default fetching
    const currentYear = new Date().getFullYear();
    // Fetch holidays for current and next year to ensure we have data
    const [currentYearResponse, nextYearResponse] = await Promise.all([
      fetch(`https://date.nager.at/api/v3/publicholidays/${currentYear}/TR`),
      fetch(`https://date.nager.at/api/v3/publicholidays/${currentYear + 1}/TR`)
    ]);
    
    const currentYearData = await currentYearResponse.json();
    const nextYearData = await nextYearResponse.json();
    
    // Combine and process holidays
    const holidays = [...currentYearData, ...nextYearData].map(holiday => {
      // Determine if it's a religious holiday based on name
      const isReligious = 
        holiday.name.includes('Ramadan') || 
        holiday.name.includes('Eid') || 
        holiday.name.includes('Feast') ||
        holiday.name.includes('Bayram');
        
      return {
        date: holiday.date,
        name: holiday.localName || holiday.name,
        class: isReligious ? 'holiday-religious' : 'holiday'
      };
    });
    
    // Update the holidays ref
    turkishHolidays.value = holidays;
  } catch (error) {
    console.error('Error fetching holidays:', error);
    
    // Fallback to static holidays if API fails
    turkishHolidays.value = [
      // 2025 Holidays (fallback when API fails)
      { date: '2025-01-01', name: 'New Year\'s Day', class: 'holiday' },
      { date: '2025-04-23', name: 'National Sovereignty and Children\'s Day', class: 'holiday' },
      { date: '2025-05-01', name: 'Labor and Solidarity Day', class: 'holiday' },
      { date: '2025-05-19', name: 'Commemoration of Atatürk, Youth and Sports Day', class: 'holiday' },
      { date: '2025-07-15', name: 'Democracy and National Unity Day', class: 'holiday' },
      { date: '2025-08-30', name: 'Victory Day', class: 'holiday' },
      { date: '2025-10-29', name: 'Republic Day', class: 'holiday' },
      
      // 2025 Religious Holidays (estimated fallback)
      { date: '2025-04-02', name: 'Ramadan Feast (1st Day)', class: 'holiday-religious' },
      { date: '2025-04-03', name: 'Ramadan Feast (2nd Day)', class: 'holiday-religious' },
      { date: '2025-04-04', name: 'Ramadan Feast (3rd Day)', class: 'holiday-religious' },
      { date: '2025-06-11', name: 'Sacrifice Feast (1st Day)', class: 'holiday-religious' },
      { date: '2025-06-12', name: 'Sacrifice Feast (2nd Day)', class: 'holiday-religious' },
      { date: '2025-06-13', name: 'Sacrifice Feast (3rd Day)', class: 'holiday-religious' },
      { date: '2025-06-14', name: 'Sacrifice Feast (4th Day)', class: 'holiday-religious' },
    ];
  } finally {
    isLoadingHolidays.value = false;
  }
};

const guests = ref({
  adults: 2,
  children: [],
  rooms: 1
});

const starFilter = ref([]);
const selectedAmenities = ref([]);
const priceRange = ref({
  min: null,
  max: null
});

// Refs for elements
const searchFormRef = ref(null);
const destinationRef = ref(null);
const datePickerRef = ref(null);
const guestsRef = ref(null);
const destinationDropdownRef = ref(null);
const datePickerDropdownRef = ref(null);
const guestsDropdownRef = ref(null);

// Computed styles for dropdown positions
const destinationDropdownStyle = ref({
  top: '0px',
  left: '0px',
  width: '320px'
});

const datePickerDropdownStyle = ref({
  top: '0px',
  left: '0px',
  width: '600px'
});

const guestsDropdownStyle = ref({
  top: '0px',
  left: '0px',
  width: '320px'
});

// Update dropdown position is no longer needed since dropdowns are positioned relatively
const updateDropdownPositions = () => {
  // This function is now empty as we don't need to update positions dynamically
  // Keeping it to avoid breaking any existing code that might call it
};

// No need to watch for scroll events anymore
// The dropdowns will naturally stay with their parent elements

// Filter destinations based on search
const filterDestinations = () => {
  if (!destinationSearch.value) {
    filteredDestinations.value = [...destinationsData];
    return;
  }
  
  const search = destinationSearch.value.toLowerCase();
  filteredDestinations.value = destinationsData.filter(
    dest => dest.name.toLowerCase().includes(search) || 
           dest.region.toLowerCase().includes(search) ||
           dest.country.toLowerCase().includes(search)
  );
};

// Dropdown toggle handlers
const openDestinationDropdown = () => {
  isDestinationOpen.value = !isDestinationOpen.value;
  if (isDestinationOpen.value) {
    isDatePickerOpen.value = false;
    isGuestsOpen.value = false;
  }
};

const openDatePickerDropdown = () => {
  isDatePickerOpen.value = !isDatePickerOpen.value;
  if (isDatePickerOpen.value) {
    isDestinationOpen.value = false;
    isGuestsOpen.value = false;
  }
};

const openGuestsDropdown = () => {
  isGuestsOpen.value = !isGuestsOpen.value;
  if (isGuestsOpen.value) {
    isDestinationOpen.value = false;
    isDatePickerOpen.value = false;
  }
};

// Select a destination
const selectDestination = (option) => {
  destination.value = option;
  isDestinationOpen.value = false;
  destinationSearch.value = '';
  filterDestinations();
};

// Format date range for display
const formatDateRange = computed(() => {
  if (!dateRange.value.start) return '';
  
  const formatDate = (date) => {
    if (!date) return '';
    return new Intl.DateTimeFormat('en-US', { month: 'short', day: 'numeric', year: 'numeric' }).format(date);
  };

  if (dateRange.value.start && dateRange.value.end) {
    return `${formatDate(dateRange.value.start)} - ${formatDate(dateRange.value.end)}`;
  }
  
  return formatDate(dateRange.value.start);
});

// Day click handler for date picker
const onDayClick = () => {
  if (dateRange.value.start && dateRange.value.end) {
    // Both dates selected, keep the dropdown open for potential changes
  }
};

// Convert Turkish holidays to calendar attributes
const holidayAttributes = computed(() => {
  const attributes = [
    // Selected date range highlight (this is already handled by v-model.range)
    
    // National holidays (red dot)
    ...turkishHolidays.value
      .filter(h => h.class === 'holiday')
      .map(holiday => ({
        key: `holiday-${holiday.date}`,
        dates: holiday.date,
        dot: {
          color: 'red',
          class: 'holiday-dot'
        },
        popover: {
          label: holiday.name,
          visibility: 'hover'
        }
      })),
    
    // Religious holidays (green dot)
    ...turkishHolidays.value
      .filter(h => h.class === 'holiday-religious')
      .map(holiday => ({
        key: `religious-${holiday.date}`,
        dates: holiday.date,
        dot: {
          color: 'green',
          class: 'religious-holiday-dot'
        },
        popover: {
          label: holiday.name,
          visibility: 'hover'
        }
      })),
    
    // High season (highlighted background)
    {
      key: 'high-season',
      dates: { start: '2025-06-01', end: '2025-09-15' },
      highlight: {
        color: 'orange',
        fillMode: 'light',
      },
      popover: {
        label: 'High Season - Best prices when booking early',
        visibility: 'hover'
      },
      order: -1 // To ensure this is shown under other highlights
    }
  ];
  
  return attributes;
});

// Clear dates
const clearDates = () => {
  dateRange.value = {
    start: null,
    end: null
  };
};

// Add a child
const addChild = () => {
  if (guests.value.children.length < 6) {
    guests.value.children.push({ age: 6 });
  }
};

// Remove a child
const removeChild = () => {
  if (guests.value.children.length > 0) {
    guests.value.children.pop();
  }
};

// Formatted guests text
const guestsText = computed(() => {
  const adultsText = `${guests.value.adults} ${guests.value.adults === 1 ? 'Adult' : 'Adults'}`;
  const childrenText = guests.value.children.length > 0 
    ? `, ${guests.value.children.length} ${guests.value.children.length === 1 ? 'Child' : 'Children'}`
    : '';
  const roomsText = `, ${guests.value.rooms} ${guests.value.rooms === 1 ? 'Room' : 'Rooms'}`;
  
  return adultsText + childrenText + roomsText;
});

// Toggle star filter
const toggleStarFilter = (star) => {
  if (starFilter.value.includes(star)) {
    starFilter.value = starFilter.value.filter(s => s !== star);
  } else {
    starFilter.value.push(star);
  }
};

// Toggle amenity
const toggleAmenity = (amenityId) => {
  if (selectedAmenities.value.includes(amenityId)) {
    selectedAmenities.value = selectedAmenities.value.filter(a => a !== amenityId);
  } else {
    selectedAmenities.value.push(amenityId);
  }
};

// Clear form
const clearForm = () => {
  destination.value = null;
  dateRange.value = { start: null, end: null };
  guests.value = { adults: 2, children: [], rooms: 1 };
  starFilter.value = [];
  selectedAmenities.value = [];
  priceRange.value = { min: null, max: null };
  showAdvancedOptions.value = false;
};

// Search function
const search = () => {
  console.log('Search with parameters:', {
    destination: destination.value,
    dates: dateRange.value,
    guests: guests.value,
    starRating: starFilter.value,
    amenities: selectedAmenities.value,
    priceRange: priceRange.value
  });
  
  // Here you would redirect to search results page with these parameters
  // This is just a placeholder
  // router.push({ name: 'hotels.index', query: {...} });
};

// Responsive breakpoints
const breakpoints = useBreakpoints({
  mobile: 640,
  tablet: 768,
  desktop: 1024,
});
const isMobile = computed(() => !breakpoints.greater('mobile'));

// Handle document clicks for closing dropdowns
const handleDocumentClick = (event) => {
  // Check if click was inside dropdown elements or their triggers
  const clickedInsideDestination = destinationRef.value?.contains(event.target) || 
                                   destinationDropdownRef.value?.contains(event.target);
                                   
  const clickedInsideDatePicker = datePickerRef.value?.contains(event.target) || 
                                  datePickerDropdownRef.value?.contains(event.target);
                                  
  const clickedInsideGuests = guestsRef.value?.contains(event.target) || 
                              guestsDropdownRef.value?.contains(event.target);
  
  if (isDestinationOpen.value && !clickedInsideDestination) {
    isDestinationOpen.value = false;
  }
  
  if (isDatePickerOpen.value && !clickedInsideDatePicker) {
    isDatePickerOpen.value = false;
  }
  
  if (isGuestsOpen.value && !clickedInsideGuests) {
    isGuestsOpen.value = false;
  }
};

// Close dropdowns when Escape key is pressed
const handleEscKey = (event) => {
  if (event.key === 'Escape') {
    isDestinationOpen.value = false;
    isDatePickerOpen.value = false;
    isGuestsOpen.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', handleDocumentClick);
  document.addEventListener('keydown', handleEscKey);

  // Fetch Turkish holidays data when component mounts
  fetchTurkishHolidays();
});

onUnmounted(() => {
  document.removeEventListener('click', handleDocumentClick);
  document.removeEventListener('keydown', handleEscKey);
});
</script>

<style>
.vc-container {
  font-family: inherit !important;
  z-index: 9999999 !important;
}

/* Ensure all date picker parts appear on top */
.vc-popover-content-wrapper {
  z-index: 9999999 !important;
} 

/* Custom styles for holiday indicators */
:deep(.holiday-dot) {
  opacity: 0.8;
  border: 1px solid #fff;
}

:deep(.religious-holiday-dot) {
  opacity: 0.8;
  border: 1px solid #fff;
}

/* Custom Tooltip styling for holidays */
:deep(.vc-day-popover-content) {
  padding: 6px 10px;
  font-size: 12px;
  background-color: rgba(0, 0, 0, 0.8);
  color: white;
  border-radius: 6px;
  font-weight: 500;
}

/* Add a legend for the calendar dots */
:deep(.vc-pane-container) {
  position: relative;
}

:deep(.calendar-legend) {
  position: absolute;
  bottom: 6px;
  left: 8px;
  right: 8px;
  display: flex;
  justify-content: center;
  gap: 12px;
  font-size: 10px;
  color: #666;
  padding: 4px;
  border-top: 1px solid #eee;
}

/* Spinner animation for loading indicator */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

/* Improve child age selector on mobile */
@media (max-width: 639px) {
  .grid-cols-3 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

/* Custom scrollbar styles */
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #555;
}

</style>