<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Employees Directory</title>
    <style>
        @page {
            margin: 25px 25px 75px 25px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 10px;
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
            border-bottom: 2px solid #0284c7;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .company-title {
            font-size: 16px;
            font-weight: bold;
            color: #0369a1;
            margin: 0;
        }
        .company-info {
            font-size: 9px;
            color: #64748b;
            margin: 1px 0;
        }
        .doc-badge {
            background-color: #0284c7;
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
            font-size: 9px;
            text-transform: uppercase;
            padding: 6px 5px;
            border: 1px solid #334155;
        }
        .data-table td {
            padding: 5px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }
        .data-table tr:nth-child(even) {
            background-color: rgba(248, 250, 252, 0.4);
        }
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-active { background-color: #dcfce7; color: #166534; }
        .status-inactive { background-color: #f1f5f9; color: #475569; }
        .footer {
            margin-top: 15px;
            text-align: right;
            font-size: 9px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }
    </style>
</head>
<body>

    <!-- Official PDF Header Matching Image 2 -->
    @include('factory-erp.common.pdf-header')

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
        <tr>
            <td style="text-align: left; vertical-align: middle;">
                <div class="doc-badge" style="background-color: #00adef;">EMPLOYEES DIRECTORY</div>
            </td>
            <td style="text-align: right; vertical-align: middle; font-size: 10px;">
                Generated: {{ date('d M, Y') }} | Total Staff: {{ $employees->count() }} Workers
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px; text-align: center;">#</th>
                <th style="width: 80px;">Emp ID</th>
                <th>Employee Name</th>
                <th>Department</th>
                <th>Designation</th>
                <th>Phone</th>
                <th style="text-align: center;">Shift</th>
                <th style="text-align: right;">Gross Salary</th>
                <th style="text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $index => $emp)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="font-weight: bold; color: #0284c7;">{{ $emp->employee_code }}</td>
                <td>
                    <strong>{{ $emp->name }}</strong>
                    <div style="font-size: 8px; color: #64748b;">Joined: {{ $emp->joining_date ? $emp->joining_date->format('d M, Y') : '—' }}</div>
                </td>
                <td>{{ $emp->department->name ?? 'N/A' }}</td>
                <td>{{ $emp->designation->title ?? 'N/A' }}</td>
                <td>{{ $emp->phone }}</td>
                <td style="text-align: center;">{{ $emp->shift }}</td>
                <td style="text-align: right; font-weight: bold; color: #059669;">
                    BDT {{ number_format($emp->gross_salary, 2) }}
                </td>
                <td style="text-align: center;">
                    <span class="status-badge {{ $emp->status === 'active' ? 'status-active' : 'status-inactive' }}">
                        {{ $emp->status }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Official PDF Footer Matching Image 2 -->
    @include('factory-erp.common.pdf-footer')

</body>
</html>

