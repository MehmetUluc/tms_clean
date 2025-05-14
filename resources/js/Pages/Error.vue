<template>
  <Head :title="title" />
  <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col items-center justify-center max-w-md w-full space-y-8">
      <div class="text-center">
        <h1 class="text-9xl font-extrabold text-blue-600">{{ status }}</h1>
        <h2 class="mt-4 text-3xl font-bold text-gray-900">{{ message || defaultMessage }}</h2>
        <p class="mt-3 text-lg text-gray-600">{{ description }}</p>
      </div>
      <div class="mt-8">
        <Link
          href="/"
          class="flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
        >
          Back to home
        </Link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
  status: {
    type: Number,
    required: true,
  },
  message: {
    type: String,
    default: '',
  },
});

const title = computed(() => {
  return `${props.status} | ${props.message || defaultTitle}`;
});

const defaultTitle = computed(() => {
  if (props.status === 404) return 'Page Not Found';
  if (props.status === 500) return 'Server Error';
  if (props.status === 403) return 'Forbidden';
  if (props.status === 401) return 'Unauthorized';
  return 'Error';
});

const defaultMessage = computed(() => {
  if (props.status === 404) return 'Page Not Found';
  if (props.status === 500) return 'Server Error';
  if (props.status === 403) return 'Forbidden';
  if (props.status === 401) return 'Unauthorized';
  return 'An Error Occurred';
});

const description = computed(() => {
  if (props.status === 404) return "We couldn't find the page you're looking for.";
  if (props.status === 500) return 'Sorry, something went wrong on our servers.';
  if (props.status === 403) return "You don't have permission to access this resource.";
  if (props.status === 401) return 'Please log in to access this resource.';
  return 'An unexpected error occurred. Please try again later.';
});
</script>