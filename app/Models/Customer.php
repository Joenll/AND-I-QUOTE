<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    // Allow mass assignment for these columns
    protected $fillable = [
        'name',
        'date_of_birth',
        'address',
        'email',
        'contact'
    ];

    /**
     * A customer can have many quotations.
     */
    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }
}
