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
      errors: {},
    };
  },
  methods: {
    async addCustomer() {
      this.saving = true;
      this.errors = {};
      try {
        await axios.post("http://localhost:8000/api/v1/customers", this.customer);
        alert("Customer added successfully!");
        this.$router.push("/");
      } catch (err) {
        if (err.response && err.response.status === 422) {
          this.errors = err.response.data.errors;
        } else {
          alert("Failed to add customer.");
        }
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
          to="/customers"
          class="text-blue-600 hover:underline text-sm"
        >
          ← Back to Customers
        </router-link>
      </div>

      <!-- Form -->
      <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Add New Customer</h1>

        <form @submit.prevent="addCustomer" class="space-y-5">
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
              placeholder="Enter full address"
              rows="3"
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
              v-model="customer.dob"
              type="date"
              placeholder="Select date of birth"
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring focus:ring-blue-200 focus:outline-none text-black placeholder-gray-400"
              required
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
              {{ saving ? "Saving..." : "Add Customer" }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
