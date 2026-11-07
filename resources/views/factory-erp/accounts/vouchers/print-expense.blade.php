<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debit Voucher - {{ $voucher->voucher_no }}</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 24px;
            color: #0f172a;
            background: #f8fafc;
        }
        .voucher-box {
            max-width: 820px;
            margin: 0 auto;
            border: 2px solid #0f172a;
            padding: 30px;
            position: relative;
            background: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        /* Top Left Cyan Accent Triangle (Matching Image 2) */
        .header-accent-triangle {
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 0;
            border-top: 60px solid #00adef;
            border-right: 85px solid transparent;
            z-index: 5;
        }
        /* Background Watermark / Jolchap */
        .watermark-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
            user-select: none;
            z-index: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 70%;
        }
        .watermark-img {
            max-width: 100%;
            max-height: 420px;
            object-fit: contain;
        }
        /* Inner Content on top of watermark */
        .voucher-content {
            position: relative;
            z-index: 1;
        }
        .header-center {
            text-align: center;
            margin-bottom: 8px;
        }
        .header-brand-wrap {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
        }
        .header-logo img {
            max-height: 54px;
            max-width: 130px;
            object-fit: contain;
            display: block;
        }
        .company-info {
            text-align: left;
        }
        .company-info h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.5px;
            text-transform: none;
        }
        .company-info .tagline {
            margin: 2px 0 0 0;
            font-size: 11px;
            font-style: italic;
            font-weight: bold;
            color: #475569;
        }
        .license-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2.5px solid #00adef;
            padding-bottom: 6px;
            margin-bottom: 14px;
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
        }
        .voucher-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
        }
        .voucher-badge-box {
            display: inline-block;
            background: #be123c;
            color: #fff;
            font-weight: 800;
            font-size: 12px;
            padding: 4px 14px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .voucher-no-text {
            font-family: monospace;
            font-weight: 800;
            font-size: 13px;
            color: #0f172a;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 20px;
            font-size: 12px;
            margin-bottom: 20px;
            background: rgba(248, 250, 252, 0.4);
            padding: 12px 18px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }
        .meta-item strong {
            color: #334155;
            display: inline-block;
            width: 125px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
            background: transparent;
        }
        .details-table th, .details-table td {
            border: 1px solid #cbd5e1;
            padding: 10px 14px;
        }
        .details-table th {
            background: rgba(241, 245, 249, 0.7);
            text-transform: uppercase;
            font-size: 11px;
            font-weight: 700;
            text-align: left;
            color: #1e293b;
        }
        .amount-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 241, 242, 0.7);
            border: 1px solid #fecdd3;
            padding: 14px 20px;
            border-radius: 6px;
            margin-bottom: 30px;
        }
        .amount-words {
            font-size: 12px;
            font-weight: 600;
            color: #9f1239;
        }
        .amount-num {
            font-size: 22px;
            font-weight: 900;
            color: #be123c;
            font-family: monospace;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 45px;
            padding-top: 10px;
            font-size: 11px;
            color: #334155;
            text-align: center;
        }
        .sign-line {
            width: 150px;
            border-top: 1px dashed #64748b;
            padding-top: 6px;
            font-weight: 600;
        }
        /* Footer matching Image 2 */
        .voucher-footer {
            border-top: 2.5px solid #00adef;
            margin-top: 35px;
            padding-top: 10px;
            font-size: 10px;
            color: #1e293b;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .voucher-footer .left-col {
            line-height: 1.5;
        }
        .voucher-footer .right-col {
            text-align: right;
            line-height: 1.5;
            max-width: 55%;
        }
        .voucher-footer .cyan-label {
            color: #00adef;
            font-weight: bold;
        }
        .no-print {
            max-width: 820px;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            border-radius: 6px;
            font-weight: 700;
            cursor: pointer;
            font-size: 13px;
            text-decoration: none;
            border: none;
        }
        .btn-print {
            background: #be123c;
            color: #fff;
        }
        .btn-print:hover {
            background: #9f1239;
        }
        .btn-back {
            background: #64748b;
            color: #fff;
        }
        .btn-back:hover {
            background: #475569;
        }
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            body {
                padding: 0;
                background: #fff;
            }
            .no-print {
                display: none !important;
            }
            .voucher-box {
                border: 1.5px solid #0f172a !important;
                box-shadow: none !important;
                margin: 0 auto !important;
                padding: 25px 30px !important;
                page-break-inside: avoid;
            }
            .watermark-container {
                display: flex !important;
            }
            .watermark-img {
                display: block !important;
            }
            .meta-grid {
                background: rgba(248, 250, 252, 0.4) !important;
            }
            .details-table {
                background: transparent !important;
            }
            .details-table th {
                background: rgba(241, 245, 249, 0.7) !important;
            }
            .amount-box {
                background: rgba(255, 241, 242, 0.7) !important;
            }
        }
    </style>
