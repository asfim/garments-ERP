@extends('layouts.app')

@section('title', 'Salary Payslip - ' . ($payroll->employee->name ?? 'Worker'))

@push('styles')
<style>
@media print {
    /* Hide Everything Else */
    header, aside, footer, nav, .mode-btn, .print-hide, .no-print,
    button, [onclick*="print"] {
        display: none !important;
    }

    body, html {
        background: #ffffff !important;
        color: #000000 !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    main {
        padding: 0 !important;
        margin: 0 !important;
        overflow: visible !important;
    }

    .glass-panel {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
    }

    #printablePayslipCard {
        position: static !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 20px !important;
        background: #ffffff !important;
        color: #000000 !important;
        border: 1px solid #333333 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        page-break-inside: avoid;
    }

    #printablePayslipCard * {
        color: #000000 !important;
        text-shadow: none !important;
    }

    .print-border {
        border-color: #333333 !important;
    }

    .print-table th {
        background-color: #f1f5f9 !important;
        color: #000000 !important;
        border: 1px solid #64748b !important;
    }

    .print-table td {
        border: 1px solid #cbd5e1 !important;
        background: #ffffff !important;
    }
}
</style>
@endpush

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="print-hide flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('factory.hr.payroll.index', ['month' => $payroll->month]) }}" 
               class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold flex items-center gap-1.5 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back to Payroll</span>
            </a>
            <div>
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice text-emerald-400"></i>
                    Employee Payslip (বেতন রসিদ)
                </h2>
                <p class="text-xs text-slate-400 font-mono">Slip #{{ $payroll->payroll_code }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <!-- Print Button -->
            <button type="button" onclick="window.print()" 
                    class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs border border-slate-700 hover:border-cyan-500/40 shadow-lg transition-all flex items-center gap-2">
                <i class="fa-solid fa-print text-cyan-400"></i>
                <span>Print Payslip</span>
            </button>

            <!-- Download PDF Button -->
            <a href="{{ route('factory.hr.payroll.payslip.pdf', $payroll->id) }}" 
               class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-lg shadow-rose-500/25 transition-all flex items-center gap-2">
                <i class="fa-solid fa-file-pdf"></i>
                <span>Download PDF</span>
            </a>
        </div>
    </div>

    <!-- Official Printable Payslip Card -->
    <div class="flex justify-center">
        <div id="printablePayslipCard" class="w-full max-w-3xl glass-panel rounded-2xl border border-slate-700/80 p-8 bg-[#09112a] text-white shadow-2xl relative">
            
            <!-- Company & Payslip Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-700/80 pb-5 mb-5 gap-4">
                <div class="flex items-center gap-3.5">
                    @if(!empty($company->logo) && file_exists(public_path('storage/' . $company->logo)))
                        <div class="h-14 w-auto max-w-[180px] bg-white rounded-xl p-1 border border-cyan-400/50 flex items-center justify-center">
                            <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo" class="h-full w-auto max-w-full object-contain">
                        </div>
                    @else
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-cyan-600 to-blue-600 flex items-center justify-center font-black text-white text-lg">
                            TBG
                        </div>
                    @endif
                    <div>
                        <h1 class="text-xl font-extrabold text-white">{{ $company->company_name ?? 'Turkey Bangla Garments Ltd' }}</h1>
                        <p class="text-xs text-slate-400">{{ $company->address ?? 'Tongi Industrial Area, Gazipur, Dhaka' }}</p>
                        <p class="text-[11px] text-slate-400">Phone: {{ $company->phone ?? '+880 1712-345678' }} | Email: {{ $company->email ?? 'hr@turkeybangla.com' }}</p>
                    </div>
                </div>

                <div class="text-right">
                    <div class="inline-block px-3 py-1 rounded-lg bg-emerald-500/20 text-emerald-300 font-bold text-xs font-mono border border-emerald-500/40">
                        SALARY PAYSLIP
                    </div>
                    <div class="text-xs text-slate-300 font-mono font-bold mt-1.5">
                        Month: {{ \Carbon\Carbon::parse($payroll->month . '-01')->format('F Y') }}
                    </div>
                    <div class="text-[10px] text-slate-400 font-mono">
                        Date: {{ $payroll->payment_date ? $payroll->payment_date->format('d M, Y') : date('d M, Y') }}
                    </div>
                </div>
            </div>

            <!-- Employee Info Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 rounded-xl bg-[#060c22] border border-slate-800 text-xs mb-6">
                <div>
                    <span class="text-[10px] text-slate-400 uppercase block font-semibold">Employee ID</span>
                    <span class="font-mono font-bold text-cyan-400 text-sm">{{ $payroll->employee->employee_code ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 uppercase block font-semibold">Worker Name</span>
                    <span class="font-bold text-white text-sm">{{ $payroll->employee->name ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 uppercase block font-semibold">Department</span>
                    <span class="text-slate-200 font-semibold">{{ $payroll->employee->department->name ?? 'Floor' }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 uppercase block font-semibold">Designation</span>
                    <span class="text-indigo-300 font-semibold">{{ $payroll->employee->designation->title ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 uppercase block font-semibold">Payment Mode</span>
                    <span class="text-slate-200">{{ $payroll->payment_method ?? ($payroll->employee->payment_method ?? 'Cash') }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 uppercase block font-semibold">Account / Phone</span>
                    <span class="font-mono text-slate-200">{{ $payroll->employee->account_number ?? ($payroll->employee->phone ?? '—') }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 uppercase block font-semibold">Shift</span>
                    <span class="text-slate-200">{{ $payroll->employee->shift ?? 'General' }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 uppercase block font-semibold">Status</span>
                    <span class="font-bold {{ $payroll->payment_status === 'paid' ? 'text-emerald-400' : 'text-amber-400' }}">
                        {{ strtoupper($payroll->payment_status) }}
                    </span>
                </div>
            </div>

            <!-- Salary Details: Earnings vs Deductions Table -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                
                <!-- Earnings Column -->
                <div class="border border-slate-800 rounded-xl overflow-hidden">
                    <div class="bg-emerald-950/40 p-2.5 border-b border-slate-800 font-bold text-xs text-emerald-300 flex items-center justify-between">
                        <span>EARNINGS (উপার্জন)</span>
                        <span class="font-mono">AMOUNT (৳)</span>
                    </div>
                    <div class="divide-y divide-slate-800/80 text-xs">
                        <div class="p-2.5 flex justify-between">
                            <span class="text-slate-300">Basic Salary</span>
                            <span class="font-mono text-white">৳{{ number_format($payroll->basic_salary, 2) }}</span>
                        </div>
                        <div class="p-2.5 flex justify-between">
                            <span class="text-slate-300">Allowances (House, Med, Conv)</span>
                            <span class="font-mono text-white">৳{{ number_format($payroll->allowances, 2) }}</span>
                        </div>
                        <div class="p-2.5 flex justify-between bg-amber-500/5">
                            <span class="text-amber-300">Overtime Pay ({{ $payroll->overtime_hours }} hrs @ ৳{{ number_format($payroll->overtime_rate, 2) }}/h)</span>
                            <span class="font-mono text-amber-300 font-bold">৳{{ number_format($payroll->overtime_amount, 2) }}</span>
                        </div>
                        <div class="p-2.5 flex justify-between">
                            <span class="text-slate-300">Bonus / Incentives</span>
                            <span class="font-mono text-white">৳{{ number_format($payroll->bonus, 2) }}</span>
                        </div>
                        <div class="p-2.5 bg-[#0a1435] font-bold text-white flex justify-between">
                            <span>Total Gross Earnings</span>
                            <span class="font-mono text-emerald-400">৳{{ number_format($payroll->basic_salary + $payroll->allowances + $payroll->overtime_amount + $payroll->bonus, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Deductions Column -->
                <div class="border border-slate-800 rounded-xl overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="bg-rose-950/40 p-2.5 border-b border-slate-800 font-bold text-xs text-rose-300 flex items-center justify-between">
                            <span>DEDUCTIONS (কর্তন)</span>
                            <span class="font-mono">AMOUNT (৳)</span>
                        </div>
                        <div class="divide-y divide-slate-800/80 text-xs">
                            <div class="p-2.5 flex justify-between">
                                <span class="text-slate-300">Absenteeism / Leave without pay</span>
                                <span class="font-mono text-rose-300">৳{{ number_format($payroll->deductions, 2) }}</span>
                            </div>
                            <div class="p-2.5 flex justify-between">
                                <span class="text-slate-300">Salary Advance / Loan</span>
                                <span class="font-mono text-slate-400">৳0.00</span>
                            </div>
                            <div class="p-2.5 flex justify-between">
                                <span class="text-slate-300">Fine / Penalties</span>
                                <span class="font-mono text-slate-400">৳0.00</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-2.5 bg-[#1a0f1d] font-bold text-white flex justify-between border-t border-slate-800 text-xs">
                        <span>Total Deductions</span>
                        <span class="font-mono text-rose-400">৳{{ number_format($payroll->deductions, 2) }}</span>
                    </div>
                </div>

            </div>

            <!-- Net Payable Banner -->
            <div class="p-4 rounded-xl bg-gradient-to-r from-emerald-950/80 via-[#0a1e2f] to-teal-950/80 border border-emerald-500/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-8">
                <div>
                    <span class="text-xs uppercase font-bold text-emerald-400 block tracking-wider">NET SALARY PAYABLE (মোট প্রদেয় বেতন)</span>
                    <span class="text-[11px] text-slate-300">Computed after overtime additions and absenteeism deductions</span>
                </div>
                <div class="text-right">
                    <span class="text-2xl font-mono font-black text-emerald-300">
                        ৳{{ number_format($payroll->net_salary, 2) }}
                    </span>
                </div>
            </div>

            <!-- Signature Lines -->
            <div class="pt-8 border-t border-slate-800 grid grid-cols-3 gap-6 text-center text-xs">
                <div>
                    <div class="border-t border-dashed border-slate-600 pt-2 font-semibold text-slate-300">
                        Worker's Signature
                    </div>
                </div>
                <div>
                    <div class="border-t border-dashed border-slate-600 pt-2 font-semibold text-slate-300">
                        Prepared by (HR/Accounts)
                    </div>
                </div>
                <div>
                    <div class="border-t border-dashed border-slate-600 pt-2 font-semibold text-slate-300">
                        Authorized Director
                    </div>
                </div>
            </div>

            <div class="mt-8 text-center text-[10px] text-slate-400">
                This is a computer-generated salary payslip from TBG Garments ERP Solution.
            </div>

        </div>
    </div>

</div>
@endsection
