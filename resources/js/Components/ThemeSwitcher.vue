<template>
  <div class="fixed bottom-4 right-4 z-50 bg-white shadow-lg rounded-full">
    <button 
      class="theme-toggle p-3 text-gray-700 hover:text-blue-600 focus:outline-none" 
      @click="toggleTheme"
      title="Toggle between Blade and Inertia themes"
    >
      <svg v-if="currentTheme === 'inertia'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
      </svg>
      <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
      </svg>
    </button>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const currentTheme = ref('inertia');

onMounted(() => {
  // Get the current theme from URL query parameter or session
  const urlParams = new URLSearchParams(window.location.search);
  currentTheme.value = urlParams.get('theme') || 'inertia';
});

const toggleTheme = () => {
  // Toggle the theme
  const newTheme = currentTheme.value === 'inertia' ? 'blade' : 'inertia';
  currentTheme.value = newTheme;
  
  // Redirect to the same page with the new theme
  const url = window.location.pathname;
  router.visit(url + '?theme=' + newTheme, {
    preserveState: false
  });
};
</script>

<style scoped>
.theme-toggle {
  transition: all 0.3s ease;
}

.theme-toggle:hover {
  transform: rotate(45deg);
}
</style>