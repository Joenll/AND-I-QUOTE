<template>
  <div
    v-if="visible"
    class="fixed inset-0  bg-opacity-200 flex items-center justify-center z-50"
  >
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative text-black">
      <!-- Close button -->
      <button
        @click="$emit('close')"
        class="absolute top-3 right-3 text-white hover:text-gray-700"
      >
        ✕
      </button>

      <h2 class="text-xl font-bold mb-4">Customer Details</h2>
      
      <div v-if="customer">
        <p><strong>Name:</strong> {{ customer.name }}</p>
        <p><strong>Email:</strong> {{ customer.email }}</p>
        <p><strong>Contact:</strong> {{ customer.contact }}</p>
        <p><strong>Address:</strong> {{ customer.address }}</p>
        <p><strong>Date of Birth:</strong> {{ customer.date_of_birth }}</p>
      </div>

      <div v-else>
        <p class="text-gray-500">No customer data.</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "CustomerDetailsModal",
  props: {
    visible: {
      type: Boolean,
      default: false
    },
    customer: {
      type: Object,
      default: null
    }
  }
};
</script>
