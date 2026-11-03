<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 12px;
            line-height: 1.4;
            padding: 20px;
        }
        .watermark {
            position: fixed;
            top: 250px;
            left: 0;
            width: 100%;
            text-align: center;
            z-index: -1;
        }
        .watermark img {
            width: 440px;
            max-width: 80%;
            height: auto;
            opacity: 0.12;
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
            font-size: 14px;
            font-weight: bold;
            border-radius: 4px;
            display: inline-block;
        }
        .bill-table {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 10px;
        }
        .bill-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #94a3b8;
            font-weight: bold;
        }
        .bill-name {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            margin: 2px 0;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .items-table th {
            background-color: #0f172a;
            color: #ffffff;
            text-align: left;
            padding: 8px 10px;
            font-size: 11px;
            text-transform: uppercase;
        }
        .items-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        .items-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals-table {
            width: 45%;
            margin-left: auto;
            margin-top: 20px;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 6px 8px;
        }
        .total-due {
            font-size: 13px;
            font-weight: bold;
            color: #e11d48;
            border-top: 2px solid #0284c7;
        }
        .footer-note {
            margin-top: 40px;
            border-top: 1px dashed #cbd5e1;
            padding-top: 10px;
            font-size: 10px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>
@php
    $watermarkBase64 = null;
    $watermarkFile = !empty($company->print_logo) && file_exists(public_path('storage/' . $company->print_logo))
        ? public_path('storage/' . $company->print_logo)
        : (!empty($company->logo) && file_exists(public_path('storage/' . $company->logo)) ? public_path('storage/' . $company->logo) : null);

    if ($watermarkFile && file_exists($watermarkFile)) {
        $type = pathinfo($watermarkFile, PATHINFO_EXTENSION);
        $data = file_get_contents($watermarkFile);
        $watermarkBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    $logoBase64 = null;
    if (!empty($company->logo) && file_exists(public_path('storage/' . $company->logo))) {
        $logoFile = public_path('storage/' . $company->logo);
        $type = pathinfo($logoFile, PATHINFO_EXTENSION);
        $data = file_get_contents($logoFile);
        $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
@endphp

    @if($watermarkBase64)
    <div class="watermark">
        <img src="{{ $watermarkBase64 }}" alt="Watermark">
    </div>
    @endif

    <!-- Header bound to Company Settings -->
    <table class="header-table">
        <tr>
            @if($logoBase64)
            <td style="width: 175px; vertical-align: top; padding-right: 15px;">
                <img src="{{ $logoBase64 }}" style="max-width: 165px; max-height: 65px; width: auto; height: auto;" alt="Logo">
            </td>
            @endif
            <td style="vertical-align: top;">
                <h1 class="company-title">{{ $company->company_name ?? 'Turkey Bangla Garments Ltd' }}</h1>
                <p class="company-info">{{ $company->address ?? 'Tongi Industrial Area, Gazipur, Dhaka' }}</p>
                <p class="company-info">Phone: {{ $company->phone ?? '+880 1712-345678' }} | Email: {{ $company->email ?? 'info@turkeybangla.com' }}</p>
            </td>
            <td class="doc-title" style="width: 30%;">
                <span class="doc-badge">TAX INVOICE</span>
                <p style="margin: 4px 0 0 0; font-weight: bold; font-size: 13px;"># {{ $order->order_number }}</p>
                <p style="margin: 2px 0 0 0; font-size: 10px; color: #64748b;">Date: {{ date('d M, Y', strtotime($order->order_date)) }}</p>
            </td>
        </tr>
    </table>

    <!-- Billed to Client -->
    <table class="bill-table">
        <tr>
            <td style="width: 60%;">
                <span class="bill-label">Billed To (Customer):</span>
                <div class="bill-name">{{ $order->customer->company_name }}</div>
                <div style="font-size: 11px; color: #475569;">{{ $order->customer->address }}</div>
                <div style="font-size: 11px; color: #0284c7;">Phone: {{ $order->customer->phone }} | Attn: {{ $order->customer->contact_person ?? 'Manager' }}</div>
            </td>
            <td style="width: 40%; text-align: right; vertical-align: top;">
                <span class="bill-label">Delivery Status:</span>
                <div style="font-weight: bold; font-size: 12px; text-transform: uppercase; color: {{ $order->delivery_status == 'delivered' ? '#16a34a' : '#d97706' }};">
                    {{ $order->delivery_status }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 8%;" class="text-center">#</th>
                <th style="width: 44%;">Item Description</th>
                <th style="width: 12%;" class="text-center">Unit</th>
                <th style="width: 12%;" class="text-center">Order Qty</th>
                <th style="width: 12%;" class="text-right">Unit Price</th>
                <th style="width: 12%;" class="text-right">Total (৳)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $idx => $item)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td><strong>{{ $item->product->name }}</strong></td>
                <td class="text-center">{{ $item->product->unit->short_code ?? 'Pcs' }}</td>
                <td class="text-center" style="font-weight: bold;">{{ number_format($item->qty) }}</td>
                <td class="text-right">৳ {{ number_format($item->sell_price, 2) }}</td>
                <td class="text-right" style="font-weight: bold;">৳ {{ number_format($item->total_price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals Table -->
    <table class="totals-table">
        <tr>
            <td><strong>Subtotal Amount:</strong></td>
            <td class="text-right">৳ {{ number_format($order->total_amount, 2) }}</td>
        </tr>
        <tr>
            <td style="color: #16a34a;"><strong>Paid / Advance:</strong></td>
            <td class="text-right" style="color: #16a34a; font-weight: bold;">৳ {{ number_format($order->paid_amount, 2) }}</td>
        </tr>
        <tr class="total-due">
            <td>Due Balance:</td>
            <td class="text-right">৳ {{ number_format($order->due_amount, 2) }}</td>
        </tr>
    </table>

    <div class="footer-note">
        Thank you for choosing {{ $company->company_name ?? 'Turkey Bangla Garments Ltd' }}. Generated on {{ date('d F Y, h:i A') }}.
    </div>

</body>
</html>

