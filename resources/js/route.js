/**
 * Simple route function for managing route names in SSR context
 */
export default function route(name, params) {
  // Basic route handling for our main routes
  if (name === 'home') return '/';
  if (name === 'hotels.index') return '/hotels';
  if (name === 'hotels.show') return `/hotels/${params}`;
  if (name === 'destinations.index' || name === 'inertia.destinations.index') return '/destinations';
  if (name === 'destinations.show' || name === 'inertia.destinations.show') return `/destinations/${params}`;
  if (name === 'about') return '/about';
  if (name === 'contact') return '/contact';
  
  // Fallback
  return '/';
}