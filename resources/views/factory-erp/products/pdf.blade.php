<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Product Price List</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 12px;
            line-height: 1.4;
            padding: 20px;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0284c7;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .company-title {
            font-size: 20px;
            font-weight: bold;
            color: #0369a1;
            margin: 0;
        }
        .company-info {
            font-size: 11px;
            color: #64748b;
            margin: 2px 0;
        }
        .doc-title {
            text-align: right;
            vertical-align: middle;
        }
        .doc-badge {
            background-color: #0284c7;
            color: #ffffff;
            padding: 6px 14px;
            font-size: 13px;
            font-weight: bold;
            border-radius: 4px;
            display: inline-block;
        }
        .date-text {
            font-size: 10px;
            color: #94a3b8;
            margin-top: 4px;
        }
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .products-table th {
            background-color: #0f172a;
            color: #ffffff;
            text-align: left;
            padding: 8px 10px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .products-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        .products-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .price-highlight {
            font-weight: bold;
            color: #0284c7;
        }
        .footer-note {
            margin-top: 30px;
            border-top: 1px dashed #cbd5e1;
            padding-top: 10px;
            font-size: 10px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Header Bound to Company Settings -->
    <table class="header-table">
        <tr>
            <td style="width: 65%;">
                <h1 class="company-title">{{ $company->company_name ?? 'Turkey Bangla Garments Ltd' }}</h1>
                <p class="company-info">{{ $company->address ?? 'Tongi Industrial Area, Gazipur, Dhaka' }}</p>
                <p class="company-info">Phone: {{ $company->phone ?? '+880 1712-345678' }} | Email: {{ $company->email ?? 'info@turkeybangla.com' }}</p>
            </td>
            <td class="doc-title" style="width: 35%;">
                <span class="doc-badge">PRODUCT PRICE LIST</span>
                <p class="date-text">Generated Date: {{ date('d M, Y') }}</p>
            </td>
        </tr>
    </table>

    <!-- Product Table with Name, Unit, Production & Sell Price -->
    <table class="products-table">
        <thead>
            <tr>
                <th style="width: 8%;" class="text-center">#SL</th>
                <th style="width: 45%;">Product Name</th>
                <th style="width: 15%;" class="text-center">Unit</th>
                <th style="width: 16%;" class="text-right">Production Cost</th>
                <th style="width: 16%;" class="text-right">Selling Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $index => $prod)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $prod->name }}</strong>
                    @if($prod->description)
                        <br><span style="font-size: 10px; color: #64748b;">{{ $prod->description }}</span>
                    @endif
                </td>
                <td class="text-center">{{ $prod->unit->name ?? 'Pcs' }} ({{ $prod->unit->short_code ?? '' }})</td>
                <td class="text-right">৳ {{ number_format($prod->production_price, 2) }}</td>
                <td class="text-right price-highlight">৳ {{ number_format($prod->sell_price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer-note">
        This is an official computer-generated document from {{ $company->company_name ?? 'Turkey Bangla Garments Ltd' }}. Generated on {{ date('d F Y, h:i A') }}.
    </div>

</body>
</html>

