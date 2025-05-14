import { ref, unref, withCtx, createTextVNode, createVNode, toDisplayString, openBlock, createBlock, Fragment, renderList, createCommentVNode, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderList, ssrRenderAttr, ssrRenderClass } from "vue/server-renderer";
import { Head, Link } from "@inertiajs/vue3";
import { G as GuestLayout } from "./GuestLayout-6b7af627.js";
const _sfc_main = {
  __name: "HotelDetail",
  __ssrInlineRender: true,
  setup(__props) {
    const hotel = ref({
      id: 1,
      name: "Grand Resort & Spa",
      location: "Antalya, Turkey",
      address: "Lara Beach Road, 07230 Antalya, Turkey",
      description: "Luxurious beachfront resort with spectacular views of the Mediterranean Sea, featuring elegant rooms and world-class dining.",
      descriptionLong: "Located directly on Lara Beach with stunning views of the Mediterranean Sea, Grand Resort & Spa offers the perfect blend of luxury, comfort, and Turkish hospitality. Our spacious rooms and suites are elegantly designed with modern amenities and private balconies overlooking the sea or our lush gardens. \n\nIndulge in our six restaurants offering international and local cuisines, relax by our multiple swimming pools, or rejuvenate at our award-winning spa. For the more active guests, we offer a range of activities including tennis, water sports, and a fully equipped fitness center. Our kids club ensures that younger guests have a memorable stay too. \n\nWith its prime location just 15 minutes from Antalya International Airport and 25 minutes from the historic city center, our resort is the ideal base for exploring the region's attractions or simply enjoying a relaxing beach vacation.",
      rating: 4.8,
      reviewCount: 642,
      price: 189,
      oldPrice: 229,
      images: [
        "/images/hotels/hotel1.jpg",
        "/images/hotels/room1.jpg",
        "/images/hotels/pool1.jpg",
        "/images/hotels/restaurant1.jpg",
        "/images/hotels/spa1.jpg"
      ],
      amenitiesByCategory: {
        "General": ["Free WiFi", "24-hour front desk", "Airport shuttle", "Room service", "Concierge service"],
        "Wellness": ["Full-service spa", "Fitness center", "Sauna", "Turkish bath", "Massage"],
        "Food & Drink": ["Restaurant", "Bar/Lounge", "Breakfast available", "Room service", "Coffee shop"],
        "Activities": ["Swimming pool", "Private beach", "Tennis court", "Water sports", "Kids club"],
        "Services": ["Laundry service", "Dry cleaning", "Business center", "Currency exchange", "Ticket service"]
      },
      roomTypes: [
        {
          name: "Deluxe Sea View Room",
          description: "Spacious room with private balcony overlooking the Mediterranean Sea, featuring a king-size bed and luxury bathroom.",
          price: 189,
          occupancy: "Sleeps 2",
          image: "/images/hotels/room1.jpg",
          features: ["Sea View", "Balcony", "Air conditioning", "Free WiFi", "Minibar"],
          freeCancellation: true
        },
        {
          name: "Superior Garden View Room",
          description: "Elegant room with views of the resort's lush gardens, featuring twin beds and a cozy sitting area.",
          price: 159,
          occupancy: "Sleeps 2",
          image: "/images/hotels/room2.jpg",
          features: ["Garden View", "Sitting Area", "Air conditioning", "Free WiFi", "Minibar"],
          freeCancellation: true
        },
        {
          name: "Family Suite",
          description: "Spacious suite with separate living area and two bathrooms, perfect for families with children.",
          price: 259,
          occupancy: "Sleeps 4",
          image: "/images/hotels/room3.jpg",
          features: ["Sea View", "Separate living area", "Two bathrooms", "Air conditioning", "Free WiFi"],
          freeCancellation: false
        }
      ],
      reviews: [
        {
          author: "John D.",
          date: "October 2024",
          rating: 5,
          comment: "Absolutely fantastic resort! The staff were incredibly attentive, the food was delicious, and the rooms were spacious and clean. The beach access was perfect, and we loved the multiple pool options. Will definitely be returning next year!"
        },
        {
          author: "Maria S.",
          date: "September 2024",
          rating: 4.5,
          comment: "We had a wonderful stay at Grand Resort. The spa treatments were amazing and the beachfront location can't be beat. Only small complaint was that the WiFi was spotty in some areas of the resort."
        },
        {
          author: "Ahmed K.",
          date: "August 2024",
          rating: 4.8,
          comment: "Perfect family vacation spot! The kids club kept our children entertained, which gave us time to relax. The all-inclusive option was great value, and the room was very comfortable with an amazing sea view."
        }
      ],
      pointsOfInterest: [
        { name: "Antalya International Airport", distance: "15 km / 20 min drive" },
        { name: "Antalya Old Town (Kaleiçi)", distance: "20 km / 30 min drive" },
        { name: "Duden Waterfalls", distance: "12 km / 15 min drive" },
        { name: "Lara Beach", distance: "Beachfront" },
        { name: "Terra City Shopping Mall", distance: "8 km / 10 min drive" }
      ]
    });
    const getRatingText = (rating) => {
      if (rating >= 4.5)
        return "Exceptional";
      if (rating >= 4)
        return "Excellent";
      if (rating >= 3.5)
        return "Very Good";
      if (rating >= 3)
        return "Good";
      if (rating >= 2)
        return "Average";
      return "Poor";
    };
    const route = (name, params) => {
      if (name === "hotels.index")
        return "/hotels";
      return "/";
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), {
        title: hotel.value.name
      }, null, _parent));
      _push(ssrRenderComponent(GuestLayout, null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="bg-gray-50 py-10"${_scopeId}><div class="container mx-auto px-4"${_scopeId}><div class="text-sm text-gray-600 mb-5"${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: "/",
              class: "hover:text-blue-600"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`Home`);
                } else {
                  return [
                    createTextVNode("Home")
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<span class="mx-2"${_scopeId}>›</span>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: route("hotels.index"),
              class: "hover:text-blue-600"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`Hotels`);
                } else {
                  return [
                    createTextVNode("Hotels")
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<span class="mx-2"${_scopeId}>›</span><span class="text-gray-900"${_scopeId}>${ssrInterpolate(hotel.value.name)}</span></div><div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6"${_scopeId}><div class="md:flex"${_scopeId}><div class="md:w-2/3 p-6"${_scopeId}><div class="flex justify-between items-start"${_scopeId}><div${_scopeId}><h1 class="text-2xl md:text-3xl font-bold text-gray-900"${_scopeId}>${ssrInterpolate(hotel.value.name)}</h1><div class="flex items-center mt-2"${_scopeId}><div class="flex"${_scopeId}><!--[-->`);
            ssrRenderList(Math.floor(hotel.value.rating), (i) => {
              _push2(`<span class="text-yellow-400"${_scopeId}>★</span>`);
            });
            _push2(`<!--]--><!--[-->`);
            ssrRenderList(5 - Math.floor(hotel.value.rating), (i) => {
              _push2(`<span class="text-gray-300"${_scopeId}>★</span>`);
            });
            _push2(`<!--]--></div><span class="ml-2 text-gray-600"${_scopeId}>${ssrInterpolate(hotel.value.rating)} (${ssrInterpolate(hotel.value.reviewCount)} reviews)</span></div><div class="flex items-center mt-2 text-gray-600"${_scopeId}><svg class="w-5 h-5 text-gray-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"${_scopeId}></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"${_scopeId}></path></svg><span${_scopeId}>${ssrInterpolate(hotel.value.location)}</span></div></div><div class="hidden md:block"${_scopeId}><button class="flex items-center text-blue-600 hover:text-blue-800"${_scopeId}><svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"${_scopeId}></path></svg> Save </button></div></div></div><div class="md:w-1/3 bg-blue-50 p-6 flex flex-col justify-center"${_scopeId}><div class="text-center"${_scopeId}><div class="text-sm uppercase font-medium text-gray-500"${_scopeId}>Price from</div><div class="text-3xl font-bold text-blue-600"${_scopeId}>$${ssrInterpolate(hotel.value.price)}<span class="text-lg font-normal"${_scopeId}>/night</span></div>`);
            if (hotel.value.oldPrice) {
              _push2(`<div class="text-gray-500 line-through"${_scopeId}>$${ssrInterpolate(hotel.value.oldPrice)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<button class="mt-4 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg w-full transition duration-200"${_scopeId}> Book Now </button></div></div></div></div><div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8"${_scopeId}><div class="md:col-span-2 md:row-span-2"${_scopeId}><img${ssrRenderAttr("src", hotel.value.images[0])} alt="Hotel main" class="w-full h-full object-cover rounded-lg"${_scopeId}></div><!--[-->`);
            ssrRenderList(hotel.value.images.slice(1, 5), (image, index) => {
              _push2(`<div class="hidden md:block"${_scopeId}><img${ssrRenderAttr("src", image)}${ssrRenderAttr("alt", `Hotel image ${index + 1}`)} class="w-full h-full object-cover rounded-lg"${_scopeId}></div>`);
            });
            _push2(`<!--]--><div class="hidden md:flex md:col-span-1 items-center justify-center bg-gray-100 rounded-lg cursor-pointer"${_scopeId}><div class="text-center p-4"${_scopeId}><svg class="w-8 h-8 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"${_scopeId}></path></svg><span class="block mt-2 text-gray-700 font-medium"${_scopeId}>View all photos</span></div></div></div><div class="md:hidden mb-6 relative"${_scopeId}><div class="flex overflow-x-auto pb-4 snap-x"${_scopeId}><!--[-->`);
            ssrRenderList(hotel.value.images, (image, index) => {
              _push2(`<div class="flex-shrink-0 w-full px-2 snap-center"${_scopeId}><img${ssrRenderAttr("src", image)}${ssrRenderAttr("alt", `Hotel image ${index + 1}`)} class="rounded-lg w-full h-64 object-cover"${_scopeId}></div>`);
            });
            _push2(`<!--]--></div><div class="absolute bottom-6 left-0 right-0 flex justify-center space-x-2"${_scopeId}><!--[-->`);
            ssrRenderList(hotel.value.images, (_2, index) => {
              _push2(`<button class="${ssrRenderClass([{ "opacity-100": index === 0 }, "w-2 h-2 rounded-full bg-white opacity-60"])}"${_scopeId}></button>`);
            });
            _push2(`<!--]--></div></div><div class="grid grid-cols-1 lg:grid-cols-3 gap-6"${_scopeId}><div class="lg:col-span-2"${_scopeId}><div class="bg-white rounded-lg shadow-sm p-6 mb-6"${_scopeId}><h2 class="text-xl font-bold text-gray-900 mb-4"${_scopeId}>About ${ssrInterpolate(hotel.value.name)}</h2><div class="prose max-w-none text-gray-700"${_scopeId}><p${_scopeId}>${ssrInterpolate(hotel.value.description)}</p><p${_scopeId}>${ssrInterpolate(hotel.value.descriptionLong)}</p></div></div><div class="bg-white rounded-lg shadow-sm p-6 mb-6"${_scopeId}><h2 class="text-xl font-bold text-gray-900 mb-4"${_scopeId}>Amenities</h2><div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"${_scopeId}><!--[-->`);
            ssrRenderList(hotel.value.amenitiesByCategory, (category, categoryName) => {
              _push2(`<div class="mb-4"${_scopeId}><h3 class="font-medium text-gray-900 mb-2"${_scopeId}>${ssrInterpolate(categoryName)}</h3><ul class="space-y-2"${_scopeId}><!--[-->`);
              ssrRenderList(category, (amenity, index) => {
                _push2(`<li class="flex items-center text-gray-700"${_scopeId}><svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"${_scopeId}></path></svg> ${ssrInterpolate(amenity)}</li>`);
              });
              _push2(`<!--]--></ul></div>`);
            });
            _push2(`<!--]--></div></div><div class="bg-white rounded-lg shadow-sm p-6 mb-6"${_scopeId}><h2 class="text-xl font-bold text-gray-900 mb-4"${_scopeId}>Available Room Types</h2><div class="space-y-6"${_scopeId}><!--[-->`);
            ssrRenderList(hotel.value.roomTypes, (room, index) => {
              _push2(`<div class="border border-gray-200 rounded-lg overflow-hidden"${_scopeId}><div class="md:flex"${_scopeId}><div class="md:w-1/3"${_scopeId}><img${ssrRenderAttr("src", room.image)}${ssrRenderAttr("alt", room.name)} class="w-full h-full object-cover"${_scopeId}></div><div class="md:w-2/3 p-4"${_scopeId}><div class="flex justify-between items-start mb-2"${_scopeId}><h3 class="text-lg font-bold text-gray-900"${_scopeId}>${ssrInterpolate(room.name)}</h3><div class="text-right"${_scopeId}><div class="text-lg font-bold text-blue-600"${_scopeId}>$${ssrInterpolate(room.price)}<span class="text-sm font-normal"${_scopeId}>/night</span></div><div class="text-sm text-gray-500"${_scopeId}>${ssrInterpolate(room.occupancy)}</div></div></div><p class="text-gray-700 text-sm mb-3"${_scopeId}>${ssrInterpolate(room.description)}</p><div class="flex flex-wrap gap-2 mb-3"${_scopeId}><!--[-->`);
              ssrRenderList(room.features, (feature, idx) => {
                _push2(`<span class="bg-gray-100 text-gray-800 text-xs px-2.5 py-1 rounded"${_scopeId}>${ssrInterpolate(feature)}</span>`);
              });
              _push2(`<!--]--></div><div class="flex justify-between items-center mt-3"${_scopeId}>`);
              if (room.freeCancellation) {
                _push2(`<div class="text-green-600 text-sm font-medium"${_scopeId}>Free cancellation</div>`);
              } else {
                _push2(`<div class="text-gray-500 text-sm"${_scopeId}>Non-refundable</div>`);
              }
              _push2(`<button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium transition duration-200"${_scopeId}> Select Room </button></div></div></div></div>`);
            });
            _push2(`<!--]--></div></div><div class="bg-white rounded-lg shadow-sm p-6"${_scopeId}><div class="flex justify-between items-center mb-4"${_scopeId}><h2 class="text-xl font-bold text-gray-900"${_scopeId}>Guest Reviews</h2><div class="flex items-center"${_scopeId}><div class="bg-blue-600 text-white font-bold rounded p-2 mr-2"${_scopeId}>${ssrInterpolate(hotel.value.rating)}</div><div${_scopeId}><div class="text-gray-900 font-medium"${_scopeId}>${ssrInterpolate(getRatingText(hotel.value.rating))}</div><div class="text-sm text-gray-600"${_scopeId}>${ssrInterpolate(hotel.value.reviewCount)} reviews</div></div></div></div><div class="flex flex-wrap gap-2 mb-4"${_scopeId}><button class="bg-blue-600 text-white px-3 py-1 rounded-full text-sm"${_scopeId}>All</button><!--[-->`);
            ssrRenderList(["Excellent", "Very Good", "Good", "Average", "Poor"], (category) => {
              _push2(`<button class="border border-gray-300 px-3 py-1 rounded-full text-sm text-gray-700 hover:bg-gray-50"${_scopeId}>${ssrInterpolate(category)}</button>`);
            });
            _push2(`<!--]--></div><div class="space-y-6"${_scopeId}><!--[-->`);
            ssrRenderList(hotel.value.reviews, (review, index) => {
              _push2(`<div class="border-b border-gray-200 pb-6 last:border-b-0 last:pb-0"${_scopeId}><div class="flex justify-between items-start mb-2"${_scopeId}><div class="flex items-center"${_scopeId}><div class="bg-blue-100 rounded-full w-10 h-10 flex items-center justify-center text-blue-600 font-medium mr-3"${_scopeId}>${ssrInterpolate(review.author.charAt(0))}</div><div${_scopeId}><div class="font-medium text-gray-900"${_scopeId}>${ssrInterpolate(review.author)}</div><div class="text-sm text-gray-600"${_scopeId}>${ssrInterpolate(review.date)}</div></div></div><div class="bg-blue-600 text-white font-bold rounded p-1 px-2 text-sm"${_scopeId}>${ssrInterpolate(review.rating)}</div></div><p class="text-gray-700"${_scopeId}>${ssrInterpolate(review.comment)}</p></div>`);
            });
            _push2(`<!--]--></div><div class="text-center mt-6"${_scopeId}><button class="border border-gray-300 rounded-md px-4 py-2 text-gray-700 hover:bg-gray-50 font-medium"${_scopeId}> Show more reviews </button></div></div></div><div class="lg:col-span-1"${_scopeId}><div class="bg-white rounded-lg shadow-sm p-6 mb-6 sticky top-4"${_scopeId}><h2 class="text-lg font-bold text-gray-900 mb-4"${_scopeId}>Book Your Stay</h2><div class="space-y-4"${_scopeId}><div${_scopeId}><label class="block text-sm font-medium text-gray-700 mb-1"${_scopeId}>Check-in / Check-out</label><div class="border border-gray-300 rounded-md p-2 flex"${_scopeId}><div class="flex-1 text-center"${_scopeId}><input type="date" class="w-full border-0 focus:ring-0"${_scopeId}><div class="text-xs text-gray-500 mt-1"${_scopeId}>Check-in</div></div><div class="border-l border-gray-300 mx-2"${_scopeId}></div><div class="flex-1 text-center"${_scopeId}><input type="date" class="w-full border-0 focus:ring-0"${_scopeId}><div class="text-xs text-gray-500 mt-1"${_scopeId}>Check-out</div></div></div></div><div${_scopeId}><label class="block text-sm font-medium text-gray-700 mb-1"${_scopeId}>Guests</label><div class="border border-gray-300 rounded-md p-2"${_scopeId}><select class="w-full border-0 focus:ring-0"${_scopeId}><option${_scopeId}>1 adult</option><option${_scopeId}>2 adults</option><option${_scopeId}>2 adults, 1 child</option><option${_scopeId}>2 adults, 2 children</option><option${_scopeId}>3 adults</option><option${_scopeId}>4 adults</option></select></div></div><div${_scopeId}><label class="block text-sm font-medium text-gray-700 mb-1"${_scopeId}>Rooms</label><div class="border border-gray-300 rounded-md p-2"${_scopeId}><select class="w-full border-0 focus:ring-0"${_scopeId}><option${_scopeId}>1 room</option><option${_scopeId}>2 rooms</option><option${_scopeId}>3 rooms</option><option${_scopeId}>4 rooms</option></select></div></div></div><div class="mt-6 space-y-4"${_scopeId}><div class="flex justify-between items-center text-gray-700"${_scopeId}><div${_scopeId}>Average nightly rate</div><div class="font-semibold"${_scopeId}>$${ssrInterpolate(hotel.value.price)}</div></div><div class="flex justify-between items-center text-gray-700"${_scopeId}><div${_scopeId}>Tax &amp; fees</div><div class="font-semibold"${_scopeId}>$${ssrInterpolate(Math.round(hotel.value.price * 0.15))}</div></div><div class="border-t border-gray-200 pt-4 flex justify-between items-center"${_scopeId}><div class="font-semibold text-gray-900"${_scopeId}>Total</div><div class="font-bold text-lg text-blue-600"${_scopeId}>$${ssrInterpolate(Math.round(hotel.value.price * 1.15))}</div></div></div><button class="mt-6 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg w-full transition duration-200"${_scopeId}> Book Now </button><p class="text-green-600 text-sm mt-3 flex items-center justify-center"${_scopeId}><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"${_scopeId}></path></svg> No payment required today </p></div><div class="bg-white rounded-lg shadow-sm p-6 mb-6"${_scopeId}><h2 class="text-lg font-bold text-gray-900 mb-4"${_scopeId}>Location</h2><div class="aspect-w-16 aspect-h-9 mb-3"${_scopeId}><div class="w-full h-48 bg-gray-200 rounded-lg mb-2 flex items-center justify-center"${_scopeId}><div class="text-gray-400"${_scopeId}>Map placeholder</div></div></div><div class="text-gray-700"${_scopeId}><p class="mb-2"${_scopeId}>${ssrInterpolate(hotel.value.address)}</p><ul class="space-y-1 text-sm"${_scopeId}><!--[-->`);
            ssrRenderList(hotel.value.pointsOfInterest, (point, index) => {
              _push2(`<li class="flex items-start"${_scopeId}><svg class="w-4 h-4 text-gray-500 mr-1 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"${_scopeId}><circle cx="12" cy="12" r="10" stroke-width="2"${_scopeId}></circle><path d="M12 8v4l3 3" stroke-width="2" stroke-linecap="round"${_scopeId}></path></svg> ${ssrInterpolate(point.name)} - ${ssrInterpolate(point.distance)}</li>`);
            });
            _push2(`<!--]--></ul></div></div><div class="bg-white rounded-lg shadow-sm p-6"${_scopeId}><h2 class="text-lg font-bold text-gray-900 mb-4"${_scopeId}>Hotel Policies</h2><div class="space-y-4 text-gray-700"${_scopeId}><div${_scopeId}><h3 class="font-medium mb-1"${_scopeId}>Check-in &amp; Check-out</h3><p class="text-sm"${_scopeId}>Check-in: 3:00 PM - 12:00 AM<br${_scopeId}>Check-out: 7:00 AM - 11:00 AM</p></div><div${_scopeId}><h3 class="font-medium mb-1"${_scopeId}>Cancellation Policy</h3><p class="text-sm"${_scopeId}>Free cancellation up to 24 hours before check-in. After that, cancellation will incur a fee equivalent to the first night&#39;s stay.</p></div><div${_scopeId}><h3 class="font-medium mb-1"${_scopeId}>Children &amp; Extra Beds</h3><p class="text-sm"${_scopeId}>Children of all ages are welcome. Children 12 and above are considered adults at this property.</p></div><div${_scopeId}><h3 class="font-medium mb-1"${_scopeId}>Pets</h3><p class="text-sm"${_scopeId}>Pets are not allowed.</p></div></div></div></div></div></div></div>`);
          } else {
            return [
              createVNode("div", { class: "bg-gray-50 py-10" }, [
                createVNode("div", { class: "container mx-auto px-4" }, [
                  createVNode("div", { class: "text-sm text-gray-600 mb-5" }, [
                    createVNode(unref(Link), {
                      href: "/",
                      class: "hover:text-blue-600"
                    }, {
                      default: withCtx(() => [
                        createTextVNode("Home")
                      ]),
                      _: 1
                    }),
                    createVNode("span", { class: "mx-2" }, "›"),
                    createVNode(unref(Link), {
                      href: route("hotels.index"),
                      class: "hover:text-blue-600"
                    }, {
                      default: withCtx(() => [
                        createTextVNode("Hotels")
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode("span", { class: "mx-2" }, "›"),
                    createVNode("span", { class: "text-gray-900" }, toDisplayString(hotel.value.name), 1)
                  ]),
                  createVNode("div", { class: "bg-white rounded-lg shadow-sm overflow-hidden mb-6" }, [
                    createVNode("div", { class: "md:flex" }, [
                      createVNode("div", { class: "md:w-2/3 p-6" }, [
                        createVNode("div", { class: "flex justify-between items-start" }, [
                          createVNode("div", null, [
                            createVNode("h1", { class: "text-2xl md:text-3xl font-bold text-gray-900" }, toDisplayString(hotel.value.name), 1),
                            createVNode("div", { class: "flex items-center mt-2" }, [
                              createVNode("div", { class: "flex" }, [
                                (openBlock(true), createBlock(Fragment, null, renderList(Math.floor(hotel.value.rating), (i) => {
                                  return openBlock(), createBlock("span", {
                                    key: i,
                                    class: "text-yellow-400"
                                  }, "★");
                                }), 128)),
                                (openBlock(true), createBlock(Fragment, null, renderList(5 - Math.floor(hotel.value.rating), (i) => {
                                  return openBlock(), createBlock("span", {
                                    key: i + Math.floor(hotel.value.rating),
                                    class: "text-gray-300"
                                  }, "★");
                                }), 128))
                              ]),
                              createVNode("span", { class: "ml-2 text-gray-600" }, toDisplayString(hotel.value.rating) + " (" + toDisplayString(hotel.value.reviewCount) + " reviews)", 1)
                            ]),
                            createVNode("div", { class: "flex items-center mt-2 text-gray-600" }, [
                              (openBlock(), createBlock("svg", {
                                class: "w-5 h-5 text-gray-500 mr-1",
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
                              createVNode("span", null, toDisplayString(hotel.value.location), 1)
                            ])
                          ]),
                          createVNode("div", { class: "hidden md:block" }, [
                            createVNode("button", { class: "flex items-center text-blue-600 hover:text-blue-800" }, [
                              (openBlock(), createBlock("svg", {
                                class: "w-5 h-5 mr-1",
                                fill: "none",
                                stroke: "currentColor",
                                viewBox: "0 0 24 24"
                              }, [
                                createVNode("path", {
                                  "stroke-linecap": "round",
                                  "stroke-linejoin": "round",
                                  "stroke-width": "2",
                                  d: "M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                                })
                              ])),
                              createTextVNode(" Save ")
                            ])
                          ])
                        ])
                      ]),
                      createVNode("div", { class: "md:w-1/3 bg-blue-50 p-6 flex flex-col justify-center" }, [
                        createVNode("div", { class: "text-center" }, [
                          createVNode("div", { class: "text-sm uppercase font-medium text-gray-500" }, "Price from"),
                          createVNode("div", { class: "text-3xl font-bold text-blue-600" }, [
                            createTextVNode("$" + toDisplayString(hotel.value.price), 1),
                            createVNode("span", { class: "text-lg font-normal" }, "/night")
                          ]),
                          hotel.value.oldPrice ? (openBlock(), createBlock("div", {
                            key: 0,
                            class: "text-gray-500 line-through"
                          }, "$" + toDisplayString(hotel.value.oldPrice), 1)) : createCommentVNode("", true),
                          createVNode("button", { class: "mt-4 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg w-full transition duration-200" }, " Book Now ")
                        ])
                      ])
                    ])
                  ]),
                  createVNode("div", { class: "grid grid-cols-1 md:grid-cols-4 gap-4 mb-8" }, [
                    createVNode("div", { class: "md:col-span-2 md:row-span-2" }, [
                      createVNode("img", {
                        src: hotel.value.images[0],
                        alt: "Hotel main",
                        class: "w-full h-full object-cover rounded-lg"
                      }, null, 8, ["src"])
                    ]),
                    (openBlock(true), createBlock(Fragment, null, renderList(hotel.value.images.slice(1, 5), (image, index) => {
                      return openBlock(), createBlock("div", {
                        key: index,
                        class: "hidden md:block"
                      }, [
                        createVNode("img", {
                          src: image,
                          alt: `Hotel image ${index + 1}`,
                          class: "w-full h-full object-cover rounded-lg"
                        }, null, 8, ["src", "alt"])
                      ]);
                    }), 128)),
                    createVNode("div", { class: "hidden md:flex md:col-span-1 items-center justify-center bg-gray-100 rounded-lg cursor-pointer" }, [
                      createVNode("div", { class: "text-center p-4" }, [
                        (openBlock(), createBlock("svg", {
                          class: "w-8 h-8 mx-auto text-gray-400",
                          fill: "none",
                          stroke: "currentColor",
                          viewBox: "0 0 24 24"
                        }, [
                          createVNode("path", {
                            "stroke-linecap": "round",
                            "stroke-linejoin": "round",
                            "stroke-width": "2",
                            d: "M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                          })
                        ])),
                        createVNode("span", { class: "block mt-2 text-gray-700 font-medium" }, "View all photos")
                      ])
                    ])
                  ]),
                  createVNode("div", { class: "md:hidden mb-6 relative" }, [
                    createVNode("div", { class: "flex overflow-x-auto pb-4 snap-x" }, [
                      (openBlock(true), createBlock(Fragment, null, renderList(hotel.value.images, (image, index) => {
                        return openBlock(), createBlock("div", {
                          key: index,
                          class: "flex-shrink-0 w-full px-2 snap-center"
                        }, [
                          createVNode("img", {
                            src: image,
                            alt: `Hotel image ${index + 1}`,
                            class: "rounded-lg w-full h-64 object-cover"
                          }, null, 8, ["src", "alt"])
                        ]);
                      }), 128))
                    ]),
                    createVNode("div", { class: "absolute bottom-6 left-0 right-0 flex justify-center space-x-2" }, [
                      (openBlock(true), createBlock(Fragment, null, renderList(hotel.value.images, (_2, index) => {
                        return openBlock(), createBlock("button", {
                          key: index,
                          class: ["w-2 h-2 rounded-full bg-white opacity-60", { "opacity-100": index === 0 }]
                        }, null, 2);
                      }), 128))
                    ])
                  ]),
                  createVNode("div", { class: "grid grid-cols-1 lg:grid-cols-3 gap-6" }, [
                    createVNode("div", { class: "lg:col-span-2" }, [
                      createVNode("div", { class: "bg-white rounded-lg shadow-sm p-6 mb-6" }, [
                        createVNode("h2", { class: "text-xl font-bold text-gray-900 mb-4" }, "About " + toDisplayString(hotel.value.name), 1),
                        createVNode("div", { class: "prose max-w-none text-gray-700" }, [
                          createVNode("p", null, toDisplayString(hotel.value.description), 1),
                          createVNode("p", null, toDisplayString(hotel.value.descriptionLong), 1)
                        ])
                      ]),
                      createVNode("div", { class: "bg-white rounded-lg shadow-sm p-6 mb-6" }, [
                        createVNode("h2", { class: "text-xl font-bold text-gray-900 mb-4" }, "Amenities"),
                        createVNode("div", { class: "grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" }, [
                          (openBlock(true), createBlock(Fragment, null, renderList(hotel.value.amenitiesByCategory, (category, categoryName) => {
                            return openBlock(), createBlock("div", {
                              key: categoryName,
                              class: "mb-4"
                            }, [
                              createVNode("h3", { class: "font-medium text-gray-900 mb-2" }, toDisplayString(categoryName), 1),
                              createVNode("ul", { class: "space-y-2" }, [
                                (openBlock(true), createBlock(Fragment, null, renderList(category, (amenity, index) => {
                                  return openBlock(), createBlock("li", {
                                    key: index,
                                    class: "flex items-center text-gray-700"
                                  }, [
                                    (openBlock(), createBlock("svg", {
                                      class: "w-5 h-5 text-green-500 mr-2",
                                      fill: "none",
                                      stroke: "currentColor",
                                      viewBox: "0 0 24 24"
                                    }, [
                                      createVNode("path", {
                                        "stroke-linecap": "round",
                                        "stroke-linejoin": "round",
                                        "stroke-width": "2",
                                        d: "M5 13l4 4L19 7"
                                      })
                                    ])),
                                    createTextVNode(" " + toDisplayString(amenity), 1)
                                  ]);
                                }), 128))
                              ])
                            ]);
                          }), 128))
                        ])
                      ]),
                      createVNode("div", { class: "bg-white rounded-lg shadow-sm p-6 mb-6" }, [
                        createVNode("h2", { class: "text-xl font-bold text-gray-900 mb-4" }, "Available Room Types"),
                        createVNode("div", { class: "space-y-6" }, [
                          (openBlock(true), createBlock(Fragment, null, renderList(hotel.value.roomTypes, (room, index) => {
                            return openBlock(), createBlock("div", {
                              key: index,
                              class: "border border-gray-200 rounded-lg overflow-hidden"
                            }, [
                              createVNode("div", { class: "md:flex" }, [
                                createVNode("div", { class: "md:w-1/3" }, [
                                  createVNode("img", {
                                    src: room.image,
                                    alt: room.name,
                                    class: "w-full h-full object-cover"
                                  }, null, 8, ["src", "alt"])
                                ]),
                                createVNode("div", { class: "md:w-2/3 p-4" }, [
                                  createVNode("div", { class: "flex justify-between items-start mb-2" }, [
                                    createVNode("h3", { class: "text-lg font-bold text-gray-900" }, toDisplayString(room.name), 1),
                                    createVNode("div", { class: "text-right" }, [
                                      createVNode("div", { class: "text-lg font-bold text-blue-600" }, [
                                        createTextVNode("$" + toDisplayString(room.price), 1),
                                        createVNode("span", { class: "text-sm font-normal" }, "/night")
                                      ]),
                                      createVNode("div", { class: "text-sm text-gray-500" }, toDisplayString(room.occupancy), 1)
                                    ])
                                  ]),
                                  createVNode("p", { class: "text-gray-700 text-sm mb-3" }, toDisplayString(room.description), 1),
                                  createVNode("div", { class: "flex flex-wrap gap-2 mb-3" }, [
                                    (openBlock(true), createBlock(Fragment, null, renderList(room.features, (feature, idx) => {
                                      return openBlock(), createBlock("span", {
                                        key: idx,
                                        class: "bg-gray-100 text-gray-800 text-xs px-2.5 py-1 rounded"
                                      }, toDisplayString(feature), 1);
                                    }), 128))
                                  ]),
                                  createVNode("div", { class: "flex justify-between items-center mt-3" }, [
                                    room.freeCancellation ? (openBlock(), createBlock("div", {
                                      key: 0,
                                      class: "text-green-600 text-sm font-medium"
                                    }, "Free cancellation")) : (openBlock(), createBlock("div", {
                                      key: 1,
                                      class: "text-gray-500 text-sm"
                                    }, "Non-refundable")),
                                    createVNode("button", { class: "bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium transition duration-200" }, " Select Room ")
                                  ])
                                ])
                              ])
                            ]);
                          }), 128))
                        ])
                      ]),
                      createVNode("div", { class: "bg-white rounded-lg shadow-sm p-6" }, [
                        createVNode("div", { class: "flex justify-between items-center mb-4" }, [
                          createVNode("h2", { class: "text-xl font-bold text-gray-900" }, "Guest Reviews"),
                          createVNode("div", { class: "flex items-center" }, [
                            createVNode("div", { class: "bg-blue-600 text-white font-bold rounded p-2 mr-2" }, toDisplayString(hotel.value.rating), 1),
                            createVNode("div", null, [
                              createVNode("div", { class: "text-gray-900 font-medium" }, toDisplayString(getRatingText(hotel.value.rating)), 1),
                              createVNode("div", { class: "text-sm text-gray-600" }, toDisplayString(hotel.value.reviewCount) + " reviews", 1)
                            ])
                          ])
                        ]),
                        createVNode("div", { class: "flex flex-wrap gap-2 mb-4" }, [
                          createVNode("button", { class: "bg-blue-600 text-white px-3 py-1 rounded-full text-sm" }, "All"),
                          (openBlock(), createBlock(Fragment, null, renderList(["Excellent", "Very Good", "Good", "Average", "Poor"], (category) => {
                            return createVNode("button", {
                              key: category,
                              class: "border border-gray-300 px-3 py-1 rounded-full text-sm text-gray-700 hover:bg-gray-50"
                            }, toDisplayString(category), 1);
                          }), 64))
                        ]),
                        createVNode("div", { class: "space-y-6" }, [
                          (openBlock(true), createBlock(Fragment, null, renderList(hotel.value.reviews, (review, index) => {
                            return openBlock(), createBlock("div", {
                              key: index,
                              class: "border-b border-gray-200 pb-6 last:border-b-0 last:pb-0"
                            }, [
                              createVNode("div", { class: "flex justify-between items-start mb-2" }, [
                                createVNode("div", { class: "flex items-center" }, [
                                  createVNode("div", { class: "bg-blue-100 rounded-full w-10 h-10 flex items-center justify-center text-blue-600 font-medium mr-3" }, toDisplayString(review.author.charAt(0)), 1),
                                  createVNode("div", null, [
                                    createVNode("div", { class: "font-medium text-gray-900" }, toDisplayString(review.author), 1),
                                    createVNode("div", { class: "text-sm text-gray-600" }, toDisplayString(review.date), 1)
                                  ])
                                ]),
                                createVNode("div", { class: "bg-blue-600 text-white font-bold rounded p-1 px-2 text-sm" }, toDisplayString(review.rating), 1)
                              ]),
                              createVNode("p", { class: "text-gray-700" }, toDisplayString(review.comment), 1)
                            ]);
                          }), 128))
                        ]),
                        createVNode("div", { class: "text-center mt-6" }, [
                          createVNode("button", { class: "border border-gray-300 rounded-md px-4 py-2 text-gray-700 hover:bg-gray-50 font-medium" }, " Show more reviews ")
                        ])
                      ])
                    ]),
                    createVNode("div", { class: "lg:col-span-1" }, [
                      createVNode("div", { class: "bg-white rounded-lg shadow-sm p-6 mb-6 sticky top-4" }, [
                        createVNode("h2", { class: "text-lg font-bold text-gray-900 mb-4" }, "Book Your Stay"),
                        createVNode("div", { class: "space-y-4" }, [
                          createVNode("div", null, [
                            createVNode("label", { class: "block text-sm font-medium text-gray-700 mb-1" }, "Check-in / Check-out"),
                            createVNode("div", { class: "border border-gray-300 rounded-md p-2 flex" }, [
                              createVNode("div", { class: "flex-1 text-center" }, [
                                createVNode("input", {
                                  type: "date",
                                  class: "w-full border-0 focus:ring-0"
                                }),
                                createVNode("div", { class: "text-xs text-gray-500 mt-1" }, "Check-in")
                              ]),
                              createVNode("div", { class: "border-l border-gray-300 mx-2" }),
                              createVNode("div", { class: "flex-1 text-center" }, [
                                createVNode("input", {
                                  type: "date",
                                  class: "w-full border-0 focus:ring-0"
                                }),
                                createVNode("div", { class: "text-xs text-gray-500 mt-1" }, "Check-out")
                              ])
                            ])
                          ]),
                          createVNode("div", null, [
                            createVNode("label", { class: "block text-sm font-medium text-gray-700 mb-1" }, "Guests"),
                            createVNode("div", { class: "border border-gray-300 rounded-md p-2" }, [
                              createVNode("select", { class: "w-full border-0 focus:ring-0" }, [
                                createVNode("option", null, "1 adult"),
                                createVNode("option", null, "2 adults"),
                                createVNode("option", null, "2 adults, 1 child"),
                                createVNode("option", null, "2 adults, 2 children"),
                                createVNode("option", null, "3 adults"),
                                createVNode("option", null, "4 adults")
                              ])
                            ])
                          ]),
                          createVNode("div", null, [
                            createVNode("label", { class: "block text-sm font-medium text-gray-700 mb-1" }, "Rooms"),
                            createVNode("div", { class: "border border-gray-300 rounded-md p-2" }, [
                              createVNode("select", { class: "w-full border-0 focus:ring-0" }, [
                                createVNode("option", null, "1 room"),
                                createVNode("option", null, "2 rooms"),
                                createVNode("option", null, "3 rooms"),
                                createVNode("option", null, "4 rooms")
                              ])
                            ])
                          ])
                        ]),
                        createVNode("div", { class: "mt-6 space-y-4" }, [
                          createVNode("div", { class: "flex justify-between items-center text-gray-700" }, [
                            createVNode("div", null, "Average nightly rate"),
                            createVNode("div", { class: "font-semibold" }, "$" + toDisplayString(hotel.value.price), 1)
                          ]),
                          createVNode("div", { class: "flex justify-between items-center text-gray-700" }, [
                            createVNode("div", null, "Tax & fees"),
                            createVNode("div", { class: "font-semibold" }, "$" + toDisplayString(Math.round(hotel.value.price * 0.15)), 1)
                          ]),
                          createVNode("div", { class: "border-t border-gray-200 pt-4 flex justify-between items-center" }, [
                            createVNode("div", { class: "font-semibold text-gray-900" }, "Total"),
                            createVNode("div", { class: "font-bold text-lg text-blue-600" }, "$" + toDisplayString(Math.round(hotel.value.price * 1.15)), 1)
                          ])
                        ]),
                        createVNode("button", { class: "mt-6 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg w-full transition duration-200" }, " Book Now "),
                        createVNode("p", { class: "text-green-600 text-sm mt-3 flex items-center justify-center" }, [
                          (openBlock(), createBlock("svg", {
                            class: "w-4 h-4 mr-1",
                            fill: "none",
                            stroke: "currentColor",
                            viewBox: "0 0 24 24"
                          }, [
                            createVNode("path", {
                              "stroke-linecap": "round",
                              "stroke-linejoin": "round",
                              "stroke-width": "2",
                              d: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                            })
                          ])),
                          createTextVNode(" No payment required today ")
                        ])
                      ]),
                      createVNode("div", { class: "bg-white rounded-lg shadow-sm p-6 mb-6" }, [
                        createVNode("h2", { class: "text-lg font-bold text-gray-900 mb-4" }, "Location"),
                        createVNode("div", { class: "aspect-w-16 aspect-h-9 mb-3" }, [
                          createVNode("div", { class: "w-full h-48 bg-gray-200 rounded-lg mb-2 flex items-center justify-center" }, [
                            createVNode("div", { class: "text-gray-400" }, "Map placeholder")
                          ])
                        ]),
                        createVNode("div", { class: "text-gray-700" }, [
                          createVNode("p", { class: "mb-2" }, toDisplayString(hotel.value.address), 1),
                          createVNode("ul", { class: "space-y-1 text-sm" }, [
                            (openBlock(true), createBlock(Fragment, null, renderList(hotel.value.pointsOfInterest, (point, index) => {
                              return openBlock(), createBlock("li", {
                                key: index,
                                class: "flex items-start"
                              }, [
                                (openBlock(), createBlock("svg", {
                                  class: "w-4 h-4 text-gray-500 mr-1 mt-0.5",
                                  fill: "none",
                                  stroke: "currentColor",
                                  viewBox: "0 0 24 24"
                                }, [
                                  createVNode("circle", {
                                    cx: "12",
                                    cy: "12",
                                    r: "10",
                                    "stroke-width": "2"
                                  }),
                                  createVNode("path", {
                                    d: "M12 8v4l3 3",
                                    "stroke-width": "2",
                                    "stroke-linecap": "round"
                                  })
                                ])),
                                createTextVNode(" " + toDisplayString(point.name) + " - " + toDisplayString(point.distance), 1)
                              ]);
                            }), 128))
                          ])
                        ])
                      ]),
                      createVNode("div", { class: "bg-white rounded-lg shadow-sm p-6" }, [
                        createVNode("h2", { class: "text-lg font-bold text-gray-900 mb-4" }, "Hotel Policies"),
                        createVNode("div", { class: "space-y-4 text-gray-700" }, [
                          createVNode("div", null, [
                            createVNode("h3", { class: "font-medium mb-1" }, "Check-in & Check-out"),
                            createVNode("p", { class: "text-sm" }, [
                              createTextVNode("Check-in: 3:00 PM - 12:00 AM"),
                              createVNode("br"),
                              createTextVNode("Check-out: 7:00 AM - 11:00 AM")
                            ])
                          ]),
                          createVNode("div", null, [
                            createVNode("h3", { class: "font-medium mb-1" }, "Cancellation Policy"),
                            createVNode("p", { class: "text-sm" }, "Free cancellation up to 24 hours before check-in. After that, cancellation will incur a fee equivalent to the first night's stay.")
                          ]),
                          createVNode("div", null, [
                            createVNode("h3", { class: "font-medium mb-1" }, "Children & Extra Beds"),
                            createVNode("p", { class: "text-sm" }, "Children of all ages are welcome. Children 12 and above are considered adults at this property.")
                          ]),
                          createVNode("div", null, [
                            createVNode("h3", { class: "font-medium mb-1" }, "Pets"),
                            createVNode("p", { class: "text-sm" }, "Pets are not allowed.")
                          ])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/HotelDetail.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
