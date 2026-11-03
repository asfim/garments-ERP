<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payslip - {{ $payroll->employee->employee_code ?? '' }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
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
            border-bottom: 2px solid #059669;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .company-title {
            font-size: 18px;
            font-weight: bold;
            color: #065f46;
            margin: 0;
        }
        .company-info {
            font-size: 10px;
            color: #64748b;
            margin: 2px 0;
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
        .info-table {
            width: 100%;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 6px 10px;
            font-size: 10px;
            border: 1px solid #e2e8f0;
        }
        .info-label {
            font-weight: bold;
            color: #475569;
            width: 18%;
            background-color: #f1f5f9;
        }
        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .salary-table th {
            background-color: #0f172a;
            color: #f8fafc;
            font-size: 10px;
            text-transform: uppercase;
            padding: 7px 10px;
            border: 1px solid #334155;
        }
        .salary-table td {
            padding: 6px 10px;
            border: 1px solid #cbd5e1;
            font-size: 10px;
        }
        .net-pay-box {
            background-color: #ecfdf5;
            border: 2px solid #059669;
            padding: 10px 15px;
            margin-bottom: 40px;
        }
        .net-pay-amount {
            font-size: 18px;
            font-weight: bold;
            color: #065f46;
            text-align: right;
        }
        .signature-table {
            width: 100%;
            border: none;
            margin-top: 40px;
        }
        .signature-table td {
            border: none;
            text-align: center;
            vertical-align: bottom;
            padding-top: 35px;
        }
        .signature-line {
            border-top: 1px dashed #64748b;
            width: 140px;
            margin: 0 auto;
            padding-top: 5px;
            font-weight: bold;
            font-size: 9px;
            color: #475569;
        }
        .footer-note {
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            margin-top: 25px;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }
    </style>
</head>
<body>

    @php
        $watermarkPath = null;
        if (!empty($company->print_logo) && file_exists(public_path('storage/' . $company->print_logo))) {
            $watermarkPath = public_path('storage/' . $company->print_logo);
        } elseif (!empty($company->logo) && file_exists(public_path('storage/' . $company->logo))) {
            $watermarkPath = public_path('storage/' . $company->logo);
        }
    @endphp

    @if($watermarkPath)
        <div class="watermark">
            <img src="{{ $watermarkPath }}" alt="Watermark">
        </div>
    @endif

    <table class="header-table">
        <tr>
            <td style="width: 175px; vertical-align: middle;">
                @if(!empty($company->logo) && file_exists(public_path('storage/' . $company->logo)))
                    <img src="{{ public_path('storage/' . $company->logo) }}" style="max-width: 165px; max-height: 65px; height: auto; width: auto; object-fit: contain;">
                @else
                    <div style="font-size: 18px; font-weight: bold; color: #059669;">{{ $company->company_name ?? 'TBG ERP' }}</div>
                @endif
            </td>
            <td style="vertical-align: middle; padding-left: 10px;">
                <div class="company-title">{{ $company->company_name ?? 'Turkey Bangla Garments Ltd' }}</div>
                <div class="company-info">{{ $company->address ?? 'Tongi Industrial Area, Gazipur, Dhaka - 1710' }}</div>
                <div class="company-info">Phone: {{ $company->phone ?? '+880 1712-345678' }} | Email: {{ $company->email ?? 'hr@turkeybangla.com' }}</div>
            </td>
            <td style="text-align: right; vertical-align: middle; width: 170px;">
                <div class="doc-badge">SALARY PAYSLIP</div>
                <div style="font-size: 9px; color: #64748b; margin-top: 4px;">Month: <strong>{{ \Carbon\Carbon::parse($payroll->month . '-01')->format('F Y') }}</strong></div>
                <div style="font-size: 9px; color: #64748b;">Slip: <strong>{{ $payroll->payroll_code }}</strong></div>
            </td>
        </tr>
    </table>

    <!-- Employee Info -->
    <table class="info-table">
        <tr>
            <td class="info-label">Employee ID:</td>
            <td style="font-weight: bold; color: #0284c7;">{{ $payroll->employee->employee_code ?? 'N/A' }}</td>
            <td class="info-label">Full Name:</td>
            <td style="font-weight: bold;">{{ $payroll->employee->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="info-label">Department:</td>
            <td>{{ $payroll->employee->department->name ?? 'N/A' }}</td>
            <td class="info-label">Designation:</td>
            <td>{{ $payroll->employee->designation->title ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="info-label">Payment Mode:</td>
            <td>{{ $payroll->payment_method ?? ($payroll->employee->payment_method ?? 'Cash') }}</td>
            <td class="info-label">Account / Phone:</td>
            <td>{{ $payroll->employee->account_number ?? ($payroll->employee->phone ?? '—') }}</td>
        </tr>
        <tr>
            <td class="info-label">Shift:</td>
            <td>{{ $payroll->employee->shift ?? 'General' }}</td>
            <td class="info-label">Payment Status:</td>
            <td style="font-weight: bold; color: {{ $payroll->payment_status === 'paid' ? '#059669' : '#d97706' }};">
                {{ strtoupper($payroll->payment_status) }}
            </td>
        </tr>
    </table>

    <!-- Earnings & Deductions Split -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 6px;">
                <table class="salary-table">
                    <thead>
                        <tr>
                            <th>Earnings (উপার্জন)</th>
                            <th style="text-align: right; width: 90px;">Amount (৳)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Basic Salary</td>
                            <td style="text-align: right; font-family: monospace;">{{ number_format($payroll->basic_salary, 2) }}</td>
                        </tr>
                        <tr>
                            <td>House Rent & Allowances</td>
                            <td style="text-align: right; font-family: monospace;">{{ number_format($payroll->allowances, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Overtime Pay ({{ $payroll->overtime_hours }} hrs)</td>
                            <td style="text-align: right; font-family: monospace; font-weight: bold;">{{ number_format($payroll->overtime_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Bonus / Incentives</td>
                            <td style="text-align: right; font-family: monospace;">{{ number_format($payroll->bonus, 2) }}</td>
                        </tr>
                        <tr style="background-color: #f1f5f9; font-weight: bold;">
                            <td>Total Gross Earnings</td>
                            <td style="text-align: right; font-family: monospace; color: #065f46;">
                                BDT {{ number_format($payroll->basic_salary + $payroll->allowances + $payroll->overtime_amount + $payroll->bonus, 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>

            <td style="width: 50%; vertical-align: top; padding-left: 6px;">
                <table class="salary-table">
                    <thead>
                        <tr>
                            <th>Deductions (কর্তন)</th>
                            <th style="text-align: right; width: 90px;">Amount (৳)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Absenteeism Deductions</td>
                            <td style="text-align: right; font-family: monospace; color: #b91c1c;">{{ number_format($payroll->absence_deduction ?? 0, 2) }}</td>
                        </tr>
                        <tr style="{{ ($payroll->advance_deduction ?? 0) > 0 ? 'background-color: #ffe4e6;' : '' }}">
                            <td>Advance Salary Recovery</td>
                            <td style="text-align: right; font-family: monospace; font-weight: bold; color: #b91c1c;">{{ number_format($payroll->advance_deduction ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Fine / Penalties</td>
                            <td style="text-align: right; font-family: monospace;">{{ number_format($payroll->penalty_deduction ?? 0, 2) }}</td>
                        </tr>
                        <tr style="background-color: #f1f5f9; font-weight: bold;">
                            <td>Total Deductions</td>
                            <td style="text-align: right; font-family: monospace; color: #b91c1c;">
                                BDT {{ number_format($payroll->deductions, 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <!-- Net Pay Box -->
    <table class="net-pay-box" style="width: 100%;">
        <tr>
            <td style="vertical-align: middle;">
                <div style="font-size: 12px; font-weight: bold; color: #065f46;">NET SALARY PAYABLE</div>
                <div style="font-size: 9px; color: #475569;">Gross Earnings minus Total Deductions</div>
            </td>
            <td class="net-pay-amount" style="vertical-align: middle;">
                BDT {{ number_format($payroll->net_salary, 2) }}
            </td>
        </tr>
    </table>

    <!-- Signatures -->
    <table class="signature-table">
        <tr>
            <td style="width: 33%;">
                <div class="signature-line">Worker's Signature</div>
            </td>
            <td style="width: 33%;">
                <div class="signature-line">Prepared By (HR)</div>
            </td>
            <td style="width: 33%;">
                <div class="signature-line">Managing Director</div>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        CONFIDENTIAL • THIS IS A SYSTEM GENERATED COMPUTERIZED PAYSLIP • TURKEY BANGLA GARMENTS LTD
    </div>

</body>
</html>

