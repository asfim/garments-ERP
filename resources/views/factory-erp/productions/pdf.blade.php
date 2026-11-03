<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Productions Report</title>
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
            background-color: #f8fafc;
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

    $totQty = $productions->sum('quantity');
@endphp

    @if($watermarkBase64)
    <div class="watermark">
        <img src="{{ $watermarkBase64 }}" alt="Watermark">
    </div>
    @endif

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
                <span class="doc-badge">PRODUCTION FLOOR REPORT</span>
                <p class="date-text">Range: {{ ucwords(str_replace('_', ' ', $range)) }} | Generated: {{ date('d M, Y') }}</p>
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

    <div class="footer-note">
        Total Records: {{ $productions->count() }} | Generated by TBG ERP on {{ date('d M, Y h:i A') }}.
    </div>
</body>
</html>

