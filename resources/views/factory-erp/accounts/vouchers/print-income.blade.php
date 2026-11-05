<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Receipt - {{ $voucher->voucher_no }}</title>
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
            opacity: 0.20;
        }
        .watermark-img {
            max-width: 100%;
            max-height: 420px;
            object-fit: contain;
            mix-blend-mode: multiply;
        }
        /* Inner Content on top of watermark */
        .voucher-content {
            position: relative;
            z-index: 1;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 16px;
            margin-bottom: 18px;
        }
        .header-brand {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .header-logo img {
            max-height: 56px;
            max-width: 130px;
            object-fit: contain;
            display: block;
        }
        .company-info h1 {
            margin: 0;
            font-size: 21px;
            text-transform: uppercase;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.5px;
        }
        .company-info p {
            margin: 3px 0 0 0;
            font-size: 11px;
            color: #475569;
        }
        .voucher-badge {
            text-align: right;
            flex-shrink: 0;
        }
        .voucher-badge span {
            display: inline-block;
            background: #047857;
            color: #fff;
            font-weight: 800;
            font-size: 12px;
            padding: 5px 14px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
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
            background: rgba(236, 253, 245, 0.7);
            border: 1px solid #a7f3d0;
            padding: 14px 20px;
            border-radius: 6px;
            margin-bottom: 35px;
        }
        .amount-words {
            font-size: 12px;
            font-weight: 600;
            color: #065f46;
        }
        .amount-num {
            font-size: 22px;
            font-weight: 900;
            color: #047857;
            font-family: monospace;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
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
            background: #047857;
            color: #fff;
        }
        .btn-print:hover {
            background: #065f46;
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
                opacity: 0.20 !important;
            }
            .watermark-img {
                mix-blend-mode: multiply !important;
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
                background: rgba(236, 253, 245, 0.7) !important;
            }
        }
    </style>
</head>
<body>

    @php
        $company = $company ?? ($globalCompany ?? \App\Models\CompanySetting::first());
        $watermarkSrc = !empty($company->print_logo) && file_exists(public_path('storage/' . $company->print_logo))
            ? asset('storage/' . $company->print_logo)
            : (!empty($company->logo) && file_exists(public_path('storage/' . $company->logo)) ? asset('storage/' . $company->logo) : null);

        $headerLogoSrc = !empty($company->logo) && file_exists(public_path('storage/' . $company->logo))
            ? asset('storage/' . $company->logo)
            : (!empty($company->print_logo) && file_exists(public_path('storage/' . $company->print_logo)) ? asset('storage/' . $company->print_logo) : null);
    @endphp

    <div class="no-print">
        <a href="{{ route('factory.accounts.income.index') }}" class="btn btn-back">
            ← Back to Incomes
        </a>
        <button class="btn btn-print" onclick="window.print()">
            🖨️ Print Money Receipt
        </button>
    </div>

    <div class="voucher-box">
        <!-- Background Watermark / Jolchap (জলছাপ) -->
        @if($watermarkSrc)
        <div class="watermark-container">
            <img src="{{ $watermarkSrc }}" alt="Company Watermark" class="watermark-img">
        </div>
        @endif

        <div class="voucher-content">
            <!-- Header -->
            <div class="header">
                <div class="header-brand">
                    @if($headerLogoSrc)
                    <div class="header-logo">
                        <img src="{{ $headerLogoSrc }}" alt="Logo">
                    </div>
                    @endif
                    <div class="company-info">
                        <h1>{{ $company->company_name ?? 'Turkey Bangla Garments Ltd' }}</h1>
                        <p>{{ $company->address ?? 'Tongi Industrial Area, Gazipur, Dhaka' }}</p>
                        <p>Phone: {{ $company->phone ?? '+880 1712-345678' }} | Email: {{ $company->email ?? 'accounts@turkeybangla.com' }}</p>
                    </div>
                </div>
                <div class="voucher-badge">
                    <span>Money Receipt</span>
                    <p style="margin: 6px 0 0 0; font-family: monospace; font-weight: 800; font-size: 13px;">{{ $voucher->voucher_no }}</p>
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
                    <strong>Deposit To A/C:</strong> {{ $voucher->debitAccount?->name ?? 'Cash in Hand' }}
                </div>
                <div class="meta-item">
                    <strong>Ref / Cheque No:</strong> {{ $voucher->reference_no ?: 'N/A' }}
                </div>
            </div>

            <!-- Details Table -->
            <table class="details-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">SL</th>
                        <th>Particulars / Income Head</th>
                        <th>Received From</th>
                        <th style="text-align: right; width: 150px;">Amount (BDT)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>
                            <strong>{{ $voucher->creditAccount?->name ?? 'Income Account' }}</strong>
                            @if($voucher->notes)
                            <div style="color: #64748b; font-size: 11px; margin-top: 3px;">{{ $voucher->notes }}</div>
                            @endif
                        </td>
                        <td>
                            {{ $voucher->party_name ?: ($voucher->customer?->company_name ?: 'Valued Client') }}
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
                    <span>Received with thanks the amount of BDT:</span><br>
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
                <div class="sign-line">Received By</div>
                <div class="sign-line">Prepared By</div>
                <div class="sign-line">Checked By</div>
                <div class="sign-line">Authorised Signatory</div>
            </div>
        </div>
    </div>

</body>
</html>
