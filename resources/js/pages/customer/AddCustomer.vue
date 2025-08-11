<script>
import axios from "axios";

export default {
  name: "AddCustomer",
  data() {
    return {
      customer: {
        name: "",
        email: "",
        contact: "",
        address: "",
        date_of_birth: "",
      },
      saving: false,
      //  Predefine all possible error fields so Vue's reactivity always picks them up
      errors: {
        name: [],
        email: [],
        contact: [],
        address: [],
        date_of_birth: [],
      },
    };
  },
  methods: {
    async addCustomer() {
      this.saving = true;
      // Clear existing errors
      this.errors = {
        name: [],
        email: [],
        contact: [],
        address: [],
        date_of_birth: [],
      };

      try {
        await axios.post(`/api/v1/customers`, this.customer);

        // ✅ Only runs if no validation errors
        alert("Customer added successfully!");
        this.$router.push("/customers");

      } catch (err) {
        if (err.response && err.response.status === 422) {
          console.log("Validation errors:", err.response.data.errors); // 🔍 Debug check
          // Assign Laravel's validation errors
          this.errors = { ...this.errors, ...err.response.data.errors };
        } else {
          alert("Failed to add customer. Please try again.");
        }
      } finally {
        this.saving = false;
      }
    },
  },
};
</script>

<template>
  <div class="p-8 bg-white min-h-screen">
    <div class="max-w-4xl mx-auto">
      <!-- Back Button -->
      <div class="mb-6 flex justify-start">
        <router-link
          to="/customers"
          class="text-blue-600 hover:underline text-md"
        >
          ← Back to Customers
        </router-link>
      </div>

      <!-- Form -->
      <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200">
        <h1 class="text-2xl font-bold text-blue-800 mb-6">Add New Customer</h1>

        <form @submit.prevent="addCustomer" class="space-y-5">
          <!-- Name -->
          <div>
            <label class="block text-gray-700 font-medium mb-2">Name</label>
            <input
              v-model="customer.name"
              type="text"
              placeholder="Enter full name"
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring focus:ring-blue-200 focus:outline-none text-black placeholder-gray-400"
            />
            <p v-if="errors.name.length" class="text-red-500 text-sm mt-1">
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
            />
            <p v-if="errors.email.length" class="text-red-500 text-sm mt-1">
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
            <p v-if="errors.contact.length" class="text-red-500 text-sm mt-1">
              {{ errors.contact[0] }}
            </p>
          </div>

          <!-- Address -->
          <div>
            <label class="block text-gray-700 font-medium mb-2">Address</label>
            <textarea
              v-model="customer.address"
              placeholder="Enter full address"
              rows="3"
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring focus:ring-blue-200 focus:outline-none text-black placeholder-gray-400"
            ></textarea>
            <p v-if="errors.address.length" class="text-red-500 text-sm mt-1">
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
            <!-- ✅ Will now always show because date_of_birth key is always reactive -->
            <p v-if="errors.date_of_birth.length" class="text-red-500 text-sm mt-1">
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
              {{ saving ? "Saving..." : "Add Customer" }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
