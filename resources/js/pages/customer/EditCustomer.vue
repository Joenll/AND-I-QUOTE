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
        date_of_birth: "",
      },
      loading: true,
      error: null,
      saving: false,
      errors: {}, // for backend validation errors
    };
  },
  async created() {
    await this.fetchCustomer();
  },
  methods: {
    async fetchCustomer() {
      try {
        const res = await axios.get(
          `/api/v1/customers/${this.$route.params.id}`
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
      this.errors = {};
      try {
        await axios.put(
          `/api/v1/customers/${this.$route.params.id}`,
          this.customer
        );
        alert("Customer updated successfully!");
        this.$router.push(`/customers`);
      } catch (err) {
        console.error(err);
        if (err.response && err.response.data && err.response.data.errors) {
          this.errors = err.response.data.errors;
        } else {
          this.error = "Failed to update customer.";
        }
      } finally {
        this.saving = false;
      }
    },
  },
};
</script>

<template>
  <div class="p-8 bg-whitemin-h-screen">
    <div class="max-w-4xl mx-auto">
      <!-- Back Button -->
      <div class="mb-6 flex justify-start">
        <router-link
          :to="`/customers`"
          class="text-blue-600 hover:underline text-md"
        >
          ← Back to Customers
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
        <h1 class="text-2xl font-bold text-blue-800 mb-6">Edit Customer</h1>

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
            <p v-if="errors.name" class="text-red-500 text-sm mt-1">
              {{ errors.name[0] }}
            </p>
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
            <p v-if="errors.email" class="text-red-500 text-sm mt-1">
              {{ errors.email[0] }}
            </p>
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
            <p v-if="errors.contact" class="text-red-500 text-sm mt-1">
              {{ errors.contact[0] }}
            </p>
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
            <p v-if="errors.address" class="text-red-500 text-sm mt-1">
              {{ errors.address[0] }}
            </p>
          </div>

          <!-- Date of Birth -->
          <div>
            <label class="block text-gray-700 font-medium mb-2">
              Date of Birth
            </label>
            <input
              v-model="customer.date_of_birth"
              type="date"
              placeholder="Select date of birth"
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring focus:ring-blue-200 focus:outline-none text-black placeholder-gray-400"
            />
            <p v-if="errors.date_of_birth" class="text-red-500 text-sm mt-1">
              {{ errors.date_of_birth[0] }}
            </p>
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
