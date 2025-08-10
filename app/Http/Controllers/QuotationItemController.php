<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuotationItem;

class QuotationItemController extends Controller
{
    public function index()
    {
        return response()->json(QuotationItem::all());
    }

    public function show(QuotationItem $quotationItem)
    {
        return response()->json($quotationItem);
    }

    public function update(Request $request, QuotationItem $quotationItem)
    {
        $data = $request->validate([
            'product_name'    => 'sometimes|string|max:255',
            'item_description'=> 'sometimes|string',
            'quantity'        => 'sometimes|integer|min:1',
            'unit_price'      => 'sometimes|numeric|min:0',
        ]);

        // Recalculate total if quantity or unit_price is updated
        if (isset($data['quantity']) || isset($data['unit_price'])) {
            $quantity = $data['quantity'] ?? $quotationItem->quantity;
            $unitPrice = $data['unit_price'] ?? $quotationItem->unit_price;
            $data['total_price'] = round($quantity * $unitPrice, 2);
        }

        $quotationItem->update($data);

        return response()->json($quotationItem);
    }

    public function destroy(QuotationItem $quotationItem)
    {
        $quotationItem->delete();
        return response()->json(['message' => 'Item deleted']);
    }
}
