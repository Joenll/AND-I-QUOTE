<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    // Allow mass assignment for these columns
   protected $fillable = [
    'quotation_id',
    'product_name',
    'item_description',
    'quantity',
    'unit_price',
    'total_price'
];

    /**
     * A quotation item belongs to a quotation.
     */
    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}
