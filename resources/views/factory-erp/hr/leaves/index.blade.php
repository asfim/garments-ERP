@extends('layouts.app')

@section('title', 'Leave Management | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-calendar-check text-purple-400"></i>
                Leave Management & Applications (ছুটি ব্যবস্থাপনা)
            </h2>
            <p class="text-xs text-slate-400">Employee leave requests, approvals, sick leave, maternity leave, and leave balance</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Apply Leave Button -->
            <button type="button" onclick="openApplyLeaveModal()"
                    class="px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-lg shadow-purple-500/25 transition-all flex items-center gap-2">
                <i class="fa-solid fa-paper-plane"></i>
                <span>Submit Leave Application</span>
            </button>
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

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="glass-panel rounded-2xl p-4 border border-amber-500/30 flex items-center justify-between bg-amber-500/5">
            <div>
                <span class="text-[11px] font-semibold text-amber-400 uppercase tracking-wider block">Pending Applications</span>
                <span class="text-2xl font-mono font-extrabold text-white mt-1 block">{{ $pendingCount }}</span>
                <span class="text-[10px] text-amber-400">Awaiting Manager Approval</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center">
                <i class="fa-solid fa-hourglass-half text-xl"></i>
            </div>
        </div>

        <div class="glass-panel rounded-2xl p-4 border border-emerald-500/30 flex items-center justify-between bg-emerald-500/5">
            <div>
                <span class="text-[11px] font-semibold text-emerald-400 uppercase tracking-wider block">Approved Leaves</span>
                <span class="text-2xl font-mono font-extrabold text-white mt-1 block">{{ $approvedCount }}</span>
                <span class="text-[10px] text-emerald-400">Granted to Workers</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center">
                <i class="fa-solid fa-circle-check text-xl"></i>
            </div>
        </div>

        <div class="glass-panel rounded-2xl p-4 border border-rose-500/30 flex items-center justify-between bg-rose-500/5">
            <div>
                <span class="text-[11px] font-semibold text-rose-400 uppercase tracking-wider block">Rejected Leaves</span>
                <span class="text-2xl font-mono font-extrabold text-white mt-1 block">{{ $rejectedCount }}</span>
                <span class="text-[10px] text-rose-400">Declined Requests</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center">
                <i class="fa-solid fa-circle-xmark text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="glass-panel rounded-2xl p-4 border border-slate-700/60">
        <form method="GET" action="{{ route('factory.hr.leaves.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Status</label>
                <select name="status" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-purple-400">
                    <option value="">All Applications</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Leave Type</label>
                <select name="leave_type" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-purple-400">
                    <option value="">All Types</option>
                    <option value="casual" {{ request('leave_type') == 'casual' ? 'selected' : '' }}>Casual Leave</option>
                    <option value="sick" {{ request('leave_type') == 'sick' ? 'selected' : '' }}>Sick Leave</option>
                    <option value="earned" {{ request('leave_type') == 'earned' ? 'selected' : '' }}>Earned Leave</option>
                    <option value="maternity" {{ request('leave_type') == 'maternity' ? 'selected' : '' }}>Maternity Leave</option>
                    <option value="unpaid" {{ request('leave_type') == 'unpaid' ? 'selected' : '' }}>Unpaid Leave</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs transition-colors flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-filter"></i>
                    <span>Filter Leaves</span>
                </button>
                <a href="{{ route('factory.hr.leaves.index') }}" class="px-3 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs">Reset</a>
            </div>
        </form>
    </div>

    <!-- Leaves Table -->
    <div class="glass-panel rounded-2xl border border-slate-700/60 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-800/80 text-slate-300 uppercase text-[10px] font-bold border-b border-slate-700/80">
                    <tr>
                        <th class="py-3 px-3">Emp ID</th>
                        <th class="py-3 px-3">Worker Name</th>
                        <th class="py-3 px-3">Section / Role</th>
                        <th class="py-3 px-3">Leave Type</th>
                        <th class="py-3 px-3">Duration (Dates)</th>
                        <th class="py-3 px-3 text-center">Days</th>
                        <th class="py-3 px-3">Reason</th>
                        <th class="py-3 px-3 text-center">Status</th>
                        <th class="py-3 px-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($leaves as $leave)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="py-3 px-3 font-mono font-bold text-cyan-400">
                            {{ $leave->employee->employee_code }}
                        </td>
                        <td class="py-3 px-3 font-bold text-white">
                            {{ $leave->employee->name }}
                        </td>
                        <td class="py-3 px-3 text-slate-300">
                            <div>{{ $leave->employee->department->name ?? 'N/A' }}</div>
                            <div class="text-[10px] text-slate-400">{{ $leave->employee->designation->title ?? 'N/A' }}</div>
                        </td>
                        <td class="py-3 px-3">
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-800 text-slate-300 border border-slate-700 capitalize">
                                {{ $leave->leave_type }}
                            </span>
                        </td>
                        <td class="py-3 px-3 font-mono text-[11px] text-slate-200">
                            {{ $leave->start_date->format('d M Y') }} - {{ $leave->end_date->format('d M Y') }}
                        </td>
                        <td class="py-3 px-3 text-center font-mono font-bold text-purple-300">
                            {{ $leave->days_count }}
                        </td>
                        <td class="py-3 px-3 text-slate-300 max-w-xs truncate" title="{{ $leave->reason }}">
                            {{ $leave->reason ?? '—' }}
                        </td>
                        <td class="py-3 px-3 text-center">
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
                        <td class="py-3 px-3 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                @if($leave->status === 'pending')
                                    <!-- Approve -->
                                    <form action="{{ route('factory.hr.leaves.status.update', $leave->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" title="Approve Leave" class="p-1.5 rounded-lg bg-emerald-500/20 hover:bg-emerald-500/40 text-emerald-400 text-xs transition-colors">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </form>

                                    <!-- Reject -->
                                    <form action="{{ route('factory.hr.leaves.status.update', $leave->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" title="Reject Leave" class="p-1.5 rounded-lg bg-rose-500/20 hover:bg-rose-500/40 text-rose-400 text-xs transition-colors">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </form>
                                @endif

                                <!-- Delete -->
                                <form action="{{ route('factory.hr.leaves.destroy', $leave->id) }}" method="POST" onsubmit="return confirm('Delete leave record?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete Leave" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-rose-400 text-xs transition-colors">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-8 text-center text-slate-400 text-xs">
                            No leave applications found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal: Apply Leave -->
<div id="applyLeaveModal" class="fixed inset-0 z-50 hidden bg-black/75 backdrop-blur-sm overflow-y-auto p-4 flex items-center justify-center">
    <div class="relative w-full max-w-lg bg-[#09112a] border border-purple-500/40 rounded-2xl shadow-2xl p-6 text-white">
        <div class="flex items-center justify-between pb-3 border-b border-slate-700/60 mb-4">
            <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-calendar-plus text-purple-400"></i>
                Submit Leave Application
            </h3>
            <button type="button" onclick="closeApplyLeaveModal()" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('factory.hr.leaves.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Select Employee *</label>
                <select name="employee_id" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-purple-400">
                    <option value="">Choose Employee</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->employee_code }} - {{ $emp->name }} ({{ $emp->department->name ?? 'Floor' }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Leave Type *</label>
                <select name="leave_type" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-purple-400">
                    <option value="casual">Casual Leave (নৈমিত্তিক ছুটি)</option>
                    <option value="sick">Sick Leave (অসুস্থতাজনিত ছুটি)</option>
                    <option value="earned">Earned Leave (অর্জিত ছুটি)</option>
                    <option value="maternity">Maternity Leave (মাতৃত্বকালীন ছুটি)</option>
                    <option value="unpaid">Unpaid Leave (বিনা বেতনে ছুটি)</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Start Date *</label>
                    <input type="date" name="start_date" id="leaveStartDate" required value="{{ date('Y-m-d') }}" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-purple-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">End Date *</label>
                    <input type="date" name="end_date" id="leaveEndDate" required value="{{ date('Y-m-d') }}" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-purple-400">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Reason / Purpose</label>
                <textarea name="reason" rows="3" placeholder="Explain the reason for leave..." class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-purple-400"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-700/60">
                <button type="button" onclick="closeApplyLeaveModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs">Submit Application</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openApplyLeaveModal() {
        document.getElementById('applyLeaveModal').classList.remove('hidden');
    }
    function closeApplyLeaveModal() {
        document.getElementById('applyLeaveModal').classList.add('hidden');
    }
</script>
@endpush
@endsection
