<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuotationItem;

class QuotationItemController extends Controller
{
    /**
     * Display a list of all quotation items.
     */
    public function index()
    {
        return response()->json(QuotationItem::all());
    }

    /**
     * Display a specific quotation item by ID.
     */
    public function show(QuotationItem $quotationItem)
    {
        return response()->json($quotationItem);
    }

    /**
     * Update the details of a specific quotation item.
     * Also recalculates the total price if quantity or unit price is changed.
     */
    public function update(Request $request, QuotationItem $quotationItem)
    {
        // Validate input data
        $data = $request->validate([
            'product_name'     => 'sometimes|string|max:255',
            'item_description' => 'sometimes|string',
            'quantity'         => 'sometimes|integer|min:1',
            'unit_price'       => 'sometimes|numeric|min:0',
        ]);

        // Recalculate total price if needed
        if (isset($data['quantity']) || isset($data['unit_price'])) {
            $quantity = $data['quantity'] ?? $quotationItem->quantity;
            $unitPrice = $data['unit_price'] ?? $quotationItem->unit_price;
            $data['total_price'] = round($quantity * $unitPrice, 2);
        }

        // Update the record
        $quotationItem->update($data);

        return response()->json($quotationItem);
    }

    /**
     * Delete a specific quotation item.
     */
    public function destroy(QuotationItem $quotationItem)
    {
        $quotationItem->delete();

        return response()->json(['message' => 'Item deleted']);
    }
}
