import { withCtx, createVNode, openBlock, createBlock, Fragment, renderList, toDisplayString, createTextVNode, createCommentVNode, useSSRContext } from "vue";
import { ssrRenderComponent, ssrRenderStyle, ssrRenderList, ssrRenderAttr, ssrInterpolate } from "vue/server-renderer";
import "@inertiajs/vue3";
import { _ as _export_sfc, G as GuestLayout } from "./GuestLayout-6b7af627.js";
const Home_vue_vue_type_style_index_0_scoped_12bd52c5_lang = "";
const _sfc_main = {
  __name: "Home",
  __ssrInlineRender: true,
  setup(__props) {
    const destinations = [
      { id: 1, name: "Istanbul", count: 124, image: "https://images.unsplash.com/photo-1524231757912-21f4fe3a7200?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2071&q=80" },
      { id: 2, name: "Antalya", count: 78, image: "https://images.unsplash.com/photo-1688841215013-6134d0fe9dd9?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" },
      { id: 3, name: "Bodrum", count: 56, image: "https://images.unsplash.com/photo-1663187171794-eaf30dd38cc2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" }
    ];
    const featuredHotels = [
      {
        id: 1,
        name: "Grand Oasis Resort",
        slug: "grand-oasis-resort",
        location: "Antalya, Turkey",
        stars: 5,
        price: 199,
        promo: "20% OFF",
        image: "https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80"
      },
      {
        id: 2,
        name: "Blue Palace Spa",
        slug: "blue-palace-spa",
        location: "Bodrum, Turkey",
        stars: 4,
        price: 149,
        promo: null,
        image: "https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80"
      },
      {
        id: 3,
        name: "Sunset Beach Hotel",
        slug: "sunset-beach-hotel",
        location: "Istanbul, Turkey",
        stars: 5,
        price: 259,
        promo: "HOT DEAL",
        image: "https://images.unsplash.com/photo-1582719508461-905c673771fd?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2025&q=80"
      }
    ];
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(GuestLayout, _attrs, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<section class="relative bg-gradient-to-r from-blue-500 to-indigo-600 text-white" data-v-12bd52c5${_scopeId}><div class="absolute inset-0 bg-cover bg-center" style="${ssrRenderStyle({ "background-image": "url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2073&q=80')", "opacity": "0.6" })}" data-v-12bd52c5${_scopeId}></div><div class="container mx-auto py-24 px-6 md:px-0 relative z-10" data-v-12bd52c5${_scopeId}><div class="max-w-3xl" data-v-12bd52c5${_scopeId}><h1 class="text-4xl md:text-6xl font-bold mb-4" data-v-12bd52c5${_scopeId}>Discover Your Perfect Getaway</h1><p class="text-xl md:text-2xl mb-8" data-v-12bd52c5${_scopeId}>Find and book the best hotels and accommodations for your dream vacation.</p><div class="bg-white rounded-lg shadow-lg p-6 text-gray-800" data-v-12bd52c5${_scopeId}><h2 class="text-xl font-semibold mb-4" data-v-12bd52c5${_scopeId}>Search for Hotels</h2><div class="grid grid-cols-1 md:grid-cols-3 gap-4" data-v-12bd52c5${_scopeId}><div data-v-12bd52c5${_scopeId}><label class="block text-sm font-medium text-gray-700 mb-1" data-v-12bd52c5${_scopeId}>Destination</label><input type="text" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Where are you going?" data-v-12bd52c5${_scopeId}></div><div data-v-12bd52c5${_scopeId}><label class="block text-sm font-medium text-gray-700 mb-1" data-v-12bd52c5${_scopeId}>Check-in / Check-out</label><input type="text" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Select dates" data-v-12bd52c5${_scopeId}></div><div data-v-12bd52c5${_scopeId}><label class="block text-sm font-medium text-gray-700 mb-1" data-v-12bd52c5${_scopeId}>Guests</label><select class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary-500 focus:border-primary-500" data-v-12bd52c5${_scopeId}><option data-v-12bd52c5${_scopeId}>2 Adults, 0 Children</option><option data-v-12bd52c5${_scopeId}>2 Adults, 1 Child</option><option data-v-12bd52c5${_scopeId}>2 Adults, 2 Children</option><option data-v-12bd52c5${_scopeId}>1 Adult, 0 Children</option></select></div></div><div class="mt-4" data-v-12bd52c5${_scopeId}><button class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-4 rounded-md transition-colors" data-v-12bd52c5${_scopeId}>Search Now</button></div></div></div></div></section><section class="py-16 bg-gray-50" data-v-12bd52c5${_scopeId}><div class="container mx-auto px-6 md:px-0" data-v-12bd52c5${_scopeId}><div class="text-center mb-12" data-v-12bd52c5${_scopeId}><h2 class="text-3xl font-bold mb-2" data-v-12bd52c5${_scopeId}>Popular Destinations</h2><p class="text-gray-600" data-v-12bd52c5${_scopeId}>Discover our most sought-after travel destinations</p></div><div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8" data-v-12bd52c5${_scopeId}><!--[-->`);
            ssrRenderList(destinations, (destination) => {
              _push2(`<div class="group" data-v-12bd52c5${_scopeId}><div class="relative rounded-xl overflow-hidden shadow-lg h-72" data-v-12bd52c5${_scopeId}><img${ssrRenderAttr("src", destination.image)}${ssrRenderAttr("alt", destination.name)} class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-300" data-v-12bd52c5${_scopeId}><div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent" data-v-12bd52c5${_scopeId}></div><div class="absolute bottom-0 left-0 p-6" data-v-12bd52c5${_scopeId}><h3 class="text-xl font-bold text-white mb-1" data-v-12bd52c5${_scopeId}>${ssrInterpolate(destination.name)}</h3><p class="text-white/80" data-v-12bd52c5${_scopeId}>${ssrInterpolate(destination.count)} Properties</p></div></div></div>`);
            });
            _push2(`<!--]--></div><div class="text-center mt-10" data-v-12bd52c5${_scopeId}><a href="/regions" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium" data-v-12bd52c5${_scopeId}> View All Destinations <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" data-v-12bd52c5${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" data-v-12bd52c5${_scopeId}></path></svg></a></div></div></section><section class="py-16" data-v-12bd52c5${_scopeId}><div class="container mx-auto px-6 md:px-0" data-v-12bd52c5${_scopeId}><div class="text-center mb-12" data-v-12bd52c5${_scopeId}><h2 class="text-3xl font-bold mb-2" data-v-12bd52c5${_scopeId}>Featured Hotels</h2><p class="text-gray-600" data-v-12bd52c5${_scopeId}>Handpicked hotels for your perfect stay</p></div><div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" data-v-12bd52c5${_scopeId}><!--[-->`);
            ssrRenderList(featuredHotels, (hotel) => {
              _push2(`<div class="bg-white rounded-xl shadow-lg overflow-hidden" data-v-12bd52c5${_scopeId}><div class="relative h-48" data-v-12bd52c5${_scopeId}><img${ssrRenderAttr("src", hotel.image)}${ssrRenderAttr("alt", hotel.name)} class="w-full h-full object-cover" data-v-12bd52c5${_scopeId}>`);
              if (hotel.promo) {
                _push2(`<div class="absolute top-4 left-4 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded" data-v-12bd52c5${_scopeId}>${ssrInterpolate(hotel.promo)}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><div class="p-6" data-v-12bd52c5${_scopeId}><div class="flex justify-between items-start mb-2" data-v-12bd52c5${_scopeId}><h3 class="text-lg font-bold" data-v-12bd52c5${_scopeId}>${ssrInterpolate(hotel.name)}</h3><div class="flex" data-v-12bd52c5${_scopeId}><!--[-->`);
              ssrRenderList(5, (i) => {
                _push2(`<span class="text-yellow-400" data-v-12bd52c5${_scopeId}>${ssrInterpolate(i <= hotel.stars ? "★" : "☆")}</span>`);
              });
              _push2(`<!--]--></div></div><p class="text-gray-500 text-sm mb-4" data-v-12bd52c5${_scopeId}>${ssrInterpolate(hotel.location)}</p><div class="flex items-center justify-between" data-v-12bd52c5${_scopeId}><div data-v-12bd52c5${_scopeId}><span class="text-lg font-bold" data-v-12bd52c5${_scopeId}>$${ssrInterpolate(hotel.price)}</span><span class="text-gray-500 text-sm" data-v-12bd52c5${_scopeId}>/night</span></div><a${ssrRenderAttr("href", "/hotels/" + hotel.slug)} class="inline-flex items-center px-4 py-2 border border-primary-600 text-primary-600 rounded hover:bg-primary-600 hover:text-white transition-colors" data-v-12bd52c5${_scopeId}> View Details </a></div></div></div>`);
            });
            _push2(`<!--]--></div><div class="text-center mt-10" data-v-12bd52c5${_scopeId}><a href="/hotels" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium" data-v-12bd52c5${_scopeId}> View All Hotels <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" data-v-12bd52c5${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" data-v-12bd52c5${_scopeId}></path></svg></a></div></div></section><section class="py-16 bg-gray-50" data-v-12bd52c5${_scopeId}><div class="container mx-auto px-6 md:px-0" data-v-12bd52c5${_scopeId}><div class="text-center mb-12" data-v-12bd52c5${_scopeId}><h2 class="text-3xl font-bold mb-2" data-v-12bd52c5${_scopeId}>Why Choose Us</h2><p class="text-gray-600" data-v-12bd52c5${_scopeId}>The best reasons to book with TravelManager</p></div><div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8" data-v-12bd52c5${_scopeId}><div class="text-center p-6" data-v-12bd52c5${_scopeId}><div class="inline-block p-4 bg-primary-100 text-primary-600 rounded-full mb-4" data-v-12bd52c5${_scopeId}><svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" data-v-12bd52c5${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" data-v-12bd52c5${_scopeId}></path></svg></div><h3 class="text-xl font-bold mb-2" data-v-12bd52c5${_scopeId}>Best Price Guarantee</h3><p class="text-gray-600" data-v-12bd52c5${_scopeId}>Find a lower price? We&#39;ll match it and give you an additional 10% off.</p></div><div class="text-center p-6" data-v-12bd52c5${_scopeId}><div class="inline-block p-4 bg-primary-100 text-primary-600 rounded-full mb-4" data-v-12bd52c5${_scopeId}><svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" data-v-12bd52c5${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" data-v-12bd52c5${_scopeId}></path></svg></div><h3 class="text-xl font-bold mb-2" data-v-12bd52c5${_scopeId}>Secure Booking</h3><p class="text-gray-600" data-v-12bd52c5${_scopeId}>Your booking and personal information are safe with our industry-leading security.</p></div><div class="text-center p-6" data-v-12bd52c5${_scopeId}><div class="inline-block p-4 bg-primary-100 text-primary-600 rounded-full mb-4" data-v-12bd52c5${_scopeId}><svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" data-v-12bd52c5${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" data-v-12bd52c5${_scopeId}></path></svg></div><h3 class="text-xl font-bold mb-2" data-v-12bd52c5${_scopeId}>24/7 Support</h3><p class="text-gray-600" data-v-12bd52c5${_scopeId}>Need help? Our customer support team is available 24/7 to assist you.</p></div><div class="text-center p-6" data-v-12bd52c5${_scopeId}><div class="inline-block p-4 bg-primary-100 text-primary-600 rounded-full mb-4" data-v-12bd52c5${_scopeId}><svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" data-v-12bd52c5${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905a3.61 3.61 0 01-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" data-v-12bd52c5${_scopeId}></path></svg></div><h3 class="text-xl font-bold mb-2" data-v-12bd52c5${_scopeId}>No Booking Fees</h3><p class="text-gray-600" data-v-12bd52c5${_scopeId}>Book without any hidden charges or booking fees. What you see is what you pay.</p></div></div></div></section><section class="py-16 bg-primary-600 text-white" data-v-12bd52c5${_scopeId}><div class="container mx-auto px-6 md:px-0" data-v-12bd52c5${_scopeId}><div class="max-w-3xl mx-auto text-center" data-v-12bd52c5${_scopeId}><h2 class="text-3xl font-bold mb-3" data-v-12bd52c5${_scopeId}>Subscribe to Our Newsletter</h2><p class="mb-6" data-v-12bd52c5${_scopeId}>Stay updated with our latest offers, deals, and travel inspiration.</p><div class="flex flex-col sm:flex-row gap-2" data-v-12bd52c5${_scopeId}><input type="email" class="flex-grow px-4 py-3 rounded-md focus:outline-none text-gray-800" placeholder="Your email address" data-v-12bd52c5${_scopeId}><button class="px-6 py-3 bg-white text-primary-600 font-bold rounded-md hover:bg-gray-100 transition-colors" data-v-12bd52c5${_scopeId}>Subscribe</button></div><p class="mt-4 text-sm text-white/80" data-v-12bd52c5${_scopeId}>By subscribing, you agree to our Privacy Policy and consent to receive updates from us.</p></div></div></section>`);
          } else {
            return [
              createVNode("section", { class: "relative bg-gradient-to-r from-blue-500 to-indigo-600 text-white" }, [
                createVNode("div", {
                  class: "absolute inset-0 bg-cover bg-center",
                  style: { "background-image": "url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2073&q=80')", "opacity": "0.6" }
                }),
                createVNode("div", { class: "container mx-auto py-24 px-6 md:px-0 relative z-10" }, [
                  createVNode("div", { class: "max-w-3xl" }, [
                    createVNode("h1", { class: "text-4xl md:text-6xl font-bold mb-4" }, "Discover Your Perfect Getaway"),
                    createVNode("p", { class: "text-xl md:text-2xl mb-8" }, "Find and book the best hotels and accommodations for your dream vacation."),
                    createVNode("div", { class: "bg-white rounded-lg shadow-lg p-6 text-gray-800" }, [
                      createVNode("h2", { class: "text-xl font-semibold mb-4" }, "Search for Hotels"),
                      createVNode("div", { class: "grid grid-cols-1 md:grid-cols-3 gap-4" }, [
                        createVNode("div", null, [
                          createVNode("label", { class: "block text-sm font-medium text-gray-700 mb-1" }, "Destination"),
                          createVNode("input", {
                            type: "text",
                            class: "w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary-500 focus:border-primary-500",
                            placeholder: "Where are you going?"
                          })
                        ]),
                        createVNode("div", null, [
                          createVNode("label", { class: "block text-sm font-medium text-gray-700 mb-1" }, "Check-in / Check-out"),
                          createVNode("input", {
                            type: "text",
                            class: "w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary-500 focus:border-primary-500",
                            placeholder: "Select dates"
                          })
                        ]),
                        createVNode("div", null, [
                          createVNode("label", { class: "block text-sm font-medium text-gray-700 mb-1" }, "Guests"),
                          createVNode("select", { class: "w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary-500 focus:border-primary-500" }, [
                            createVNode("option", null, "2 Adults, 0 Children"),
                            createVNode("option", null, "2 Adults, 1 Child"),
                            createVNode("option", null, "2 Adults, 2 Children"),
                            createVNode("option", null, "1 Adult, 0 Children")
                          ])
                        ])
                      ]),
                      createVNode("div", { class: "mt-4" }, [
                        createVNode("button", { class: "w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-4 rounded-md transition-colors" }, "Search Now")
                      ])
                    ])
                  ])
                ])
              ]),
              createVNode("section", { class: "py-16 bg-gray-50" }, [
                createVNode("div", { class: "container mx-auto px-6 md:px-0" }, [
                  createVNode("div", { class: "text-center mb-12" }, [
                    createVNode("h2", { class: "text-3xl font-bold mb-2" }, "Popular Destinations"),
                    createVNode("p", { class: "text-gray-600" }, "Discover our most sought-after travel destinations")
                  ]),
                  createVNode("div", { class: "grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8" }, [
                    (openBlock(), createBlock(Fragment, null, renderList(destinations, (destination) => {
                      return createVNode("div", {
                        key: destination.id,
                        class: "group"
                      }, [
                        createVNode("div", { class: "relative rounded-xl overflow-hidden shadow-lg h-72" }, [
                          createVNode("img", {
                            src: destination.image,
                            alt: destination.name,
                            class: "w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-300"
                          }, null, 8, ["src", "alt"]),
                          createVNode("div", { class: "absolute inset-0 bg-gradient-to-t from-black/70 to-transparent" }),
                          createVNode("div", { class: "absolute bottom-0 left-0 p-6" }, [
                            createVNode("h3", { class: "text-xl font-bold text-white mb-1" }, toDisplayString(destination.name), 1),
                            createVNode("p", { class: "text-white/80" }, toDisplayString(destination.count) + " Properties", 1)
                          ])
                        ])
                      ]);
                    }), 64))
                  ]),
                  createVNode("div", { class: "text-center mt-10" }, [
                    createVNode("a", {
                      href: "/regions",
                      class: "inline-flex items-center text-primary-600 hover:text-primary-700 font-medium"
                    }, [
                      createTextVNode(" View All Destinations "),
                      (openBlock(), createBlock("svg", {
                        class: "ml-2 w-5 h-5",
                        fill: "none",
                        stroke: "currentColor",
                        viewBox: "0 0 24 24",
                        xmlns: "http://www.w3.org/2000/svg"
                      }, [
                        createVNode("path", {
                          "stroke-linecap": "round",
                          "stroke-linejoin": "round",
                          "stroke-width": "2",
                          d: "M14 5l7 7m0 0l-7 7m7-7H3"
                        })
                      ]))
                    ])
                  ])
                ])
              ]),
              createVNode("section", { class: "py-16" }, [
                createVNode("div", { class: "container mx-auto px-6 md:px-0" }, [
                  createVNode("div", { class: "text-center mb-12" }, [
                    createVNode("h2", { class: "text-3xl font-bold mb-2" }, "Featured Hotels"),
                    createVNode("p", { class: "text-gray-600" }, "Handpicked hotels for your perfect stay")
                  ]),
                  createVNode("div", { class: "grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" }, [
                    (openBlock(), createBlock(Fragment, null, renderList(featuredHotels, (hotel) => {
                      return createVNode("div", {
                        key: hotel.id,
                        class: "bg-white rounded-xl shadow-lg overflow-hidden"
                      }, [
                        createVNode("div", { class: "relative h-48" }, [
                          createVNode("img", {
                            src: hotel.image,
                            alt: hotel.name,
                            class: "w-full h-full object-cover"
                          }, null, 8, ["src", "alt"]),
                          hotel.promo ? (openBlock(), createBlock("div", {
                            key: 0,
                            class: "absolute top-4 left-4 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded"
                          }, toDisplayString(hotel.promo), 1)) : createCommentVNode("", true)
                        ]),
                        createVNode("div", { class: "p-6" }, [
                          createVNode("div", { class: "flex justify-between items-start mb-2" }, [
                            createVNode("h3", { class: "text-lg font-bold" }, toDisplayString(hotel.name), 1),
                            createVNode("div", { class: "flex" }, [
                              (openBlock(), createBlock(Fragment, null, renderList(5, (i) => {
                                return createVNode("span", {
                                  key: i,
                                  class: "text-yellow-400"
                                }, toDisplayString(i <= hotel.stars ? "★" : "☆"), 1);
                              }), 64))
                            ])
                          ]),
                          createVNode("p", { class: "text-gray-500 text-sm mb-4" }, toDisplayString(hotel.location), 1),
                          createVNode("div", { class: "flex items-center justify-between" }, [
                            createVNode("div", null, [
                              createVNode("span", { class: "text-lg font-bold" }, "$" + toDisplayString(hotel.price), 1),
                              createVNode("span", { class: "text-gray-500 text-sm" }, "/night")
                            ]),
                            createVNode("a", {
                              href: "/hotels/" + hotel.slug,
                              class: "inline-flex items-center px-4 py-2 border border-primary-600 text-primary-600 rounded hover:bg-primary-600 hover:text-white transition-colors"
                            }, " View Details ", 8, ["href"])
                          ])
                        ])
                      ]);
                    }), 64))
                  ]),
                  createVNode("div", { class: "text-center mt-10" }, [
                    createVNode("a", {
                      href: "/hotels",
                      class: "inline-flex items-center text-primary-600 hover:text-primary-700 font-medium"
                    }, [
                      createTextVNode(" View All Hotels "),
                      (openBlock(), createBlock("svg", {
                        class: "ml-2 w-5 h-5",
                        fill: "none",
                        stroke: "currentColor",
                        viewBox: "0 0 24 24",
                        xmlns: "http://www.w3.org/2000/svg"
                      }, [
                        createVNode("path", {
                          "stroke-linecap": "round",
                          "stroke-linejoin": "round",
                          "stroke-width": "2",
                          d: "M14 5l7 7m0 0l-7 7m7-7H3"
                        })
                      ]))
                    ])
                  ])
                ])
              ]),
              createVNode("section", { class: "py-16 bg-gray-50" }, [
                createVNode("div", { class: "container mx-auto px-6 md:px-0" }, [
                  createVNode("div", { class: "text-center mb-12" }, [
                    createVNode("h2", { class: "text-3xl font-bold mb-2" }, "Why Choose Us"),
                    createVNode("p", { class: "text-gray-600" }, "The best reasons to book with TravelManager")
                  ]),
                  createVNode("div", { class: "grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8" }, [
                    createVNode("div", { class: "text-center p-6" }, [
                      createVNode("div", { class: "inline-block p-4 bg-primary-100 text-primary-600 rounded-full mb-4" }, [
                        (openBlock(), createBlock("svg", {
                          class: "h-8 w-8",
                          fill: "none",
                          stroke: "currentColor",
                          viewBox: "0 0 24 24",
                          xmlns: "http://www.w3.org/2000/svg"
                        }, [
                          createVNode("path", {
                            "stroke-linecap": "round",
                            "stroke-linejoin": "round",
                            "stroke-width": "2",
                            d: "M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                          })
                        ]))
                      ]),
                      createVNode("h3", { class: "text-xl font-bold mb-2" }, "Best Price Guarantee"),
                      createVNode("p", { class: "text-gray-600" }, "Find a lower price? We'll match it and give you an additional 10% off.")
                    ]),
                    createVNode("div", { class: "text-center p-6" }, [
                      createVNode("div", { class: "inline-block p-4 bg-primary-100 text-primary-600 rounded-full mb-4" }, [
                        (openBlock(), createBlock("svg", {
                          class: "h-8 w-8",
                          fill: "none",
                          stroke: "currentColor",
                          viewBox: "0 0 24 24",
                          xmlns: "http://www.w3.org/2000/svg"
                        }, [
                          createVNode("path", {
                            "stroke-linecap": "round",
                            "stroke-linejoin": "round",
                            "stroke-width": "2",
                            d: "M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                          })
                        ]))
                      ]),
                      createVNode("h3", { class: "text-xl font-bold mb-2" }, "Secure Booking"),
                      createVNode("p", { class: "text-gray-600" }, "Your booking and personal information are safe with our industry-leading security.")
                    ]),
                    createVNode("div", { class: "text-center p-6" }, [
                      createVNode("div", { class: "inline-block p-4 bg-primary-100 text-primary-600 rounded-full mb-4" }, [
                        (openBlock(), createBlock("svg", {
                          class: "h-8 w-8",
                          fill: "none",
                          stroke: "currentColor",
                          viewBox: "0 0 24 24",
                          xmlns: "http://www.w3.org/2000/svg"
                        }, [
                          createVNode("path", {
                            "stroke-linecap": "round",
                            "stroke-linejoin": "round",
                            "stroke-width": "2",
                            d: "M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                          })
                        ]))
                      ]),
                      createVNode("h3", { class: "text-xl font-bold mb-2" }, "24/7 Support"),
                      createVNode("p", { class: "text-gray-600" }, "Need help? Our customer support team is available 24/7 to assist you.")
                    ]),
                    createVNode("div", { class: "text-center p-6" }, [
                      createVNode("div", { class: "inline-block p-4 bg-primary-100 text-primary-600 rounded-full mb-4" }, [
                        (openBlock(), createBlock("svg", {
                          class: "h-8 w-8",
                          fill: "none",
                          stroke: "currentColor",
                          viewBox: "0 0 24 24",
                          xmlns: "http://www.w3.org/2000/svg"
                        }, [
                          createVNode("path", {
                            "stroke-linecap": "round",
                            "stroke-linejoin": "round",
                            "stroke-width": "2",
                            d: "M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905a3.61 3.61 0 01-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"
                          })
                        ]))
                      ]),
                      createVNode("h3", { class: "text-xl font-bold mb-2" }, "No Booking Fees"),
                      createVNode("p", { class: "text-gray-600" }, "Book without any hidden charges or booking fees. What you see is what you pay.")
                    ])
                  ])
                ])
              ]),
              createVNode("section", { class: "py-16 bg-primary-600 text-white" }, [
                createVNode("div", { class: "container mx-auto px-6 md:px-0" }, [
                  createVNode("div", { class: "max-w-3xl mx-auto text-center" }, [
                    createVNode("h2", { class: "text-3xl font-bold mb-3" }, "Subscribe to Our Newsletter"),
                    createVNode("p", { class: "mb-6" }, "Stay updated with our latest offers, deals, and travel inspiration."),
                    createVNode("div", { class: "flex flex-col sm:flex-row gap-2" }, [
                      createVNode("input", {
                        type: "email",
                        class: "flex-grow px-4 py-3 rounded-md focus:outline-none text-gray-800",
                        placeholder: "Your email address"
                      }),
                      createVNode("button", { class: "px-6 py-3 bg-white text-primary-600 font-bold rounded-md hover:bg-gray-100 transition-colors" }, "Subscribe")
                    ]),
                    createVNode("p", { class: "mt-4 text-sm text-white/80" }, "By subscribing, you agree to our Privacy Policy and consent to receive updates from us.")
                  ])
                ])
              ])
            ];
          }
        }),
        _: 1
      }, _parent));
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Home.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Home = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-12bd52c5"]]);
export {
  Home as default
};
