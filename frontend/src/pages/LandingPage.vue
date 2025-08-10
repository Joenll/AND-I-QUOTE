<template>
  <div class="flex flex-col items-center justify-center h-screen bg-gray-100 text-center">
    <h1 class="text-4xl font-bold text-black mb-4">Welcome to AND I QUOTE</h1>
    <p class="text-lg text-gray-700 mb-8">
      Your simple and efficient product quotation system.
    </p>
    <router-link 
      to="/customers" 
      class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition"
    >
      <span class="text-white">Get Started</span> 
    </router-link>
  </div>
</template>

<script>
export default {
  name: "LandingPage",
};
</script>
