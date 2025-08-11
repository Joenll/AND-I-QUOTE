import { createRouter, createWebHistory } from 'vue-router'

// Landing page
import LandingPage from '../pages/LandingPage.vue'

// Customer pages
import Customers from '../pages/customer/Customer.vue'
import CustomerDetails from '../pages/customer/CustomerDetails.vue'
import AddCustomer from '../pages/customer/AddCustomer.vue'
import EditCustomer from '../pages/customer/EditCustomer.vue'

// Quotation pages
import AddQuotation from '../pages/quotation/AddQuotation.vue'
import EditQuotation from '../pages/quotation/EditQuotation.vue'
import QuotationDetails from '../pages/quotation/QuotationDetails.vue'

const routes = [
  // Landing Page
  { path: '/', name: 'LandingPage', component: LandingPage },

  // Customers
  { path: '/customers', name: 'Customers', component: Customers },
  { path: '/customer/:id', name: 'CustomerDetails', component: CustomerDetails },
  { path: '/customer/add', name: 'AddCustomer', component: AddCustomer },
  { path: '/customer/:id/edit', name: 'EditCustomer', component: EditCustomer },

  // Quotations
  { path: '/quotation/add', name: 'AddQuotation', component: AddQuotation },
  { path: '/quotation/:id/edit', name: 'EditQuotation', component: EditQuotation },
  { path: '/quotation/:id', name: 'QuotationDetails', component: QuotationDetails },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// ✅ Back button blocking only for certain pages
router.beforeEach((to, from, next) => {
  if (to.meta.preventBack) {
    window.history.pushState(null, '', window.location.href)
    alert('Please use the navigation buttons.')
    next(false)
  } else {
    next()
  }
})

export default router
