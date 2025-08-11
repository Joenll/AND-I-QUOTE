<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;

class CustomerController extends Controller
{
    /**
     * Display a paginated list of customers with their quotations and quotation items.
     * Default is 25 results per page but can be changed via `per_page` query parameter.
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 25);

        $customers = Customer::with('quotations.items')
            ->paginate($perPage);

        return response()->json($customers);
    }

    /**
     * Search customers by name, email, or contact number.
     * Includes associated quotations for each matched customer.
     */
    public function search(Request $request)
    {
        $term = $request->query('term');

        $customers = Customer::with('quotations')
            ->where(function ($query) use ($term) {
                $query->where('name', 'like', "%{$term}%")
                      ->orWhere('email', 'like', "%{$term}%")
                      ->orWhere('contact', 'like', "%{$term}%");
            })
            ->get();

        return response()->json($customers);
    }

    /**
     * Retrieve detailed information for a single customer.
     * Includes all quotations, item count per quotation, and grand total calculation.
     */
    public function show(Customer $customer)
    {
        $quotations = $customer->quotations()
            ->with('items')
            ->withCount('items as total_items')
            ->get()
            ->map(function ($quotation) {
                $quotation->grand_total = $quotation->items->sum('total_price') ?? 0;
                return $quotation;
            });

        return response()->json([
            'customer'    => $customer,
            'quotations'  => $quotations,
        ]);
    }

    /**
     * Store a newly created customer in the database.
     * Uses validation rules from StoreCustomerRequest.
     */
    public function store(StoreCustomerRequest $request)
    {
        $customer = Customer::create($request->validated());

        return response()->json($customer, 201);
    }

    /**
     * Update the specified customer in the database.
     * Uses validation rules from UpdateCustomerRequest.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());

        return response()->json($customer);
    }

    /**
     * Remove the specified customer from the database.
     * This will also delete related data if database constraints are set.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->json(['message' => 'Customer deleted.']);
    }
}
