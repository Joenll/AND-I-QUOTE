<script>
import axios from "axios";

export default {
  name: "EditCustomer",
  data() {
    return {
      customer: {
        name: "",
        email: "",
        contact: "",
        address: "",
        dob: "",
      },
      loading: true,
      error: null,
      saving: false,
    };
  },
  async created() {
    await this.fetchCustomer();
  },
  methods: {
    async fetchCustomer() {
      try {
        const res = await axios.get(
          `http://localhost:8000/api/v1/customers/${this.$route.params.id}`
        );
        this.customer = res.data.customer;
      } catch (err) {
        console.error(err);
        this.error = "Failed to load customer details.";
      } finally {
        this.loading = false;
      }
    },
    async updateCustomer() {
      this.saving = true;
      this.error = null;
      try {
        await axios.put(
          `http://localhost:8000/api/v1/customers/${this.$route.params.id}`,
          this.customer
        );
        alert("Customer updated successfully!");
        this.$router.push(`/customer/${this.$route.params.id}`);
      } catch (err) {
        console.error(err);
        this.error = "Failed to update customer.";
      } finally {
        this.saving = false;
      }
    },
  },
};
</script>

<template>
  <div class="p-8 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto">
      <!-- Back Button -->
      <div class="mb-6">
        <router-link
          :to="`/customer/${$route.params.id}`"
          class="text-blue-600 hover:underline text-sm"
        >
          ← Back to Customer Details
        </router-link>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-gray-500 text-lg">Loading...</div>

      <!-- Error -->
      <div v-if="error" class="text-red-600 text-lg">{{ error }}</div>

      <!-- Form -->
      <div
        v-if="!loading"
        class="bg-white p-6 rounded-xl shadow-md border border-gray-200"
      >
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Customer</h1>

        <form @submit.prevent="updateCustomer" class="space-y-5">
          <!-- Name -->
          <div>
            <label class="block text-gray-700 font-medium mb-2">Name</label>
            <input
              v-model="customer.name"
              type="text"
              placeholder="Enter full name"
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring focus:ring-blue-200 focus:outline-none text-black placeholder-gray-400"
              required
            />
          </div>

          <!-- Email -->
          <div>
            <label class="block text-gray-700 font-medium mb-2">Email</label>
            <input
              v-model="customer.email"
              type="email"
              placeholder="Enter email address"
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring focus:ring-blue-200 focus:outline-none text-black placeholder-gray-400"
              required
            />
          </div>

          <!-- Contact -->
          <div>
            <label class="block text-gray-700 font-medium mb-2">Contact</label>
            <input
              v-model="customer.contact"
              type="text"
              placeholder="Enter contact number"
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring focus:ring-blue-200 focus:outline-none text-black placeholder-gray-400"
            />
          </div>

          <!-- Address -->
          <div>
            <label class="block text-gray-700 font-medium mb-2">Address</label>
            <textarea
              v-model="customer.address"
              rows="3"
              placeholder="Enter full address"
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring focus:ring-blue-200 focus:outline-none text-black placeholder-gray-400"
            ></textarea>
          </div>

          <!-- Date of Birth -->
          <div>
            <label class="block text-gray-700 font-medium mb-2">
              Date of Birth
            </label>
            <input
              v-model="customer.dob"
              type="date"
              placeholder="Select date of birth"
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring focus:ring-blue-200 focus:outline-none text-black placeholder-gray-400"
            />
          </div>

          <!-- Submit Button -->
          <div>
            <button
              type="submit"
              class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition"
              :disabled="saving"
            >
              {{ saving ? "Saving..." : "Update Customer" }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
