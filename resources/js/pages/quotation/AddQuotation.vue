<script>
import axios from "axios";

export default {
  name: "AddQuotation",
  data() {
    return {
      customer: null,
      quotation_date: new Date().toISOString().substr(0, 10),
      items: [
        { product_name: "", item_description: "", quantity: 1, unit_price: 0 }
      ],
      loading: false,
      error: null,
      errors: {} // for field-specific backend errors
    };
  },
  computed: {
    customerId() {
      return this.$route.query.customer_id;
    },
    grandTotal() {
      return this.items.reduce((sum, item) => {
        return sum + (item.quantity * item.unit_price);
      }, 0);
    }
  },
  async created() {
    if (this.customerId) {
      try {
        const res = await axios.get(`/api/v1/customers/${this.customerId}`);
        this.customer = res.data.customer || res.data;
      } catch (err) {
        console.error(err);
        this.error = "Failed to load customer details.";
      }
    }
  },
  methods: {
    addItemRow() {
      this.items.push({ product_name: "", item_description: "", quantity: 1, unit_price: 0 });
    },
    removeItemRow(index) {
      this.items.splice(index, 1);
    },
    async saveQuotation() {
      if (!this.customerId) {
        alert("No customer ID found.");
        return;
      }

      this.loading = true;
      this.error = null;
      this.errors = {};
      try {
        const payload = {
          customer_id: this.customerId,
          quotation_date: this.quotation_date,
          items: this.items
        };
        await axios.post("/api/v1/quotations", payload);
        alert("Quotation added successfully!");
        this.$router.push(`/customer/${this.customerId}`);
      } catch (err) {
        console.error(err);
        if (err.response && err.response.data && err.response.data.errors) {
          this.errors = err.response.data.errors;
          this.error = err.response.data.message || "Validation failed.";
        } else {
          this.error = "Failed to add quotation.";
        }
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
        class="text-blue-600 hover:underline text-lg"
      >
        ← Back to Customer Details
      </router-link>
    </div>

  <h1 class="text-2xl font-bold text-gray-800 mb-4 text-center">
  <span class="text-blue-600">Add Quotation</span> for
  <span v-if="customer" class="text-gray-700">{{ customer.name }}</span>
  <span v-else class="text-gray-400 italic">Customer No.{{ customerId }}</span>
</h1>

    <div v-if="error" class="text-red-500 mb-4">{{ error }}</div>

    <div class="mb-4">
      <label class="block text-sm font-medium mb-1 text-left">Quotation Date</label>
      <input
        v-model="quotation_date"
        type="date"
        class="border p-2 rounded w-full text-black"
      />
      <p v-if="errors.quotation_date" class="text-red-500 text-sm mt-1">
        {{ errors.quotation_date[0] }}
      </p>
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
            <p v-if="errors[`items.${index}.product_name`]" class="text-red-500 text-sm mt-1">
              {{ errors[`items.${index}.product_name`][0] }}
            </p>
          </td>
          <td class="p-2 border">
            <input v-model="item.item_description" class="border p-1 rounded w-full text-black" />
            <p v-if="errors[`items.${index}.item_description`]" class="text-red-500 text-sm mt-1">
              {{ errors[`items.${index}.item_description`][0] }}
            </p>
          </td>
          <td class="p-2 border">
            <input v-model.number="item.quantity" type="number" min="1" class="border p-1 rounded w-20 text-black" />
            <p v-if="errors[`items.${index}.quantity`]" class="text-red-500 text-sm mt-1">
              {{ errors[`items.${index}.quantity`][0] }}
            </p>
          </td>
          <td class="p-2 border">
            <input v-model.number="item.unit_price" type="number" min="0" step="0.01" class="border p-1 rounded w-28 text-black" />
            <p v-if="errors[`items.${index}.unit_price`]" class="text-red-500 text-sm mt-1">
              {{ errors[`items.${index}.unit_price`][0] }}
            </p>
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
      @click="saveQuotation"
      :disabled="loading"
      class="bg-green-500 text-white px-4 py-2 rounded"
    >
      {{ loading ? "Saving..." : "Save Quotation" }}
    </button>
  </div>
</template>
