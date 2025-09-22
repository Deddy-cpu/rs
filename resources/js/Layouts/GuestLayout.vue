<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
  <div
    class="flex min-h-screen flex-col items-center justify-center bg-cover bg-center"
    style="background-image: url('/images/bg-login.png')"
  >
    <!-- Logo RS -->
    <div class="mb-6">
      <Link href="/">
        <ApplicationLogo />
      </Link>
    </div>

    <!-- Card Login -->
    <div
      class="w-full overflow-hidden bg-white/90 px-6 py-6 shadow-lg sm:max-w-md sm:rounded-2xl"
    >
      <slot />
    </div>
  </div>
</template>
