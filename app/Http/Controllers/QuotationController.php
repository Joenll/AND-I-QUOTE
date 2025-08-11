<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Support\Facades\DB;
use Brevo\Client\Configuration;
use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Model\SendSmtpEmail;
use App\Http\Requests\StoreQuotationRequest;
use App\Http\Requests\UpdateQuotationRequest;

class QuotationController extends Controller
{
    /**
     * Display a paginated list of quotations with customer and item details.
     */
    public function index(Request $request)
    {
        // Returning paginated response is a very good approach
        // This helps in loading time and prevent server stress
        $perPage = $request->query('per_page', 25);

        $quotations = Quotation::with(['customer', 'items'])
            ->paginate($perPage);

        return response()->json($quotations);
    }

    /**
     * Show details of a single quotation including customer, items, totals, and date.
     */
    public function show(Quotation $quotation)
    {
        $quotation->load(['customer', 'items']);

        return response()->json([
            'quotation'      => $quotation,
            'grand_total'    => (float) $quotation->grand_total,
            'total_items'    => $quotation->items()->sum('quantity'),
            'quotation_date' => $quotation->quotation_date->toDateString(),
        ]);
    }

    /**
     * Store a new quotation with its items.
     */
    public function store(StoreQuotationRequest $request)
    {
        DB::beginTransaction();

        try {
            $quotation = Quotation::create([
                'customer_id'    => $request->customer_id,
                'quotation_date' => $request->quotation_date,
                'grand_total'    => 0,
            ]);

            $grandTotal = 0;

            foreach ($request->items as $it) {
                // Using `round` php function in monetary values is not
                // a good approach if taking only 2 decimal places
                // In monetary values, it should be atleast 4 decimal places
                // Or using number_format() is a better way to go without rounding the numerical values

                // This block can be added into a private method to apply DRY principle
                // since this is also repeated in the update() method
                $total = round($it['quantity'] * $it['unit_price'], 2);

                $quotation->items()->create([
                    'product_name'    => $it['product_name'],
                    'item_description'=> $it['item_description'] ?? null,
                    'quantity'        => $it['quantity'],
                    'unit_price'      => $it['unit_price'],
                    'total_price'     => $total,
                ]);

                $grandTotal += $total;
                // Up to this block
            }

            $quotation->update(['grand_total' => round($grandTotal, 2)]);

            DB::commit();

            return response()->json($quotation->load(['customer', 'items']), 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'error'   => 'Could not create quotation.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an existing quotation and replace its items.
     */
    public function update(UpdateQuotationRequest $request, Quotation $quotation)
    {
        // Using transactional approach is nice, good job
        DB::beginTransaction();

        try {
            if ($request->filled('quotation_date')) {
                $quotation->quotation_date = $request->quotation_date;
                $quotation->save();
            }

            if ($request->has('items')) {
                $quotation->items()->delete();

                $grandTotal = 0;

                foreach ($request->items as $it) {
                    // Using `round` php function in monetary values is not
                    // a good approach if taking only 2 decimal places
                    // In monetary values, it should be atleast 4 decimal places
                    // Or using number_format() is a better way to go without rounding the numerical values
                    $total = round($it['quantity'] * $it['unit_price'], 2);

                    $quotation->items()->create([
                        'product_name'    => $it['product_name'],
                        'item_description'=> $it['item_description'] ?? null,
                        'quantity'        => $it['quantity'],
                        'unit_price'      => $it['unit_price'],
                        'total_price'     => $total,
                    ]);

                    $grandTotal += $total;
                }

                $quotation->update(['grand_total' => round($grandTotal, 2)]);
            }

            DB::commit();

            return response()->json($quotation->load(['customer', 'items']));
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'error'   => 'Could not update quotation.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a quotation.
     */
    public function destroy(Quotation $quotation)
    {
        $quotation->delete();

        return response()->json(['message' => 'Quotation deleted.']);
    }

    /**
     * Send a quotation to the customer via Brevo API.
     */
    public function sendEmail(Quotation $quotation)
    {
        $quotation->load(['customer', 'items']);

        if (! $quotation->customer || ! $quotation->customer->email) {
            return response()->json(['error' => 'Customer or email not found.'], 422);
        }

        /**
         * Interactions with APIs should be done in a separate class library eg. creating App\Libraries\Brevo
         * 
         * Directly adding .env variables to controllers are okay but highly discouraged
         * it must be added to a configuration file eg. config/brevo.php
         * This fails when the application cache is not updated
         */

        try {
            $config = Configuration::getDefaultConfiguration()
                ->setApiKey('api-key', env('BREVO_API_KEY'));

            $apiInstance = new TransactionalEmailsApi(null, $config);

            // Adding a content to a separate view is very good.
            $htmlContent = view('emails.quotation', [
                'quotation' => $quotation,
                'customer'  => $quotation->customer,
                'items'     => $quotation->items,
            ])->render();

            $sendSmtpEmail = new SendSmtpEmail([
                'subject'     => 'Your Quotation from AND I QUOTE',
                'sender'      => [
                    'name'  => env('BREVO_SENDER_NAME'),
                    'email' => env('BREVO_SENDER_EMAIL'),
                ],
                'to'          => [[
                    'email' => $quotation->customer->email,
                    'name'  => $quotation->customer->name,
                ]],
                'htmlContent' => $htmlContent,
            ]);

            $apiInstance->sendTransacEmail($sendSmtpEmail);

            return response()->json(['message' => 'Email sent to customer via Brevo API.']);
        } catch (\Throwable $e) {

            // Suggestion:
            // These fallback errors must be added to logs and errors must be specific coming from the actual API response
            // Adding these to logs helps in better and faster resolution
            return response()->json([
                'error'   => 'Failed to send email via Brevo API',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
