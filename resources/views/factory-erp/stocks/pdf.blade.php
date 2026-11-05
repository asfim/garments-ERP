<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $tab === 'balance' ? 'Current Stock Balance Report' : 'Stock Movement History Report' }}</title>
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
            background-color: #d97706;
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
    $company = $company ?? ($globalCompany ?? \App\Models\CompanySetting::first());
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
                <span class="doc-badge">{{ $tab === 'balance' ? 'WAREHOUSE STOCK BALANCE' : 'STOCK ADJUSTMENT LOGS' }}</span>
                <p class="date-text">Range: {{ ucwords(str_replace('_', ' ', $range)) }} | Generated: {{ date('d M, Y') }}</p>
            </td>
        </tr>
    </table>

    @if($tab === 'balance')
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">#</th>
                <th>Product Name</th>
                <th class="text-center">Unit</th>
                <th class="text-right">Total Produced (+)</th>
                <th class="text-right">Total Delivered (-)</th>
                <th class="text-right">Manual Adjusted</th>
                <th class="text-right">Available Stock</th>
                <th class="text-right">Sell Price</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $idx => $prod)
            @php
                $stk = $prod->stock;
                $avail = $stk ? $stk->current_stock : 0;
            @endphp
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td style="font-weight: bold; color: #0f172a;">{{ $prod->name }}</td>
                <td class="text-center">{{ $prod->unit->short_code ?? 'Pcs' }}</td>
                <td class="text-right" style="color: #059669;">{{ number_format($stk->total_produced ?? 0) }}</td>
                <td class="text-right" style="color: #e11d48;">{{ number_format($stk->total_delivered ?? 0) }}</td>
                <td class="text-right">{{ ($stk && $stk->manual_adjustment > 0 ? '+' : '') . number_format($stk->manual_adjustment ?? 0) }}</td>
                <td class="text-right" style="font-weight: bold; font-size: 11px; color: {{ $avail > 0 ? '#0284c7' : '#94a3b8' }};">
                    {{ number_format($avail) }}
                </td>
                <td class="text-right">৳ {{ number_format($prod->sell_price, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 20px;">No product stock data found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @else
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">#</th>
                <th>Date</th>
                <th>Product</th>
                <th class="text-center">Type</th>
                <th class="text-right">Quantity</th>
                <th>Reason / Reason Description</th>
                <th>Created By</th>
            </tr>
        </thead>
        <tbody>
            @forelse($adjustments as $idx => $adj)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>{{ date('d M, Y', strtotime($adj->adjustment_date)) }}</td>
                <td style="font-weight: bold;">{{ $adj->product->name ?? 'N/A' }}</td>
                <td class="text-center" style="font-weight: bold; color: {{ $adj->type === 'addition' ? '#059669' : '#e11d48' }};">
                    {{ strtoupper($adj->type) }}
                </td>
                <td class="text-right" style="font-weight: bold;">{{ ($adj->type === 'addition' ? '+' : '-') . number_format($adj->quantity) }}</td>
                <td>{{ $adj->reason }}</td>
                <td>{{ $adj->created_by ?? 'System' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px;">No adjustment history records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @endif

    <div class="footer-note">
        Generated by TBG ERP on {{ date('d M, Y h:i A') }}.
    </div>
</body>
</html>

