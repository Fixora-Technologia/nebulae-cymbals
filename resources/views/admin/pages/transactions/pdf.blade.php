<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        h1 {
            text-align: center;
            font-size: 18px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-style: italic;
        }
    </style>
</head>

<body>
    <h1>{{ $title }}</h1>

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Kode</th>
                <th>Tanggal</th>
                <th>Tipe</th>
                @if (!$transactions[0]->transaction_type == 'in')
                    <th>Customer</th>
                @endif
                <th>Created By</th>
                <th>Qty</th>
                <th class="text-right">Total Nilai</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $key => $transaction)
                <tr>
                    <td class="text-center">{{ $key + 1 }}</td>
                    <td>{{ $transaction->transaction_code }}</td>
                    <td>{{ $transaction->created_at ? date('d F Y', strtotime($transaction->created_at)) : '-' }}</td>
                    <td>{{ $transaction->transaction_type == 'in' ? 'Stock In' : 'Sales' }}</td>
                    @if (!$transaction->transaction_type == 'in')
                        <td>{{ $transaction->customer->name ?? '-' }}</td>
                    @endif
                    <td>{{ $transaction->user->name ?? '-' }}</td>
                    <td>{{ $transaction->items->sum('quantity') ?? 0 }} </td>
                    <td class="text-right">{{ number_format($transaction->total_value, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ date('Y-m-d H:i:s') }}
    </div>
</body>

</html>
