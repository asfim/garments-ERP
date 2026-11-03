@extends('layouts.app')

@section('title', 'HR Management Dashboard | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-users-gear text-emerald-400"></i>
                Human Resources & Payroll Dashboard (এইচআর ড্যাশবোর্ড)
            </h2>
            <p class="text-xs text-slate-400">Garment workers management, attendance tracking, monthly payroll, payslip, and leave control</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Quick Link: Employees -->
            <a href="{{ route('factory.hr.employees.index') }}" 
               class="px-3.5 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs shadow-lg transition-all flex items-center gap-2">
                <i class="fa-solid fa-user-plus"></i>
                <span>Employees</span>
            </a>

            <!-- Quick Link: Attendance -->
            <a href="{{ route('factory.hr.attendance.index') }}" 
               class="px-3.5 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs shadow-lg transition-all flex items-center gap-2">
                <i class="fa-solid fa-clipboard-user"></i>
                <span>Today's Attendance</span>
            </a>

            <!-- Quick Link: Payroll -->
            <a href="{{ route('factory.hr.payroll.index') }}" 
               class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg transition-all flex items-center gap-2">
                <i class="fa-solid fa-file-invoice-dollar"></i>
                <span>Payroll</span>
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
    @if(session('error'))
    <div class="p-3 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs flex items-center gap-2">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- Top KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Employees -->
        <div class="glass-panel rounded-2xl p-4 border border-slate-700/60 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Total Workers</span>
                <span class="text-2xl font-mono font-extrabold text-white mt-1 block">{{ $totalEmployees }}</span>
                <span class="text-[10px] text-emerald-400 font-semibold">{{ $activeEmployees }} Active on Floor</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center">
                <i class="fa-solid fa-users text-xl"></i>
            </div>
        </div>

        <!-- Today Attendance -->
        <div class="glass-panel rounded-2xl p-4 border border-slate-700/60 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Present Today</span>
                <span class="text-2xl font-mono font-extrabold text-emerald-400 mt-1 block">{{ $presentToday }}</span>
                <span class="text-[10px] text-slate-400">
                    Late: <span class="text-amber-400 font-bold">{{ $lateToday }}</span> | 
                    Leave: <span class="text-purple-400 font-bold">{{ $leaveToday }}</span> | 
                    Absent: <span class="text-rose-400 font-bold">{{ $absentToday }}</span>
                </span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center">
                <i class="fa-solid fa-user-check text-xl"></i>
            </div>
        </div>

        <!-- Monthly Payroll Cost -->
        <div class="glass-panel rounded-2xl p-4 border border-slate-700/60 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Payroll Budget ({{ date('M Y') }})</span>
                <span class="text-2xl font-mono font-extrabold text-white mt-1 block">৳{{ number_format($totalPayrollAmount, 0) }}</span>
                <span class="text-[10px] text-cyan-400 font-semibold">{{ $paidPayrollCount }} of {{ $totalPayrollCount }} Disbursed</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center">
                <i class="fa-solid fa-money-bill-wave text-xl"></i>
            </div>
        </div>

        <!-- Departments Count -->
        <div class="glass-panel rounded-2xl p-4 border border-slate-700/60 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Factory Sections</span>
                <span class="text-2xl font-mono font-extrabold text-white mt-1 block">{{ $departmentsCount }}</span>
                <span class="text-[10px] text-indigo-400 font-semibold">Cutting, Sewing, Finishing & QC</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center">
                <i class="fa-solid fa-sitemap text-xl"></i>
            </div>
        </div>

    </div>

    <!-- Quick Modules Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <a href="{{ route('factory.hr.employees.index') }}" class="glass-panel rounded-xl p-3 border border-slate-800 hover:border-cyan-500/50 hover:bg-slate-800/40 transition-all text-center group">
            <div class="w-10 h-10 mx-auto rounded-lg bg-cyan-500/10 text-cyan-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-id-badge text-lg"></i>
            </div>
            <div class="text-xs font-bold text-white">Employee List</div>
            <div class="text-[10px] text-slate-400">Add & Manage</div>
        </a>

        <a href="{{ route('factory.hr.attendance.index') }}" class="glass-panel rounded-xl p-3 border border-slate-800 hover:border-amber-500/50 hover:bg-slate-800/40 transition-all text-center group">
            <div class="w-10 h-10 mx-auto rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-clipboard-user text-lg"></i>
            </div>
            <div class="text-xs font-bold text-white">Daily Attendance</div>
            <div class="text-[10px] text-slate-400">Punch & Overtime</div>
        </a>

        <a href="{{ route('factory.hr.leaves.index') }}" class="glass-panel rounded-xl p-3 border border-slate-800 hover:border-purple-500/50 hover:bg-slate-800/40 transition-all text-center group">
            <div class="w-10 h-10 mx-auto rounded-lg bg-purple-500/10 text-purple-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-calendar-check text-lg"></i>
            </div>
            <div class="text-xs font-bold text-white">Leave Requests</div>
            <div class="text-[10px] text-slate-400">Apply & Approve</div>
        </a>

        <a href="{{ route('factory.hr.payroll.index') }}" class="glass-panel rounded-xl p-3 border border-slate-800 hover:border-emerald-500/50 hover:bg-slate-800/40 transition-all text-center group">
            <div class="w-10 h-10 mx-auto rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-file-invoice-dollar text-lg"></i>
            </div>
            <div class="text-xs font-bold text-white">Salary & Payroll</div>
            <div class="text-[10px] text-slate-400">Generate & Payslip</div>
        </a>

        <a href="{{ route('factory.hr.departments.index') }}" class="glass-panel rounded-xl p-3 border border-slate-800 hover:border-indigo-500/50 hover:bg-slate-800/40 transition-all text-center group">
            <div class="w-10 h-10 mx-auto rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-sitemap text-lg"></i>
            </div>
            <div class="text-xs font-bold text-white">Departments</div>
            <div class="text-[10px] text-slate-400">Sections & Ranks</div>
        </a>

        <a href="{{ route('factory.hr.attendance.index') }}?date={{ $today }}" class="glass-panel rounded-xl p-3 border border-slate-800 hover:border-rose-500/50 hover:bg-slate-800/40 transition-all text-center group">
            <div class="w-10 h-10 mx-auto rounded-lg bg-rose-500/10 text-rose-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-business-time text-lg"></i>
            </div>
            <div class="text-xs font-bold text-white">Overtime Log</div>
            <div class="text-[10px] text-slate-400">OT Tracking</div>
        </a>
    </div>

    <!-- Main 2-Column Content: Today's Attendance & Pending Leaves -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Column 1 & 2: Today's Floor Attendance -->
        <div class="lg:col-span-2 glass-panel rounded-2xl border border-slate-700/60 overflow-hidden">
            <div class="p-4 border-b border-slate-700/60 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-clipboard-check text-emerald-400"></i>
                        Today's Floor Presence (আজকের হাজিরা লগ)
                    </h3>
                    <p class="text-[11px] text-slate-400">Live check-in and overtime recorded for {{ \Carbon\Carbon::parse($today)->format('l, d F Y') }}</p>
                </div>
                <a href="{{ route('factory.hr.attendance.index') }}" class="text-xs text-cyan-400 hover:text-cyan-300 font-semibold flex items-center gap-1">
                    <span>Full Sheet</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-800/60 text-slate-400 uppercase text-[10px] font-bold border-b border-slate-700/60">
                        <tr>
                            <th class="py-2.5 px-3">Emp ID</th>
                            <th class="py-2.5 px-3">Employee Name</th>
                            <th class="py-2.5 px-3">Department</th>
                            <th class="py-2.5 px-3 text-center">Status</th>
                            <th class="py-2.5 px-3 text-center">In / Out</th>
                            <th class="py-2.5 px-3 text-center">OT (Hrs)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @forelse($recentAttendances as $att)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-2.5 px-3 font-mono font-bold text-cyan-400">{{ $att->employee->employee_code }}</td>
                            <td class="py-2.5 px-3">
                                <div class="font-bold text-white">{{ $att->employee->name }}</div>
                                <div class="text-[10px] text-slate-400">{{ $att->employee->designation->title ?? 'N/A' }}</div>
                            </td>
                            <td class="py-2.5 px-3 text-slate-300">{{ $att->employee->department->name ?? 'N/A' }}</td>
                            <td class="py-2.5 px-3 text-center">
                                @if($att->status === 'present')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Present</span>
                                @elseif($att->status === 'late')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">Late</span>
                                @elseif($att->status === 'leave')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">Leave</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">Absent</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-center font-mono text-[11px] text-slate-300">
                                {{ $att->check_in ? substr($att->check_in, 0, 5) : '—' }} - {{ $att->check_out ? substr($att->check_out, 0, 5) : '—' }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-mono font-bold {{ $att->overtime_hours > 0 ? 'text-amber-400' : 'text-slate-400' }}">
                                {{ $att->overtime_hours > 0 ? $att->overtime_hours . ' hrs' : '—' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-400 text-xs">
                                No attendance marked yet today.
                                <a href="{{ route('factory.hr.attendance.index') }}" class="text-cyan-400 font-bold hover:underline ml-1">Open Attendance Sheet</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Column 3: Department Breakdown & Pending Leaves -->
        <div class="space-y-4">
            
            <!-- Pending Leaves Card -->
            <div class="glass-panel rounded-2xl border border-slate-700/60 overflow-hidden">
                <div class="p-4 border-b border-slate-700/60 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-amber-400"></i>
                        Pending Leaves (ছুটির আবেদন)
                    </h3>
                    <a href="{{ route('factory.hr.leaves.index') }}" class="text-[11px] text-cyan-400 hover:text-cyan-300 font-semibold">View All</a>
                </div>
                <div class="p-3 divide-y divide-slate-800/80">
                    @forelse($pendingLeaves as $leave)
                    <div class="py-2.5 first:pt-0 last:pb-0 flex items-center justify-between">
                        <div>
                            <div class="font-bold text-xs text-white">{{ $leave->employee->name }}</div>
                            <div class="text-[10px] text-slate-400">
                                {{ ucfirst($leave->leave_type) }} Leave ({{ $leave->days_count }} days)
                            </div>
                            <div class="text-[10px] text-slate-400 font-mono">
                                {{ $leave->start_date->format('d M') }} to {{ $leave->end_date->format('d M, Y') }}
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <form action="{{ route('factory.hr.leaves.status.update', $leave->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" title="Approve" class="p-1.5 rounded-lg bg-emerald-500/20 hover:bg-emerald-500/40 text-emerald-400 text-xs transition-colors">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                            </form>
                            <form action="{{ route('factory.hr.leaves.status.update', $leave->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" title="Reject" class="p-1.5 rounded-lg bg-rose-500/20 hover:bg-rose-500/40 text-rose-400 text-xs transition-colors">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="py-4 text-center text-slate-400 text-xs">
                        <i class="fa-solid fa-circle-check text-emerald-400 text-base mb-1 block"></i>
                        No pending leave applications.
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Factory Department Strength -->
            <div class="glass-panel rounded-2xl border border-slate-700/60 p-4">
                <h3 class="text-sm font-bold text-white flex items-center justify-between mb-3">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-sitemap text-indigo-400"></i>
                        Section Strength
                    </span>
                    <a href="{{ route('factory.hr.departments.index') }}" class="text-[11px] text-cyan-400 hover:text-cyan-300 font-semibold">Manage</a>
                </h3>
                <div class="space-y-2">
                    @foreach($departments as $dept)
                    <div class="flex items-center justify-between text-xs py-1 border-b border-slate-800/80 last:border-0">
                        <span class="text-slate-300 font-medium">{{ $dept->name }}</span>
                        <span class="px-2 py-0.5 rounded-full bg-blue-500/20 text-cyan-300 font-mono font-bold text-[10px]">
                            {{ $dept->employees_count }} Workers
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
