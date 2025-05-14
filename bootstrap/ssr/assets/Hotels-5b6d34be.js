import { ref, computed, onMounted, unref, withCtx, createTextVNode, createVNode, withDirectives, vModelText, openBlock, createBlock, vModelCheckbox, Fragment, renderList, toDisplayString, vModelSelect, createCommentVNode, useSSRContext } from "vue";
import { ssrRenderComponent, ssrRenderAttr, ssrIncludeBooleanAttr, ssrLooseContain, ssrRenderList, ssrInterpolate, ssrLooseEqual, ssrRenderClass } from "vue/server-renderer";
import { Head, Link } from "@inertiajs/vue3";
import { G as GuestLayout } from "./GuestLayout-6b7af627.js";
const itemsPerPage = 5;
const _sfc_main = {
  __name: "Hotels",
  __ssrInlineRender: true,
  setup(__props) {
    const hotels = ref([
      {
        id: 1,
        name: "Grand Resort & Spa",
        location: "Antalya, Turkey",
        description: "Luxurious beachfront resort with spectacular views of the Mediterranean Sea, featuring elegant rooms and world-class dining.",
        rating: 4.8,
        reviewCount: 642,
        price: 189,
        oldPrice: 229,
        discount: 18,
        image: "/images/hotels/hotel1.jpg",
        type: "resort",
        amenities: ["Free WiFi", "Swimming Pool", "Spa", "Restaurant", "Fitness Center"],
        freeCancellation: true
      },
      {
        id: 2,
        name: "Azure Bay Hotel",
        location: "Bodrum, Turkey",
        description: "Modern boutique hotel nestled in a quiet bay, offering stylish accommodations with private balconies overlooking the Aegean Sea.",
        rating: 4.6,
        reviewCount: 428,
        price: 159,
        image: "/images/hotels/hotel2.jpg",
        type: "hotel",
        amenities: ["Free WiFi", "Swimming Pool", "Restaurant", "Beach Access"],
        freeCancellation: true
      },
      {
        id: 3,
        name: "Mountain View Lodge",
        location: "Cappadocia, Turkey",
        description: "Charming lodge with stunning views of the unique Cappadocian landscape, featuring cave-inspired rooms and authentic Turkish hospitality.",
        rating: 4.4,
        reviewCount: 356,
        price: 129,
        oldPrice: 149,
        discount: 13,
        image: "/images/hotels/hotel3.jpg",
        type: "hotel",
        amenities: ["Free WiFi", "Restaurant", "Free Parking", "Airport Shuttle"],
        freeCancellation: false
      },
      {
        id: 4,
        name: "Sunset Beach Villas",
        location: "Fethiye, Turkey",
        description: "Exclusive beachfront villas with private pools, perfect for families seeking privacy and direct beach access in a serene environment.",
        rating: 4.9,
        reviewCount: 287,
        price: 349,
        image: "/images/hotels/hotel4.jpg",
        type: "villa",
        amenities: ["Free WiFi", "Private Pool", "Beach Access", "Kitchen", "Free Parking"],
        freeCancellation: true
      },
      {
        id: 5,
        name: "Urban Deluxe Suites",
        location: "Istanbul, Turkey",
        description: "Contemporary apartment-style accommodations in the heart of Istanbul, offering spacious living areas and panoramic city views.",
        rating: 4.3,
        reviewCount: 512,
        price: 139,
        oldPrice: 179,
        discount: 22,
        image: "/images/hotels/hotel5.jpg",
        type: "apartment",
        amenities: ["Free WiFi", "Kitchen", "Fitness Center", "Laundry Service"],
        freeCancellation: false
      },
      {
        id: 6,
        name: "Historic Palace Hotel",
        location: "Istanbul, Turkey",
        description: "Elegant hotel housed in a restored Ottoman palace, combining historical architecture with modern luxury amenities.",
        rating: 4.7,
        reviewCount: 723,
        price: 229,
        image: "/images/hotels/hotel6.jpg",
        type: "hotel",
        amenities: ["Free WiFi", "Swimming Pool", "Spa", "Restaurant", "Fitness Center"],
        freeCancellation: true
      },
      {
        id: 7,
        name: "Aegean Breeze Resort",
        location: "Izmir, Turkey",
        description: "Family-friendly beachfront resort with extensive recreational facilities, including water parks and kids' clubs.",
        rating: 4.5,
        reviewCount: 489,
        price: 169,
        oldPrice: 199,
        discount: 15,
        image: "/images/hotels/hotel7.jpg",
        type: "resort",
        amenities: ["Free WiFi", "Swimming Pool", "Kids Club", "Restaurant", "Beach Access"],
        freeCancellation: true
      },
      {
        id: 8,
        name: "Pine Forest Cabins",
        location: "Bursa, Turkey",
        description: "Cozy wooden cabins nestled in a pine forest, offering a tranquil retreat with mountain views and outdoor activities.",
        rating: 4.2,
        reviewCount: 231,
        price: 99,
        image: "/images/hotels/hotel8.jpg",
        type: "villa",
        amenities: ["Free WiFi", "Fireplace", "Free Parking", "Nature Trails"],
        freeCancellation: false
      }
    ]);
    const search = ref("");
    const sortBy = ref("recommended");
    const currentPage = ref(1);
    const filters = ref({
      minPrice: null,
      maxPrice: null,
      rating: [],
      amenities: [],
      propertyType: []
    });
    const resetFilters = () => {
      search.value = "";
      filters.value = {
        minPrice: null,
        maxPrice: null,
        rating: [],
        amenities: [],
        propertyType: []
      };
      applyFilters();
    };
    const applyFilters = () => {
      currentPage.value = 1;
    };
    const filteredHotels = computed(() => {
      let result = [...hotels.value];
      if (search.value) {
        const searchLower = search.value.toLowerCase();
        result = result.filter(
          (hotel) => hotel.name.toLowerCase().includes(searchLower) || hotel.location.toLowerCase().includes(searchLower) || hotel.description.toLowerCase().includes(searchLower)
        );
      }
      if (filters.value.minPrice) {
        result = result.filter((hotel) => hotel.price >= filters.value.minPrice);
      }
      if (filters.value.maxPrice) {
        result = result.filter((hotel) => hotel.price <= filters.value.maxPrice);
      }
      if (filters.value.rating.length > 0) {
        result = result.filter((hotel) => {
          return filters.value.rating.some((r) => {
            const ratingValue = parseInt(r);
            return Math.floor(hotel.rating) === ratingValue;
          });
        });
      }
      if (filters.value.amenities.length > 0) {
        result = result.filter((hotel) => {
          return filters.value.amenities.every((amenity) => {
            amenity.charAt(0).toUpperCase() + amenity.slice(1);
            return hotel.amenities.some((a) => a.toLowerCase().includes(amenity));
          });
        });
      }
      if (filters.value.propertyType.length > 0) {
        result = result.filter(
          (hotel) => filters.value.propertyType.includes(hotel.type)
        );
      }
      switch (sortBy.value) {
        case "price-asc":
          result.sort((a, b) => a.price - b.price);
          break;
        case "price-desc":
          result.sort((a, b) => b.price - a.price);
          break;
        case "rating-desc":
          result.sort((a, b) => b.rating - a.rating);
          break;
        case "popularity":
          result.sort((a, b) => b.reviewCount - a.reviewCount);
          break;
      }
      return result;
    });
    const totalPages = computed(() => {
      return Math.ceil(filteredHotels.value.length / itemsPerPage);
    });
    computed(() => {
      const start = (currentPage.value - 1) * itemsPerPage;
      const end = start + itemsPerPage;
      return filteredHotels.value.slice(start, end);
    });
    const route = (name, params) => {
      if (name === "hotels.show") {
        return `/hotels/${params}`;
      }
      return "/";
    };
    onMounted(() => {
      console.log("Hotels page mounted");
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), { title: "Hotels" }, null, _parent));
      _push(ssrRenderComponent(GuestLayout, null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="bg-gray-50 py-10"${_scopeId}><div class="container mx-auto px-4"${_scopeId}><div class="mb-8"${_scopeId}><h1 class="text-3xl font-bold text-gray-900"${_scopeId}>Find Your Perfect Hotel</h1><p class="text-gray-600 mt-2"${_scopeId}>Browse our curated selection of premium hotels and accommodations</p></div><div class="flex flex-col lg:flex-row gap-8"${_scopeId}><div class="w-full lg:w-1/4 bg-white rounded-lg shadow-sm p-4"${_scopeId}><div class="mb-6"${_scopeId}><h3 class="font-medium text-lg mb-3 text-gray-900"${_scopeId}>Search</h3><div class="relative"${_scopeId}><input type="text" class="w-full border-gray-300 rounded-md pl-10 pr-4 py-2 focus:border-blue-500 focus:ring-blue-500" placeholder="Search hotels..."${ssrRenderAttr("value", search.value)}${_scopeId}><div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"${_scopeId}><svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"${_scopeId}></path></svg></div></div></div><div class="mb-6"${_scopeId}><h3 class="font-medium text-lg mb-3 text-gray-900"${_scopeId}>Price Range</h3><div class="flex items-center space-x-3"${_scopeId}><input type="number" class="w-full border-gray-300 rounded-md" placeholder="Min"${ssrRenderAttr("value", filters.value.minPrice)}${_scopeId}><span class="text-gray-500"${_scopeId}>-</span><input type="number" class="w-full border-gray-300 rounded-md" placeholder="Max"${ssrRenderAttr("value", filters.value.maxPrice)}${_scopeId}></div></div><div class="mb-6"${_scopeId}><h3 class="font-medium text-lg mb-3 text-gray-900"${_scopeId}>Rating</h3><div class="space-y-2"${_scopeId}><label class="flex items-center"${_scopeId}><input type="checkbox" class="rounded text-blue-600"${ssrIncludeBooleanAttr(Array.isArray(filters.value.rating) ? ssrLooseContain(filters.value.rating, "5") : filters.value.rating) ? " checked" : ""} value="5"${_scopeId}><span class="ml-2 text-gray-700 flex"${_scopeId}><!--[-->`);
            ssrRenderList(5, (i) => {
              _push2(`<span class="text-yellow-400"${_scopeId}>★</span>`);
            });
            _push2(`<!--]--></span></label><label class="flex items-center"${_scopeId}><input type="checkbox" class="rounded text-blue-600"${ssrIncludeBooleanAttr(Array.isArray(filters.value.rating) ? ssrLooseContain(filters.value.rating, "4") : filters.value.rating) ? " checked" : ""} value="4"${_scopeId}><span class="ml-2 text-gray-700 flex"${_scopeId}><!--[-->`);
            ssrRenderList(4, (i) => {
              _push2(`<span class="text-yellow-400"${_scopeId}>★</span>`);
            });
            _push2(`<!--]--><span class="text-gray-300"${_scopeId}>★</span></span></label><label class="flex items-center"${_scopeId}><input type="checkbox" class="rounded text-blue-600"${ssrIncludeBooleanAttr(Array.isArray(filters.value.rating) ? ssrLooseContain(filters.value.rating, "3") : filters.value.rating) ? " checked" : ""} value="3"${_scopeId}><span class="ml-2 text-gray-700 flex"${_scopeId}><!--[-->`);
            ssrRenderList(3, (i) => {
              _push2(`<span class="text-yellow-400"${_scopeId}>★</span>`);
            });
            _push2(`<!--]--><!--[-->`);
            ssrRenderList(2, (i) => {
              _push2(`<span class="text-gray-300"${_scopeId}>★</span>`);
            });
            _push2(`<!--]--></span></label><label class="flex items-center"${_scopeId}><input type="checkbox" class="rounded text-blue-600"${ssrIncludeBooleanAttr(Array.isArray(filters.value.rating) ? ssrLooseContain(filters.value.rating, "2") : filters.value.rating) ? " checked" : ""} value="2"${_scopeId}><span class="ml-2 text-gray-700 flex"${_scopeId}><!--[-->`);
            ssrRenderList(2, (i) => {
              _push2(`<span class="text-yellow-400"${_scopeId}>★</span>`);
            });
            _push2(`<!--]--><!--[-->`);
            ssrRenderList(3, (i) => {
              _push2(`<span class="text-gray-300"${_scopeId}>★</span>`);
            });
            _push2(`<!--]--></span></label></div></div><div class="mb-6"${_scopeId}><h3 class="font-medium text-lg mb-3 text-gray-900"${_scopeId}>Amenities</h3><div class="space-y-2"${_scopeId}><label class="flex items-center"${_scopeId}><input type="checkbox" class="rounded text-blue-600"${ssrIncludeBooleanAttr(Array.isArray(filters.value.amenities) ? ssrLooseContain(filters.value.amenities, "wifi") : filters.value.amenities) ? " checked" : ""} value="wifi"${_scopeId}><span class="ml-2 text-gray-700"${_scopeId}>Free WiFi</span></label><label class="flex items-center"${_scopeId}><input type="checkbox" class="rounded text-blue-600"${ssrIncludeBooleanAttr(Array.isArray(filters.value.amenities) ? ssrLooseContain(filters.value.amenities, "pool") : filters.value.amenities) ? " checked" : ""} value="pool"${_scopeId}><span class="ml-2 text-gray-700"${_scopeId}>Swimming Pool</span></label><label class="flex items-center"${_scopeId}><input type="checkbox" class="rounded text-blue-600"${ssrIncludeBooleanAttr(Array.isArray(filters.value.amenities) ? ssrLooseContain(filters.value.amenities, "spa") : filters.value.amenities) ? " checked" : ""} value="spa"${_scopeId}><span class="ml-2 text-gray-700"${_scopeId}>Spa &amp; Wellness</span></label><label class="flex items-center"${_scopeId}><input type="checkbox" class="rounded text-blue-600"${ssrIncludeBooleanAttr(Array.isArray(filters.value.amenities) ? ssrLooseContain(filters.value.amenities, "restaurant") : filters.value.amenities) ? " checked" : ""} value="restaurant"${_scopeId}><span class="ml-2 text-gray-700"${_scopeId}>Restaurant</span></label><label class="flex items-center"${_scopeId}><input type="checkbox" class="rounded text-blue-600"${ssrIncludeBooleanAttr(Array.isArray(filters.value.amenities) ? ssrLooseContain(filters.value.amenities, "gym") : filters.value.amenities) ? " checked" : ""} value="gym"${_scopeId}><span class="ml-2 text-gray-700"${_scopeId}>Fitness Center</span></label><label class="flex items-center"${_scopeId}><input type="checkbox" class="rounded text-blue-600"${ssrIncludeBooleanAttr(Array.isArray(filters.value.amenities) ? ssrLooseContain(filters.value.amenities, "parking") : filters.value.amenities) ? " checked" : ""} value="parking"${_scopeId}><span class="ml-2 text-gray-700"${_scopeId}>Free Parking</span></label></div></div><div class="mb-6"${_scopeId}><h3 class="font-medium text-lg mb-3 text-gray-900"${_scopeId}>Property Type</h3><div class="space-y-2"${_scopeId}><label class="flex items-center"${_scopeId}><input type="checkbox" class="rounded text-blue-600"${ssrIncludeBooleanAttr(Array.isArray(filters.value.propertyType) ? ssrLooseContain(filters.value.propertyType, "hotel") : filters.value.propertyType) ? " checked" : ""} value="hotel"${_scopeId}><span class="ml-2 text-gray-700"${_scopeId}>Hotel</span></label><label class="flex items-center"${_scopeId}><input type="checkbox" class="rounded text-blue-600"${ssrIncludeBooleanAttr(Array.isArray(filters.value.propertyType) ? ssrLooseContain(filters.value.propertyType, "resort") : filters.value.propertyType) ? " checked" : ""} value="resort"${_scopeId}><span class="ml-2 text-gray-700"${_scopeId}>Resort</span></label><label class="flex items-center"${_scopeId}><input type="checkbox" class="rounded text-blue-600"${ssrIncludeBooleanAttr(Array.isArray(filters.value.propertyType) ? ssrLooseContain(filters.value.propertyType, "villa") : filters.value.propertyType) ? " checked" : ""} value="villa"${_scopeId}><span class="ml-2 text-gray-700"${_scopeId}>Villa</span></label><label class="flex items-center"${_scopeId}><input type="checkbox" class="rounded text-blue-600"${ssrIncludeBooleanAttr(Array.isArray(filters.value.propertyType) ? ssrLooseContain(filters.value.propertyType, "apartment") : filters.value.propertyType) ? " checked" : ""} value="apartment"${_scopeId}><span class="ml-2 text-gray-700"${_scopeId}>Apartment</span></label></div></div><button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded transition duration-200"${_scopeId}> Apply Filters </button></div><div class="w-full lg:w-3/4"${_scopeId}><div class="bg-white p-4 rounded-lg shadow-sm mb-6 flex justify-between items-center"${_scopeId}><div class="text-sm text-gray-500"${_scopeId}> Showing <span class="font-medium text-gray-900"${_scopeId}>${ssrInterpolate(filteredHotels.value.length)}</span> properties </div><div class="flex items-center space-x-2"${_scopeId}><span class="text-sm text-gray-700"${_scopeId}>Sort by:</span><select class="border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500"${_scopeId}><option value="recommended"${ssrIncludeBooleanAttr(Array.isArray(sortBy.value) ? ssrLooseContain(sortBy.value, "recommended") : ssrLooseEqual(sortBy.value, "recommended")) ? " selected" : ""}${_scopeId}>Recommended</option><option value="price-asc"${ssrIncludeBooleanAttr(Array.isArray(sortBy.value) ? ssrLooseContain(sortBy.value, "price-asc") : ssrLooseEqual(sortBy.value, "price-asc")) ? " selected" : ""}${_scopeId}>Price: Low to High</option><option value="price-desc"${ssrIncludeBooleanAttr(Array.isArray(sortBy.value) ? ssrLooseContain(sortBy.value, "price-desc") : ssrLooseEqual(sortBy.value, "price-desc")) ? " selected" : ""}${_scopeId}>Price: High to Low</option><option value="rating-desc"${ssrIncludeBooleanAttr(Array.isArray(sortBy.value) ? ssrLooseContain(sortBy.value, "rating-desc") : ssrLooseEqual(sortBy.value, "rating-desc")) ? " selected" : ""}${_scopeId}>Rating: High to Low</option><option value="popularity"${ssrIncludeBooleanAttr(Array.isArray(sortBy.value) ? ssrLooseContain(sortBy.value, "popularity") : ssrLooseEqual(sortBy.value, "popularity")) ? " selected" : ""}${_scopeId}>Popularity</option></select></div></div><div class="space-y-6"${_scopeId}><!--[-->`);
            ssrRenderList(filteredHotels.value, (hotel) => {
              _push2(`<div class="bg-white rounded-lg shadow-sm overflow-hidden transition-transform duration-300 hover:shadow-md hover:-translate-y-1"${_scopeId}><div class="flex flex-col md:flex-row"${_scopeId}><div class="w-full md:w-1/3 relative"${_scopeId}><img${ssrRenderAttr("src", hotel.image)}${ssrRenderAttr("alt", hotel.name)} class="h-full w-full object-cover"${_scopeId}>`);
              if (hotel.discount) {
                _push2(`<div class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 text-xs font-bold rounded"${_scopeId}> SAVE ${ssrInterpolate(hotel.discount)}% </div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><div class="w-full md:w-2/3 p-4 md:p-6 flex flex-col"${_scopeId}><div class="flex justify-between items-start mb-2"${_scopeId}><div${_scopeId}><h2 class="text-xl font-bold text-gray-900"${_scopeId}>${ssrInterpolate(hotel.name)}</h2><div class="flex items-center mt-1"${_scopeId}><div class="flex"${_scopeId}><!--[-->`);
              ssrRenderList(Math.floor(hotel.rating), (i) => {
                _push2(`<span class="text-yellow-400"${_scopeId}>★</span>`);
              });
              _push2(`<!--]--><!--[-->`);
              ssrRenderList(5 - Math.floor(hotel.rating), (i) => {
                _push2(`<span class="text-gray-300"${_scopeId}>★</span>`);
              });
              _push2(`<!--]--></div><span class="ml-2 text-sm text-gray-600"${_scopeId}>${ssrInterpolate(hotel.reviewCount)} reviews</span></div></div><div class="text-right"${_scopeId}><div class="text-lg md:text-xl font-bold text-blue-600"${_scopeId}>$${ssrInterpolate(hotel.price)}<span class="text-sm font-normal"${_scopeId}>/night</span></div>`);
              if (hotel.oldPrice) {
                _push2(`<div class="text-sm text-gray-500 line-through"${_scopeId}>$${ssrInterpolate(hotel.oldPrice)}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div></div><div class="text-sm text-gray-600 mb-3 flex items-center"${_scopeId}><svg class="w-4 h-4 text-gray-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"${_scopeId}></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"${_scopeId}></path></svg> ${ssrInterpolate(hotel.location)}</div><p class="text-gray-700 text-sm mb-4 flex-grow"${_scopeId}>${ssrInterpolate(hotel.description)}</p><div class="flex flex-wrap gap-2 mb-4"${_scopeId}><!--[-->`);
              ssrRenderList(hotel.amenities, (amenity, index) => {
                _push2(`<span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-1 rounded"${_scopeId}>${ssrInterpolate(amenity)}</span>`);
              });
              _push2(`<!--]--></div><div class="flex justify-between items-center mt-auto"${_scopeId}><div class="text-sm"${_scopeId}>`);
              if (hotel.freeCancellation) {
                _push2(`<span class="text-green-600 font-medium"${_scopeId}>Free cancellation</span>`);
              } else {
                _push2(`<span class="text-gray-500"${_scopeId}>Non-refundable</span>`);
              }
              _push2(`</div>`);
              _push2(ssrRenderComponent(unref(Link), {
                href: route("hotels.show", hotel.id),
                class: "bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium transition duration-200"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(` View Details `);
                  } else {
                    return [
                      createTextVNode(" View Details ")
                    ];
                  }
                }),
                _: 2
              }, _parent2, _scopeId));
              _push2(`</div></div></div></div>`);
            });
            _push2(`<!--]-->`);
            if (filteredHotels.value.length === 0) {
              _push2(`<div class="text-center py-10"${_scopeId}><svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"${_scopeId}></path></svg><h3 class="mt-2 text-sm font-medium text-gray-900"${_scopeId}>No hotels found</h3><p class="mt-1 text-sm text-gray-500"${_scopeId}>Try adjusting your search or filter criteria.</p><div class="mt-6"${_scopeId}><button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"${_scopeId}> Reset all filters </button></div></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div><div class="mt-8 flex justify-center"${_scopeId}><nav class="flex items-center"${_scopeId}><button class="px-2 py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"${ssrIncludeBooleanAttr(currentPage.value === 1) ? " disabled" : ""}${_scopeId}> Previous </button><div class="flex mx-2"${_scopeId}><!--[-->`);
            ssrRenderList(totalPages.value, (page) => {
              _push2(`<button class="${ssrRenderClass([
                "px-3 py-1 mx-1 rounded-md",
                currentPage.value === page ? "bg-blue-600 text-white" : "text-gray-700 hover:bg-gray-50 border border-gray-300"
              ])}"${_scopeId}>${ssrInterpolate(page)}</button>`);
            });
            _push2(`<!--]--></div><button class="px-2 py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"${ssrIncludeBooleanAttr(currentPage.value === totalPages.value) ? " disabled" : ""}${_scopeId}> Next </button></nav></div></div></div></div></div>`);
          } else {
            return [
              createVNode("div", { class: "bg-gray-50 py-10" }, [
                createVNode("div", { class: "container mx-auto px-4" }, [
                  createVNode("div", { class: "mb-8" }, [
                    createVNode("h1", { class: "text-3xl font-bold text-gray-900" }, "Find Your Perfect Hotel"),
                    createVNode("p", { class: "text-gray-600 mt-2" }, "Browse our curated selection of premium hotels and accommodations")
                  ]),
                  createVNode("div", { class: "flex flex-col lg:flex-row gap-8" }, [
                    createVNode("div", { class: "w-full lg:w-1/4 bg-white rounded-lg shadow-sm p-4" }, [
                      createVNode("div", { class: "mb-6" }, [
                        createVNode("h3", { class: "font-medium text-lg mb-3 text-gray-900" }, "Search"),
                        createVNode("div", { class: "relative" }, [
                          withDirectives(createVNode("input", {
                            type: "text",
                            class: "w-full border-gray-300 rounded-md pl-10 pr-4 py-2 focus:border-blue-500 focus:ring-blue-500",
                            placeholder: "Search hotels...",
                            "onUpdate:modelValue": ($event) => search.value = $event
                          }, null, 8, ["onUpdate:modelValue"]), [
                            [vModelText, search.value]
                          ]),
                          createVNode("div", { class: "absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none" }, [
                            (openBlock(), createBlock("svg", {
                              class: "h-5 w-5 text-gray-400",
                              fill: "none",
                              viewBox: "0 0 24 24",
                              stroke: "currentColor"
                            }, [
                              createVNode("path", {
                                "stroke-linecap": "round",
                                "stroke-linejoin": "round",
                                "stroke-width": "2",
                                d: "M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                              })
                            ]))
                          ])
                        ])
                      ]),
                      createVNode("div", { class: "mb-6" }, [
                        createVNode("h3", { class: "font-medium text-lg mb-3 text-gray-900" }, "Price Range"),
                        createVNode("div", { class: "flex items-center space-x-3" }, [
                          withDirectives(createVNode("input", {
                            type: "number",
                            class: "w-full border-gray-300 rounded-md",
                            placeholder: "Min",
                            "onUpdate:modelValue": ($event) => filters.value.minPrice = $event
                          }, null, 8, ["onUpdate:modelValue"]), [
                            [vModelText, filters.value.minPrice]
                          ]),
                          createVNode("span", { class: "text-gray-500" }, "-"),
                          withDirectives(createVNode("input", {
                            type: "number",
                            class: "w-full border-gray-300 rounded-md",
                            placeholder: "Max",
                            "onUpdate:modelValue": ($event) => filters.value.maxPrice = $event
                          }, null, 8, ["onUpdate:modelValue"]), [
                            [vModelText, filters.value.maxPrice]
                          ])
                        ])
                      ]),
                      createVNode("div", { class: "mb-6" }, [
                        createVNode("h3", { class: "font-medium text-lg mb-3 text-gray-900" }, "Rating"),
                        createVNode("div", { class: "space-y-2" }, [
                          createVNode("label", { class: "flex items-center" }, [
                            withDirectives(createVNode("input", {
                              type: "checkbox",
                              class: "rounded text-blue-600",
                              "onUpdate:modelValue": ($event) => filters.value.rating = $event,
                              value: "5"
                            }, null, 8, ["onUpdate:modelValue"]), [
                              [vModelCheckbox, filters.value.rating]
                            ]),
                            createVNode("span", { class: "ml-2 text-gray-700 flex" }, [
                              (openBlock(), createBlock(Fragment, null, renderList(5, (i) => {
                                return createVNode("span", {
                                  key: i,
                                  class: "text-yellow-400"
                                }, "★");
                              }), 64))
                            ])
                          ]),
                          createVNode("label", { class: "flex items-center" }, [
                            withDirectives(createVNode("input", {
                              type: "checkbox",
                              class: "rounded text-blue-600",
                              "onUpdate:modelValue": ($event) => filters.value.rating = $event,
                              value: "4"
                            }, null, 8, ["onUpdate:modelValue"]), [
                              [vModelCheckbox, filters.value.rating]
                            ]),
                            createVNode("span", { class: "ml-2 text-gray-700 flex" }, [
                              (openBlock(), createBlock(Fragment, null, renderList(4, (i) => {
                                return createVNode("span", {
                                  key: i,
                                  class: "text-yellow-400"
                                }, "★");
                              }), 64)),
                              createVNode("span", { class: "text-gray-300" }, "★")
                            ])
                          ]),
                          createVNode("label", { class: "flex items-center" }, [
                            withDirectives(createVNode("input", {
                              type: "checkbox",
                              class: "rounded text-blue-600",
                              "onUpdate:modelValue": ($event) => filters.value.rating = $event,
                              value: "3"
                            }, null, 8, ["onUpdate:modelValue"]), [
                              [vModelCheckbox, filters.value.rating]
                            ]),
                            createVNode("span", { class: "ml-2 text-gray-700 flex" }, [
                              (openBlock(), createBlock(Fragment, null, renderList(3, (i) => {
                                return createVNode("span", {
                                  key: i,
                                  class: "text-yellow-400"
                                }, "★");
                              }), 64)),
                              (openBlock(), createBlock(Fragment, null, renderList(2, (i) => {
                                return createVNode("span", {
                                  key: i + 3,
                                  class: "text-gray-300"
                                }, "★");
                              }), 64))
                            ])
                          ]),
                          createVNode("label", { class: "flex items-center" }, [
                            withDirectives(createVNode("input", {
                              type: "checkbox",
                              class: "rounded text-blue-600",
                              "onUpdate:modelValue": ($event) => filters.value.rating = $event,
                              value: "2"
                            }, null, 8, ["onUpdate:modelValue"]), [
                              [vModelCheckbox, filters.value.rating]
                            ]),
                            createVNode("span", { class: "ml-2 text-gray-700 flex" }, [
                              (openBlock(), createBlock(Fragment, null, renderList(2, (i) => {
                                return createVNode("span", {
                                  key: i,
                                  class: "text-yellow-400"
                                }, "★");
                              }), 64)),
                              (openBlock(), createBlock(Fragment, null, renderList(3, (i) => {
                                return createVNode("span", {
                                  key: i + 2,
                                  class: "text-gray-300"
                                }, "★");
                              }), 64))
                            ])
                          ])
                        ])
                      ]),
                      createVNode("div", { class: "mb-6" }, [
                        createVNode("h3", { class: "font-medium text-lg mb-3 text-gray-900" }, "Amenities"),
                        createVNode("div", { class: "space-y-2" }, [
                          createVNode("label", { class: "flex items-center" }, [
                            withDirectives(createVNode("input", {
                              type: "checkbox",
                              class: "rounded text-blue-600",
                              "onUpdate:modelValue": ($event) => filters.value.amenities = $event,
                              value: "wifi"
                            }, null, 8, ["onUpdate:modelValue"]), [
                              [vModelCheckbox, filters.value.amenities]
                            ]),
                            createVNode("span", { class: "ml-2 text-gray-700" }, "Free WiFi")
                          ]),
                          createVNode("label", { class: "flex items-center" }, [
                            withDirectives(createVNode("input", {
                              type: "checkbox",
                              class: "rounded text-blue-600",
                              "onUpdate:modelValue": ($event) => filters.value.amenities = $event,
                              value: "pool"
                            }, null, 8, ["onUpdate:modelValue"]), [
                              [vModelCheckbox, filters.value.amenities]
                            ]),
                            createVNode("span", { class: "ml-2 text-gray-700" }, "Swimming Pool")
                          ]),
                          createVNode("label", { class: "flex items-center" }, [
                            withDirectives(createVNode("input", {
                              type: "checkbox",
                              class: "rounded text-blue-600",
                              "onUpdate:modelValue": ($event) => filters.value.amenities = $event,
                              value: "spa"
                            }, null, 8, ["onUpdate:modelValue"]), [
                              [vModelCheckbox, filters.value.amenities]
                            ]),
                            createVNode("span", { class: "ml-2 text-gray-700" }, "Spa & Wellness")
                          ]),
                          createVNode("label", { class: "flex items-center" }, [
                            withDirectives(createVNode("input", {
                              type: "checkbox",
                              class: "rounded text-blue-600",
                              "onUpdate:modelValue": ($event) => filters.value.amenities = $event,
                              value: "restaurant"
                            }, null, 8, ["onUpdate:modelValue"]), [
                              [vModelCheckbox, filters.value.amenities]
                            ]),
                            createVNode("span", { class: "ml-2 text-gray-700" }, "Restaurant")
                          ]),
                          createVNode("label", { class: "flex items-center" }, [
                            withDirectives(createVNode("input", {
                              type: "checkbox",
                              class: "rounded text-blue-600",
                              "onUpdate:modelValue": ($event) => filters.value.amenities = $event,
                              value: "gym"
                            }, null, 8, ["onUpdate:modelValue"]), [
                              [vModelCheckbox, filters.value.amenities]
                            ]),
                            createVNode("span", { class: "ml-2 text-gray-700" }, "Fitness Center")
                          ]),
                          createVNode("label", { class: "flex items-center" }, [
                            withDirectives(createVNode("input", {
                              type: "checkbox",
                              class: "rounded text-blue-600",
                              "onUpdate:modelValue": ($event) => filters.value.amenities = $event,
                              value: "parking"
                            }, null, 8, ["onUpdate:modelValue"]), [
                              [vModelCheckbox, filters.value.amenities]
                            ]),
                            createVNode("span", { class: "ml-2 text-gray-700" }, "Free Parking")
                          ])
                        ])
                      ]),
                      createVNode("div", { class: "mb-6" }, [
                        createVNode("h3", { class: "font-medium text-lg mb-3 text-gray-900" }, "Property Type"),
                        createVNode("div", { class: "space-y-2" }, [
                          createVNode("label", { class: "flex items-center" }, [
                            withDirectives(createVNode("input", {
                              type: "checkbox",
                              class: "rounded text-blue-600",
                              "onUpdate:modelValue": ($event) => filters.value.propertyType = $event,
                              value: "hotel"
                            }, null, 8, ["onUpdate:modelValue"]), [
                              [vModelCheckbox, filters.value.propertyType]
                            ]),
                            createVNode("span", { class: "ml-2 text-gray-700" }, "Hotel")
                          ]),
                          createVNode("label", { class: "flex items-center" }, [
                            withDirectives(createVNode("input", {
                              type: "checkbox",
                              class: "rounded text-blue-600",
                              "onUpdate:modelValue": ($event) => filters.value.propertyType = $event,
                              value: "resort"
                            }, null, 8, ["onUpdate:modelValue"]), [
                              [vModelCheckbox, filters.value.propertyType]
                            ]),
                            createVNode("span", { class: "ml-2 text-gray-700" }, "Resort")
                          ]),
                          createVNode("label", { class: "flex items-center" }, [
                            withDirectives(createVNode("input", {
                              type: "checkbox",
                              class: "rounded text-blue-600",
                              "onUpdate:modelValue": ($event) => filters.value.propertyType = $event,
                              value: "villa"
                            }, null, 8, ["onUpdate:modelValue"]), [
                              [vModelCheckbox, filters.value.propertyType]
                            ]),
                            createVNode("span", { class: "ml-2 text-gray-700" }, "Villa")
                          ]),
                          createVNode("label", { class: "flex items-center" }, [
                            withDirectives(createVNode("input", {
                              type: "checkbox",
                              class: "rounded text-blue-600",
                              "onUpdate:modelValue": ($event) => filters.value.propertyType = $event,
                              value: "apartment"
                            }, null, 8, ["onUpdate:modelValue"]), [
                              [vModelCheckbox, filters.value.propertyType]
                            ]),
                            createVNode("span", { class: "ml-2 text-gray-700" }, "Apartment")
                          ])
                        ])
                      ]),
                      createVNode("button", {
                        class: "w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded transition duration-200",
                        onClick: applyFilters
                      }, " Apply Filters ")
                    ]),
                    createVNode("div", { class: "w-full lg:w-3/4" }, [
                      createVNode("div", { class: "bg-white p-4 rounded-lg shadow-sm mb-6 flex justify-between items-center" }, [
                        createVNode("div", { class: "text-sm text-gray-500" }, [
                          createTextVNode(" Showing "),
                          createVNode("span", { class: "font-medium text-gray-900" }, toDisplayString(filteredHotels.value.length), 1),
                          createTextVNode(" properties ")
                        ]),
                        createVNode("div", { class: "flex items-center space-x-2" }, [
                          createVNode("span", { class: "text-sm text-gray-700" }, "Sort by:"),
                          withDirectives(createVNode("select", {
                            "onUpdate:modelValue": ($event) => sortBy.value = $event,
                            class: "border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500"
                          }, [
                            createVNode("option", { value: "recommended" }, "Recommended"),
                            createVNode("option", { value: "price-asc" }, "Price: Low to High"),
                            createVNode("option", { value: "price-desc" }, "Price: High to Low"),
                            createVNode("option", { value: "rating-desc" }, "Rating: High to Low"),
                            createVNode("option", { value: "popularity" }, "Popularity")
                          ], 8, ["onUpdate:modelValue"]), [
                            [vModelSelect, sortBy.value]
                          ])
                        ])
                      ]),
                      createVNode("div", { class: "space-y-6" }, [
                        (openBlock(true), createBlock(Fragment, null, renderList(filteredHotels.value, (hotel) => {
                          return openBlock(), createBlock("div", {
                            key: hotel.id,
                            class: "bg-white rounded-lg shadow-sm overflow-hidden transition-transform duration-300 hover:shadow-md hover:-translate-y-1"
                          }, [
                            createVNode("div", { class: "flex flex-col md:flex-row" }, [
                              createVNode("div", { class: "w-full md:w-1/3 relative" }, [
                                createVNode("img", {
                                  src: hotel.image,
                                  alt: hotel.name,
                                  class: "h-full w-full object-cover"
                                }, null, 8, ["src", "alt"]),
                                hotel.discount ? (openBlock(), createBlock("div", {
                                  key: 0,
                                  class: "absolute top-2 left-2 bg-red-500 text-white px-2 py-1 text-xs font-bold rounded"
                                }, " SAVE " + toDisplayString(hotel.discount) + "% ", 1)) : createCommentVNode("", true)
                              ]),
                              createVNode("div", { class: "w-full md:w-2/3 p-4 md:p-6 flex flex-col" }, [
                                createVNode("div", { class: "flex justify-between items-start mb-2" }, [
                                  createVNode("div", null, [
                                    createVNode("h2", { class: "text-xl font-bold text-gray-900" }, toDisplayString(hotel.name), 1),
                                    createVNode("div", { class: "flex items-center mt-1" }, [
                                      createVNode("div", { class: "flex" }, [
                                        (openBlock(true), createBlock(Fragment, null, renderList(Math.floor(hotel.rating), (i) => {
                                          return openBlock(), createBlock("span", {
                                            key: i,
                                            class: "text-yellow-400"
                                          }, "★");
                                        }), 128)),
                                        (openBlock(true), createBlock(Fragment, null, renderList(5 - Math.floor(hotel.rating), (i) => {
                                          return openBlock(), createBlock("span", {
                                            key: i + Math.floor(hotel.rating),
                                            class: "text-gray-300"
                                          }, "★");
                                        }), 128))
                                      ]),
                                      createVNode("span", { class: "ml-2 text-sm text-gray-600" }, toDisplayString(hotel.reviewCount) + " reviews", 1)
                                    ])
                                  ]),
                                  createVNode("div", { class: "text-right" }, [
                                    createVNode("div", { class: "text-lg md:text-xl font-bold text-blue-600" }, [
                                      createTextVNode("$" + toDisplayString(hotel.price), 1),
                                      createVNode("span", { class: "text-sm font-normal" }, "/night")
                                    ]),
                                    hotel.oldPrice ? (openBlock(), createBlock("div", {
                                      key: 0,
                                      class: "text-sm text-gray-500 line-through"
                                    }, "$" + toDisplayString(hotel.oldPrice), 1)) : createCommentVNode("", true)
                                  ])
                                ]),
                                createVNode("div", { class: "text-sm text-gray-600 mb-3 flex items-center" }, [
                                  (openBlock(), createBlock("svg", {
                                    class: "w-4 h-4 text-gray-500 mr-1",
                                    fill: "none",
                                    stroke: "currentColor",
                                    viewBox: "0 0 24 24"
                                  }, [
                                    createVNode("path", {
                                      "stroke-linecap": "round",
                                      "stroke-linejoin": "round",
                                      "stroke-width": "2",
                                      d: "M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                    }),
                                    createVNode("path", {
                                      "stroke-linecap": "round",
                                      "stroke-linejoin": "round",
                                      "stroke-width": "2",
                                      d: "M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                    })
                                  ])),
                                  createTextVNode(" " + toDisplayString(hotel.location), 1)
                                ]),
                                createVNode("p", { class: "text-gray-700 text-sm mb-4 flex-grow" }, toDisplayString(hotel.description), 1),
                                createVNode("div", { class: "flex flex-wrap gap-2 mb-4" }, [
                                  (openBlock(true), createBlock(Fragment, null, renderList(hotel.amenities, (amenity, index) => {
                                    return openBlock(), createBlock("span", {
                                      key: index,
                                      class: "bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-1 rounded"
                                    }, toDisplayString(amenity), 1);
                                  }), 128))
                                ]),
                                createVNode("div", { class: "flex justify-between items-center mt-auto" }, [
                                  createVNode("div", { class: "text-sm" }, [
                                    hotel.freeCancellation ? (openBlock(), createBlock("span", {
                                      key: 0,
                                      class: "text-green-600 font-medium"
                                    }, "Free cancellation")) : (openBlock(), createBlock("span", {
                                      key: 1,
                                      class: "text-gray-500"
                                    }, "Non-refundable"))
                                  ]),
                                  createVNode(unref(Link), {
                                    href: route("hotels.show", hotel.id),
                                    class: "bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium transition duration-200"
                                  }, {
                                    default: withCtx(() => [
                                      createTextVNode(" View Details ")
                                    ]),
                                    _: 2
                                  }, 1032, ["href"])
                                ])
                              ])
                            ])
                          ]);
                        }), 128)),
                        filteredHotels.value.length === 0 ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "text-center py-10"
                        }, [
                          (openBlock(), createBlock("svg", {
                            class: "mx-auto h-12 w-12 text-gray-400",
                            fill: "none",
                            viewBox: "0 0 24 24",
                            stroke: "currentColor"
                          }, [
                            createVNode("path", {
                              "stroke-linecap": "round",
                              "stroke-linejoin": "round",
                              "stroke-width": "2",
                              d: "M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            })
                          ])),
                          createVNode("h3", { class: "mt-2 text-sm font-medium text-gray-900" }, "No hotels found"),
                          createVNode("p", { class: "mt-1 text-sm text-gray-500" }, "Try adjusting your search or filter criteria."),
                          createVNode("div", { class: "mt-6" }, [
                            createVNode("button", {
                              onClick: resetFilters,
                              class: "inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            }, " Reset all filters ")
                          ])
                        ])) : createCommentVNode("", true)
                      ]),
                      createVNode("div", { class: "mt-8 flex justify-center" }, [
                        createVNode("nav", { class: "flex items-center" }, [
                          createVNode("button", {
                            class: "px-2 py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed",
                            disabled: currentPage.value === 1,
                            onClick: ($event) => currentPage.value--
                          }, " Previous ", 8, ["disabled", "onClick"]),
                          createVNode("div", { class: "flex mx-2" }, [
                            (openBlock(true), createBlock(Fragment, null, renderList(totalPages.value, (page) => {
                              return openBlock(), createBlock("button", {
                                key: page,
                                onClick: ($event) => currentPage.value = page,
                                class: [
                                  "px-3 py-1 mx-1 rounded-md",
                                  currentPage.value === page ? "bg-blue-600 text-white" : "text-gray-700 hover:bg-gray-50 border border-gray-300"
                                ]
                              }, toDisplayString(page), 11, ["onClick"]);
                            }), 128))
                          ]),
                          createVNode("button", {
                            class: "px-2 py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed",
                            disabled: currentPage.value === totalPages.value,
                            onClick: ($event) => currentPage.value++
                          }, " Next ", 8, ["disabled", "onClick"])
                        ])
                      ])
                    ])
                  ])
                ])
              ])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<!--]-->`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Hotels.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
