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

        try {
            $config = Configuration::getDefaultConfiguration()
                ->setApiKey('api-key', env('BREVO_API_KEY'));

            $apiInstance = new TransactionalEmailsApi(null, $config);

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
            return response()->json([
                'error'   => 'Failed to send email via Brevo API',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
