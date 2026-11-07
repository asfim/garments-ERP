@extends('layouts.app')

@section('title', 'Employee Self-Service Portal | ' . $employee->name)

@section('content')
<div class="space-y-6">

    <!-- Top Employee Profile Banner -->
    <div class="glass-panel rounded-3xl p-6 sm:p-8 border border-emerald-500/30 shadow-2xl relative overflow-hidden bg-gradient-to-r from-[#09152e] via-[#071329] to-[#061826]">
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex flex-col md:flex-row items-center md:items-start justify-between gap-6 relative z-10">
            
            <!-- Avatar & Profile Details -->
            <div class="flex flex-col sm:flex-row items-center gap-5 text-center sm:text-left">
                <div class="relative w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-[#0b1736] border-2 border-emerald-400/50 shadow-xl overflow-hidden flex-shrink-0 flex items-center justify-center">
                    @if(!empty($employee->photo) && file_exists(public_path('storage/' . $employee->photo)))
                        <img src="{{ asset('storage/' . $employee->photo) }}" alt="{{ $employee->name }}" class="w-full h-full object-cover">
                    @else
                        <i class="fa-solid fa-user-tie text-4xl text-emerald-400"></i>
                    @endif
                    <span class="absolute bottom-1 right-1 w-3.5 h-3.5 rounded-full bg-emerald-400 border-2 border-[#09152e]" title="Active Worker"></span>
                </div>

                <div class="space-y-1.5">
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                        <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight">{{ $employee->name }}</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">
                            {{ $employee->employee_code }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            {{ strtoupper($employee->status ?? 'ACTIVE') }}
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3 text-xs text-slate-300">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-briefcase text-cyan-400"></i>
                            <strong>{{ $employee->designation->title ?? 'Worker' }}</strong>
                        </span>
                        <span>•</span>
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-building text-purple-400"></i>
                            <span>{{ $employee->department->name ?? 'Production' }}</span>
                        </span>
                        <span>•</span>
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-calendar-check text-amber-400"></i>
                            <span>Joined: {{ $employee->joining_date ? $employee->joining_date->format('d M, Y') : 'N/A' }}</span>
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-4 text-[11px] text-slate-400 pt-1 font-mono">
                        <span><i class="fa-solid fa-phone text-slate-500 mr-1"></i>{{ $employee->phone }}</span>
                        @if($employee->email)
                            <span><i class="fa-solid fa-envelope text-slate-500 mr-1"></i>{{ $employee->email }}</span>
                        @endif
                        <span><i class="fa-solid fa-fingerprint text-cyan-400 mr-1"></i>Bio ID: {{ $employee->device_user_id ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex flex-wrap items-center gap-2.5">
                <button type="button" onclick="openAdvanceModal()" 
                        class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-600 to-amber-500 hover:from-amber-500 hover:to-amber-400 text-white font-bold text-xs shadow-lg shadow-amber-600/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                    <span>Request Advance (অগ্রিম আবেদন)</span>
                </button>

                <button type="button" onclick="openLeaveModal()" 
                        class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-purple-500 hover:from-purple-500 hover:to-purple-400 text-white font-bold text-xs shadow-lg shadow-purple-600/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-calendar-plus"></i>
                    <span>Apply for Leave (ছুটির আবেদন)</span>
                </button>
            </div>

        </div>

    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs flex items-center gap-2 shadow-lg">
        <i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <!-- High-Impact KPI Metric Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        
        <!-- 1. Monthly Basic / Gross Salary -->
        <div class="glass-panel rounded-2xl p-5 border border-slate-700/60 shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-400">Monthly Gross Salary</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </span>
            </div>
            <div class="text-2xl font-black text-white font-mono">
                ৳ {{ number_format($employee->gross_salary ?? 0, 2) }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1 flex items-center justify-between">
                <span>Basic: ৳{{ number_format($employee->basic_salary ?? 0) }}</span>
                <span class="text-emerald-400 font-semibold">{{ $employee->payment_method ?? 'Cash' }}</span>
            </div>
        </div>

        <!-- 2. Attendance Summary this month -->
        <div class="glass-panel rounded-2xl p-5 border border-slate-700/60 shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-400">Attendance ({{ date('F Y') }})</span>
                <span class="w-8 h-8 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-fingerprint"></i>
                </span>
            </div>
            <div class="text-2xl font-black text-cyan-300 font-mono">
                {{ $presentDays }} <span class="text-xs font-normal text-slate-400">Days Present</span>
            </div>
            <div class="text-[10px] text-slate-400 mt-1 flex items-center gap-2">
                <span class="text-amber-400">Late: {{ $lateDays }}</span>
                <span>•</span>
                <span class="text-purple-400">Leave: {{ $leaveDays }}</span>
                <span>•</span>
                <span class="text-rose-400">Absent: {{ $absentDays }}</span>
            </div>
        </div>

        <!-- 3. Overtime Hours & OT Earnings -->
        <div class="glass-panel rounded-2xl p-5 border border-slate-700/60 shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-400">Total Overtime (OT)</span>
                <span class="w-8 h-8 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-clock"></i>
                </span>
            </div>
            <div class="text-2xl font-black text-purple-300 font-mono">
                {{ number_format($totalOvertimeHours, 1) }} <span class="text-xs font-normal text-slate-400">Hours</span>
            </div>
            @php
                $hourlyRate = $employee->basic_salary ? round(($employee->basic_salary / 208) * 2, 2) : 0;
                $estOtPay = round($totalOvertimeHours * $hourlyRate, 2);
            @endphp
            <div class="text-[11px] text-slate-400 mt-1">
                Est. OT Pay: <strong class="text-white font-mono">৳ {{ number_format($estOtPay, 2) }}</strong>
            </div>
        </div>

        <!-- 4. Advance Salary Loan Balance -->
        <div class="glass-panel rounded-2xl p-5 border border-slate-700/60 shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-400">Advance Balance (বকেয়া ঋণ)</span>
                <span class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </span>
            </div>
            <div class="text-2xl font-black text-amber-300 font-mono">
                ৳ {{ number_format($outstandingAdvance, 2) }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">
                Total Taken: ৳{{ number_format($totalAdvanceTaken) }} (Repaid: ৳{{ number_format($totalAdvanceDeducted) }})
            </div>
        </div>

    </div>

    <!-- Main Tabbed Navigation -->
    <div class="glass-panel rounded-3xl border border-slate-700/60 shadow-xl overflow-hidden">
        
        <!-- Tab Headers -->
        <div class="border-b border-slate-700/60 flex overflow-x-auto p-2 gap-2 bg-[#060c20]">
            <button type="button" onclick="switchEmpTab('salary')" id="btnTabSalary"
                    class="emp-tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md">
                <i class="fa-solid fa-file-invoice-dollar"></i>
                <span>Salary Statements (বেতন বিবরণী)</span>
            </button>

            <button type="button" onclick="switchEmpTab('attendance')" id="btnTabAttendance"
                    class="emp-tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 text-slate-400 hover:text-white">
                <i class="fa-solid fa-calendar-check"></i>
                <span>Attendance & Overtime (হাজিরা ও ওভারটাইম)</span>
            </button>

            <button type="button" onclick="switchEmpTab('advances')" id="btnTabAdvances"
                    class="emp-tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 text-slate-400 hover:text-white">
                <i class="fa-solid fa-hand-holding-dollar"></i>
                <span>Advance Loans (অগ্রিম ঋণ)</span>
            </button>

            <button type="button" onclick="switchEmpTab('leaves')" id="btnTabLeaves"
                    class="emp-tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 text-slate-400 hover:text-white">
                <i class="fa-solid fa-mug-hot"></i>
                <span>Leave History (ছুটি)</span>
            </button>

            <button type="button" onclick="switchEmpTab('profile')" id="btnTabProfile"
                    class="emp-tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 text-slate-400 hover:text-white">
                <i class="fa-solid fa-id-card"></i>
                <span>My Profile (ব্যক্তিগত তথ্য)</span>
            </button>
        </div>

        <div class="p-6">
            
            <!-- ========================================== -->
            <!-- TAB 1: SALARY STATEMENT DETAILS            -->
            <!-- ========================================== -->
            <div id="tabSalary" class="emp-tab-content space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                            <i class="fa-solid fa-receipt text-emerald-400"></i>
                            <span>Complete Salary & Payslip History (মাসিক বেতন বিবরণী ও পে-স্লিপ)</span>
                        </h3>
                        <p class="text-[11px] text-slate-400">Every monthly disbursement, overtime calculation, and breakdown.</p>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-[#060c20]">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-[#0b142e] text-slate-300 uppercase text-[10px] font-bold border-b border-slate-700">
                            <tr>
                                <th class="py-3 px-3">Slip ID</th>
                                <th class="py-3 px-3">Month</th>
                                <th class="py-3 px-3 text-right">Basic (৳)</th>
                                <th class="py-3 px-3 text-right">Allowances</th>
                                <th class="py-3 px-3 text-center">OT (Hrs)</th>
                                <th class="py-3 px-3 text-right">OT Pay</th>
                                <th class="py-3 px-3 text-right">Deductions</th>
                                <th class="py-3 px-3 text-right">Net Payable</th>
                                <th class="py-3 px-3 text-center">Status</th>
                                <th class="py-3 px-3 text-center">Slip Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @forelse($payrolls as $p)
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="py-3 px-3 font-mono font-bold text-cyan-400">{{ $p->payroll_code }}</td>
                                <td class="py-3 px-3 font-bold text-white">
                                    {{ \Carbon\Carbon::parse($p->month . '-01')->format('F Y') }}
                                </td>
                                <td class="py-3 px-3 text-right font-mono text-slate-300">{{ number_format($p->basic_salary, 2) }}</td>
                                <td class="py-3 px-3 text-right font-mono text-slate-300">
                                    +{{ number_format(($p->house_rent + $p->medical_allowance + $p->conveyance_allowance + $p->bonus), 2) }}
                                </td>
                                <td class="py-3 px-3 text-center font-mono text-purple-300">{{ $p->overtime_hours }}h</td>
                                <td class="py-3 px-3 text-right font-mono text-emerald-300">+{{ number_format($p->overtime_pay, 2) }}</td>
                                <td class="py-3 px-3 text-right font-mono text-rose-400">
                                    -{{ number_format($p->deductions, 2) }}
                                    @if($p->advance_deduction > 0)
                                        <div class="text-[9px] text-amber-300 font-sans">(Adv: ৳{{ $p->advance_deduction }})</div>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-right font-mono font-black text-white text-sm">
                                    ৳ {{ number_format($p->net_salary, 2) }}
                                </td>
                                <td class="py-3 px-3 text-center">
                                    @if($p->payment_status === 'paid')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                            PAID
                                        </span>
                                        @if($p->payment_date)
                                            <div class="text-[9px] text-slate-400 font-mono mt-0.5">{{ $p->payment_date->format('d M, Y') }}</div>
                                        @endif
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                            UNPAID
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('factory.hr.payroll.payslip', $p->id) }}" target="_blank" title="View Payslip"
                                           class="p-1.5 rounded-lg bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 text-xs transition-colors">
                                            <i class="fa-solid fa-file-invoice"></i>
                                        </a>
                                        <a href="{{ route('factory.hr.payroll.payslip.pdf', $p->id) }}" title="Download PDF"
                                           class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 text-xs transition-colors">
                                            <i class="fa-solid fa-file-pdf"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="py-8 text-center text-slate-400 text-xs">
                                    <i class="fa-solid fa-receipt text-3xl text-slate-500 mb-2 block"></i>
                                    No payslip records generated yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TAB 2: ATTENDANCE & BIOMETRIC OVERTIME     -->
            <!-- ========================================== -->
            <div id="tabAttendance" class="emp-tab-content hidden space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                            <i class="fa-solid fa-fingerprint text-cyan-400"></i>
                            <span>Biometric Attendance & Overtime Logs (হাজিরা ও ওভারটাইম রেকর্ড)</span>
                        </h3>
                        <p class="text-[11px] text-slate-400">Records synced directly from factory fingerprint machine.</p>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-[#060c20]">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-[#0b142e] text-slate-300 uppercase text-[10px] font-bold border-b border-slate-700">
                            <tr>
                                <th class="py-3 px-3">Date</th>
                                <th class="py-3 px-3">Day</th>
                                <th class="py-3 px-3 text-center">Status</th>
                                <th class="py-3 px-3 text-center">In Time</th>
                                <th class="py-3 px-3 text-center">Out Time</th>
                                <th class="py-3 px-3 text-center">OT Hours</th>
                                <th class="py-3 px-3">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @forelse($attendances as $att)
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="py-2.5 px-3 font-mono font-bold text-white">{{ $att->date->format('d M, Y') }}</td>
                                <td class="py-2.5 px-3 text-slate-400">{{ $att->date->format('l') }}</td>
                                <td class="py-2.5 px-3 text-center">
                                    @if($att->status === 'present')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">PRESENT</span>
                                    @elseif($att->status === 'late')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">LATE</span>
                                    @elseif($att->status === 'leave')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">LEAVE</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">ABSENT</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3 text-center font-mono text-cyan-300">{{ $att->in_time ? date('h:i A', strtotime($att->in_time)) : '--:--' }}</td>
                                <td class="py-2.5 px-3 text-center font-mono text-cyan-300">{{ $att->out_time ? date('h:i A', strtotime($att->out_time)) : '--:--' }}</td>
                                <td class="py-2.5 px-3 text-center font-mono font-bold text-purple-300">
                                    {{ $att->overtime_hours > 0 ? $att->overtime_hours . ' hrs' : '-' }}
                                </td>
                                <td class="py-2.5 px-3 text-slate-400 text-[11px]">{{ $att->notes ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400 text-xs">
                                    <i class="fa-solid fa-calendar-xmark text-3xl text-slate-500 mb-2 block"></i>
                                    No attendance recorded for this month.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TAB 3: ADVANCE SALARY & LOANS             -->
            <!-- ========================================== -->
            <div id="tabAdvances" class="emp-tab-content hidden space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                            <i class="fa-solid fa-hand-holding-dollar text-amber-400"></i>
                            <span>Advance Salary & Loan Statement (অগ্রিম ঋণ ও বকেয়া)</span>
                        </h3>
                        <p class="text-[11px] text-slate-400">Track applied loans, monthly deductions, and balance.</p>
                    </div>

                    <button type="button" onclick="openAdvanceModal()"
                            class="px-3.5 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs shadow-md transition-all flex items-center gap-1.5">
                        <i class="fa-solid fa-plus"></i>
                        <span>Apply New Advance</span>
                    </button>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-[#060c20]">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-[#0b142e] text-slate-300 uppercase text-[10px] font-bold border-b border-slate-700">
                            <tr>
                                <th class="py-3 px-3">Application Date</th>
                                <th class="py-3 px-3 text-right">Amount (৳)</th>
                                <th class="py-3 px-3 text-center">Repayment Term</th>
                                <th class="py-3 px-3 text-right">Deducted (৳)</th>
                                <th class="py-3 px-3 text-right">Remaining (৳)</th>
                                <th class="py-3 px-3 text-center">Status</th>
                                <th class="py-3 px-3">Reason</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @forelse($advances as $adv)
                            @php
                                $rem = max(0, $adv->amount - $adv->deducted_amount);
                            @endphp
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="py-2.5 px-3 font-mono font-bold text-white">{{ $adv->created_at->format('d M, Y') }}</td>
                                <td class="py-2.5 px-3 text-right font-mono font-bold text-amber-300">৳ {{ number_format($adv->amount, 2) }}</td>
                                <td class="py-2.5 px-3 text-center text-slate-300">{{ $adv->repayment_months ?? 1 }} Month(s)</td>
                                <td class="py-2.5 px-3 text-right font-mono text-emerald-400">৳ {{ number_format($adv->deducted_amount, 2) }}</td>
                                <td class="py-2.5 px-3 text-right font-mono font-bold text-rose-300">৳ {{ number_format($rem, 2) }}</td>
                                <td class="py-2.5 px-3 text-center">
                                    @if($adv->status === 'approved')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">APPROVED</span>
                                    @elseif($adv->status === 'rejected')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">REJECTED</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">PENDING</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3 text-slate-400 text-[11px]">{{ $adv->reason ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400 text-xs">
                                    <i class="fa-solid fa-hand-holding-dollar text-3xl text-slate-500 mb-2 block"></i>
                                    No advance loans applied yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TAB 4: LEAVE REQUESTS                     -->
            <!-- ========================================== -->
            <div id="tabLeaves" class="emp-tab-content hidden space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                            <i class="fa-solid fa-mug-hot text-purple-400"></i>
                            <span>Leave Applications & Balance (ছুটির ইতিহাস)</span>
                        </h3>
                        <p class="text-[11px] text-slate-400">Apply for leaves and track HR approval status.</p>
                    </div>

                    <button type="button" onclick="openLeaveModal()"
                            class="px-3.5 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-md transition-all flex items-center gap-1.5">
                        <i class="fa-solid fa-plus"></i>
                        <span>Apply for Leave</span>
                    </button>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-[#060c20]">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-[#0b142e] text-slate-300 uppercase text-[10px] font-bold border-b border-slate-700">
                            <tr>
                                <th class="py-3 px-3">Type</th>
                                <th class="py-3 px-3">From Date</th>
                                <th class="py-3 px-3">To Date</th>
                                <th class="py-3 px-3 text-center">Days</th>
                                <th class="py-3 px-3 text-center">Status</th>
                                <th class="py-3 px-3">Reason</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @forelse($leaves as $lv)
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="py-2.5 px-3 font-bold text-cyan-400">{{ strtoupper($lv->leave_type) }}</td>
                                <td class="py-2.5 px-3 font-mono text-white">{{ $lv->start_date->format('d M, Y') }}</td>
                                <td class="py-2.5 px-3 font-mono text-white">{{ $lv->end_date->format('d M, Y') }}</td>
                                <td class="py-2.5 px-3 text-center font-mono font-bold text-amber-300">{{ $lv->days }} Days</td>
                                <td class="py-2.5 px-3 text-center">
                                    @if($lv->status === 'approved')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">APPROVED</span>
                                    @elseif($lv->status === 'rejected')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">REJECTED</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">PENDING</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3 text-slate-400 text-[11px]">{{ $lv->reason ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400 text-xs">
                                    <i class="fa-solid fa-mug-hot text-3xl text-slate-500 mb-2 block"></i>
                                    No leave applications recorded.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TAB 5: PROFILE & IDENTITY DETAILS          -->
            <!-- ========================================== -->
            <div id="tabProfile" class="emp-tab-content hidden space-y-6">
                <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                    <i class="fa-solid fa-id-card text-cyan-400"></i>
                    <span>Employee Record & Official File Details</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                    <div class="p-4 rounded-xl bg-[#060c20] border border-slate-800 space-y-1">
                        <span class="text-slate-400 text-[11px] block">Full Name</span>
                        <span class="font-bold text-white text-sm">{{ $employee->name }}</span>
                    </div>

                    <div class="p-4 rounded-xl bg-[#060c20] border border-slate-800 space-y-1">
                        <span class="text-slate-400 text-[11px] block">National ID (NID)</span>
                        <span class="font-mono text-cyan-300 text-sm">{{ $employee->nid ?? 'Not provided' }}</span>
                    </div>

                    <div class="p-4 rounded-xl bg-[#060c20] border border-slate-800 space-y-1">
                        <span class="text-slate-400 text-[11px] block">Blood Group & Gender</span>
                        <span class="font-bold text-white text-sm">{{ $employee->blood_group ?? 'N/A' }} ({{ ucfirst($employee->gender ?? 'N/A') }})</span>
                    </div>

                    <div class="p-4 rounded-xl bg-[#060c20] border border-slate-800 space-y-1">
                        <span class="text-slate-400 text-[11px] block">Emergency Contact</span>
                        <span class="font-mono text-emerald-300 text-sm">{{ $employee->emergency_phone ?? 'N/A' }}</span>
                    </div>

                    <div class="p-4 rounded-xl bg-[#060c20] border border-slate-800 space-y-1">
                        <span class="text-slate-400 text-[11px] block">Bank / Payment Account</span>
                        <span class="font-bold text-white text-sm">{{ $employee->payment_method ?? 'Cash' }} ({{ $employee->account_number ?? 'N/A' }})</span>
                    </div>

                    <div class="p-4 rounded-xl bg-[#060c20] border border-slate-800 space-y-1">
                        <span class="text-slate-400 text-[11px] block">Shift & Work Schedule</span>
                        <span class="font-bold text-white text-sm">{{ ucfirst($employee->shift ?? 'Day') }} Shift</span>
                    </div>

                    <div class="md:col-span-3 p-4 rounded-xl bg-[#060c20] border border-slate-800 space-y-1">
                        <span class="text-slate-400 text-[11px] block">Present Address</span>
                        <span class="text-slate-200">{{ $employee->present_address ?? 'Not recorded' }}</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- MODAL: REQUEST ADVANCE SALARY -->
<div id="advanceModal" class="fixed inset-0 z-50 hidden bg-black/75 backdrop-blur-sm p-4 flex items-center justify-center">
    <div class="relative w-full max-w-md bg-[#09112a] border border-amber-500/40 rounded-3xl shadow-2xl p-6 text-white">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800 mb-4">
            <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-hand-holding-dollar text-amber-400"></i>
                <span>Request Advance Salary (অগ্রিম বেতনের আবেদন)</span>
            </h3>
            <button type="button" onclick="closeAdvanceModal()" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('employee.advance.request') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Requested Amount (টাকার পরিমাণ) *</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold">৳</span>
                    <input type="number" step="100" name="amount" required placeholder="e.g. 5000"
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl pl-8 pr-3 py-2.5 text-xs text-white font-mono font-bold focus:outline-none focus:border-amber-400">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Repayment Installments (কয় মাসে কর্তন হবে)</label>
                <select name="repayment_months" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-400 font-bold">
                    <option value="1">1 Month (পরবর্তী মাসের বেতনে সমন্বয়)</option>
                    <option value="2">2 Months (২ কিস্তিতে কর্তন)</option>
                    <option value="3">3 Months (৩ কিস্তিতে কর্তন)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Reason for Advance (আবেদনের কারণ) *</label>
                <textarea name="reason" rows="3" required placeholder="e.g. জরুরি পারিবারিক চিকিৎসা খরচ..."
                          class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-400"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-800">
                <button type="button" onclick="closeAdvanceModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs shadow-lg">Submit Request</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: APPLY FOR LEAVE -->
<div id="leaveModal" class="fixed inset-0 z-50 hidden bg-black/75 backdrop-blur-sm p-4 flex items-center justify-center">
    <div class="relative w-full max-w-md bg-[#09112a] border border-purple-500/40 rounded-3xl shadow-2xl p-6 text-white">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800 mb-4">
            <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-mug-hot text-purple-400"></i>
                <span>Apply for Leave (ছুটির আবেদন)</span>
            </h3>
            <button type="button" onclick="closeLeaveModal()" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('employee.leave.request') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Leave Type (ছুটির ধরণ) *</label>
                <select name="leave_type" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-purple-400 font-bold">
                    <option value="casual">Casual Leave (নৈমিত্তিক ছুটি)</option>
                    <option value="sick">Sick / Medical Leave (অসুস্থতাজনিত ছুটি)</option>
                    <option value="annual">Annual / Earned Leave (বাৎসরিক ছুটি)</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Start Date *</label>
                    <input type="date" name="start_date" required value="{{ date('Y-m-d') }}"
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-purple-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">End Date *</label>
                    <input type="date" name="end_date" required value="{{ date('Y-m-d') }}"
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-purple-400">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Reason (ছুটির কারণ)</label>
                <textarea name="reason" rows="2" placeholder="কারণ লিখুন..."
                          class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-purple-400"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-800">
                <button type="button" onclick="closeLeaveModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-lg">Submit Leave Application</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function switchEmpTab(tab) {
        document.querySelectorAll('.emp-tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.emp-tab-btn').forEach(b => {
            b.className = "emp-tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 text-slate-400 hover:text-white";
        });

        if (tab === 'salary') {
            document.getElementById('tabSalary').classList.remove('hidden');
            document.getElementById('btnTabSalary').className = "emp-tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md";
        } else if (tab === 'attendance') {
            document.getElementById('tabAttendance').classList.remove('hidden');
            document.getElementById('btnTabAttendance').className = "emp-tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 bg-gradient-to-r from-cyan-600 to-blue-600 text-white shadow-md";
        } else if (tab === 'advances') {
            document.getElementById('tabAdvances').classList.remove('hidden');
            document.getElementById('btnTabAdvances').className = "emp-tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 bg-gradient-to-r from-amber-600 to-orange-600 text-white shadow-md";
        } else if (tab === 'leaves') {
            document.getElementById('tabLeaves').classList.remove('hidden');
            document.getElementById('btnTabLeaves').className = "emp-tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white shadow-md";
        } else if (tab === 'profile') {
            document.getElementById('tabProfile').classList.remove('hidden');
            document.getElementById('btnTabProfile').className = "emp-tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 bg-gradient-to-r from-blue-600 to-cyan-600 text-white shadow-md";
        }
    }

    function openAdvanceModal() {
        document.getElementById('advanceModal').classList.remove('hidden');
    }
    function closeAdvanceModal() {
        document.getElementById('advanceModal').classList.add('hidden');
    }

    function openLeaveModal() {
        document.getElementById('leaveModal').classList.remove('hidden');
    }
    function closeLeaveModal() {
        document.getElementById('leaveModal').classList.add('hidden');
    }
</script>
@endpush
@endsection

