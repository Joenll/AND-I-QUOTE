<script>
import axios from "axios";
import {
  EyeIcon,
  PencilSquareIcon,
  EnvelopeIcon,
  TrashIcon,
  PencilIcon,
  MagnifyingGlassIcon,
} from "@heroicons/vue/24/solid";

export default {
  name: "CustomerDetails",
  components: {
    EyeIcon,
    PencilSquareIcon,
    EnvelopeIcon,
    TrashIcon,
    PencilIcon,
    MagnifyingGlassIcon,
  },
  data() {
    return {
      customer: null,
      quotations: [],
      expandedQuotationId: null,
      loading: true,
      error: null,
      searchQuery: "",
    };
  },
  async created() {
    await this.fetchCustomerDetails();
  },
  computed: {
    filteredQuotations() {
      if (!this.searchQuery) return this.quotations;
      const query = this.searchQuery.toLowerCase();
      return this.quotations.filter(
        (q) =>
          q.id.toString().includes(query) ||
          q.items?.some((item) =>
            item.item_description?.toLowerCase().includes(query)
          )
      );
    },
  },
  methods: {
    async fetchCustomerDetails() {
      this.loading = true;
      this.error = null;
      try {
        const res = await axios.get(
          `http://localhost:8000/api/v1/customers/${this.$route.params.id}`
        );
        this.customer = res.data.customer;
        this.quotations = res.data.quotations || [];
      } catch (err) {
        console.error(err);
        this.error = "Failed to load customer details.";
      } finally {
        this.loading = false;
      }
    },
    async sendQuotationEmail(quotationId) {
      if (!confirm("Send this quotation to the customer via email?")) return;
      try {
        await axios.post(
          `http://localhost:8000/api/v1/quotations/${quotationId}/send-email`
        );
        alert("Quotation email sent successfully!");
      } catch (err) {
        console.error(err);
        alert("Failed to send email.");
      }
    },
    async deleteQuotation(quotationId) {
      if (!confirm("Are you sure you want to delete this quotation?")) return;
      try {
        await axios.delete(
          `http://localhost:8000/api/v1/quotations/${quotationId}`
        );
        alert("Quotation deleted successfully!");
        this.quotations = this.quotations.filter((q) => q.id !== quotationId);
      } catch (err) {
        console.error(err);
        alert("Failed to delete quotation.");
      }
    },
    toggleQuotationItems(quotationId) {
      this.expandedQuotationId =
        this.expandedQuotationId === quotationId ? null : quotationId;
    },
  },
};
</script>

