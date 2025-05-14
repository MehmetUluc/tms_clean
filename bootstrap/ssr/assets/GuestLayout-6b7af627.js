import { useSSRContext, ref, onMounted, mergeProps, watch, unref, withCtx, createVNode, createTextVNode, toDisplayString, openBlock, createBlock, createCommentVNode } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderList, ssrRenderClass, ssrInterpolate, ssrRenderAttr, ssrRenderStyle, ssrRenderSlot } from "vue/server-renderer";
import { Link } from "@inertiajs/vue3";
const ThemeSwitcher_vue_vue_type_style_index_0_scoped_c1273812_lang = "";
const _export_sfc = (sfc, props) => {
  const target = sfc.__vccOpts || sfc;
  for (const [key, val] of props) {
    target[key] = val;
  }
  return target;
};
const _sfc_main$3 = {
  __name: "ThemeSwitcher",
  __ssrInlineRender: true,
  setup(__props) {
    const currentTheme = ref("inertia");
    onMounted(() => {
      const urlParams = new URLSearchParams(window.location.search);
      currentTheme.value = urlParams.get("theme") || "inertia";
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "fixed bottom-4 right-4 z-50 bg-white shadow-lg rounded-full" }, _attrs))} data-v-c1273812><button class="theme-toggle p-3 text-gray-700 hover:text-blue-600 focus:outline-none" title="Toggle between Blade and Inertia themes" data-v-c1273812>`);
      if (currentTheme.value === "inertia") {
        _push(`<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" data-v-c1273812><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" data-v-c1273812></path></svg>`);
      } else {
        _push(`<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" data-v-c1273812><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" data-v-c1273812></path></svg>`);
      }
      _push(`</button></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/ThemeSwitcher.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const ThemeSwitcher = /* @__PURE__ */ _export_sfc(_sfc_main$3, [["__scopeId", "data-v-c1273812"]]);
