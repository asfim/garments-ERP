<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Orders Report</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 11px;
            line-height: 1.4;
            padding: 15px;
        }
        .watermark {
            position: fixed;
            top: 25%;
            left: 15%;
            width: 70%;
            height: 50%;
            text-align: center;
            opacity: 0.08;
            z-index: -1000;
        }
        .watermark img {
            max-width: 400px;
            max-height: 400px;
            opacity: 0.08;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0284c7;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .company-title {
            font-size: 18px;
            font-weight: bold;
            color: #0369a1;
            margin: 0;
        }
        .company-info {
            font-size: 10px;
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
            padding: 5px 12px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 4px;
            display: inline-block;
        }
        .date-text {
            font-size: 9px;
            color: #94a3b8;
            margin-top: 4px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            text-align: left;
            padding: 6px 8px;
            font-size: 10px;
            text-transform: uppercase;
        }
        .data-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-right {
            text-align: right;
        }
        .footer-note {
            margin-top: 25px;
            border-top: 1px dashed #cbd5e1;
            padding-top: 8px;
            font-size: 9px;
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

    $totAmount = $orders->sum('total_amount');
    $totPaid = $orders->sum('paid_amount');
    $totDue = $orders->sum('due_amount');
@endphp

    @if($watermarkBase64)
    <div class="watermark">
        <img src="{{ $watermarkBase64 }}" alt="Watermark">
    </div>
    @endif

    <table class="header-table">
        <tr>
            @if($logoBase64)
            <td style="width: 55px; vertical-align: top; padding-right: 10px;">
                <img src="{{ $logoBase64 }}" style="max-width: 50px; max-height: 50px;" alt="Logo">
            </td>
            @endif
            <td style="vertical-align: top;">
                <h1 class="company-title">{{ $company->company_name ?? 'Turkey Bangla Garments Ltd' }}</h1>
                <p class="company-info">{{ $company->address ?? 'Tongi Industrial Area, Gazipur, Dhaka' }}</p>
                <p class="company-info">Phone: {{ $company->phone ?? '+880 1712-345678' }} | Email: {{ $company->email ?? 'info@turkeybangla.com' }}</p>
            </td>
            <td class="doc-title" style="width: 35%;">
                <span class="doc-badge">ORDERS SUMMARY REPORT</span>
                <p class="date-text">Range: {{ ucwords(str_replace('_', ' ', $range)) }} | Generated: {{ date('d M, Y') }}</p>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">#</th>
                <th>Order #</th>
                <th>Order Date</th>
                <th>Customer</th>
                <th>Items Count</th>
                <th class="text-right">Total (৳)</th>
                <th class="text-right">Paid (৳)</th>
                <th class="text-right">Due (৳)</th>
                <th>Delivery</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $idx => $ord)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td style="font-weight: bold; color: #0284c7;">{{ $ord->order_number }}</td>
                <td>{{ date('d M, Y', strtotime($ord->order_date)) }}</td>
                <td style="font-weight: bold;">{{ $ord->customer->company_name ?? 'N/A' }}</td>
                <td>{{ $ord->items->count() }} items</td>
                <td class="text-right" style="font-weight: bold;">৳ {{ number_format($ord->total_amount, 2) }}</td>
                <td class="text-right" style="color: #059669;">৳ {{ number_format($ord->paid_amount, 2) }}</td>
                <td class="text-right" style="color: #e11d48; font-weight: bold;">৳ {{ number_format($ord->due_amount, 2) }}</td>
                <td>{{ ucfirst($ord->delivery_status) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center; padding: 20px;">No orders recorded for this period.</td>
            </tr>
            @endforelse
        </tbody>
        @if($orders->count() > 0)
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="5" class="text-right">GRAND TOTALS:</td>
                <td class="text-right">৳ {{ number_format($totAmount, 2) }}</td>
                <td class="text-right" style="color: #059669;">৳ {{ number_format($totPaid, 2) }}</td>
                <td class="text-right" style="color: #e11d48;">৳ {{ number_format($totDue, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer-note">
        Total Orders: {{ $orders->count() }} | Generated by TBG ERP on {{ date('d M, Y h:i A') }}.
    </div>
</body>
</html>
