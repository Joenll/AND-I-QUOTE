<script>
import axios from "axios";

export default {
  name: "EditQuotation",
  data() {
    return {
      customer: null,
      quotation_date: "",
      items: [],
      loading: false,
      error: null
    };
  },
  computed: {
    quotationId() {
      return this.$route.params.id;
    },
    grandTotal() {
      return this.items.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
    }
  },
  async created() {
    try {
      // Fetch quotation with customer details
      const res = await axios.get(`http://localhost:8000/api/v1/quotations/${this.quotationId}`);
      const quotation = res.data.quotation || res.data;

      this.customer = quotation.customer || {};
      this.quotation_date = quotation.quotation_date;
      this.items = quotation.items || [];
    } catch (err) {
      console.error(err);
      this.error = "Failed to load quotation details.";
    }
  },
  methods: {
    addItemRow() {
      this.items.push({ product_name: "", item_description: "", quantity: 1, unit_price: 0 });
    },
    removeItemRow(index) {
      this.items.splice(index, 1);
    },
    async updateQuotation() {
      this.loading = true;
      this.error = null;
      try {
        const payload = {
          quotation_date: this.quotation_date,
          items: this.items
        };
        await axios.put(`http://localhost:8000/api/v1/quotations/${this.quotationId}`, payload);
        alert("Quotation updated successfully!");
        this.$router.push(`/customer/${this.customer.id}`);
      } catch (err) {
        console.error(err);
        this.error = "Failed to update quotation.";
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>

<template>
  <div class="p-8 max-w-4xl mx-auto text-black">

        
<!-- Back Button -->
<div class="mb-6 flex justify-start" v-if="customer && customer.id">
  <router-link
    :to="`/customer/${customer.id}`"
    class="text-blue-600 hover:underline text-sm"
  >
    ← Back to Customer Details
  </router-link>
</div>


    <h1 class="text-2xl font-bold mb-4">
      Edit Quotation for
      <span v-if="customer">{{ customer.name }}</span>
      <span v-else>Customer</span>
    </h1>
    

    <div v-if="error" class="text-red-500 mb-4">{{ error }}</div>

    <div class="mb-4">
      <label class="block text-sm font-medium mb-1">Quotation Date</label>
      <input
        v-model="quotation_date"
        type="date"
        class="border p-2 rounded w-full text-black"
      />
    </div>

    <table class="w-full border mb-4 text-black">
      <thead class="bg-gray-100">
        <tr>
          <th class="p-2 border">Product Name</th>
          <th class="p-2 border">Description</th>
          <th class="p-2 border">Quantity</th>
          <th class="p-2 border">Unit Price</th>
          <th class="p-2 border">Total</th>
          <th class="p-2 border"></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in items" :key="index">
          <td class="p-2 border">
            <input v-model="item.product_name" class="border p-1 rounded w-full text-black" />
          </td>
          <td class="p-2 border">
            <input v-model="item.item_description" class="border p-1 rounded w-full text-black" />
          </td>
          <td class="p-2 border">
            <input v-model.number="item.quantity" type="number" min="1" class="border p-1 rounded w-20 text-black" />
          </td>
          <td class="p-2 border">
            <input v-model.number="item.unit_price" type="number" min="0" step="0.01" class="border p-1 rounded w-28 text-black" />
          </td>
          <td class="p-2 border">₱{{ (item.quantity * item.unit_price).toFixed(2) }}</td>
          <td class="p-2 border">
            <button @click="removeItemRow(index)" class="text-red-500">X</button>
          </td>
        </tr>
      </tbody>
    </table>

    <button
      @click="addItemRow"
      class="bg-blue-500 text-white px-3 py-1 rounded mb-4"
    >
      + Add Item
    </button>

    <div class="font-bold mb-4">Grand Total: ₱{{ grandTotal.toFixed(2) }}</div>

    <button
      @click="updateQuotation"
      :disabled="loading"
      class="bg-green-500 text-white px-4 py-2 rounded"
    >
      {{ loading ? "Updating..." : "Update Quotation" }}
    </button>
  </div>
</template>
