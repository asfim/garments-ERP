<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debit Voucher - {{ $voucher->voucher_no }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            color: #1e293b;
            background: #fff;
        }
        .voucher-box {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #0f172a;
            padding: 25px 30px;
            position: relative;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .company-info h1 {
            margin: 0;
            font-size: 22px;
            text-transform: uppercase;
            font-weight: 800;
            color: #0f172a;
        }
        .company-info p {
            margin: 3px 0 0 0;
            font-size: 11px;
            color: #475569;
        }
        .voucher-badge {
            text-align: right;
        }
        .voucher-badge span {
            display: inline-block;
            background: #be123c;
            color: #fff;
            font-weight: 800;
            font-size: 12px;
            padding: 5px 12px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            font-size: 12px;
            margin-bottom: 20px;
            background: #f8fafc;
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }
        .meta-item strong {
            color: #334155;
            display: inline-block;
            width: 120px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }
        .details-table th, .details-table td {
            border: 1px solid #cbd5e1;
            padding: 10px 12px;
        }
        .details-table th {
            background: #f1f5f9;
            text-transform: uppercase;
            font-size: 11px;
            font-weight: 700;
            text-align: left;
        }
        .amount-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff1f2;
            border: 1px solid #fecdd3;
            padding: 12px 18px;
            border-radius: 6px;
            margin-bottom: 30px;
        }
        .amount-words {
            font-size: 12px;
            font-weight: 600;
            color: #9f1239;
        }
        .amount-num {
            font-size: 20px;
            font-weight: 900;
            color: #be123c;
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
            padding-top: 5px;
        }
        .no-print {
            text-align: center;
            margin-bottom: 20px;
        }
        .print-btn {
            background: #be123c;
            color: #fff;
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-weight: 700;
            cursor: pointer;
            font-size: 13px;
        }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
            .voucher-box { border: 1px solid #000; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="print-btn" onclick="window.print()">🖨️ Print Debit Voucher</button>
    </div>

    <div class="voucher-box">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <h1>{{ $company->company_name ?? 'Turkey Bangla Garments Ltd' }}</h1>
                <p>{{ $company->address ?? 'Tongi Industrial Area, Gazipur, Dhaka' }}</p>
                <p>Phone: {{ $company->phone ?? '+880 1712-345678' }} | Email: {{ $company->email ?? 'accounts@turkeybangla.com' }}</p>
            </div>
            <div class="voucher-badge">
                <span>Debit Voucher</span>
                <p style="margin: 5px 0 0 0; font-family: monospace; font-weight: 800; font-size: 13px;">{{ $voucher->voucher_no }}</p>
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

        <!-- Table -->
        <table class="details-table">
            <thead>
                <tr>
                    <th style="width: 50px;">SL</th>
                    <th>Head of Accounts (Debit)</th>
                    <th>Paid To / Payee</th>
                    <th style="text-align: right; width: 140px;">Amount (BDT)</th>
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
    </div>

</body>
</html>

