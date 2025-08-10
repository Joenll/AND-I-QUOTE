<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // List customers with quotations (paginated)
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 25);
        $customers = Customer::with('quotations.items')->paginate($perPage);
        return response()->json($customers);
    }

    public function search(Request $request)
{
    $term = $request->query('term');

    $customers = Customer::with('quotations')
        ->where('name', 'like', "%{$term}%")
        ->orWhere('email', 'like', "%{$term}%")
        ->orWhere('contact', 'like', "%{$term}%")
        ->get();

    return response()->json($customers);
}


    // Show single customer with quotations
 public function show(Customer $customer)
{
    $quotations = $customer->quotations()
        ->with('items')
        ->withCount('items as total_items') // creates "total_items"
        ->get()
        ->map(function ($quotation) {
            $quotation->grand_total = $quotation->items->sum('total_price') ?? 0;
            return $quotation;
        });

    return response()->json([
        'customer' => $customer,
        'quotations' => $quotations
    ]);
}

    // Create new customer
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'dob' => 'required|date',
            'address' => 'nullable|string|max:1000',
            'email' => 'required|email|unique:customers,email',
            'contact' => 'nullable|string|max:50',
        ]);

        $customer = Customer::create($data);
        return response()->json($customer, 201);
    }

    // Update customer
    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'dob' => 'sometimes|required|date',
            'address' => 'nullable|string|max:1000',
            'email' => ['sometimes','required','email', Rule::unique('customers','email')->ignore($customer->id)],
            'contact' => 'nullable|string|max:50',
        ]);

        $customer->update($data);
        return response()->json($customer);
    }

    // Delete customer (quotations will cascade from FK)
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json(['message' => 'Customer deleted.']);
    }
}
