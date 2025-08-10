@component('mail::message')
# Quotation #{{ $quotation->id }}

Hello {{ $customer->name }},

Thank you for your interest. Here is your quotation dated {{ $quotation->quotation_date->toDateString() }}.

@component('mail::table')
| Product | Description | Qty | Unit | Subtotal |
|--------|-------------:|:---:|-----:|--------:|
@foreach($items as $it)
| {{ $it->item_description }} | {{ $it->item_description }} | {{ $it->quantity }} | {{ number_format($it->unit_price,2) }} | {{ number_format($it->total_price,2) }} |
@endforeach
@endcomponent

**Grand Total:** {{ number_format($quotation->grand_total,2) }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
