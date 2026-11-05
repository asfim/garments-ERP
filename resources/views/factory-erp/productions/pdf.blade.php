<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Productions Report</title>
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
            background-color: #7c3aed;
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
        .text-center {
            text-align: center;
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
    $totQty = $productions->sum('quantity');
@endphp

    <!-- Official PDF Header Matching Image 2 -->
    @include('factory-erp.common.pdf-header')

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
        <tr>
            <td style="text-align: left; vertical-align: middle;">
                <span class="doc-badge" style="background-color: #00adef;">PRODUCTION FLOOR REPORT</span>
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
                <th>Batch #</th>
                <th>Date</th>
                <th>Product Name</th>
                <th class="text-center">Unit</th>
                <th class="text-right">Produced Qty</th>
                <th>Operator / Supervisor</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productions as $idx => $prod)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td style="font-weight: bold; color: #7c3aed;">{{ $prod->batch_number ?? ('BATCH-' . $prod->id) }}</td>
                <td>{{ date('d M, Y', strtotime($prod->production_date)) }}</td>
                <td style="font-weight: bold; color: #0f172a;">{{ $prod->product->name ?? 'N/A' }}</td>
                <td class="text-center">{{ $prod->unit->short_code ?? ($prod->product->unit->short_code ?? 'Pcs') }}</td>
                <td class="text-right" style="font-weight: bold; color: #059669;">+ {{ number_format($prod->quantity) }}</td>
                <td>{{ $prod->operator_name ?? 'N/A' }}</td>
                <td>{{ $prod->notes ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 20px;">No production records found for this period.</td>
            </tr>
            @endforelse
        </tbody>
        @if($productions->count() > 0)
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="5" class="text-right">TOTAL PRODUCED QUANTITY:</td>
                <td class="text-right" style="color: #059669;">{{ number_format($totQty) }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <!-- Official PDF Footer Matching Image 2 -->
    @include('factory-erp.common.pdf-footer')

</body>
</html>

