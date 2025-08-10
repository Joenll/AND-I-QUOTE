<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\QuotationMail;

class QuotationController extends Controller
{
    // List quotations (with customer and items)
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 25);
        $quotations = Quotation::with(['customer', 'items'])->paginate($perPage);
        return response()->json($quotations);
    }

    // Show single quotation with details
    public function show(Quotation $quotation)
    {
        $quotation->load(['customer', 'items']);
        $summary = [
            'quotation' => $quotation,
            'grand_total' => (float)$quotation->grand_total,
            'total_items' => $quotation->items()->sum('quantity'),
            'quotation_date' => $quotation->quotation_date->toDateString(),
        ];
        return response()->json($summary);
    }

    // Create quotation with multiple items
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'quotation_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.item_description' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $quotation = Quotation::create([
                'customer_id' => $request->customer_id,
                'quotation_date' => $request->quotation_date,
                'grand_total' => 0,
            ]);

            $grandTotal = 0;
            $totalItemsCount = 0;

            foreach ($request->items as $it) {
                $quantity = (int)$it['quantity'];
                $unitPrice = (float)$it['unit_price'];
                $total = round($quantity * $unitPrice, 2);

                $quotation->items()->create([
                    'product_name'    => $it['product_name'],
                    'item_description'=> $it['item_description'] ?? null,
                    'quantity'        => $quantity,
                    'unit_price'      => $unitPrice,
                    'total_price'     => $total,
                ]);

                $grandTotal += $total;
                $totalItemsCount += $quantity;
            }

            $quotation->grand_total = round($grandTotal, 2);
            $quotation->save();

            DB::commit();
            $quotation->load(['customer', 'items']);
            return response()->json($quotation, 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => 'Could not create quotation', 'message' => $e->getMessage()], 500);
        }
    }

    // Update quotation header & items
    public function update(Request $request, Quotation $quotation)
    {
        $validator = Validator::make($request->all(), [
            'quotation_date' => 'sometimes|date',
            'items' => 'sometimes|array|min:1',
            'items.*.product_name' => 'required_with:items|string|max:255',
            'items.*.item_description' => 'nullable|string',
            'items.*.quantity' => 'required_with:items|integer|min:1',
            'items.*.unit_price' => 'required_with:items|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            if ($request->has('quotation_date')) {
                $quotation->quotation_date = $request->quotation_date;
                $quotation->save();
            }

            if ($request->has('items')) {
                $quotation->items()->delete();

                $grandTotal = 0;
                foreach ($request->items as $it) {
                    $quantity = (int)$it['quantity'];
                    $unitPrice = (float)$it['unit_price'];
                    $total = round($quantity * $unitPrice, 2);

                    $quotation->items()->create([
                        'product_name'    => $it['product_name'],
                        'item_description'=> $it['item_description'] ?? null,
                        'quantity'        => $quantity,
                        'unit_price'      => $unitPrice,
                        'total_price'     => $total,
                    ]);

                    $grandTotal += $total;
                }

                $quotation->grand_total = round($grandTotal, 2);
                $quotation->save();
            }

            DB::commit();
            $quotation->load(['customer', 'items']);
            return response()->json($quotation);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => 'Could not update', 'message' => $e->getMessage()], 500);
        }
    }

    // Delete quotation
    public function destroy(Quotation $quotation)
    {
        $quotation->delete();
        return response()->json(['message' => 'Quotation deleted.']);
    }

    // Send quotation by email
    public function sendEmail(Quotation $quotation)
    {
        $quotation->load(['customer', 'items']);
        if (!$quotation->customer || !$quotation->customer->email) {
            return response()->json(['error' => 'Customer or email not found.'], 422);
        }

        try {
            Mail::to($quotation->customer->email)
                ->send(new QuotationMail($quotation));

            return response()->json(['message' => 'Email sent to customer.']);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Failed to send email', 'message' => $e->getMessage()], 500);
        }
    }
}