</head>
<body>

    @php
        $company = $company ?? ($globalCompany ?? \App\Models\CompanySetting::first());

        // Header Logo Base64
        $headerLogoSrc = null;
        if (!empty($company?->logo)) {
            $logoFile = public_path('storage/' . $company->logo);
            if (file_exists($logoFile)) {
                $ext = strtolower(pathinfo($logoFile, PATHINFO_EXTENSION));
                $mime = ($ext === 'png') ? 'image/png' : (($ext === 'svg') ? 'image/svg+xml' : 'image/jpeg');
                $headerLogoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoFile));
            }
        }

        // Watermark Logo Base64 (Bound to /factory/settings)
        $watermarkSrc = null;
        $wmFile = !empty($company?->print_logo) && file_exists(public_path('storage/' . $company->print_logo))
            ? public_path('storage/' . $company->print_logo)
            : (!empty($company?->logo) && file_exists(public_path('storage/' . $company->logo)) ? public_path('storage/' . $company->logo) : null);

        if ($wmFile && file_exists($wmFile)) {
            $ext = strtolower(pathinfo($wmFile, PATHINFO_EXTENSION));
            $mime = ($ext === 'png') ? 'image/png' : (($ext === 'svg') ? 'image/svg+xml' : 'image/jpeg');
            $watermarkSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($wmFile));
        }
    @endphp

    <div class="no-print">
        <a href="{{ route('factory.accounts.expense.index') }}" class="btn btn-back">
            ← Back to Expenses
        </a>
        <button class="btn btn-print" onclick="window.print()">
            🖨️ Print Debit Voucher
        </button>
    </div>

    <div class="voucher-box">
        <!-- Top Left Cyan Geometric Accent Triangle (Exact Image 2 Match) -->
        <div class="header-accent-triangle" aria-hidden="true"></div>

        <!-- Background Watermark (Bound to /factory/settings) -->
        @if($watermarkSrc)
        <div class="watermark-container">
            <img src="{{ $watermarkSrc }}" alt="Company Watermark" class="watermark-img">
        </div>
        @endif

        <div class="voucher-content">
            <!-- Header Brand (Image 2 Design) -->
            <div class="header-center">
                <div class="header-brand-wrap">
                    @if($headerLogoSrc)
                    <div class="header-logo">
                        <img src="{{ $headerLogoSrc }}" alt="Logo">
                    </div>
                    @endif
                    <div class="company-info">
                        <h1>{{ $company->company_name ?? 'Turkey Bangla Consumer and Food' }}</h1>
                        <p class="tagline">{{ $company->tagline ?? 'Quality is our Priority' }}</p>
                    </div>
                </div>
            </div>

            <!-- Subheader: Trade Licence & TIN No. with Cyan Divider Line -->
            <div class="license-bar">
                <span>Trade Licence No. {{ $company->trade_licence ?? '019268' }}</span>
                <span>TIN No. {{ $company->tin_no ?? '369123229836' }}</span>
            </div>

            <div class="voucher-header-row">
                <div>
                    <span class="voucher-badge-box">Debit Voucher</span>
                </div>
                <div class="voucher-no-text">
                    Voucher No: <span style="color: #be123c;">{{ $voucher->voucher_no }}</span>
                </div>
            </div>

            <!-- Meta Grid -->
            <div class="meta-grid">
                <div class="meta-item">
                    <strong>Voucher Date:</strong> {{ $voucher->voucher_date->format('d F, Y') }}
                </div>
                <div class="meta-item">
                    <strong>Payment Method:</strong> {{ $voucher->payment_method_label }}
                </div>
                <div class="meta-item">
                    <strong>Paid From A/C:</strong> {{ $voucher->creditAccount?->name ?? 'Cash in Hand' }}
                </div>
                <div class="meta-item">
                    <strong>Bill / Ref No:</strong> {{ $voucher->reference_no ?: 'N/A' }}
                </div>
            </div>

            <!-- Details Table -->
            <table class="details-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">SL</th>
                        <th>Head of Accounts (Debit)</th>
                        <th>Paid To / Payee</th>
                        <th style="text-align: right; width: 150px;">Amount (BDT)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>
                            <strong>{{ $voucher->debitAccount?->name ?? 'Expense Account' }}</strong>
                            @if($voucher->notes)
                            <div style="color: #64748b; font-size: 11px; margin-top: 3px;">{{ $voucher->notes }}</div>
                            @endif
                        </td>
                        <td>
                            {{ $voucher->party_name ?: ($voucher->supplier?->name ?: ($voucher->employee?->name ?: 'Payee')) }}
                        </td>
                        <td style="text-align: right; font-weight: 800; font-family: monospace; font-size: 14px;">
                            ৳ {{ number_format($voucher->amount, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Amount Box -->
            <div class="amount-box">
                <div class="amount-words">
                    <span>Paid the amount of BDT:</span><br>
                    <span style="font-style: italic; font-weight: bold; color: #0f172a;">
                        {{ number_format($voucher->amount, 2) }} Taka Only
                    </span>
                </div>
                <div class="amount-num">
                    ৳ {{ number_format($voucher->amount, 2) }}
                </div>
            </div>

            <!-- Signatures -->
            <div class="signatures">
                <div class="sign-line">Payee's Signature</div>
                <div class="sign-line">Prepared By</div>
                <div class="sign-line">Checked By</div>
                <div class="sign-line">Managing Director / Partner</div>
            </div>

            <!-- Footer (Image 2 Design: Cyan Top Border, 3 Columns) -->
            <div class="voucher-footer">
                <div class="left-col">
                    <div><span class="cyan-label">Email:</span> {{ $company->email ?? 'turkiasugarbd@gmail.com' }}</div>
                    <div><span class="cyan-label">Phone:</span> {{ $company->phone ?? '+8801891153903, +8801718521488' }}</div>
                    <div><span class="cyan-label">Web:</span> {{ $company->website ?? 'Turkiabazar.com' }}</div>
                </div>
                <div class="right-col" style="display: flex; align-items: center; justify-content: flex-end; gap: 12px;">
                    <div>
                        <div><strong>Head Office:</strong> {{ $company->address ?? 'Zawad House, House# 73 (1st Floor), Road# 14, Sector# 13, Uttara, Dhaka-1230, Bangladesh.' }}</div>
                        <div style="margin-top: 3px;"><strong>Hotline:</strong> <span style="color: #00adef; font-weight: bold;">{{ $company->hotline ?? '+8801891-153903' }}</span></div>
                    </div>
                    @if(file_exists(public_path('images/company_qr.png')))
                    <img src="{{ asset('images/company_qr.png') }}" alt="QR" style="width: 46px; height: 46px; object-fit: contain; border: 1px solid #cbd5e1; border-radius: 4px; padding: 2px; background: #fff;">
                    @endif
                </div>
            </div>
        </div>
    </div>

</body>
</html>
