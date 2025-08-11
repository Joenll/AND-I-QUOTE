<script>
import axios from "axios";
import {
  EyeIcon,
  PencilSquareIcon,
  TrashIcon,
  MagnifyingGlassIcon,
} from "@heroicons/vue/24/outline";
import CustomerDetailsModal from "@/components/CustomerDetailsModal.vue"; // <-- import modal

export default {
  name: "Customers",
  components: {
    EyeIcon,
    PencilSquareIcon,
    TrashIcon,
    MagnifyingGlassIcon,
    CustomerDetailsModal
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
      selectedCustomer: null, // <-- for modal data
      showModal: false // <-- modal visibility
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
          `/api/v1/customers?page=${page}&per_page=${this.pagination.per_page}`
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
          `/api/v1/customers/search?term=${this.searchTerm}`
        );
        this.customers = res.data;
        this.pagination.last_page = 1;
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
      }, 500);
    },
    async deleteCustomer(id) {
      if (!confirm("Are you sure you want to delete this customer?")) return;
      try {
        await axios.delete(`/api/v1/customers/${id}`);
        this.customers = this.customers.filter((c) => c.id !== id);
      } catch (err) {
        console.error(err);
        alert("Failed to delete customer.");
      }
    },
async viewDetails(customer) {
  try {
    const res = await axios.get(`/api/v1/customers/${customer.id}`);
    this.selectedCustomer = res.data.customer;; // full customer object
    this.showModal = true;
  } catch (err) {
    console.error(err);
    alert("Failed to load customer details.");
  }
}
  }
};
</script>


<template>
  <div class="p-8 bg-white min-h-screen">
    <div class="max-w-6xl mx-auto">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-extrabold text-blue-800">Customers</h1>
      </div>
      <!-- Search & Add Button Row -->
      <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        
        <!-- Search Box -->
        <div class="relative w-full sm:w-80">
          <MagnifyingGlassIcon
            class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"
          />
          <input
            type="text"
            v-model="searchTerm"
            @input="handleSearchInput"
            placeholder="Search customers..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200 text-black shadow-sm"
          />
        </div>

        <!-- Add Customer Button -->
        <router-link to="/customer/add">
          <button
            class="flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-500 transition shadow-sm"
          >
            <span class="text-lg">+</span>
            Add Customer
          </button>
        </router-link>
        
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
                <div class="flex justify-center gap-4">

                  <!-- View Details -->
                  <span
                    @click="viewDetails(customer)"
                    class="text-purple-600 hover:underline cursor-pointer"
                  >
                    Details
                  </span>

                  <!-- View -->
                  <router-link
                    :to="`/customer/${customer.id}`"
                  
                  >
                    <span class="text-green-600 hover:underline cursor-pointer">
                      Quotations
                    </span>
                  </router-link>

                  <!-- Edit -->
                  <router-link
                    :to="`/customer/${customer.id}/edit`"
                    
                  >
                    <span class="text-blue-600 hover:underline cursor-pointer">
                      Edit
                    </span>
                  </router-link>

                  <!-- Delete -->
                  <span
                    @click="deleteCustomer(customer.id)"
                    class="text-red-600 hover:underline cursor-pointer"
                  >
                    Delete
                  </span>
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

<CustomerDetailsModal
  :visible="showModal"
  :customer="selectedCustomer"
  @close="showModal = false"
/>



</template>




