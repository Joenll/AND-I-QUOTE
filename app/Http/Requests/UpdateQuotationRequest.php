<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuotationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'quotation_date' => 'sometimes|required|date|before_or_equal:today',
            'items' => 'sometimes|array|min:1',
            'items.*.product_name' => 'required_with:items|string|max:255',
            'items.*.item_description' => 'nullable|string',
            'items.*.quantity' => 'required_with:items|integer|min:1',
            'items.*.unit_price' => 'required_with:items|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'quotation_date.before_or_equal' => 'Quotation date cannot be in the future.',
            'items.min' => 'You must have at least one item.',
            'items.*.product_name.required_with' => 'Each item must have a product name.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
            'items.*.unit_price.min' => 'Unit price cannot be negative.',
        ];
    }
}
