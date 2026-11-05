<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Orders Report</title>
    <style>
        @page {
            margin: 25px 25px 75px 25px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 11px;
            line-height: 1.4;
            padding: 0;
            margin: 0;
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
            opacity: 0.22;
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
            background-color: rgba(248, 250, 252, 0.4);
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
    $totAmount = $orders->sum('total_amount');
    $totPaid = $orders->sum('paid_amount');
    $totDue = $orders->sum('due_amount');
@endphp

    <!-- Official PDF Header Matching Image 2 -->
    @include('factory-erp.common.pdf-header')

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
        <tr>
            <td style="text-align: left; vertical-align: middle;">
                <span class="doc-badge" style="background-color: #00adef;">ORDERS SUMMARY REPORT</span>
            </td>
            <td style="text-align: right; vertical-align: middle; font-size: 11px;">
                Range: {{ ucwords(str_replace('_', ' ', $range)) }} | Generated: {{ date('d M, Y') }}
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

    <!-- Official PDF Footer Matching Image 2 -->
    @include('factory-erp.common.pdf-footer')

</body>
</html>

