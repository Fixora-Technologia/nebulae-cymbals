<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $transaction->transaction_code }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .invoice-header {
            margin-bottom: 30px;
        }
        .invoice-title {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .invoice-subtitle {
            text-align: center;
            font-size: 16px;
            margin-bottom: 20px;
        }
        .company-info {
            text-align: center;
            margin-bottom: 20px;
        }
        .invoice-details {
            width: 100%;
            margin-bottom: 20px;
        }
        .invoice-details td {
            vertical-align: top;
            padding: 5px;
        }
        .customer-details {
            width: 50%;
        }
        .invoice-info {
            width: 50%;
            text-align: right;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.items th, table.items td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        table.items th {
            background-color: #f2f2f2;
            text-align: left;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .total-section {
            margin-top: 20px;
            text-align: right;
        }
        .total-row {
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
        }
        .signature-section {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
            text-align: center;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #000;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body>
    <div class="invoice-header">
        <div class="invoice-title">INVOICE</div>
        <div class="invoice-subtitle">{{ $transaction->transaction_type == 'in' ? 'Stock In' : 'Sales' }}</div>
        <div class="company-info">
            <strong>Nebulae Cymbals</strong><br>
            Jl. Industri No. 123, Bandung<br>
            Phone: (022) 123-4567<br>
            Email: info@nebulaecymbals.com
        </div>
    </div>
    
    <table class="invoice-details">
        <tr>
            <td class="customer-details">
                <strong>To:</strong><br>
                {{ $transaction->customer ? $transaction->customer->name : 'Internal Stock' }}<br>
                @if($transaction->customer)
                    {{ $transaction->customer->address ?? 'No address provided' }}<br>
                    Phone: {{ $transaction->customer->phone ?? 'N/A' }}<br>
                    Contact: {{ $transaction->customer->contact ?? 'N/A' }}
                @endif
            </td>
            <td class="invoice-info">
                <strong>Invoice #:</strong> {{ $transaction->transaction_code }}<br>
                <strong>Date:</strong> {{ date('d/m/Y', strtotime($transaction->transaction_date)) }}<br>
                <strong>Created By:</strong> {{ $transaction->user->name ?? 'System' }}
            </td>
        </tr>
    </table>
    
    <table class="items">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Product</th>
                <th>SKU</th>
                <th class="text-center">Quantity</th>
                <th>Unit</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaction->items as $key => $item)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->product->sku }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td>{{ $item->product->unit->name ?? '-' }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="6" class="text-right">Total:</td>
                <td class="text-right">{{ number_format($transaction->total_value, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
    
    @if($transaction->notes)
    <div>
        <strong>Notes:</strong><br>
        {{ $transaction->notes }}
    </div>
    @endif
    
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div>Received By</div>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div>Authorized By</div>
        </div>
    </div>
    
    <div class="footer">
        <p>Thank you for your business!</p>
        <p>Generated on {{ date('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>
