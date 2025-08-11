<template>
  <div class="min-h-screen w-full flex flex-col">
    <!-- Landing Page -->
    <div v-if="$route.path === '/'" class="flex flex-col items-center justify-center flex-grow px-6 text-center">
      <!-- Title -->
      <h1 class="text-5xl font-extrabold text-gray-800 mb-6 tracking-tight">
        Welcome to <span class="text-blue-600">AND I QUOTE</span>
      </h1>

      <!-- Subtitle -->
      <p class="text-lg text-gray-600 max-w-md mb-10 leading-relaxed">
        Your simple and efficient product quotation system — built to make your workflow effortless.
      </p>

      <!-- Button -->
      <router-link 
        to="/customers" 
        
      >
      <span class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-blue-500 transition duration-200">
         Get Started
      </span>
      </router-link>
    </div>

    <!-- Other Pages -->
    <router-view v-else class="flex-grow" />
  </div>
</template>

<script>
export default {
  name: "App",
};
</script>
