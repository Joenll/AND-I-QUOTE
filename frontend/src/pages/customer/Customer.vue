<script>
import axios from "axios";
// Import Heroicons (Outline)
import {
  EyeIcon,
  PencilSquareIcon,
  TrashIcon,
  MagnifyingGlassIcon,
} from "@heroicons/vue/24/outline";

export default {
  name: "Customers",
  components: {
    EyeIcon,
    PencilSquareIcon,
    TrashIcon,
    MagnifyingGlassIcon,
  },
  data() {
    return {
      customers: [],
      loading: true,
      error: null,
      searchTerm: "",
      debounceTimeout: null,
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 10,
      },
    };
  },
  async created() {
    await this.fetchCustomers();
  },
  methods: {
    async fetchCustomers(page = 1) {
      this.loading = true;
      this.error = null;
      try {
        const res = await axios.get(
          `http://localhost:8000/api/v1/customers?page=${page}&per_page=${this.pagination.per_page}`
        );
        this.customers = res.data.data;
        this.pagination = {
          current_page: res.data.current_page,
          last_page: res.data.last_page,
          per_page: res.data.per_page,
        };
      } catch (err) {
        console.error(err);
        this.error = "Failed to load customers.";
      } finally {
        this.loading = false;
      }
    },
    async searchCustomers() {
      if (!this.searchTerm.trim()) {
        this.fetchCustomers();
        return;
      }
      this.loading = true;
      this.error = null;
      try {
        const res = await axios.get(
          `http://localhost:8000/api/v1/customers/search?term=${this.searchTerm}`
        );
        this.customers = res.data;
        this.pagination.last_page = 1; // disable pagination for search results
      } catch (err) {
        console.error(err);
        this.error = "Failed to search customers.";
      } finally {
        this.loading = false;
      }
    },
    handleSearchInput() {
      clearTimeout(this.debounceTimeout);
      this.debounceTimeout = setTimeout(() => {
        this.searchCustomers();
      }, 500); // 500ms debounce delay
    },
    async deleteCustomer(id) {
      if (!confirm("Are you sure you want to delete this customer?")) return;
      try {
        await axios.delete(`http://localhost:8000/api/v1/customers/${id}`);
        this.customers = this.customers.filter((c) => c.id !== id);
      } catch (err) {
        console.error(err);
        alert("Failed to delete customer.");
      }
    },
  },
};
</script>

<template>
  <div class="p-8 bg-gray-50 min-h-screen">
    <div class="max-w-6xl mx-auto">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-extrabold text-gray-800">Customers</h1>
        <router-link to="/customer/add">
          <button class="px-6 py-3 bg-blue-600 text-white rounded hover:bg-blue-500 transition">
            + Add Customer
          </button>
        </router-link>
      </div>

      <!-- Search -->
      <div class="mb-6 relative max-w-md">
        <MagnifyingGlassIcon
          class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"
        />
        <input
          type="text"
          v-model="searchTerm"
          @input="handleSearchInput"
          placeholder="Search customers..."
          class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring focus:ring-blue-200 text-black"
        />
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-gray-500 text-lg">Loading...</div>

      <!-- Error -->
      <div v-if="error" class="text-red-600 text-lg">{{ error }}</div>

      <!-- Table -->
      <div
        v-if="!loading && !error"
        class="overflow-x-auto bg-white rounded-xl shadow-lg border border-gray-200"
      >
        <table class="min-w-full text-left text-gray-700">
          <thead>
            <tr class="bg-gray-100 border-b border-gray-200">
              <th class="p-5 font-semibold text-sm uppercase tracking-wider">Name</th>
              <th class="p-5 font-semibold text-sm uppercase tracking-wider">Email</th>
              <th class="p-5 font-semibold text-sm uppercase tracking-wider">Quotations</th>
              <th class="p-5 font-semibold text-sm uppercase tracking-wider">Grand Total</th>
              <th class="p-5 font-semibold text-sm uppercase tracking-wider text-center w-36">
                Actions
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="customer in customers"
              :key="customer.id"
              class="hover:bg-gray-50 transition"
            >
              <td class="p-5 border-b border-gray-100">{{ customer.name }}</td>
              <td class="p-5 border-b border-gray-100">{{ customer.email }}</td>
              <td class="p-5 border-b border-gray-100">
                {{ customer.quotations?.length || 0 }}
              </td>
              <td class="p-5 border-b border-gray-100">
                ₱{{
                  customer.quotations?.reduce(
                    (sum, q) => sum + (q.grand_total || 0),
                    0
                  ) || 0
                }}
              </td>
              <td class="p-5 border-b border-gray-100 text-center">
                <div class="flex justify-center gap-2">
                  <!-- View -->
                  <router-link
                    :to="`/customer/${customer.id}`"
                    class="p-2 bg-black text-white rounded hover:bg-gray-800 transition"
                    title="View"
                  >
                    <EyeIcon class="w-10 h-5 text-white" />
                  </router-link>
                  <!-- Edit -->
                  <router-link
                    :to="`/customer/${customer.id}/edit`"
                    class="p-2 bg-black text-white rounded hover:bg-gray-800 transition"
                    title="Edit"
                  >
                    <PencilSquareIcon class="w-10 h-5 text-white" />
                  </router-link>
                  <!-- Delete -->
                  <button
                    @click="deleteCustomer(customer.id)"
                    class="p-2 bg-red-500 text-white rounded hover:bg-red-400 transition"
                    title="Delete"
                  >
                    <TrashIcon class="w-5 h-5" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="customers.length === 0">
              <td colspan="5" class="p-5 text-center text-gray-500">
                No customers found.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div
        v-if="pagination.last_page > 1"
        class="flex justify-center mt-6 space-x-2"
      >
        <button
          v-for="page in pagination.last_page"
          :key="page"
          @click="fetchCustomers(page)"
          :class="[ 'px-4 py-2 rounded border',
            page === pagination.current_page
              ? 'bg-blue-600 text-white'
              : 'bg-white hover:bg-gray-100 text-gray-700',
          ]"
        >
          {{ page }}
        </button>
      </div>
    </div>
  </div>
</template>
