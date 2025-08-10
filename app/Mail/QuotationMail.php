<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Quotation;

class QuotationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $quotation;

    public function __construct(Quotation $quotation)
    {
        $this->quotation = $quotation;
    }

    public function build()
    {
        return $this->subject('Your Quotation from AND I QUOTE')
                    ->markdown('emails.quotation')
                    ->with([
                        'quotation' => $this->quotation,
                        'customer' => $this->quotation->customer,
                        'items' => $this->quotation->items,
                    ]);
    }
}