const Header_vue_vue_type_style_index_0_scoped_aad41fef_lang = "";
const _sfc_main$2 = {
  __name: "Header",
  __ssrInlineRender: true,
  setup(__props) {
    const navigationItems = ref([]);
    const fetchMenuData = async () => {
      try {
        console.log("Using fallback menu data instead of API");
        useStaticFallbackMenu();
        return;
        if (data && data.menu && data.menu.items) {
          navigationItems.value = parseMenuItems(data.menu.items);
        } else {
          console.error("Menu data format is not as expected");
          useStaticFallbackMenu();
        }
      } catch (error) {
        console.error("Error fetching menu data:", error);
        useStaticFallbackMenu();
      }
    };
    const parseMenuItems = (items) => {
      return items.map((item) => {
        let url = item.url || "#";
        if (!url.match(/^(https?:)?\/\//) && !url.startsWith("/") && url !== "#") {
          url = "/" + url;
        }
        const parsedItem = {
          title: item.title,
          url
        };
        if (item.children && item.children.length > 0) {
          parsedItem.children = parseMenuItems(item.children);
        }
        if (item.is_mega_menu) {
          parsedItem.isMegaMenu = true;
          if (item.mega_menu_content) {
            const megaContent = typeof item.mega_menu_content === "string" ? JSON.parse(item.mega_menu_content) : item.mega_menu_content;
            parsedItem.megaMenuSections = megaContent.map((section) => {
              var _a;
              const parsedSection = {
                title: section.title || "",
                type: section.content_type || "links",
                colSpan: parseInt(((_a = section.width) == null ? void 0 : _a.replace("col-span-", "")) || "1")
              };
              if (section.content_type === "links" && section.links) {
                parsedSection.links = section.links.map((link) => {
                  let linkUrl = link.url || "#";
                  if (!linkUrl.match(/^(https?:)?\/\//) && !linkUrl.startsWith("/") && linkUrl !== "#") {
                    linkUrl = "/" + linkUrl;
                  }
                  return {
                    title: link.title,
                    url: linkUrl,
                    featured: link.featured || false
                  };
                });
              } else if (section.content_type === "featured") {
                parsedSection.title = section.featured_title || section.title;
                parsedSection.description = section.featured_description || "";
                let featuredUrl = section.featured_url || "#";
                if (!featuredUrl.match(/^(https?:)?\/\//) && !featuredUrl.startsWith("/") && featuredUrl !== "#") {
                  featuredUrl = "/" + featuredUrl;
                }
                parsedSection.url = featuredUrl;
                parsedSection.image = section.featured_image ? section.featured_image.startsWith("/") || section.featured_image.match(/^(https?:)?\/\//) ? section.featured_image : `/${section.featured_image}` : null;
              } else if (section.content_type === "html") {
                parsedSection.content = section.html_content || "";
                parsedSection.mobileContent = section.mobile_html_content || section.html_content || "";
              }
              return parsedSection;
            });
          }
        }
        return parsedItem;
      });
    };
    const useStaticFallbackMenu = () => {
      navigationItems.value = [
        {
          title: "Home",
          url: "/"
        },
        {
          title: "Hotels",
          url: "/hotels",
          isMegaMenu: true,
          megaMenuSections: [
            {
              title: "Hotel Types",
              type: "links",
              colSpan: 1,
              links: [
                { title: "Luxury Hotels", url: "/hotels?type=luxury" },
                { title: "Boutique Hotels", url: "/hotels?type=boutique" },
                { title: "Resorts", url: "/hotels?type=resort" },
                { title: "Villas", url: "/hotels?type=villa" },
                { title: "Apartments", url: "/hotels?type=apartment" }
              ]
            },
            {
              title: "Top Destinations",
              type: "links",
              colSpan: 1,
              links: [
                { title: "Istanbul Hotels", url: "/hotels?location=istanbul" },
                { title: "Antalya Hotels", url: "/hotels?location=antalya" },
                { title: "Bodrum Hotels", url: "/hotels?location=bodrum" },
                { title: "Cappadocia Hotels", url: "/hotels?location=cappadocia" },
                { title: "Izmir Hotels", url: "/hotels?location=izmir" }
              ]
            },
            {
              title: "Special Offers",
              type: "links",
              colSpan: 1,
              links: [
                { title: "Last Minute Deals", url: "/hotels?offer=last-minute", featured: true },
                { title: "Summer Specials", url: "/hotels?offer=summer" },
                { title: "Weekend Getaways", url: "/hotels?offer=weekend" },
                { title: "Family Packages", url: "/hotels?offer=family" },
                { title: "Honeymoon Suites", url: "/hotels?offer=honeymoon" }
              ]
            },
            {
              title: "Featured Hotel",
              type: "featured",
              colSpan: 1,
              title: "Grand Resort & Spa",
              description: "Luxury beachfront resort with spectacular views of the Mediterranean Sea",
              url: "/hotels/grand-resort-spa",
              image: "/images/hotels/hotel1.jpg"
            }
          ]
        },
        {
          title: "Destinations",
          url: "/destinations",
          isMegaMenu: true,
          megaMenuSections: [
            {
              title: "Regions",
              type: "links",
              colSpan: 1,
              links: [
                { title: "Aegean Coast", url: "/destinations?region=aegean" },
                { title: "Mediterranean Coast", url: "/destinations?region=mediterranean" },
                { title: "Black Sea Coast", url: "/destinations?region=blacksea" },
                { title: "Marmara Region", url: "/destinations?region=marmara" },
                { title: "Central Anatolia", url: "/destinations?region=central" }
              ]
            },
            {
              title: "Popular Cities",
              type: "links",
              colSpan: 1,
              links: [
                { title: "Istanbul", url: "/destinations/istanbul" },
                { title: "Antalya", url: "/destinations/antalya" },
                { title: "Bodrum", url: "/destinations/bodrum" },
                { title: "Izmir", url: "/destinations/izmir" },
                { title: "Cappadocia", url: "/destinations/cappadocia" }
              ]
            },
            {
              title: "Explore By Type",
              type: "links",
              colSpan: 1,
              links: [
                { title: "Beach Destinations", url: "/destinations?feature=beach" },
                { title: "Historical Sites", url: "/destinations?feature=historical" },
                { title: "Natural Wonders", url: "/destinations?feature=natural" },
                { title: "Cultural Experiences", url: "/destinations?feature=cultural" },
                { title: "Adventure Destinations", url: "/destinations?feature=adventure" }
              ]
            },
            {
              title: "Featured Destination",
              type: "featured",
              colSpan: 1,
              title: "Magical Cappadocia",
              description: "Experience the unique landscape of fairy chimneys and hot air balloon rides",
              url: "/destinations/cappadocia",
              image: "/images/destinations/cappadocia.jpg"
            }
          ]
        },
        {
          title: "About",
          url: "/about",
          children: [
            { title: "Our Story", url: "/about/story" },
            { title: "Our Team", url: "/about/team" },
            { title: "Careers", url: "/about/careers" }
          ]
        },
        {
          title: "Contact",
          url: "/contact"
        }
      ];
    };
    const mobileMenuOpen = ref(false);
    const openMobileSubmenus = ref([]);
    watch(() => window.innerWidth, (width) => {
      if (width >= 1024 && mobileMenuOpen.value) {
        mobileMenuOpen.value = false;
        openMobileSubmenus.value = [];
      }
    });
    onMounted(() => {
      fetchMenuData();
      document.addEventListener("click", (event) => {
        const header = document.querySelector("header");
        if (header && !header.contains(event.target) && mobileMenuOpen.value) {
          mobileMenuOpen.value = false;
          openMobileSubmenus.value = [];
        }
      });
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<header${ssrRenderAttrs(mergeProps({ class: "relative z-50" }, _attrs))} data-v-aad41fef><div class="bg-gradient-to-r from-blue-700 to-indigo-800 text-white py-2 px-6" data-v-aad41fef><div class="container mx-auto flex justify-between items-center" data-v-aad41fef><div class="flex items-center text-sm space-x-6" data-v-aad41fef><div class="flex items-center" data-v-aad41fef><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" data-v-aad41fef><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" data-v-aad41fef></path></svg><span data-v-aad41fef>+90 212 555 1234</span></div><div class="flex items-center" data-v-aad41fef><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" data-v-aad41fef><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" data-v-aad41fef></path></svg><span data-v-aad41fef>info@travelmanager.com</span></div></div><div class="flex items-center space-x-4" data-v-aad41fef><div class="flex space-x-2" data-v-aad41fef><a href="#" class="hover:text-blue-200 transition-colors" aria-label="Facebook" data-v-aad41fef><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" data-v-aad41fef><path d="M22.675 0H1.325C.593 0 0 .593 0 1.325v21.351C0 23.407.593 24 1.325 24H12.82v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116c.73 0 1.323-.593 1.323-1.325V1.325C24 .593 23.407 0 22.675 0z" data-v-aad41fef></path></svg></a><a href="#" class="hover:text-blue-200 transition-colors" aria-label="Twitter" data-v-aad41fef><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" data-v-aad41fef><path d="M23.954 4.569c-.885.389-1.83.654-2.825.775 1.014-.611 1.794-1.574 2.163-2.723-.951.555-2.005.959-3.127 1.184-.896-.959-2.173-1.559-3.591-1.559-2.717 0-4.92 2.203-4.92 4.917 0 .39.045.765.127 1.124C7.691 8.094 4.066 6.13 1.64 3.161c-.427.722-.666 1.561-.666 2.475 0 1.71.87 3.213 2.188 4.096-.807-.026-1.566-.248-2.228-.616v.061c0 2.385 1.693 4.374 3.946 4.827-.413.111-.849.171-1.296.171-.314 0-.615-.03-.916-.086.631 1.953 2.445 3.377 4.604 3.417-1.68 1.319-3.809 2.105-6.102 2.105-.39 0-.779-.023-1.17-.067 2.189 1.394 4.768 2.209 7.557 2.209 9.054 0 14-7.503 14-14.001 0-.21-.005-.429-.015-.636.961-.689 1.8-1.561 2.457-2.549l-.001-.001z" data-v-aad41fef></path></svg></a><a href="#" class="hover:text-blue-200 transition-colors" aria-label="Instagram" data-v-aad41fef><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" data-v-aad41fef><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" data-v-aad41fef></path></svg></a></div><div class="text-sm border-l border-blue-500 pl-4" data-v-aad41fef><a href="/login" class="hover:text-blue-200 transition-colors mr-3" data-v-aad41fef>Login</a><a href="/register" class="bg-white text-blue-700 px-3 py-1 rounded-full hover:bg-blue-50 transition-colors" data-v-aad41fef>Sign Up</a></div></div></div></div><div class="bg-white shadow" data-v-aad41fef><div class="container mx-auto px-6 relative" data-v-aad41fef><nav class="flex justify-between items-center py-4" data-v-aad41fef><div class="flex-shrink-0" data-v-aad41fef>`);
      _push(ssrRenderComponent(unref(Link), {
        href: "/",
        class: "flex items-center"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<img src="/images/logo-dark.svg" alt="Travel Manager" class="h-10 w-auto mr-2" data-v-aad41fef${_scopeId}><span class="sr-only" data-v-aad41fef${_scopeId}>Travel Manager</span>`);
          } else {
            return [
              createVNode("img", {
                src: "/images/logo-dark.svg",
                alt: "Travel Manager",
                class: "h-10 w-auto mr-2"
              }),
              createVNode("span", { class: "sr-only" }, "Travel Manager")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div><div class="hidden lg:flex items-center justify-center space-x-1 flex-1 mx-8" data-v-aad41fef><!--[-->`);
      ssrRenderList(navigationItems.value, (item, index) => {
        var _a;
        _push(`<div class="${ssrRenderClass([{ "mega-menu-parent": item.isMegaMenu }, "relative group"])}" data-v-aad41fef>`);
        _push(ssrRenderComponent(unref(Link), {
          href: item.url,
          class: "block px-4 py-3 text-gray-700 font-medium rounded-md hover:bg-gray-50 hover:text-blue-600 transition-colors"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            var _a2, _b;
            if (_push2) {
              _push2(`${ssrInterpolate(item.title)} `);
              if (((_a2 = item.children) == null ? void 0 : _a2.length) || item.isMegaMenu) {
                _push2(`<svg class="w-4 h-4 ml-1 inline-block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" data-v-aad41fef${_scopeId}><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" data-v-aad41fef${_scopeId}></path></svg>`);
              } else {
                _push2(`<!---->`);
              }
            } else {
              return [
                createTextVNode(toDisplayString(item.title) + " ", 1),
                ((_b = item.children) == null ? void 0 : _b.length) || item.isMegaMenu ? (openBlock(), createBlock("svg", {
                  key: 0,
                  class: "w-4 h-4 ml-1 inline-block",
                  xmlns: "http://www.w3.org/2000/svg",
                  viewBox: "0 0 20 20",
                  fill: "currentColor"
                }, [
                  createVNode("path", {
                    "fill-rule": "evenodd",
                    d: "M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z",
                    "clip-rule": "evenodd"
                  })
                ])) : createCommentVNode("", true)
              ];
            }
          }),
          _: 2
        }, _parent));
        if (item.isMegaMenu) {
          _push(`<div class="${ssrRenderClass([item.megaMenuClasses || "", "mega-menu absolute left-0 transform translate-y-2 w-full bg-white shadow-xl border-t border-gray-100 py-8 px-6 hidden group-hover:block transition-all duration-300 z-50 rounded-b-lg"])}" data-v-aad41fef><div class="grid grid-cols-4 gap-8" data-v-aad41fef><!--[-->`);
          ssrRenderList(item.megaMenuSections, (section, sIdx) => {
            _push(`<div class="${ssrRenderClass(section.colSpan ? `col-span-${section.colSpan}` : "")}" data-v-aad41fef><h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-3" data-v-aad41fef>${ssrInterpolate(section.title)}</h3>`);
            if (section.type === "links") {
              _push(`<ul class="space-y-2" data-v-aad41fef><!--[-->`);
              ssrRenderList(section.links, (link, lIdx) => {
                _push(`<li data-v-aad41fef>`);
                _push(ssrRenderComponent(unref(Link), {
                  href: link.url,
                  class: ["text-gray-700 hover:text-blue-600 transition-colors", { "font-semibold text-blue-600": link.featured }]
                }, {
                  default: withCtx((_, _push2, _parent2, _scopeId) => {
                    if (_push2) {
                      _push2(`${ssrInterpolate(link.title)}`);
                    } else {
                      return [
                        createTextVNode(toDisplayString(link.title), 1)
                      ];
                    }
                  }),
                  _: 2
                }, _parent));
                _push(`</li>`);
              });
              _push(`<!--]--></ul>`);
            } else if (section.type === "featured") {
              _push(`<div class="bg-gray-50 rounded-lg p-4" data-v-aad41fef>`);
              if (section.image) {
                _push(`<div class="mb-3 rounded overflow-hidden" data-v-aad41fef><img${ssrRenderAttr("src", section.image)}${ssrRenderAttr("alt", section.title)} class="w-full h-32 object-cover" data-v-aad41fef></div>`);
              } else {
                _push(`<!---->`);
              }
              _push(`<h4 class="font-bold text-gray-800 mb-1" data-v-aad41fef>${ssrInterpolate(section.title)}</h4>`);
              if (section.description) {
                _push(`<p class="text-sm text-gray-600 mb-2" data-v-aad41fef>${ssrInterpolate(section.description)}</p>`);
              } else {
                _push(`<!---->`);
              }
              if (section.url) {
                _push(ssrRenderComponent(unref(Link), {
                  href: section.url,
                  class: "text-sm font-medium text-blue-600 hover:underline"
                }, {
                  default: withCtx((_, _push2, _parent2, _scopeId) => {
                    if (_push2) {
                      _push2(` Learn More <span aria-hidden="true" data-v-aad41fef${_scopeId}>→</span>`);
                    } else {
                      return [
                        createTextVNode(" Learn More "),
                        createVNode("span", { "aria-hidden": "true" }, "→")
                      ];
                    }
                  }),
                  _: 2
                }, _parent));
              } else {
                _push(`<!---->`);
              }
              _push(`</div>`);
            } else if (section.type === "html") {
              _push(`<div data-v-aad41fef>${section.content ?? ""}</div>`);
            } else {
              _push(`<!---->`);
            }
            _push(`</div>`);
          });
          _push(`<!--]--></div></div>`);
        } else if ((_a = item.children) == null ? void 0 : _a.length) {
          _push(`<div class="absolute z-50 mt-1 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden group-hover:block transition-all duration-300" data-v-aad41fef><div class="py-1" role="menu" aria-orientation="vertical" data-v-aad41fef><!--[-->`);
          ssrRenderList(item.children, (child, cIdx) => {
            _push(ssrRenderComponent(unref(Link), {
              key: cIdx,
              href: child.url,
              class: "block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-600 transition-colors",
              role: "menuitem"
            }, {
              default: withCtx((_, _push2, _parent2, _scopeId) => {
                if (_push2) {
                  _push2(`${ssrInterpolate(child.title)}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(child.title), 1)
                  ];
                }
              }),
              _: 2
            }, _parent));
          });
          _push(`<!--]--></div></div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div>`);
      });
      _push(`<!--]--></div><div class="hidden lg:flex items-center space-x-4" data-v-aad41fef><a href="/search" class="flex items-center p-2 rounded hover:bg-gray-100 transition-colors" title="Search" data-v-aad41fef><svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" data-v-aad41fef><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" data-v-aad41fef></path></svg></a>`);
      _push(ssrRenderComponent(unref(Link), {
        href: "/hotels",
        class: "bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded transition-colors"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(` Book Now `);
          } else {
            return [
              createTextVNode(" Book Now ")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div><div class="lg:hidden" data-v-aad41fef><button class="p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none" data-v-aad41fef>`);
      if (!mobileMenuOpen.value) {
        _push(`<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" data-v-aad41fef><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" data-v-aad41fef></path></svg>`);
      } else {
        _push(`<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" data-v-aad41fef><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" data-v-aad41fef></path></svg>`);
      }
      _push(`</button></div></nav></div></div><div style="${ssrRenderStyle(mobileMenuOpen.value ? null : { display: "none" })}" class="lg:hidden" data-v-aad41fef><div class="bg-white shadow-xl border-t border-gray-100 py-2 px-4 space-y-1" data-v-aad41fef><!--[-->`);
      ssrRenderList(navigationItems.value, (item, index) => {
        var _a, _b;
        _push(`<div class="py-1" data-v-aad41fef><div class="flex items-center justify-between py-2 px-3 rounded-md hover:bg-gray-50 hover:text-blue-600" data-v-aad41fef>`);
        _push(ssrRenderComponent(unref(Link), {
          href: item.url,
          class: "flex-1 font-medium"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`${ssrInterpolate(item.title)}`);
            } else {
              return [
                createTextVNode(toDisplayString(item.title), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
        if (((_a = item.children) == null ? void 0 : _a.length) || item.isMegaMenu) {
          _push(`<button class="p-1" data-v-aad41fef><svg class="${ssrRenderClass([{ "transform rotate-180": openMobileSubmenus.value.includes(index) }, "w-5 h-5 text-gray-500"])}" fill="none" viewBox="0 0 24 24" stroke="currentColor" data-v-aad41fef><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" data-v-aad41fef></path></svg></button>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div>`);
        if ((item.isMegaMenu || ((_b = item.children) == null ? void 0 : _b.length)) && openMobileSubmenus.value.includes(index)) {
          _push(`<div class="pl-4 py-2 border-l-2 border-gray-100 ml-4 space-y-2" data-v-aad41fef>`);
          if (item.isMegaMenu) {
            _push(`<!--[-->`);
            ssrRenderList(item.megaMenuSections, (section, sIdx) => {
              _push(`<div class="mb-4" data-v-aad41fef><h3 class="font-bold text-sm text-gray-500 uppercase tracking-wide mb-2" data-v-aad41fef>${ssrInterpolate(section.title)}</h3>`);
              if (section.type === "links") {
                _push(`<ul class="space-y-2" data-v-aad41fef><!--[-->`);
                ssrRenderList(section.links, (link, lIdx) => {
                  _push(`<li data-v-aad41fef>`);
                  _push(ssrRenderComponent(unref(Link), {
                    href: link.url,
                    class: ["block py-1 text-gray-700 hover:text-blue-600", { "font-medium text-blue-600": link.featured }]
                  }, {
                    default: withCtx((_, _push2, _parent2, _scopeId) => {
                      if (_push2) {
                        _push2(`${ssrInterpolate(link.title)}`);
                      } else {
                        return [
                          createTextVNode(toDisplayString(link.title), 1)
                        ];
                      }
                    }),
                    _: 2
                  }, _parent));
                  _push(`</li>`);
                });
                _push(`<!--]--></ul>`);
              } else if (section.type === "featured") {
                _push(`<div data-v-aad41fef>`);
                _push(ssrRenderComponent(unref(Link), {
                  href: section.url,
                  class: "flex items-center py-1 text-blue-600 font-medium"
                }, {
                  default: withCtx((_, _push2, _parent2, _scopeId) => {
                    if (_push2) {
                      _push2(`${ssrInterpolate(section.title)} <span class="ml-1" data-v-aad41fef${_scopeId}>→</span>`);
                    } else {
                      return [
                        createTextVNode(toDisplayString(section.title) + " ", 1),
                        createVNode("span", { class: "ml-1" }, "→")
                      ];
                    }
                  }),
                  _: 2
                }, _parent));
                _push(`</div>`);
              } else if (section.type === "html" && section.mobileContent) {
                _push(`<div data-v-aad41fef>${section.mobileContent ?? ""}</div>`);
              } else {
                _push(`<!---->`);
              }
              _push(`</div>`);
            });
            _push(`<!--]-->`);
          } else {
            _push(`<!--[-->`);
            ssrRenderList(item.children, (child, cIdx) => {
              _push(ssrRenderComponent(unref(Link), {
                key: cIdx,
                href: child.url,
                class: "block py-2 text-gray-700 hover:text-blue-600"
              }, {
                default: withCtx((_, _push2, _parent2, _scopeId) => {
                  if (_push2) {
                    _push2(`${ssrInterpolate(child.title)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(child.title), 1)
                    ];
                  }
                }),
                _: 2
              }, _parent));
            });
            _push(`<!--]-->`);
          }
          _push(`</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div>`);
      });
      _push(`<!--]--><div class="pt-2 pb-3 border-t border-gray-100 mt-2" data-v-aad41fef><div class="flex items-center justify-between" data-v-aad41fef><a href="/search" class="flex items-center px-3 py-2 text-gray-700 hover:text-blue-600" data-v-aad41fef><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" data-v-aad41fef><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" data-v-aad41fef></path></svg> Search </a>`);
      _push(ssrRenderComponent(unref(Link), {
        href: "/hotels",
        class: "bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded transition-colors"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(` Book Now `);
          } else {
            return [
              createTextVNode(" Book Now ")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div></div></div></div></header>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Header.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const Header = /* @__PURE__ */ _export_sfc(_sfc_main$2, [["__scopeId", "data-v-aad41fef"]]);
const Footer_vue_vue_type_style_index_0_scoped_b9b468c1_lang = "";
const _sfc_main$1 = {
  __name: "Footer",
  __ssrInlineRender: true,
  setup(__props) {
    const quickLinks = ref([
      { title: "Home", url: "/" },
      { title: "About Us", url: "/about" },
      { title: "Hotels", url: "/hotels" },
      { title: "Special Offers", url: "/special-offers" },
      { title: "Contact Us", url: "/contact" }
    ]);
    const destinations = ref([
      { title: "Istanbul", url: "/destinations/istanbul" },
      { title: "Antalya", url: "/destinations/antalya" },
      { title: "Bodrum", url: "/destinations/bodrum" },
      { title: "Cappadocia", url: "/destinations/cappadocia" },
      { title: "Izmir", url: "/destinations/izmir" }
    ]);
    const resources = ref([
      { title: "Travel Tips", url: "/travel-tips" },
      { title: "FAQs", url: "/faq" },
      { title: "Travel Blog", url: "/blog" },
      { title: "Booking Guide", url: "/booking-guide" },
      { title: "Payment Options", url: "/payment-options" }
    ]);
    const legalLinks = ref([
      { title: "Privacy Policy", url: "/privacy" },
      { title: "Terms of Service", url: "/terms" },
      { title: "Cookie Policy", url: "/cookies" },
      { title: "Refund Policy", url: "/refunds" }
    ]);
    const paymentMethods = ref([
      { name: "Visa", icon: "/images/payment/visa.svg" },
      { name: "Mastercard", icon: "/images/payment/mastercard.svg" },
      { name: "PayPal", icon: "/images/payment/paypal.svg" },
      { name: "Apple Pay", icon: "/images/payment/apple-pay.svg" }
    ]);
    const fetchFooterData = async () => {
      try {
        console.log("Using static footer data instead of API");
        return;
        if (data.success && data.menu && data.menu.items) {
          const footerMenus = data.menu.items;
          footerMenus.forEach((menu) => {
            if (menu.title.toLowerCase().includes("quick") || menu.slug === "quick-links") {
              quickLinks.value = parseMenuLinks(menu.children || []);
            } else if (menu.title.toLowerCase().includes("destination")) {
              destinations.value = parseMenuLinks(menu.children || []);
            } else if (menu.title.toLowerCase().includes("resource")) {
              resources.value = parseMenuLinks(menu.children || []);
            } else if (menu.title.toLowerCase().includes("legal")) {
              legalLinks.value = parseMenuLinks(menu.children || []);
            }
          });
        }
      } catch (error) {
        console.error("Error fetching footer menu data:", error);
      }
    };
    const parseMenuLinks = (items) => {
      return items.map((item) => ({
        title: item.title,
        url: safeUrl(item.url || item.link || "#")
      }));
    };
    const safeUrl = (url) => {
      if (!url || url === "#")
        return "#";
      if (!url.match(/^(https?:)?\/\//) && !url.startsWith("/")) {
        return "/" + url;
      }
      return url;
    };
    onMounted(() => {
      fetchFooterData();
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<footer${ssrRenderAttrs(mergeProps({ class: "bg-gradient-to-br from-gray-900 to-gray-800 text-white" }, _attrs))} data-v-b9b468c1><div class="bg-blue-700 py-12" data-v-b9b468c1><div class="container mx-auto px-6 lg:px-8" data-v-b9b468c1><div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8" data-v-b9b468c1><div class="max-w-lg" data-v-b9b468c1><h3 class="text-2xl font-bold text-white mb-2" data-v-b9b468c1>Subscribe to Our Newsletter</h3><p class="text-blue-100" data-v-b9b468c1>Get exclusive offers, travel tips and updates directly to your inbox.</p></div><div class="w-full lg:w-auto max-w-md" data-v-b9b468c1><div class="flex flex-col sm:flex-row gap-2 sm:gap-0" data-v-b9b468c1><input type="email" placeholder="Your email address" class="px-4 py-3 bg-white border-2 border-white focus:border-blue-300 focus:ring-blue-300 focus:outline-none rounded-l-md w-full sm:w-64" data-v-b9b468c1><button class="px-5 py-3 bg-blue-900 text-white font-semibold rounded-r-md hover:bg-blue-800 transition-colors" data-v-b9b468c1> Subscribe </button></div><p class="mt-2 text-xs text-blue-200" data-v-b9b468c1>By subscribing, you agree to our Privacy Policy and consent to receive updates.</p></div></div></div></div><div class="container mx-auto px-6 py-12 lg:py-16" data-v-b9b468c1><div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-12 mb-12" data-v-b9b468c1><div class="lg:col-span-2" data-v-b9b468c1><div class="flex items-center mb-6" data-v-b9b468c1><img src="/images/logo-light.svg" alt="TravelManager" class="h-10 w-auto" data-v-b9b468c1></div><p class="text-gray-300 mb-4 max-w-md" data-v-b9b468c1> TravelManager provides exceptional hotel booking experiences with a wide selection of accommodations, competitive pricing, and seamless booking processes tailored to your travel needs. </p><div class="flex space-x-4 mb-8" data-v-b9b468c1><a href="#" class="text-gray-300 hover:text-white transition-colors" aria-label="Facebook" data-v-b9b468c1><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" data-v-b9b468c1><path d="M22.675 0H1.325C.593 0 0 .593 0 1.325v21.351C0 23.407.593 24 1.325 24H12.82v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116c.73 0 1.323-.593 1.323-1.325V1.325C24 .593 23.407 0 22.675 0z" data-v-b9b468c1></path></svg></a><a href="#" class="text-gray-300 hover:text-white transition-colors" aria-label="Twitter" data-v-b9b468c1><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" data-v-b9b468c1><path d="M23.954 4.569c-.885.389-1.83.654-2.825.775 1.014-.611 1.794-1.574 2.163-2.723-.951.555-2.005.959-3.127 1.184-.896-.959-2.173-1.559-3.591-1.559-2.717 0-4.92 2.203-4.92 4.917 0 .39.045.765.127 1.124C7.691 8.094 4.066 6.13 1.64 3.161c-.427.722-.666 1.561-.666 2.475 0 1.71.87 3.213 2.188 4.096-.807-.026-1.566-.248-2.228-.616v.061c0 2.385 1.693 4.374 3.946 4.827-.413.111-.849.171-1.296.171-.314 0-.615-.03-.916-.086.631 1.953 2.445 3.377 4.604 3.417-1.68 1.319-3.809 2.105-6.102 2.105-.39 0-.779-.023-1.17-.067 2.189 1.394 4.768 2.209 7.557 2.209 9.054 0 14-7.503 14-14.001 0-.21-.005-.429-.015-.636.961-.689 1.8-1.561 2.457-2.549l-.001-.001z" data-v-b9b468c1></path></svg></a><a href="#" class="text-gray-300 hover:text-white transition-colors" aria-label="Instagram" data-v-b9b468c1><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" data-v-b9b468c1><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" data-v-b9b468c1></path></svg></a><a href="#" class="text-gray-300 hover:text-white transition-colors" aria-label="YouTube" data-v-b9b468c1><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" data-v-b9b468c1><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" data-v-b9b468c1></path></svg></a></div><div data-v-b9b468c1><h4 class="text-white font-semibold mb-4" data-v-b9b468c1>Contact Us</h4><div class="flex items-start space-x-3 mb-2" data-v-b9b468c1><svg class="w-5 h-5 text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" data-v-b9b468c1><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" data-v-b9b468c1></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" data-v-b9b468c1></path></svg><span class="text-gray-300" data-v-b9b468c1>123 Travel Street, Destination City, 34567</span></div><div class="flex items-start space-x-3 mb-2" data-v-b9b468c1><svg class="w-5 h-5 text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" data-v-b9b468c1><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" data-v-b9b468c1></path></svg><span class="text-gray-300" data-v-b9b468c1>+90 212 555 1234</span></div><div class="flex items-start space-x-3" data-v-b9b468c1><svg class="w-5 h-5 text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" data-v-b9b468c1><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" data-v-b9b468c1></path></svg><span class="text-gray-300" data-v-b9b468c1>info@travelmanager.com</span></div></div></div><div data-v-b9b468c1><h4 class="text-white text-lg font-semibold mb-6" data-v-b9b468c1>Quick Links</h4><ul class="space-y-4" data-v-b9b468c1><!--[-->`);
      ssrRenderList(quickLinks.value, (link, index) => {
        _push(`<li data-v-b9b468c1>`);
        _push(ssrRenderComponent(unref(Link), {
          href: link.url,
          class: "text-gray-300 hover:text-white transition-colors"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`${ssrInterpolate(link.title)}`);
            } else {
              return [
                createTextVNode(toDisplayString(link.title), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`</li>`);
      });
      _push(`<!--]--></ul></div><div data-v-b9b468c1><h4 class="text-white text-lg font-semibold mb-6" data-v-b9b468c1>Popular Destinations</h4><ul class="space-y-4" data-v-b9b468c1><!--[-->`);
      ssrRenderList(destinations.value, (destination, index) => {
        _push(`<li data-v-b9b468c1>`);
        _push(ssrRenderComponent(unref(Link), {
          href: destination.url,
          class: "text-gray-300 hover:text-white transition-colors"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`${ssrInterpolate(destination.title)}`);
            } else {
              return [
                createTextVNode(toDisplayString(destination.title), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`</li>`);
      });
      _push(`<!--]--></ul></div><div data-v-b9b468c1><h4 class="text-white text-lg font-semibold mb-6" data-v-b9b468c1>Travel Resources</h4><ul class="space-y-4" data-v-b9b468c1><!--[-->`);
      ssrRenderList(resources.value, (resource, index) => {
        _push(`<li data-v-b9b468c1>`);
        _push(ssrRenderComponent(unref(Link), {
          href: resource.url,
          class: "text-gray-300 hover:text-white transition-colors"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`${ssrInterpolate(resource.title)}`);
            } else {
              return [
                createTextVNode(toDisplayString(resource.title), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`</li>`);
      });
      _push(`<!--]--></ul></div></div><div class="pt-8 mt-8 border-t border-gray-700" data-v-b9b468c1><div class="flex flex-col md:flex-row md:justify-between items-center" data-v-b9b468c1><div class="mb-6 md:mb-0" data-v-b9b468c1><p class="text-gray-400 text-sm" data-v-b9b468c1>© ${ssrInterpolate((/* @__PURE__ */ new Date()).getFullYear())} TravelManager. All rights reserved.</p></div><div class="flex space-x-6" data-v-b9b468c1><!--[-->`);
      ssrRenderList(legalLinks.value, (legal, index) => {
        _push(ssrRenderComponent(unref(Link), {
          key: `legal-${index}`,
          href: legal.url,
          class: "text-sm text-gray-400 hover:text-white"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`${ssrInterpolate(legal.title)}`);
            } else {
              return [
                createTextVNode(toDisplayString(legal.title), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
      });
      _push(`<!--]--></div></div></div></div><div class="bg-black py-4" data-v-b9b468c1><div class="container mx-auto px-6 flex flex-col sm:flex-row justify-between items-center" data-v-b9b468c1><div class="text-xs text-gray-400 mb-4 sm:mb-0" data-v-b9b468c1> Designed and developed with ❤️ by TravelManager Team </div><div class="flex items-center" data-v-b9b468c1><div class="text-xs text-gray-400 mr-4" data-v-b9b468c1>Payment Methods:</div><div class="flex space-x-3" data-v-b9b468c1><!--[-->`);
      ssrRenderList(paymentMethods.value, (method, index) => {
        _push(`<span class="w-8 h-5 bg-white rounded flex items-center justify-center" data-v-b9b468c1><img${ssrRenderAttr("src", method.icon)}${ssrRenderAttr("alt", method.name)} class="h-4 w-auto" data-v-b9b468c1></span>`);
      });
      _push(`<!--]--></div></div></div></div></footer>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Footer.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const Footer = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["__scopeId", "data-v-b9b468c1"]]);
const GuestLayout_vue_vue_type_style_index_0_scoped_12a10aae_lang = "";
const _sfc_main = {
  __name: "GuestLayout",
  __ssrInlineRender: true,
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "min-h-screen flex flex-col" }, _attrs))} data-v-12a10aae>`);
      _push(ssrRenderComponent(Header, null, null, _parent));
      _push(`<main class="flex-grow" data-v-12a10aae>`);
      ssrRenderSlot(_ctx.$slots, "default", {}, null, _push, _parent);
      _push(`</main>`);
      _push(ssrRenderComponent(Footer, null, null, _parent));
      _push(ssrRenderComponent(ThemeSwitcher, null, null, _parent));
      _push(`</div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Layouts/GuestLayout.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const GuestLayout = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-12a10aae"]]);
export {
  GuestLayout as G,
  _export_sfc as _
};
