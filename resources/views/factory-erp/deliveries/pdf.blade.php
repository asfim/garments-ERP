<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $tab === 'orders' ? 'Pending Shipments Report' : 'Delivery Challans Report' }}</title>
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
            background-color: #059669;
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

    <!-- Official PDF Header Matching Image 2 -->
    @include('factory-erp.common.pdf-header')

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
        <tr>
            <td style="text-align: left; vertical-align: middle;">
                <span class="doc-badge" style="background-color: #00adef;">{{ $tab === 'orders' ? 'PENDING SHIPMENTS' : 'DELIVERY CHALLANS' }}</span>
            </td>
            <td style="text-align: right; vertical-align: middle; font-size: 11px;">
                Range: {{ ucwords(str_replace('_', ' ', $range)) }} | Generated: {{ date('d M, Y') }}
            </td>
        </tr>
    </table>

    @if($tab === 'orders')
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">#</th>
                <th>Order #</th>
                <th>Order Date</th>
                <th>Customer</th>
                <th>Delivery Status</th>
                <th class="text-right">Total Amount (৳)</th>
                <th class="text-right">Due Amount (৳)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $idx => $ord)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td style="font-weight: bold; color: #0284c7;">{{ $ord->order_number }}</td>
                <td>{{ date('d M, Y', strtotime($ord->order_date)) }}</td>
                <td style="font-weight: bold;">{{ $ord->customer->company_name ?? 'N/A' }}</td>
                <td style="color: #d97706; font-weight: bold;">{{ ucfirst($ord->delivery_status) }}</td>
                <td class="text-right">৳ {{ number_format($ord->total_amount, 2) }}</td>
                <td class="text-right" style="color: #e11d48; font-weight: bold;">৳ {{ number_format($ord->due_amount, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px;">No pending shipments found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @else
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">#</th>
                <th>Challan #</th>
                <th>Delivery Date</th>
                <th>Order #</th>
                <th>Customer</th>
                <th>Receiver Name / Contact</th>
                <th>Vehicle / Transporter</th>
            </tr>
        </thead>
        <tbody>
            @forelse($deliveries as $idx => $del)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td style="font-weight: bold; color: #059669;">{{ $del->challan_number }}</td>
                <td>{{ date('d M, Y', strtotime($del->delivery_date)) }}</td>
                <td>{{ $del->order->order_number ?? 'N/A' }}</td>
                <td style="font-weight: bold;">{{ $del->order->customer->company_name ?? 'N/A' }}</td>
                <td>{{ $del->receiver_name ?? 'N/A' }} ({{ $del->receiver_phone ?? 'N/A' }})</td>
                <td>{{ $del->vehicle_number ?? 'N/A' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px;">No delivery challans recorded for this period.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @endif

    <!-- Official PDF Footer Matching Image 2 -->
    @include('factory-erp.common.pdf-footer')

</body>
</html>

