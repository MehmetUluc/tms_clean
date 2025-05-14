import { ref, computed, unref, withCtx, createVNode, toDisplayString, openBlock, createBlock, createTextVNode, withDirectives, vModelText, vModelSelect, Fragment, renderList, useSSRContext } from "vue";
import { ssrRenderComponent, ssrRenderAttr, ssrIncludeBooleanAttr, ssrLooseContain, ssrLooseEqual, ssrRenderList, ssrInterpolate, ssrRenderClass } from "vue/server-renderer";
import { Head, Link } from "@inertiajs/vue3";
import { G as GuestLayout } from "./GuestLayout-6b7af627.js";
const itemsPerPage = 16;
const _sfc_main = {
  __name: "Destinations",
  __ssrInlineRender: true,
  setup(__props) {
    const search = ref("");
    const regionFilter = ref("all");
    const viewMode = ref("grid");
    const currentPage = ref(1);
    const featuredDestinations = [
      {
        id: 1,
        name: "Istanbul",
        region: "Marmara Region",
        image: "/images/destinations/istanbul.jpg",
        hotelCount: 1245
      },
      {
        id: 2,
        name: "Antalya",
        region: "Mediterranean Coast",
        image: "/images/destinations/antalya.jpg",
        hotelCount: 873
      },
      {
        id: 3,
        name: "Cappadocia",
        region: "Central Anatolia",
        image: "/images/destinations/cappadocia.jpg",
        hotelCount: 342
      }
    ];
    const allDestinations = ref([
      {
        id: 1,
        name: "Istanbul",
        region: "Marmara Region",
        regionKey: "marmara",
        image: "/images/destinations/istanbul.jpg",
        description: "Explore the fascinating cultural fusion of East and West in Istanbul, a city straddling two continents with a rich historical heritage.",
        hotelCount: 1245
      },
      {
        id: 2,
        name: "Antalya",
        region: "Mediterranean Coast",
        regionKey: "mediterranean",
        image: "/images/destinations/antalya.jpg",
        description: "Enjoy the stunning beaches and ancient ruins of Antalya, a beautiful coastal city on the Mediterranean Turkish Riviera.",
        hotelCount: 873
      },
      {
        id: 3,
        name: "Cappadocia",
        region: "Central Anatolia",
        regionKey: "central",
        image: "/images/destinations/cappadocia.jpg",
        description: "Discover the otherworldly landscapes of Cappadocia with its famous fairy chimneys, hot air balloon rides, and cave dwellings.",
        hotelCount: 342
      },
      {
        id: 4,
        name: "Bodrum",
        region: "Aegean Coast",
        regionKey: "aegean",
        image: "/images/destinations/bodrum.jpg",
        description: "Experience the vibrant nightlife and beautiful beaches of Bodrum, a popular resort town on the Aegean Sea.",
        hotelCount: 521
      },
      {
        id: 5,
        name: "Izmir",
        region: "Aegean Coast",
        regionKey: "aegean",
        image: "/images/destinations/izmir.jpg",
        description: "Visit Izmir, Turkey's third-largest city, known for its modern atmosphere, seaside promenade, and proximity to ancient Ephesus.",
        hotelCount: 456
      },
      {
        id: 6,
        name: "Fethiye",
        region: "Mediterranean Coast",
        regionKey: "mediterranean",
        image: "/images/destinations/fethiye.jpg",
        description: "Relax in Fethiye with its marina, beautiful beaches, and the famous Blue Lagoon at nearby Ölüdeniz.",
        hotelCount: 328
      },
      {
        id: 7,
        name: "Trabzon",
        region: "Black Sea Coast",
        regionKey: "blacksea",
        image: "/images/destinations/trabzon.jpg",
        description: "Discover the lush green landscapes of Trabzon on the Black Sea coast, home to the historic Sumela Monastery.",
        hotelCount: 187
      },
      {
        id: 8,
        name: "Konya",
        region: "Central Anatolia",
        regionKey: "central",
        image: "/images/destinations/konya.jpg",
        description: "Experience the spiritual heritage of Konya, the city of the whirling dervishes and Rumi's final resting place.",
        hotelCount: 132
      },
      {
        id: 9,
        name: "Bursa",
        region: "Marmara Region",
        regionKey: "marmara",
        image: "/images/destinations/bursa.jpg",
        description: "Visit Bursa, the first capital of the Ottoman Empire, known for its historic architecture and thermal springs.",
        hotelCount: 245
      },
      {
        id: 10,
        name: "Marmaris",
        region: "Aegean Coast",
        regionKey: "aegean",
        image: "/images/destinations/marmaris.jpg",
        description: "Enjoy the lively atmosphere of Marmaris, a popular resort town with beautiful beaches and a vibrant marina.",
        hotelCount: 387
      },
      {
        id: 11,
        name: "Alanya",
        region: "Mediterranean Coast",
        regionKey: "mediterranean",
        image: "/images/destinations/alanya.jpg",
        description: "Discover Alanya with its iconic castle, beautiful beaches, and vibrant nightlife on the Mediterranean coast.",
        hotelCount: 421
      },
      {
        id: 12,
        name: "Ankara",
        region: "Central Anatolia",
        regionKey: "central",
        image: "/images/destinations/ankara.jpg",
        description: "Explore Turkey's capital city Ankara, home to important museums, government buildings, and Atatürk's mausoleum.",
        hotelCount: 312
      },
      {
        id: 13,
        name: "Erzurum",
        region: "Eastern Anatolia",
        regionKey: "eastern",
        image: "/images/destinations/erzurum.jpg",
        description: "Visit Erzurum, an important center in Eastern Turkey known for winter sports and historic architecture.",
        hotelCount: 98
      },
      {
        id: 14,
        name: "Kayseri",
        region: "Central Anatolia",
        regionKey: "central",
        image: "/images/destinations/kayseri.jpg",
        description: "Discover Kayseri, a city at the base of Mount Erciyes, known for its historic sites and as a gateway to Cappadocia.",
        hotelCount: 143
      },
      {
        id: 15,
        name: "Canakkale",
        region: "Marmara Region",
        regionKey: "marmara",
        image: "/images/destinations/canakkale.jpg",
        description: "Explore Canakkale, the gateway to the ancient city of Troy and the Gallipoli battlefields.",
        hotelCount: 156
      },
      {
        id: 16,
        name: "Datca",
        region: "Aegean Coast",
        regionKey: "aegean",
        image: "/images/destinations/datca.jpg",
        description: "Relax in the unspoiled beauty of Datca Peninsula with its pristine beaches and traditional villages.",
        hotelCount: 87
      },
      {
        id: 17,
        name: "Rize",
        region: "Black Sea Coast",
        regionKey: "blacksea",
        image: "/images/destinations/rize.jpg",
        description: "Visit Rize, famous for its tea plantations and beautiful green landscapes on the Black Sea coast.",
        hotelCount: 76
      },
      {
        id: 18,
        name: "Cesme",
        region: "Aegean Coast",
        regionKey: "aegean",
        image: "/images/destinations/cesme.jpg",
        description: "Enjoy the thermal springs and beautiful beaches of Cesme, a popular resort town near Izmir.",
        hotelCount: 213
      },
      {
        id: 19,
        name: "Gaziantep",
        region: "Eastern Anatolia",
        regionKey: "eastern",
        image: "/images/destinations/gaziantep.jpg",
        description: "Discover Gaziantep, known for its amazing cuisine, especially baklava, and rich cultural heritage.",
        hotelCount: 124
      },
      {
        id: 20,
        name: "Kusadasi",
        region: "Aegean Coast",
        regionKey: "aegean",
        image: "/images/destinations/kusadasi.jpg",
        description: "Visit Kusadasi, a popular cruise port and beach resort town close to the ancient city of Ephesus.",
        hotelCount: 287
      }
    ]);
    const filteredDestinations = computed(() => {
      let results = [...allDestinations.value];
      if (search.value) {
        const searchLower = search.value.toLowerCase();
        results = results.filter(
          (destination) => destination.name.toLowerCase().includes(searchLower) || destination.region.toLowerCase().includes(searchLower) || destination.description.toLowerCase().includes(searchLower)
        );
      }
      if (regionFilter.value !== "all") {
        results = results.filter((destination) => destination.regionKey === regionFilter.value);
      }
      return results;
    });
    const totalPages = computed(() => {
      return Math.ceil(filteredDestinations.value.length / itemsPerPage);
    });
    computed(() => {
      const start = (currentPage.value - 1) * itemsPerPage;
      const end = start + itemsPerPage;
      return filteredDestinations.value.slice(start, end);
    });
    const route = (name, params) => {
      if (name === "destinations.show") {
        return `/destinations/${params}`;
      }
      return "/";
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), { title: "Destinations" }, null, _parent));
      _push(ssrRenderComponent(GuestLayout, null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="bg-gray-50 py-10"${_scopeId}><div class="container mx-auto px-4"${_scopeId}><div class="text-center mb-12"${_scopeId}><h1 class="text-3xl font-bold text-gray-900 md:text-4xl mb-3"${_scopeId}>Explore Destinations</h1><p class="text-gray-600 max-w-2xl mx-auto"${_scopeId}>Discover amazing places to stay and experience authentic Turkish hospitality. From historic cities to seaside resorts, find your perfect destination.</p></div><div class="bg-white rounded-lg shadow-sm p-4 mb-8"${_scopeId}><div class="flex flex-col md:flex-row gap-4"${_scopeId}><div class="flex-grow"${_scopeId}><div class="relative"${_scopeId}><input type="text" class="w-full border-gray-300 rounded-md pl-10 pr-4 py-2 focus:border-blue-500 focus:ring-blue-500" placeholder="Search destinations..."${ssrRenderAttr("value", search.value)}${_scopeId}><div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"${_scopeId}><svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"${_scopeId}></path></svg></div></div></div><div class="flex-shrink-0 w-full md:w-auto"${_scopeId}><select class="w-full border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500"${_scopeId}><option value="all"${ssrIncludeBooleanAttr(Array.isArray(regionFilter.value) ? ssrLooseContain(regionFilter.value, "all") : ssrLooseEqual(regionFilter.value, "all")) ? " selected" : ""}${_scopeId}>All regions</option><option value="aegean"${ssrIncludeBooleanAttr(Array.isArray(regionFilter.value) ? ssrLooseContain(regionFilter.value, "aegean") : ssrLooseEqual(regionFilter.value, "aegean")) ? " selected" : ""}${_scopeId}>Aegean Coast</option><option value="mediterranean"${ssrIncludeBooleanAttr(Array.isArray(regionFilter.value) ? ssrLooseContain(regionFilter.value, "mediterranean") : ssrLooseEqual(regionFilter.value, "mediterranean")) ? " selected" : ""}${_scopeId}>Mediterranean Coast</option><option value="blacksea"${ssrIncludeBooleanAttr(Array.isArray(regionFilter.value) ? ssrLooseContain(regionFilter.value, "blacksea") : ssrLooseEqual(regionFilter.value, "blacksea")) ? " selected" : ""}${_scopeId}>Black Sea Coast</option><option value="central"${ssrIncludeBooleanAttr(Array.isArray(regionFilter.value) ? ssrLooseContain(regionFilter.value, "central") : ssrLooseEqual(regionFilter.value, "central")) ? " selected" : ""}${_scopeId}>Central Anatolia</option><option value="marmara"${ssrIncludeBooleanAttr(Array.isArray(regionFilter.value) ? ssrLooseContain(regionFilter.value, "marmara") : ssrLooseEqual(regionFilter.value, "marmara")) ? " selected" : ""}${_scopeId}>Marmara Region</option><option value="eastern"${ssrIncludeBooleanAttr(Array.isArray(regionFilter.value) ? ssrLooseContain(regionFilter.value, "eastern") : ssrLooseEqual(regionFilter.value, "eastern")) ? " selected" : ""}${_scopeId}>Eastern Anatolia</option></select></div></div></div><div class="mb-12"${_scopeId}><h2 class="text-2xl font-bold text-gray-900 mb-6"${_scopeId}>Featured Destinations</h2><div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"${_scopeId}><!--[-->`);
            ssrRenderList(featuredDestinations, (destination) => {
              _push2(`<div class="relative group rounded-lg overflow-hidden shadow-sm transition-transform duration-300 hover:-translate-y-1 hover:shadow-md"${_scopeId}>`);
              _push2(ssrRenderComponent(unref(Link), {
                href: route("destinations.show", destination.id),
                class: "block"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<div class="aspect-w-16 aspect-h-9"${_scopeId2}><img${ssrRenderAttr("src", destination.image)}${ssrRenderAttr("alt", destination.name)} class="w-full h-full object-cover"${_scopeId2}></div><div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent pointer-events-none"${_scopeId2}></div><div class="absolute bottom-0 left-0 right-0 p-4 text-white"${_scopeId2}><h3 class="text-xl font-bold mb-1"${_scopeId2}>${ssrInterpolate(destination.name)}</h3><div class="flex items-center text-sm"${_scopeId2}><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"${_scopeId2}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"${_scopeId2}></path></svg> ${ssrInterpolate(destination.hotelCount)} hotels </div></div><div class="absolute top-3 right-3 bg-white/90 text-blue-600 font-bold px-2 py-1 rounded text-sm"${_scopeId2}>${ssrInterpolate(destination.region)}</div>`);
                  } else {
                    return [
                      createVNode("div", { class: "aspect-w-16 aspect-h-9" }, [
                        createVNode("img", {
                          src: destination.image,
                          alt: destination.name,
                          class: "w-full h-full object-cover"
                        }, null, 8, ["src", "alt"])
                      ]),
                      createVNode("div", { class: "absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent pointer-events-none" }),
                      createVNode("div", { class: "absolute bottom-0 left-0 right-0 p-4 text-white" }, [
                        createVNode("h3", { class: "text-xl font-bold mb-1" }, toDisplayString(destination.name), 1),
                        createVNode("div", { class: "flex items-center text-sm" }, [
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
                              d: "M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                            })
                          ])),
                          createTextVNode(" " + toDisplayString(destination.hotelCount) + " hotels ", 1)
                        ])
                      ]),
                      createVNode("div", { class: "absolute top-3 right-3 bg-white/90 text-blue-600 font-bold px-2 py-1 rounded text-sm" }, toDisplayString(destination.region), 1)
                    ];
                  }
                }),
                _: 2
              }, _parent2, _scopeId));
              _push2(`</div>`);
            });
            _push2(`<!--]--></div></div><div${_scopeId}><div class="flex justify-between items-center mb-6"${_scopeId}><h2 class="text-2xl font-bold text-gray-900"${_scopeId}>All Destinations</h2><div class="flex items-center space-x-2"${_scopeId}><span class="text-sm text-gray-700"${_scopeId}>View:</span><button class="${ssrRenderClass([viewMode.value === "grid" ? "bg-gray-200" : "hover:bg-gray-100", "p-1 rounded-md"])}"${_scopeId}><svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"${_scopeId}></path></svg></button><button class="${ssrRenderClass([viewMode.value === "list" ? "bg-gray-200" : "hover:bg-gray-100", "p-1 rounded-md"])}"${_scopeId}><svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"${_scopeId}></path></svg></button></div></div>`);
            if (viewMode.value === "grid") {
              _push2(`<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-8"${_scopeId}><!--[-->`);
              ssrRenderList(filteredDestinations.value, (destination) => {
                _push2(ssrRenderComponent(unref(Link), {
                  key: destination.id,
                  href: route("destinations.show", destination.id),
                  class: "block bg-white rounded-lg overflow-hidden shadow-sm transition-transform duration-300 hover:-translate-y-1 hover:shadow-md"
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`<div class="aspect-w-3 aspect-h-2"${_scopeId2}><img${ssrRenderAttr("src", destination.image)}${ssrRenderAttr("alt", destination.name)} class="w-full h-full object-cover"${_scopeId2}></div><div class="p-4"${_scopeId2}><h3 class="font-bold text-gray-900"${_scopeId2}>${ssrInterpolate(destination.name)}</h3><p class="text-gray-600 text-sm mb-2"${_scopeId2}>${ssrInterpolate(destination.region)}</p><div class="flex items-center text-xs text-gray-500"${_scopeId2}><svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"${_scopeId2}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"${_scopeId2}></path></svg> ${ssrInterpolate(destination.hotelCount)} hotels </div></div>`);
                    } else {
                      return [
                        createVNode("div", { class: "aspect-w-3 aspect-h-2" }, [
                          createVNode("img", {
                            src: destination.image,
                            alt: destination.name,
                            class: "w-full h-full object-cover"
                          }, null, 8, ["src", "alt"])
                        ]),
                        createVNode("div", { class: "p-4" }, [
                          createVNode("h3", { class: "font-bold text-gray-900" }, toDisplayString(destination.name), 1),
                          createVNode("p", { class: "text-gray-600 text-sm mb-2" }, toDisplayString(destination.region), 1),
                          createVNode("div", { class: "flex items-center text-xs text-gray-500" }, [
                            (openBlock(), createBlock("svg", {
                              class: "w-3 h-3 mr-1",
                              fill: "none",
                              stroke: "currentColor",
                              viewBox: "0 0 24 24"
                            }, [
                              createVNode("path", {
                                "stroke-linecap": "round",
                                "stroke-linejoin": "round",
                                "stroke-width": "2",
                                d: "M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                              })
                            ])),
                            createTextVNode(" " + toDisplayString(destination.hotelCount) + " hotels ", 1)
                          ])
                        ])
                      ];
                    }
                  }),
                  _: 2
                }, _parent2, _scopeId));
              });
              _push2(`<!--]--></div>`);
            } else {
              _push2(`<div class="space-y-4 mb-8"${_scopeId}><!--[-->`);
              ssrRenderList(filteredDestinations.value, (destination) => {
                _push2(ssrRenderComponent(unref(Link), {
                  key: destination.id,
                  href: route("destinations.show", destination.id),
                  class: "flex bg-white rounded-lg overflow-hidden shadow-sm transition-transform duration-300 hover:-translate-y-1 hover:shadow-md"
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`<div class="w-1/4 h-32"${_scopeId2}><img${ssrRenderAttr("src", destination.image)}${ssrRenderAttr("alt", destination.name)} class="w-full h-full object-cover"${_scopeId2}></div><div class="w-3/4 p-4 flex flex-col"${_scopeId2}><h3 class="font-bold text-gray-900 mb-1"${_scopeId2}>${ssrInterpolate(destination.name)}</h3><p class="text-gray-600 text-sm mb-2"${_scopeId2}>${ssrInterpolate(destination.region)}</p><p class="text-gray-700 text-sm mb-auto line-clamp-2"${_scopeId2}>${ssrInterpolate(destination.description)}</p><div class="flex items-center justify-between mt-2"${_scopeId2}><div class="text-sm text-gray-500"${_scopeId2}><span class="font-medium"${_scopeId2}>${ssrInterpolate(destination.hotelCount)}</span> hotels </div><div class="text-blue-600 text-sm font-medium"${_scopeId2}> Explore <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"${_scopeId2}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"${_scopeId2}></path></svg></div></div></div>`);
                    } else {
                      return [
                        createVNode("div", { class: "w-1/4 h-32" }, [
                          createVNode("img", {
                            src: destination.image,
                            alt: destination.name,
                            class: "w-full h-full object-cover"
                          }, null, 8, ["src", "alt"])
                        ]),
                        createVNode("div", { class: "w-3/4 p-4 flex flex-col" }, [
                          createVNode("h3", { class: "font-bold text-gray-900 mb-1" }, toDisplayString(destination.name), 1),
                          createVNode("p", { class: "text-gray-600 text-sm mb-2" }, toDisplayString(destination.region), 1),
                          createVNode("p", { class: "text-gray-700 text-sm mb-auto line-clamp-2" }, toDisplayString(destination.description), 1),
                          createVNode("div", { class: "flex items-center justify-between mt-2" }, [
                            createVNode("div", { class: "text-sm text-gray-500" }, [
                              createVNode("span", { class: "font-medium" }, toDisplayString(destination.hotelCount), 1),
                              createTextVNode(" hotels ")
                            ]),
                            createVNode("div", { class: "text-blue-600 text-sm font-medium" }, [
                              createTextVNode(" Explore "),
                              (openBlock(), createBlock("svg", {
                                class: "w-4 h-4 inline-block ml-1",
                                fill: "none",
                                stroke: "currentColor",
                                viewBox: "0 0 24 24"
                              }, [
                                createVNode("path", {
                                  "stroke-linecap": "round",
                                  "stroke-linejoin": "round",
                                  "stroke-width": "2",
                                  d: "M9 5l7 7-7 7"
                                })
                              ]))
                            ])
                          ])
                        ])
                      ];
                    }
                  }),
                  _: 2
                }, _parent2, _scopeId));
              });
              _push2(`<!--]--></div>`);
            }
            _push2(`<div class="flex justify-center"${_scopeId}><nav class="flex items-center"${_scopeId}><button class="px-2 py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"${ssrIncludeBooleanAttr(currentPage.value === 1) ? " disabled" : ""}${_scopeId}> Previous </button><div class="flex mx-2"${_scopeId}><!--[-->`);
            ssrRenderList(totalPages.value, (page) => {
              _push2(`<button class="${ssrRenderClass([
                "px-3 py-1 mx-1 rounded-md",
                currentPage.value === page ? "bg-blue-600 text-white" : "text-gray-700 hover:bg-gray-50 border border-gray-300"
              ])}"${_scopeId}>${ssrInterpolate(page)}</button>`);
            });
            _push2(`<!--]--></div><button class="px-2 py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"${ssrIncludeBooleanAttr(currentPage.value === totalPages.value) ? " disabled" : ""}${_scopeId}> Next </button></nav></div></div></div></div>`);
          } else {
            return [
              createVNode("div", { class: "bg-gray-50 py-10" }, [
                createVNode("div", { class: "container mx-auto px-4" }, [
                  createVNode("div", { class: "text-center mb-12" }, [
                    createVNode("h1", { class: "text-3xl font-bold text-gray-900 md:text-4xl mb-3" }, "Explore Destinations"),
                    createVNode("p", { class: "text-gray-600 max-w-2xl mx-auto" }, "Discover amazing places to stay and experience authentic Turkish hospitality. From historic cities to seaside resorts, find your perfect destination.")
                  ]),
                  createVNode("div", { class: "bg-white rounded-lg shadow-sm p-4 mb-8" }, [
                    createVNode("div", { class: "flex flex-col md:flex-row gap-4" }, [
                      createVNode("div", { class: "flex-grow" }, [
                        createVNode("div", { class: "relative" }, [
                          withDirectives(createVNode("input", {
                            type: "text",
                            class: "w-full border-gray-300 rounded-md pl-10 pr-4 py-2 focus:border-blue-500 focus:ring-blue-500",
                            placeholder: "Search destinations...",
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
                      createVNode("div", { class: "flex-shrink-0 w-full md:w-auto" }, [
                        withDirectives(createVNode("select", {
                          "onUpdate:modelValue": ($event) => regionFilter.value = $event,
                          class: "w-full border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500"
                        }, [
                          createVNode("option", { value: "all" }, "All regions"),
                          createVNode("option", { value: "aegean" }, "Aegean Coast"),
                          createVNode("option", { value: "mediterranean" }, "Mediterranean Coast"),
                          createVNode("option", { value: "blacksea" }, "Black Sea Coast"),
                          createVNode("option", { value: "central" }, "Central Anatolia"),
                          createVNode("option", { value: "marmara" }, "Marmara Region"),
                          createVNode("option", { value: "eastern" }, "Eastern Anatolia")
                        ], 8, ["onUpdate:modelValue"]), [
                          [vModelSelect, regionFilter.value]
                        ])
                      ])
                    ])
                  ]),
                  createVNode("div", { class: "mb-12" }, [
                    createVNode("h2", { class: "text-2xl font-bold text-gray-900 mb-6" }, "Featured Destinations"),
                    createVNode("div", { class: "grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" }, [
                      (openBlock(), createBlock(Fragment, null, renderList(featuredDestinations, (destination) => {
                        return createVNode("div", {
                          key: destination.id,
                          class: "relative group rounded-lg overflow-hidden shadow-sm transition-transform duration-300 hover:-translate-y-1 hover:shadow-md"
                        }, [
                          createVNode(unref(Link), {
                            href: route("destinations.show", destination.id),
                            class: "block"
                          }, {
                            default: withCtx(() => [
                              createVNode("div", { class: "aspect-w-16 aspect-h-9" }, [
                                createVNode("img", {
                                  src: destination.image,
                                  alt: destination.name,
                                  class: "w-full h-full object-cover"
                                }, null, 8, ["src", "alt"])
                              ]),
                              createVNode("div", { class: "absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent pointer-events-none" }),
                              createVNode("div", { class: "absolute bottom-0 left-0 right-0 p-4 text-white" }, [
                                createVNode("h3", { class: "text-xl font-bold mb-1" }, toDisplayString(destination.name), 1),
                                createVNode("div", { class: "flex items-center text-sm" }, [
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
                                      d: "M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                                    })
                                  ])),
                                  createTextVNode(" " + toDisplayString(destination.hotelCount) + " hotels ", 1)
                                ])
                              ]),
                              createVNode("div", { class: "absolute top-3 right-3 bg-white/90 text-blue-600 font-bold px-2 py-1 rounded text-sm" }, toDisplayString(destination.region), 1)
                            ]),
                            _: 2
                          }, 1032, ["href"])
                        ]);
                      }), 64))
                    ])
                  ]),
                  createVNode("div", null, [
                    createVNode("div", { class: "flex justify-between items-center mb-6" }, [
                      createVNode("h2", { class: "text-2xl font-bold text-gray-900" }, "All Destinations"),
                      createVNode("div", { class: "flex items-center space-x-2" }, [
                        createVNode("span", { class: "text-sm text-gray-700" }, "View:"),
                        createVNode("button", {
                          onClick: ($event) => viewMode.value = "grid",
                          class: ["p-1 rounded-md", viewMode.value === "grid" ? "bg-gray-200" : "hover:bg-gray-100"]
                        }, [
                          (openBlock(), createBlock("svg", {
                            class: "w-5 h-5 text-gray-700",
                            fill: "none",
                            stroke: "currentColor",
                            viewBox: "0 0 24 24"
                          }, [
                            createVNode("path", {
                              "stroke-linecap": "round",
                              "stroke-linejoin": "round",
                              "stroke-width": "2",
                              d: "M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                            })
                          ]))
                        ], 10, ["onClick"]),
                        createVNode("button", {
                          onClick: ($event) => viewMode.value = "list",
                          class: ["p-1 rounded-md", viewMode.value === "list" ? "bg-gray-200" : "hover:bg-gray-100"]
                        }, [
                          (openBlock(), createBlock("svg", {
                            class: "w-5 h-5 text-gray-700",
                            fill: "none",
                            stroke: "currentColor",
                            viewBox: "0 0 24 24"
                          }, [
                            createVNode("path", {
                              "stroke-linecap": "round",
                              "stroke-linejoin": "round",
                              "stroke-width": "2",
                              d: "M4 6h16M4 10h16M4 14h16M4 18h16"
                            })
                          ]))
                        ], 10, ["onClick"])
                      ])
                    ]),
                    viewMode.value === "grid" ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-8"
                    }, [
                      (openBlock(true), createBlock(Fragment, null, renderList(filteredDestinations.value, (destination) => {
                        return openBlock(), createBlock(unref(Link), {
                          key: destination.id,
                          href: route("destinations.show", destination.id),
                          class: "block bg-white rounded-lg overflow-hidden shadow-sm transition-transform duration-300 hover:-translate-y-1 hover:shadow-md"
                        }, {
                          default: withCtx(() => [
                            createVNode("div", { class: "aspect-w-3 aspect-h-2" }, [
                              createVNode("img", {
                                src: destination.image,
                                alt: destination.name,
                                class: "w-full h-full object-cover"
                              }, null, 8, ["src", "alt"])
                            ]),
                            createVNode("div", { class: "p-4" }, [
                              createVNode("h3", { class: "font-bold text-gray-900" }, toDisplayString(destination.name), 1),
                              createVNode("p", { class: "text-gray-600 text-sm mb-2" }, toDisplayString(destination.region), 1),
                              createVNode("div", { class: "flex items-center text-xs text-gray-500" }, [
                                (openBlock(), createBlock("svg", {
                                  class: "w-3 h-3 mr-1",
                                  fill: "none",
                                  stroke: "currentColor",
                                  viewBox: "0 0 24 24"
                                }, [
                                  createVNode("path", {
                                    "stroke-linecap": "round",
                                    "stroke-linejoin": "round",
                                    "stroke-width": "2",
                                    d: "M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                                  })
                                ])),
                                createTextVNode(" " + toDisplayString(destination.hotelCount) + " hotels ", 1)
                              ])
                            ])
                          ]),
                          _: 2
                        }, 1032, ["href"]);
                      }), 128))
                    ])) : (openBlock(), createBlock("div", {
                      key: 1,
                      class: "space-y-4 mb-8"
                    }, [
                      (openBlock(true), createBlock(Fragment, null, renderList(filteredDestinations.value, (destination) => {
                        return openBlock(), createBlock(unref(Link), {
                          key: destination.id,
                          href: route("destinations.show", destination.id),
                          class: "flex bg-white rounded-lg overflow-hidden shadow-sm transition-transform duration-300 hover:-translate-y-1 hover:shadow-md"
                        }, {
                          default: withCtx(() => [
                            createVNode("div", { class: "w-1/4 h-32" }, [
                              createVNode("img", {
                                src: destination.image,
                                alt: destination.name,
                                class: "w-full h-full object-cover"
                              }, null, 8, ["src", "alt"])
                            ]),
                            createVNode("div", { class: "w-3/4 p-4 flex flex-col" }, [
                              createVNode("h3", { class: "font-bold text-gray-900 mb-1" }, toDisplayString(destination.name), 1),
                              createVNode("p", { class: "text-gray-600 text-sm mb-2" }, toDisplayString(destination.region), 1),
                              createVNode("p", { class: "text-gray-700 text-sm mb-auto line-clamp-2" }, toDisplayString(destination.description), 1),
                              createVNode("div", { class: "flex items-center justify-between mt-2" }, [
                                createVNode("div", { class: "text-sm text-gray-500" }, [
                                  createVNode("span", { class: "font-medium" }, toDisplayString(destination.hotelCount), 1),
                                  createTextVNode(" hotels ")
                                ]),
                                createVNode("div", { class: "text-blue-600 text-sm font-medium" }, [
                                  createTextVNode(" Explore "),
                                  (openBlock(), createBlock("svg", {
                                    class: "w-4 h-4 inline-block ml-1",
                                    fill: "none",
                                    stroke: "currentColor",
                                    viewBox: "0 0 24 24"
                                  }, [
                                    createVNode("path", {
                                      "stroke-linecap": "round",
                                      "stroke-linejoin": "round",
                                      "stroke-width": "2",
                                      d: "M9 5l7 7-7 7"
                                    })
                                  ]))
                                ])
                              ])
                            ])
                          ]),
                          _: 2
                        }, 1032, ["href"]);
                      }), 128))
                    ])),
                    createVNode("div", { class: "flex justify-center" }, [
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Destinations.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
