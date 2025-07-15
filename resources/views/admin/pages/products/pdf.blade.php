<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Products List</title>
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
        th, td {
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
    <h1>Products List</h1>
    
    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>SKU</th>
                <th>Name</th>
                <th>Category</th>
                <th>Unit</th>
                <th class="text-right">Price</th>
                <th class="text-center">Stock</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $key => $product)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td>{{ $product->sku }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category->name ?? '-' }}</td>
                <td>{{ $product->unit->name ?? '-' }}</td>
                <td class="text-right">{{ number_format($product->price, 0, ',', '.') }}</td>
                <td class="text-center">{{ $product->stock }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        Generated on {{ date('Y-m-d H:i:s') }}
    </div>
</body>
</html>
