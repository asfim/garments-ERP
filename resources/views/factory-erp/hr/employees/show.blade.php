@extends('layouts.app')

@section('title', $employee->name . ' - Employee 360 Profile | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Action Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('factory.hr.employees.index') }}" 
               class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold flex items-center gap-1.5 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back to Employees</span>
            </a>
            <div>
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-id-badge text-cyan-400"></i>
                    Employee 360° Profile (কর্মী প্রোফাইল ও পূর্ণাঙ্গ হিসাব)
                </h2>
                <p class="text-xs text-slate-400 font-mono">{{ $employee->employee_code }} • {{ $employee->name }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('factory.hr.employees.index') }}" 
               class="px-4 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs shadow-lg transition-all flex items-center gap-2">
                <i class="fa-solid fa-users"></i>
                <span>Employee Directory</span>
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="p-3 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs flex items-center gap-2">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <!-- Profile Header Card -->
    <div class="glass-panel rounded-2xl border border-slate-700/80 p-6 bg-[#09112a] text-white shadow-xl relative overflow-hidden">
        <div class="absolute -top-12 -right-12 w-48 h-48 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex flex-col md:flex-row items-center md:items-start gap-6 relative z-10">
            <!-- Employee Avatar / Photo -->
            <div class="relative flex-shrink-0">
                @if($employee->photo && file_exists(public_path('storage/' . $employee->photo)))
                    <div class="w-28 h-28 rounded-2xl overflow-hidden ring-4 ring-cyan-500/40 shadow-xl bg-slate-800">
                        <img src="{{ asset('storage/' . $employee->photo) }}" alt="{{ $employee->name }}" class="w-full h-full object-cover">
                    </div>
                @else
                    <div class="w-28 h-28 rounded-2xl bg-gradient-to-tr from-cyan-600 via-blue-600 to-indigo-600 flex items-center justify-center text-white font-extrabold text-3xl shadow-xl ring-4 ring-cyan-500/40">
                        {{ strtoupper(substr($employee->name, 0, 2)) }}
                    </div>
                @endif
                <span class="absolute -bottom-1 -right-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $employee->status === 'active' ? 'bg-emerald-500 text-slate-950' : 'bg-rose-500 text-white' }} shadow-md uppercase">
                    {{ $employee->status }}
                </span>
            </div>

            <!-- Profile Info -->
            <div class="flex-1 text-center md:text-left space-y-2">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-2">
                    <div>
                        <h1 class="text-2xl font-black text-white flex items-center justify-center md:justify-start gap-2.5">
                            {{ $employee->name }}
                            <span class="px-2.5 py-0.5 rounded-lg text-xs font-mono font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/40">
                                {{ $employee->employee_code }}
                            </span>
                        </h1>
                        <p class="text-xs text-cyan-400 font-semibold mt-0.5">
                            {{ $employee->designation->title ?? 'Garment Worker' }} 
                            <span class="text-slate-400">• {{ $employee->department->name ?? 'Floor Line' }}</span>
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center justify-center md:justify-end gap-2">
                        <span class="px-3 py-1 rounded-xl bg-slate-800 text-slate-300 text-xs border border-slate-700">
                            <i class="fa-solid fa-clock mr-1 text-cyan-400"></i>{{ $employee->shift }} Shift
                        </span>
                        <span class="px-3 py-1 rounded-xl bg-slate-800 text-slate-300 text-xs border border-slate-700 capitalize">
                            <i class="fa-solid fa-briefcase mr-1 text-indigo-400"></i>{{ $employee->employment_type }}
                        </span>
                        <span class="px-3 py-1 rounded-xl bg-slate-800 text-emerald-400 font-bold text-xs border border-slate-700 font-mono">
                            Gross: ৳{{ number_format($employee->gross_salary, 2) }}
                        </span>
                    </div>
                </div>

                <!-- Personal & Contact Details Pills -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-3 border-t border-slate-800 text-xs">
                    <div>
                        <span class="text-[10px] text-slate-400 block">Phone</span>
                        <span class="font-mono font-bold text-white">{{ $employee->phone }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 block">Emergency Contact</span>
                        <span class="font-mono text-slate-300">{{ $employee->emergency_phone ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 block">Joining Date</span>
                        <span class="font-mono text-slate-300">{{ $employee->joining_date ? $employee->joining_date->format('d M, Y') : '—' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 block">Payment Method</span>
                        <span class="text-emerald-400 font-semibold">{{ $employee->payment_method }} ({{ $employee->account_number ?? 'Hand Cash' }})</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4 Big Financial & Performance KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Salary Paid -->
        <div class="glass-panel rounded-2xl p-4 border border-emerald-500/30 bg-emerald-500/5 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-emerald-400 uppercase tracking-wider block">Total Salary Disbursed</span>
                <span class="text-2xl font-mono font-extrabold text-white mt-1 block">৳{{ number_format($totalSalaryPaid, 2) }}</span>
                <span class="text-[10px] text-slate-400">{{ $payrolls->where('payment_status', 'paid')->count() }} Monthly Payments</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center">
                <i class="fa-solid fa-money-bill-wave text-xl"></i>
            </div>
        </div>

        <!-- Overtime Hours & OT Earned -->
        <div class="glass-panel rounded-2xl p-4 border border-amber-500/30 bg-amber-500/5 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-amber-400 uppercase tracking-wider block">Overtime Pay Earned</span>
                <span class="text-2xl font-mono font-extrabold text-amber-300 mt-1 block">৳{{ number_format($totalOtEarned, 2) }}</span>
                <span class="text-[10px] text-slate-400">Total OT Recorded</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center">
                <i class="fa-solid fa-business-time text-xl"></i>
            </div>
        </div>

        <!-- Advance Salary & Recovery -->
        <div class="glass-panel rounded-2xl p-4 border border-rose-500/30 bg-rose-500/5 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-rose-400 uppercase tracking-wider block">Advance Salary Status</span>
                <span class="text-2xl font-mono font-extrabold text-white mt-1 block">৳{{ number_format($totalAdvanceTaken, 2) }}</span>
                <span class="text-[10px] text-emerald-400 font-bold">
                    Recovered (শোধ): ৳{{ number_format($totalAdvanceRecovered, 2) }}
                    @if($totalAdvancePending > 0)
                        <span class="text-rose-400 ml-1">| বাকি: ৳{{ number_format($totalAdvancePending, 2) }}</span>
                    @endif
                </span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center">
                <i class="fa-solid fa-hand-holding-dollar text-xl"></i>
            </div>
        </div>

        <!-- Leaves Taken -->
        <div class="glass-panel rounded-2xl p-4 border border-purple-500/30 bg-purple-500/5 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-purple-400 uppercase tracking-wider block">Leaves Taken</span>
                <span class="text-2xl font-mono font-extrabold text-purple-300 mt-1 block">{{ $totalLeavesTaken }} Days</span>
                <span class="text-[10px] text-slate-400">{{ $leaves->where('status', 'approved')->count() }} Approved Requests</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-500/10 border border-purple-500/20 text-purple-400 flex items-center justify-center">
                <i class="fa-solid fa-calendar-check text-xl"></i>
            </div>
        </div>

    </div>

    <!-- ============================================================== -->
    <!-- 1. ATTENDANCE CALENDAR (YEAR & MONTH)                           -->
    <!-- ============================================================== -->
    <div class="glass-panel rounded-2xl border border-slate-700/80 p-5 bg-[#09112a]">
        
        <!-- Calendar Header & Year/Month Selector -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-700/80 mb-5">
            <div>
                <h3 class="text-base font-extrabold text-white flex items-center gap-2">
                    <i class="fa-solid fa-calendar-days text-cyan-400"></i>
                    Attendance Calendar (হাজিরা ক্যালেন্ডার)
                </h3>
                <p class="text-xs text-slate-400">Visual monthly calendar showing daily present, late, absent, leaves, and overtime hours</p>
            </div>

            <!-- Month & Year Selector Form -->
            <form method="GET" action="{{ route('factory.hr.employees.show', $employee->id) }}" class="flex items-center gap-2">
                <select name="month" class="bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none focus:border-cyan-400 font-semibold">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endfor
                </select>

                <select name="year" class="bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none focus:border-cyan-400 font-semibold font-mono">
                    @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>

                <button type="submit" class="px-3 py-1.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs transition-colors flex items-center gap-1">
                    <i class="fa-solid fa-rotate"></i>
                    <span>Load</span>
                </button>
            </form>
        </div>

        <!-- Attendance Stats for this Month -->
        <div class="grid grid-cols-2 sm:grid-cols-6 gap-2 mb-4 text-center">
            <div class="p-2 rounded-xl bg-emerald-500/10 border border-emerald-500/30">
                <span class="text-[10px] text-emerald-400 block font-bold uppercase">Present</span>
                <span class="text-base font-mono font-extrabold text-emerald-300">{{ $monthPresent }} Days</span>
            </div>
            <div class="p-2 rounded-xl bg-amber-500/10 border border-amber-500/30">
                <span class="text-[10px] text-amber-400 block font-bold uppercase">Late</span>
                <span class="text-base font-mono font-extrabold text-amber-300">{{ $monthLate }} Days</span>
            </div>
            <div class="p-2 rounded-xl bg-purple-500/10 border border-purple-500/30">
                <span class="text-[10px] text-purple-400 block font-bold uppercase">On Leave</span>
                <span class="text-base font-mono font-extrabold text-purple-300">{{ $monthLeave }} Days</span>
            </div>
            <div class="p-2 rounded-xl bg-rose-500/10 border border-rose-500/30">
                <span class="text-[10px] text-rose-400 block font-bold uppercase">Absent</span>
                <span class="text-base font-mono font-extrabold text-rose-300">{{ $monthAbsent }} Days</span>
            </div>
            <div class="p-2 rounded-xl bg-amber-500/10 border border-amber-500/30">
                <span class="text-[10px] text-amber-400 block font-bold uppercase">Total OT</span>
                <span class="text-base font-mono font-extrabold text-amber-300">{{ $monthTotalOtHours }} Hours</span>
            </div>
            <div class="p-2 rounded-xl bg-cyan-500/10 border border-cyan-500/30">
                <span class="text-[10px] text-cyan-400 block font-bold uppercase">Month</span>
                <span class="text-xs font-mono font-bold text-cyan-300">{{ $monthName }} {{ $year }}</span>
            </div>
        </div>

        <!-- 7-Column Visual Calendar Grid -->
        <div class="overflow-x-auto">
            <div class="min-w-[650px]">
                
                <!-- Weekday Headers -->
                <div class="grid grid-cols-7 gap-1 text-center font-bold text-xs uppercase mb-1">
                    <div class="py-2 rounded-lg bg-slate-800/80 text-slate-400">Sun</div>
                    <div class="py-2 rounded-lg bg-slate-800/80 text-slate-400">Mon</div>
                    <div class="py-2 rounded-lg bg-slate-800/80 text-slate-400">Tue</div>
                    <div class="py-2 rounded-lg bg-slate-800/80 text-slate-400">Wed</div>
                    <div class="py-2 rounded-lg bg-slate-800/80 text-slate-400">Thu</div>
                    <div class="py-2 rounded-lg bg-rose-950/60 text-rose-400 border border-rose-500/30">Fri (Weekend)</div>
                    <div class="py-2 rounded-lg bg-slate-800/80 text-slate-400">Sat</div>
                </div>

                <!-- Calendar Days Grid -->
                <div class="grid grid-cols-7 gap-1">
                    <!-- Blank cells before 1st day of month -->
                    @for($i = 0; $i < $startDayOfWeek; $i++)
                        <div class="h-20 rounded-xl bg-slate-900/30 border border-slate-800/40 opacity-40"></div>
                    @endfor

                    <!-- Actual Month Days -->
                    @for($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);
                            $carbonDay = \Carbon\Carbon::createFromDate($year, $month, $day);
                            $isFriday = ($carbonDay->dayOfWeek === 5); // 5 = Friday
                            $att = $attendances->get($dateStr);
                        @endphp

                        <div class="h-20 rounded-xl p-1.5 border transition-all flex flex-col justify-between 
                            {{ $att ? (
                                $att->status === 'present' ? 'bg-emerald-950/30 border-emerald-500/40 hover:border-emerald-400' :
                                ($att->status === 'late' ? 'bg-amber-950/30 border-amber-500/40 hover:border-amber-400' :
                                ($att->status === 'leave' ? 'bg-purple-950/30 border-purple-500/40 hover:border-purple-400' :
                                'bg-rose-950/30 border-rose-500/40 hover:border-rose-400'))
                            ) : (
                                $isFriday ? 'bg-rose-950/20 border-rose-500/20' : 'bg-slate-900/60 border-slate-800 hover:border-slate-700'
                            ) }}">
                            
                            <!-- Day Number & Status Dot -->
                            <div class="flex items-center justify-between text-xs font-mono">
                                <span class="font-bold {{ $att ? 'text-white' : ($isFriday ? 'text-rose-400' : 'text-slate-400') }}">
                                    {{ $day }}
                                </span>
                                @if($att)
                                    @if($att->status === 'present')
                                        <span class="w-2 h-2 rounded-full bg-emerald-400 shadow-sm shadow-emerald-400/50"></span>
                                    @elseif($att->status === 'late')
                                        <span class="w-2 h-2 rounded-full bg-amber-400 shadow-sm shadow-amber-400/50"></span>
                                    @elseif($att->status === 'leave')
                                        <span class="w-2 h-2 rounded-full bg-purple-400 shadow-sm shadow-purple-400/50"></span>
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-rose-400 shadow-sm shadow-rose-400/50"></span>
                                    @endif
                                @elseif($isFriday)
                                    <span class="text-[9px] text-rose-400 font-bold">OFF</span>
                                @endif
                            </div>

                            <!-- Punch Time & Overtime Details -->
                            <div class="text-[9px] font-mono leading-tight">
                                @if($att)
                                    @if($att->check_in)
                                        <div class="text-slate-300">{{ substr($att->check_in, 0, 5) }} - {{ $att->check_out ? substr($att->check_out, 0, 5) : '...' }}</div>
                                    @endif
                                    @if($att->overtime_hours > 0)
                                        <div class="text-amber-400 font-bold">+{{ $att->overtime_hours }}h OT</div>
                                    @endif
                                    <div class="capitalize font-bold text-[8px] 
                                        {{ $att->status === 'present' ? 'text-emerald-400' : ($att->status === 'late' ? 'text-amber-400' : ($att->status === 'leave' ? 'text-purple-400' : 'text-rose-400')) }}">
                                        {{ $att->status }}
                                    </div>
                                @elseif($isFriday)
                                    <div class="text-rose-400/80 text-[10px] font-semibold text-center mt-2">Weekend</div>
                                @else
                                    <div class="text-slate-600 text-[10px] text-center mt-2">—</div>
                                @endif
                            </div>

                        </div>
                    @endfor
                </div>

            </div>
        </div>

    </div>

    <!-- ============================================================== -->
    <!-- 2. ADVANCE SALARY & LOAN REPAYMENT HISTORY (অগ্রিম ও শোধ)       -->
    <!-- ============================================================== -->
    <div class="glass-panel rounded-2xl border border-slate-700/80 p-5 bg-[#09112a]">
        <div class="flex items-center justify-between pb-3 border-b border-slate-700/80 mb-4">
            <div>
                <h3 class="text-base font-extrabold text-white flex items-center gap-2">
                    <i class="fa-solid fa-hand-holding-dollar text-rose-400"></i>
                    Advance Salary & Repayment Log (অগ্রিম বেতন ও ঋণ পরিশোধ হিসাব)
                </h3>
                <p class="text-xs text-slate-400">Track all mid-month advances taken, deduction month, and repayment status</p>
            </div>

            <div class="text-right">
                <span class="text-xs text-slate-400">Total Taken: <strong class="text-white font-mono">৳{{ number_format($totalAdvanceTaken, 2) }}</strong></span>
                <span class="mx-2 text-slate-600">|</span>
                <span class="text-xs text-emerald-400">Recovered (শোধ): <strong class="font-mono">৳{{ number_format($totalAdvanceRecovered, 2) }}</strong></span>
                @if($totalAdvancePending > 0)
                    <span class="mx-2 text-slate-600">|</span>
                    <span class="text-xs text-rose-400">Outstanding (বাকি): <strong class="font-mono">৳{{ number_format($totalAdvancePending, 2) }}</strong></span>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-800/80 text-slate-300 uppercase text-[10px] font-bold border-b border-slate-700/80">
                    <tr>
                        <th class="py-2.5 px-3">Date</th>
                        <th class="py-2.5 px-3 text-right">Advance Amount (৳)</th>
                        <th class="py-2.5 px-3 text-center">Deduct Month</th>
                        <th class="py-2.5 px-3">Payment Mode</th>
                        <th class="py-2.5 px-3">Reason / Purpose</th>
                        <th class="py-2.5 px-3 text-center">Repayment Status</th>
                        <th class="py-2.5 px-3">Approved By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($advances as $adv)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="py-2.5 px-3 font-mono text-slate-300">
                            {{ $adv->advance_date->format('d M, Y') }}
                        </td>
                        <td class="py-2.5 px-3 text-right font-mono font-bold text-rose-400 text-sm">
                            ৳{{ number_format($adv->amount, 2) }}
                        </td>
                        <td class="py-2.5 px-3 text-center font-mono text-cyan-300 font-bold">
                            {{ $adv->deduct_month }}
                        </td>
                        <td class="py-2.5 px-3 text-slate-200">
                            {{ $adv->payment_method }}
                        </td>
                        <td class="py-2.5 px-3 text-slate-300 max-w-xs truncate" title="{{ $adv->reason }}">
                            {{ $adv->reason ?? '—' }}
                        </td>
                        <td class="py-2.5 px-3 text-center">
                            @if($adv->status === 'recovered')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    RECOVERED (পরিশোধিত)
                                </span>
                            @elseif($adv->status === 'approved')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                    APPROVED (বেতন থেকে কাটা হবে)
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-700 text-slate-300">
                                    {{ strtoupper($adv->status) }}
                                </span>
                            @endif
                        </td>
                        <td class="py-2.5 px-3 text-slate-400 text-[10px]">
                            {{ $adv->approved_by ?? 'Admin' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-6 text-center text-slate-400 text-xs">
                            No advance salary taken by this worker.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- 3. PAYROLL & MONTHLY SALARY HISTORY (বেতন বিতরণ হিস্ট্রি)       -->
    <!-- ============================================================== -->
    <div class="glass-panel rounded-2xl border border-slate-700/80 p-5 bg-[#09112a]">
        <div class="flex items-center justify-between pb-3 border-b border-slate-700/80 mb-4">
            <div>
                <h3 class="text-base font-extrabold text-white flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice-dollar text-emerald-400"></i>
                    Monthly Salary & Payroll History (বেতন পরিশোধ হিস্ট্রি)
                </h3>
                <p class="text-xs text-slate-400">Monthly gross earnings, overtime additions, advance deductions, and net paid salary</p>
            </div>
            <div class="font-mono text-xs text-emerald-400 font-bold">
                Total Paid: ৳{{ number_format($totalSalaryPaid, 2) }}
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-800/80 text-slate-300 uppercase text-[10px] font-bold border-b border-slate-700/80">
                    <tr>
                        <th class="py-2.5 px-3">Month</th>
                        <th class="py-2.5 px-3">Slip No</th>
                        <th class="py-2.5 px-3 text-right">Basic</th>
                        <th class="py-2.5 px-3 text-right">Allowances</th>
                        <th class="py-2.5 px-3 text-center">OT (Hrs)</th>
                        <th class="py-2.5 px-3 text-right">OT Pay</th>
                        <th class="py-2.5 px-3 text-right">Advance Ded.</th>
                        <th class="py-2.5 px-3 text-right">Total Ded.</th>
                        <th class="py-2.5 px-3 text-right">Net Payable</th>
                        <th class="py-2.5 px-3 text-center">Status</th>
                        <th class="py-2.5 px-3 text-center">Payslip</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($payrolls as $p)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="py-2.5 px-3 font-bold text-white font-mono">
                            {{ \Carbon\Carbon::parse($p->month . '-01')->format('M Y') }}
                        </td>
                        <td class="py-2.5 px-3 font-mono text-cyan-400 text-[10px]">
                            {{ $p->payroll_code }}
                        </td>
                        <td class="py-2.5 px-3 text-right font-mono text-slate-300">
                            ৳{{ number_format($p->basic_salary, 2) }}
                        </td>
                        <td class="py-2.5 px-3 text-right font-mono text-slate-300">
                            ৳{{ number_format($p->allowances, 2) }}
                        </td>
                        <td class="py-2.5 px-3 text-center font-mono font-bold text-amber-400">
                            {{ $p->overtime_hours }}h
                        </td>
                        <td class="py-2.5 px-3 text-right font-mono font-bold text-amber-300">
                            ৳{{ number_format($p->overtime_amount, 2) }}
                        </td>
                        <td class="py-2.5 px-3 text-right font-mono font-bold {{ $p->advance_deduction > 0 ? 'text-rose-400' : 'text-slate-400' }}">
                            ৳{{ number_format($p->advance_deduction, 2) }}
                        </td>
                        <td class="py-2.5 px-3 text-right font-mono font-bold text-rose-400">
                            ৳{{ number_format($p->deductions, 2) }}
                        </td>
                        <td class="py-2.5 px-3 text-right font-mono font-extrabold text-emerald-400 text-sm">
                            ৳{{ number_format($p->net_salary, 2) }}
                        </td>
                        <td class="py-2.5 px-3 text-center">
                            @if($p->payment_status === 'paid')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">PAID</span>
                                @if($p->payment_date)
                                    <div class="text-[9px] text-slate-400 font-mono mt-0.5">{{ $p->payment_date->format('d M') }}</div>
                                @endif
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">UNPAID</span>
                            @endif
                        </td>
                        <td class="py-2.5 px-3 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('factory.hr.payroll.payslip', $p->id) }}" title="View Payslip" 
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
                        <td colspan="11" class="py-6 text-center text-slate-400 text-xs">
                            No salary slips generated for this worker yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- 4. LEAVES RECORD (ছুটির হিসাব)                                 -->
    <!-- ============================================================== -->
    <div class="glass-panel rounded-2xl border border-slate-700/80 p-5 bg-[#09112a]">
        <div class="flex items-center justify-between pb-3 border-b border-slate-700/80 mb-4">
            <div>
                <h3 class="text-base font-extrabold text-white flex items-center gap-2">
                    <i class="fa-solid fa-calendar-check text-purple-400"></i>
                    Leaves & Absence Record (ছুটির হিসাব)
                </h3>
                <p class="text-xs text-slate-400">Casual, sick, maternity, and unpaid leaves taken by this employee</p>
            </div>
            <div class="font-mono text-xs text-purple-300 font-bold">
                Total Leaves: {{ $totalLeavesTaken }} Days
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-800/80 text-slate-300 uppercase text-[10px] font-bold border-b border-slate-700/80">
                    <tr>
                        <th class="py-2.5 px-3">Leave Type</th>
                        <th class="py-2.5 px-3">Duration (Dates)</th>
                        <th class="py-2.5 px-3 text-center">Total Days</th>
                        <th class="py-2.5 px-3">Reason</th>
                        <th class="py-2.5 px-3 text-center">Status</th>
                        <th class="py-2.5 px-3">Approved By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($leaves as $leave)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="py-2.5 px-3 font-semibold text-white capitalize">
                            {{ $leave->leave_type }} Leave
                        </td>
                        <td class="py-2.5 px-3 font-mono text-slate-200">
                            {{ $leave->start_date->format('d M, Y') }} to {{ $leave->end_date->format('d M, Y') }}
                        </td>
                        <td class="py-2.5 px-3 text-center font-mono font-bold text-purple-300">
                            {{ $leave->days_count }}
                        </td>
                        <td class="py-2.5 px-3 text-slate-300 max-w-xs truncate" title="{{ $leave->reason }}">
                            {{ $leave->reason ?? '—' }}
                        </td>
                        <td class="py-2.5 px-3 text-center">
                            @if($leave->status === 'approved')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    Approved
                                </span>
                            @elseif($leave->status === 'rejected')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                    Rejected
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                    Pending
                                </span>
                            @endif
                        </td>
                        <td class="py-2.5 px-3 text-slate-400 text-[10px]">
                            {{ $leave->approved_by ?? '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-slate-400 text-xs">
                            No leave applications on file.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

