<template>
    <div class="min-h-screen flex flex-col">
        <Header />

        <!-- Main Content -->
        <main class="flex-grow">
            <slot />
        </main>

        <!-- Footer -->
        <Footer />

        <!-- Theme Switcher -->
        <ThemeSwitcher />
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import ThemeSwitcher from '@/Components/ThemeSwitcher.vue';
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
</script>

<style scoped>
/* Additional styling can be added here */
</style>