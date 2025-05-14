import { createSSRApp, h } from "vue";
import { renderToString } from "@vue/server-renderer";
import { createInertiaApp } from "@inertiajs/vue3";
import inertiaServer from "@inertiajs/server";
async function resolvePageComponent(path, pages) {
  const page = pages[path];
  if (typeof page === "undefined") {
    throw new Error(`Page not found: ${path}`);
  }
  return typeof page === "function" ? page() : page;
}
function route(name, params) {
  if (name === "home")
    return "/";
  if (name === "hotels.index")
    return "/hotels";
  if (name === "hotels.show")
    return `/hotels/${params}`;
  if (name === "destinations.index")
    return "/destinations";
  if (name === "destinations.show")
    return `/destinations/${params}`;
  if (name === "about")
    return "/about";
  if (name === "contact")
    return "/contact";
  return "/";
}
const { server } = inertiaServer;
server(
  (page) => createInertiaApp({
    page,
    render: renderToString,
    title: (title) => `${title} - TravelManager`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, /* @__PURE__ */ Object.assign({ "./Pages/Destinations.vue": () => import("./assets/Destinations-2792a3b4.js"), "./Pages/Error.vue": () => import("./assets/Error-876e88d8.js"), "./Pages/Home.vue": () => import("./assets/Home-8414f358.js"), "./Pages/HotelDetail.vue": () => import("./assets/HotelDetail-6c191697.js"), "./Pages/Hotels.vue": () => import("./assets/Hotels-5b6d34be.js") })),
    setup({ App, props, plugin }) {
      global.route = route;
      return createSSRApp({ render: () => h(App, props) }).use(plugin);
    }
  })
);