<template>
  <div class="p-8 bg-gray-50 min-h-screen">
    <div class="max-w-6xl mx-auto">
      <!-- Top Actions -->
      <div class="mb-6 flex justify-between items-center">
        <router-link to="/customers" class="text-blue-600 hover:underline text-sm">
          ← Back to Customers
        </router-link>
        <div class="flex gap-3">
          <router-link
            :to="`/customer/${$route.params.id}/edit`"
            class="px-4 py-2 bg-black rounded-lg text-sm hover:bg-gray-800 transition flex items-center gap-2"
          >
            <PencilIcon class="w-4 h-4 text-white" />
            <span class="text-white">Edit Customer</span>
          </router-link>
          <router-link
            :to="`/quotation/add?customer_id=${$route.params.id}`"
            class="px-4 py-2 bg-black text-white rounded-lg text-sm hover:bg-gray-800 transition flex items-center gap-2"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 text-white"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 4v16m8-8H4"
              />
            </svg>
            <span class="text-white">Add Quotation</span>
          </router-link>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-gray-500 text-lg">Loading...</div>
      <!-- Error -->
      <div v-if="error" class="text-red-600 text-lg">{{ error }}</div>

      <!-- Customer Info -->
      <div
        v-if="!loading && customer"
        class="bg-white p-6 rounded-xl shadow-md border border-gray-200 mb-8"
      >
        <h1 class="text-2xl font-bold text-gray-800 mb-4">
          {{ customer.name }}
        </h1>
        <div class="space-y-1 text-gray-600">
          <p><strong>Email:</strong> {{ customer.email }}</p>
          <p><strong>Contact:</strong> {{ customer.contact || "—" }}</p>
          <p><strong>Address:</strong> {{ customer.address || "—" }}</p>
          <p><strong>Date of Birth:</strong> {{ customer.dob }}</p>
        </div>
      </div>

      <!-- Search Bar -->
      <div v-if="quotations.length > 0" class="mb-4 flex items-center gap-2">
        <MagnifyingGlassIcon class="w-5 h-5 text-black" />
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search quotations..."
          class="border border-gray-300 px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-black w-64 bg-white text-black"
        />
      </div>

      <!-- Quotations Table (Always Rendered) -->
      <div
        v-if="quotations.length > 0"
        class="overflow-x-auto bg-white rounded-xl shadow-lg border border-gray-200"
      >
        <table class="min-w-full text-left text-gray-700">
          <thead>
            <tr class="bg-gray-100 border-b border-gray-200">
              <th class="p-5 font-semibold text-sm uppercase tracking-wider">
                Quotation #
              </th>
              <th class="p-5 font-semibold text-sm uppercase tracking-wider">
                Date
              </th>
              <th class="p-5 font-semibold text-sm uppercase tracking-wider">
                Total Items
              </th>
              <th class="p-5 font-semibold text-sm uppercase tracking-wider">
                Grand Total
              </th>
              <th
                class="p-5 font-semibold text-sm uppercase tracking-wider text-center"
              >
                Actions
              </th>
            </tr>
          </thead>
          <tbody>
            <template v-if="filteredQuotations.length > 0">
              <template v-for="quote in filteredQuotations" :key="quote.id">
                <tr class="hover:bg-gray-50 transition">
                  <td class="p-5 border-b border-gray-100">{{ quote.id }}</td>
                  <td class="p-5 border-b border-gray-100">
                    {{ new Date(quote.created_at).toLocaleDateString() }}
                  </td>
                  <td class="p-5 border-b border-gray-100">
                    {{ quote.items?.length || 0 }}
                  </td>
                  <td class="p-5 border-b border-gray-100">
                    ₱{{ quote.grand_total.toFixed(2) }}
                  </td>
                  <td class="p-5 border-b border-gray-100 text-center">
                    <div class="flex justify-center gap-2">
                      <button
                        @click="toggleQuotationItems(quote.id)"
                        class="p-2 bg-blue-500 text-white rounded hover:bg-blue-400 transition"
                        :title="
                          expandedQuotationId === quote.id
                            ? 'Hide Items'
                            : 'View Items'
                        "
                      >
                        <EyeIcon class="w-5 h-5" />
                      </button>
                      <router-link
                        :to="`/quotation/${quote.id}/edit`"
                        class="p-2 bg-black rounded-lg transition hover:bg-gray-800"
                        title="Edit"
                      >
                        <PencilSquareIcon class="w-10 h-5 text-white" />
                      </router-link>
                      <button
                        @click="sendQuotationEmail(quote.id)"
                        class="p-2 bg-green-500 text-white rounded hover:bg-green-400 transition"
                        title="Send Email"
                      >
                        <EnvelopeIcon class="w-5 h-5" />
                      </button>
                      <button
                        @click="deleteQuotation(quote.id)"
                        class="p-2 bg-red-500 text-white rounded hover:bg-red-400 transition"
                        title="Delete"
                      >
                        <TrashIcon class="w-5 h-5" />
                      </button>
                    </div>
                  </td>
                </tr>

                <!-- Quotation Items -->
                <tr
                  v-if="expandedQuotationId === quote.id"
                  class="bg-gray-50"
                >
                  <td colspan="5" class="p-4">
                    <table class="w-full border text-sm">
                      <thead>
                        <tr class="bg-gray-200">
                          <th class="p-2 border">Description</th>
                          <th class="p-2 border">Quantity</th>
                          <th class="p-2 border">Unit Price</th>
                          <th class="p-2 border">Total Price</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr
                          v-for="item in quote.items || []"
                          :key="item.id"
                        >
                          <td class="p-2 border">
                            {{ item.item_description }}
                          </td>
                          <td class="p-2 border">{{ item.quantity }}</td>
                          <td class="p-2 border">₱{{ item.unit_price }}</td>
                          <td class="p-2 border">₱{{ item.total_price }}</td>
                        </tr>
                        <tr
                          v-if="!quote.items || quote.items.length === 0"
                        >
                          <td
                            colspan="4"
                            class="text-center text-gray-500 p-2"
                          >
                            No items found for this quotation.
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </template>
            </template>

            <!-- Empty State Row (When No Matches) -->
            <tr v-else>
              <td
                colspan="5"
                class="text-center text-gray-500 p-5"
              >
                No quotations found.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- If customer has no quotations at all -->
      <div
        v-if="!loading && quotations.length === 0"
        class="text-gray-500 text-center mt-6"
      >
        No quotations available for this customer.
      </div>
    </div>
  </div>
</template>
