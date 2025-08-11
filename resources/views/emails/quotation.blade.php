<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Quotation #{{ $quotation->id }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.5; color: #333;">

    <h2 style="color: #2a9d8f;">Quotation #{{ $quotation->id }}</h2>

    <p>Hello {{ $customer->name }},</p>

    <p>Thank you for your interest. Here is your quotation dated <strong>{{ $quotation->quotation_date->toDateString() }}</strong>.</p>

    <table border="1" cellpadding="8" cellspacing="0" width="100%" style="border-collapse: collapse; margin-top: 15px;">
        <thead style="background-color: #f4f4f4;">
            <tr>
                <th align="left">Product</th>
                <th align="left">Description</th>
                <th align="center">Qty</th>
                <th align="right">Unit Price</th>
                <th align="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $it)
                <tr>
                    <td>{{ $it->product_name ?? '-' }}</td>
                    <td>{{ $it->item_description }}</td>
                    <td align="center">{{ $it->quantity }}</td>
                    <td align="right">{{ number_format($it->unit_price, 2) }}</td>
                    <td align="right">{{ number_format($it->total_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top: 20px; font-size: 16px;">
        <strong>Grand Total:</strong> {{ number_format($quotation->grand_total, 2) }}
    </p>

    <p>Thanks,<br>
    {{ config('app.name') }}</p>

</body>
</html>
