<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customers List</title>
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
        .footer {
            margin-top: 20px;
            text-align: right;
            font-style: italic;
        }
    </style>
</head>
<body>
    <h1>Customers List</h1>
    
    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Name</th>
                <th>Contact</th>
                <th>Phone</th>
                <th>Address</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $key => $customer)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td>{{ $customer->name }}</td>
                <td>{{ $customer->contact ?? '-' }}</td>
                <td>{{ $customer->phone ?? '-' }}</td>
                <td>{{ $customer->address ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        Generated on {{ date('Y-m-d H:i:s') }}
    </div>
</body>
</html>
