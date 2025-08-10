<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\QuotationItemController;


Route::prefix('v1')->group(function () {
    // Customers
    Route::get('customers/search', [CustomerController::class, 'search']); //  Must come first
    Route::get('customers', [CustomerController::class, 'index']);
    Route::get('customers/{customer}', [CustomerController::class, 'show']);
    Route::post('customers', [CustomerController::class, 'store']);
    Route::put('customers/{customer}', [CustomerController::class, 'update']);
    Route::delete('customers/{customer}', [CustomerController::class, 'destroy']);

    // Quotations
    Route::get('quotations', [QuotationController::class, 'index']);
    Route::get('quotations/{quotation}', [QuotationController::class, 'show']);
    Route::post('quotations', [QuotationController::class, 'store']);
    Route::put('quotations/{quotation}', [QuotationController::class, 'update']);
    Route::delete('quotations/{quotation}', [QuotationController::class, 'destroy']);

    // Send quotation email
    Route::post('quotations/{quotation}/send-email', [QuotationController::class, 'sendEmail']);

    // Quotation items (optional direct endpoints)
    Route::apiResource('quotation-items', QuotationItemController::class)
        ->only(['index', 'show', 'update', 'destroy']);
});
