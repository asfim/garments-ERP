<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Payroll Sheet - {{ $month }}</title>
    <style>
        @page {
            margin: 25px 25px 75px 25px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 9px;
            line-height: 1.3;
            padding: 0;
            margin: 0;
        }
        .watermark {
            position: fixed;
            top: 140px;
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
            border-bottom: 2px solid #059669;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .company-title {
            font-size: 16px;
            font-weight: bold;
            color: #065f46;
            margin: 0;
        }
        .company-info {
            font-size: 9px;
            color: #64748b;
            margin: 1px 0;
        }
        .doc-badge {
            background-color: #059669;
            color: #ffffff;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: bold;
            border-radius: 4px;
            display: inline-block;
        }
        .date-text {
            font-size: 9px;
            color: #94a3b8;
            margin-top: 3px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        .data-table th {
            background-color: #0f172a;
            color: #f8fafc;
            font-weight: bold;
            font-size: 8px;
            text-transform: uppercase;
            padding: 5px 4px;
            border: 1px solid #334155;
        }
        .data-table td {
            padding: 5px 4px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }
        .data-table tr:nth-child(even) {
            background-color: rgba(248, 250, 252, 0.4);
        }
        .status-badge {
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-paid { background-color: #dcfce7; color: #166534; }
        .status-unpaid { background-color: #fef3c7; color: #92400e; }
        .footer {
            margin-top: 20px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <!-- Official PDF Header Matching Image 2 -->
    @include('factory-erp.common.pdf-header')

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
        <tr>
            <td style="text-align: left; vertical-align: middle;">
                <div class="doc-badge" style="background-color: #00adef;">MONTHLY PAYROLL SHEET</div>
            </td>
            <td style="text-align: right; vertical-align: middle; font-size: 10px;">
                Month: <strong>{{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</strong> | Total Staff: {{ $payrolls->count() }} Workers
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20px; text-align: center;">#</th>
                <th style="width: 65px;">Slip No</th>
                <th style="width: 65px;">Emp ID</th>
                <th>Worker Name</th>
                <th>Department</th>
                <th>Designation</th>
                <th style="text-align: right;">Basic (৳)</th>
                <th style="text-align: right;">Allowances (৳)</th>
                <th style="text-align: center;">OT (h)</th>
                <th style="text-align: right;">OT Pay (৳)</th>
                <th style="text-align: right;">Deductions (৳)</th>
                <th style="text-align: right;">Net Salary (৳)</th>
                <th style="text-align: center;">Status</th>
                <th style="width: 70px; text-align: center;">Signature</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totNet = 0;
                $totBasic = 0;
                $totOT = 0;
                $totDed = 0;
            @endphp
            @foreach($payrolls as $index => $p)
            @php
                $totNet += $p->net_salary;
                $totBasic += $p->basic_salary;
                $totOT += $p->overtime_amount;
                $totDed += $p->deductions;
            @endphp
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="font-family: monospace; font-size: 8px;">{{ $p->payroll_code }}</td>
                <td style="font-weight: bold; color: #0284c7; font-family: monospace;">{{ $p->employee->employee_code ?? '' }}</td>
                <td><strong>{{ $p->employee->name ?? 'N/A' }}</strong></td>
                <td>{{ $p->employee->department->name ?? 'N/A' }}</td>
                <td>{{ $p->employee->designation->title ?? 'N/A' }}</td>
                <td style="text-align: right; font-family: monospace;">{{ number_format($p->basic_salary, 2) }}</td>
                <td style="text-align: right; font-family: monospace;">{{ number_format($p->allowances, 2) }}</td>
                <td style="text-align: center; font-family: monospace;">{{ $p->overtime_hours }}</td>
                <td style="text-align: right; font-family: monospace;">{{ number_format($p->overtime_amount, 2) }}</td>
                <td style="text-align: right; font-family: monospace; color: #b91c1c;">{{ number_format($p->deductions, 2) }}</td>
                <td style="text-align: right; font-family: monospace; font-weight: bold; color: #065f46;">{{ number_format($p->net_salary, 2) }}</td>
                <td style="text-align: center;">
                    <span class="status-badge {{ $p->payment_status === 'paid' ? 'status-paid' : 'status-unpaid' }}">
                        {{ strtoupper($p->payment_status) }}
                    </span>
                </td>
                <td style="text-align: center;"></td>
            </tr>
            @endforeach
            <!-- Totals row -->
            <tr style="background-color: #e2e8f0; font-weight: bold;">
                <td colspan="6" style="text-align: right;">GRAND TOTALS:</td>
                <td style="text-align: right; font-family: monospace;">{{ number_format($totBasic, 2) }}</td>
                <td></td>
                <td></td>
                <td style="text-align: right; font-family: monospace;">{{ number_format($totOT, 2) }}</td>
                <td style="text-align: right; font-family: monospace; color: #b91c1c;">{{ number_format($totDed, 2) }}</td>
                <td style="text-align: right; font-family: monospace; color: #065f46; font-size: 10px;">BDT {{ number_format($totNet, 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; width: 33%; text-align: center; padding-top: 35px;">
                    <div style="border-top: 1px dashed #64748b; width: 140px; margin: 0 auto; padding-top: 4px;">Prepared by (Accounts)</div>
                </td>
                <td style="border: none; width: 33%; text-align: center; padding-top: 35px;">
                    <div style="border-top: 1px dashed #64748b; width: 140px; margin: 0 auto; padding-top: 4px;">HR & Compliance Manager</div>
                </td>
                <td style="border: none; width: 33%; text-align: center; padding-top: 35px;">
                    <div style="border-top: 1px dashed #64748b; width: 140px; margin: 0 auto; padding-top: 4px;">Managing Director</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Official PDF Footer Matching Image 2 -->
    @include('factory-erp.common.pdf-footer')

</body>
</html>

