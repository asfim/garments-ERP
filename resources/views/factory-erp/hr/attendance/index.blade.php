@extends('layouts.app')

@section('title', 'Daily Attendance | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-clipboard-user text-amber-400"></i>
                Daily Attendance & Overtime Tracker (দৈনিক হাজিরা ও ওভারটাইম)
            </h2>
            <p class="text-xs text-slate-400">Mark daily factory employee presence, in/out punch times, and track overtime hours</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Quick Action: Mark All Present -->
            <form action="{{ route('factory.hr.attendance.bulk.store') }}" method="POST" onsubmit="return confirm('Mark all unmarked active employees as PRESENT for {{ $date }}?')">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">
                <button type="submit" 
                        class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-500/25 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-check-double"></i>
                    <span>Mark All Present</span>
                </button>
            </form>

            <!-- Download CSV -->
            <a href="{{ route('factory.hr.attendance.export.csv', ['date' => $date]) }}" 
               class="px-3.5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-emerald-400 hover:text-emerald-300 border border-slate-700 hover:border-emerald-500/40 font-bold text-xs shadow-lg transition-all flex items-center gap-2">
                <i class="fa-solid fa-file-csv text-sm"></i>
                <span>Download CSV</span>
            </a>

            <!-- Download PDF -->
            <a href="{{ route('factory.hr.attendance.export.pdf', ['date' => $date]) }}" 
               class="px-3.5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-rose-400 hover:text-rose-300 border border-slate-700 hover:border-rose-500/40 font-bold text-xs shadow-lg transition-all flex items-center gap-2">
                <i class="fa-solid fa-file-pdf text-sm"></i>
                <span>Download PDF</span>
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

    <!-- Date & Filter Selector -->
    <div class="glass-panel rounded-2xl p-4 border border-slate-700/60">
        <form method="GET" action="{{ route('factory.hr.attendance.index') }}" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-4 items-end">
            <!-- Date Picker -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Attendance Date *</label>
                <input type="date" name="date" value="{{ $date }}" 
                       class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-400 font-mono">
            </div>

            <!-- Department Filter -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Section / Department</label>
                <select name="department_id" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-400">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ $deptId == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs transition-colors flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>Load Attendance</span>
                </button>
                <a href="{{ route('factory.hr.attendance.index') }}" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs">
                    Today
                </a>
            </div>
        </form>
    </div>

    <!-- Attendance KPI Cards for the Date -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="glass-panel rounded-xl p-3 border border-slate-700/60 text-center">
            <span class="text-[10px] uppercase font-bold text-slate-400 block">Total Staff</span>
            <span class="text-xl font-mono font-extrabold text-white mt-1 block">{{ $totalEmp }}</span>
        </div>

        <div class="glass-panel rounded-xl p-3 border border-emerald-500/30 text-center bg-emerald-500/5">
            <span class="text-[10px] uppercase font-bold text-emerald-400 block">Present</span>
            <span class="text-xl font-mono font-extrabold text-emerald-300 mt-1 block">{{ $presentCount }}</span>
        </div>

        <div class="glass-panel rounded-xl p-3 border border-amber-500/30 text-center bg-amber-500/5">
            <span class="text-[10px] uppercase font-bold text-amber-400 block">Late</span>
            <span class="text-xl font-mono font-extrabold text-amber-300 mt-1 block">{{ $lateCount }}</span>
        </div>

        <div class="glass-panel rounded-xl p-3 border border-purple-500/30 text-center bg-purple-500/5">
            <span class="text-[10px] uppercase font-bold text-purple-400 block">Leave</span>
            <span class="text-xl font-mono font-extrabold text-purple-300 mt-1 block">{{ $leaveCount }}</span>
        </div>

        <div class="glass-panel rounded-xl p-3 border border-rose-500/30 text-center bg-rose-500/5">
            <span class="text-[10px] uppercase font-bold text-rose-400 block">Absent</span>
            <span class="text-xl font-mono font-extrabold text-rose-300 mt-1 block">{{ $absentCount }}</span>
        </div>

        <div class="glass-panel rounded-xl p-3 border border-slate-700/60 text-center bg-slate-800/40">
            <span class="text-[10px] uppercase font-bold text-slate-400 block">Not Marked</span>
            <span class="text-xl font-mono font-extrabold text-cyan-400 mt-1 block">{{ $notMarkedCount }}</span>
        </div>
    </div>

    <!-- Attendance Form & Table -->
    <div class="glass-panel rounded-2xl border border-slate-700/60 overflow-hidden">
        <form action="{{ route('factory.hr.attendance.bulk.store') }}" method="POST">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">

            <div class="p-4 border-b border-slate-700/60 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-amber-400"></i>
                    Daily Roster for <span class="font-mono text-cyan-300">{{ \Carbon\Carbon::parse($date)->format('l, d M Y') }}</span>
                </h3>

                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs shadow-lg shadow-amber-500/25 transition-all flex items-center gap-2 self-start sm:self-auto">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Save All Changes</span>
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-800/80 text-slate-300 uppercase text-[10px] font-bold border-b border-slate-700/80">
                        <tr>
                            <th class="py-3 px-3">Emp ID</th>
                            <th class="py-3 px-3">Worker Name & Section</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3">Check In</th>
                            <th class="py-3 px-3">Check Out</th>
                            <th class="py-3 px-3">OT (Hours)</th>
                            <th class="py-3 px-3">Note / Remarks</th>
                            <th class="py-3 px-3 text-center">Quick Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @forelse($employees as $emp)
                        @php
                            $att = $attendances->get($emp->id);
                            $status = $att ? $att->status : 'present';
                            $checkIn = $att && $att->check_in ? substr($att->check_in, 0, 5) : '08:00';
                            $checkOut = $att && $att->check_out ? substr($att->check_out, 0, 5) : '17:00';
                            $ot = $att ? $att->overtime_hours : 0;
                            $notes = $att ? $att->notes : '';
                        @endphp
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-2.5 px-3 font-mono font-bold text-cyan-400">
                                {{ $emp->employee_code }}
                            </td>
                            <td class="py-2.5 px-3">
                                <div class="font-bold text-white">{{ $emp->name }}</div>
                                <div class="text-[10px] text-slate-400">
                                    {{ $emp->department->name ?? 'N/A' }} • <span class="text-indigo-300">{{ $emp->designation->title ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="py-2.5 px-3">
                                <select name="attendance[{{ $emp->id }}][status]" 
                                        class="bg-[#0a122e] border border-slate-700 rounded-lg px-2.5 py-1 text-xs font-semibold focus:outline-none focus:border-cyan-400 {{ $status === 'present' ? 'text-emerald-400' : ($status === 'absent' ? 'text-rose-400' : ($status === 'late' ? 'text-amber-400' : 'text-purple-400')) }}">
                                    <option value="present" {{ $status === 'present' ? 'selected' : '' }}>Present</option>
                                    <option value="late" {{ $status === 'late' ? 'selected' : '' }}>Late</option>
                                    <option value="half_day" {{ $status === 'half_day' ? 'selected' : '' }}>Half Day</option>
                                    <option value="leave" {{ $status === 'leave' ? 'selected' : '' }}>On Leave</option>
                                    <option value="absent" {{ $status === 'absent' ? 'selected' : '' }}>Absent</option>
                                </select>
                            </td>
                            <td class="py-2.5 px-3">
                                <input type="time" name="attendance[{{ $emp->id }}][check_in]" value="{{ $checkIn }}" 
                                       class="bg-[#0a122e] border border-slate-700 rounded-lg px-2 py-1 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                            </td>
                            <td class="py-2.5 px-3">
                                <input type="time" name="attendance[{{ $emp->id }}][check_out]" value="{{ $checkOut }}" 
                                       class="bg-[#0a122e] border border-slate-700 rounded-lg px-2 py-1 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                            </td>
                            <td class="py-2.5 px-3">
                                <input type="number" step="0.5" min="0" max="12" name="attendance[{{ $emp->id }}][overtime_hours]" value="{{ $ot }}" 
                                       class="w-20 bg-[#0a122e] border border-slate-700 rounded-lg px-2 py-1 text-xs text-amber-300 font-mono font-bold focus:outline-none focus:border-amber-400">
                            </td>
                            <td class="py-2.5 px-3">
                                <input type="text" name="attendance[{{ $emp->id }}][notes]" value="{{ $notes }}" placeholder="Optional note..." 
                                       class="w-full bg-[#0a122e] border border-slate-700 rounded-lg px-2.5 py-1 text-xs text-slate-300 focus:outline-none focus:border-cyan-400">
                            </td>
                            <td class="py-2.5 px-3 text-center">
                                @if($att)
                                    <span class="inline-flex items-center gap-1 text-[10px] text-emerald-400 font-mono">
                                        <i class="fa-solid fa-circle-check"></i>
                                        <span>Saved</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[10px] text-slate-500 font-mono">
                                        <i class="fa-regular fa-clock"></i>
                                        <span>Pending</span>
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400 text-xs">
                                No active employees found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-700/60 flex items-center justify-between">
                <span class="text-xs text-slate-400">Review attendance and overtime hours before saving.</span>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs shadow-lg shadow-amber-500/25 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Save All Changes</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
