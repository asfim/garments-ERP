@extends('layouts.app')

@section('title', 'Advance Salary & Deductions | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-hand-holding-dollar text-rose-400"></i>
                Advance Salary & Loan Management (অগ্রিম বেতন ও লোন ব্যবস্থাপনা)
            </h2>
            <p class="text-xs text-slate-400">Disburse mid-month worker salary advances and automatically deduct them during monthly payroll generation</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Add Advance Button -->
            <button type="button" onclick="openCreateAdvanceModal()"
                    class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-lg shadow-rose-500/25 transition-all flex items-center gap-2">
                <i class="fa-solid fa-money-bill-transfer"></i>
                <span>Disburse Advance Salary</span>
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

    <!-- Month Filter -->
    <div class="glass-panel rounded-2xl p-4 border border-slate-700/60">
        <form method="GET" action="{{ route('factory.hr.advances.index') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Deduction Month *</label>
                <input type="month" name="month" value="{{ $month }}" 
                       class="bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-rose-400 font-mono">
            </div>

            <button type="submit" class="py-2 px-4 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-filter"></i>
                <span>Filter Month</span>
            </button>
            <a href="{{ route('factory.hr.advances.index') }}" class="px-3 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs">Current Month</a>

            <span class="text-xs text-slate-400 ml-auto hidden sm:block">
                Advances to be deducted from: <strong class="text-rose-400 font-mono">{{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }} Payroll</strong>
            </span>
        </form>
    </div>

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="glass-panel rounded-2xl p-4 border border-rose-500/30 flex items-center justify-between bg-rose-500/5">
            <div>
                <span class="text-[11px] font-semibold text-rose-400 uppercase tracking-wider block">Total Advances Given</span>
                <span class="text-2xl font-mono font-extrabold text-white mt-1 block">৳{{ number_format($totalAdvance, 2) }}</span>
                <span class="text-[10px] text-slate-400">Total in {{ $month }}</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center">
                <i class="fa-solid fa-hand-holding-dollar text-xl"></i>
            </div>
        </div>

        <div class="glass-panel rounded-2xl p-4 border border-amber-500/30 flex items-center justify-between bg-amber-500/5">
            <div>
                <span class="text-[11px] font-semibold text-amber-400 uppercase tracking-wider block">Active / To Deduct</span>
                <span class="text-2xl font-mono font-extrabold text-amber-300 mt-1 block">৳{{ number_format($approvedAdvance, 2) }}</span>
                <span class="text-[10px] text-amber-400">Will deduct in Payroll</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center">
                <i class="fa-solid fa-clock-rotate-left text-xl"></i>
            </div>
        </div>

        <div class="glass-panel rounded-2xl p-4 border border-emerald-500/30 flex items-center justify-between bg-emerald-500/5">
            <div>
                <span class="text-[11px] font-semibold text-emerald-400 uppercase tracking-wider block">Recovered / Settled</span>
                <span class="text-2xl font-mono font-extrabold text-emerald-300 mt-1 block">৳{{ number_format($recoveredAdvance, 2) }}</span>
                <span class="text-[10px] text-emerald-400">Deducted from salary</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center">
                <i class="fa-solid fa-circle-check text-xl"></i>
            </div>
        </div>

        <div class="glass-panel rounded-2xl p-4 border border-slate-700/60 flex items-center justify-between bg-slate-800/40">
            <div>
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Pending Requests</span>
                <span class="text-2xl font-mono font-extrabold text-cyan-300 mt-1 block">{{ $pendingCount }}</span>
                <span class="text-[10px] text-slate-400">Awaiting Manager sign</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center">
                <i class="fa-solid fa-hourglass text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Advances Table -->
    <div class="glass-panel rounded-2xl border border-slate-700/60 overflow-hidden">
        <div class="p-4 border-b border-slate-700/60 flex items-center justify-between">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-receipt text-rose-400"></i>
                Advance Salary Records for <span class="font-mono text-rose-300">{{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-800/80 text-slate-300 uppercase text-[10px] font-bold border-b border-slate-700/80">
                    <tr>
                        <th class="py-3 px-3">Date</th>
                        <th class="py-3 px-3">Emp ID</th>
                        <th class="py-3 px-3">Worker Name</th>
                        <th class="py-3 px-3">Section / Role</th>
                        <th class="py-3 px-3 text-right">Advance Amount</th>
                        <th class="py-3 px-3 text-center">Deduct Month</th>
                        <th class="py-3 px-3">Method</th>
                        <th class="py-3 px-3">Reason</th>
                        <th class="py-3 px-3 text-center">Status</th>
                        <th class="py-3 px-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($advances as $adv)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="py-3 px-3 font-mono text-slate-300">
                            {{ $adv->advance_date->format('d M, Y') }}
                        </td>
                        <td class="py-3 px-3 font-mono font-bold text-cyan-400">
                            {{ $adv->employee->employee_code ?? 'N/A' }}
                        </td>
                        <td class="py-3 px-3 font-bold text-white">
                            {{ $adv->employee->name ?? 'N/A' }}
                        </td>
                        <td class="py-3 px-3 text-slate-300">
                            <div>{{ $adv->employee->department->name ?? 'Floor' }}</div>
                            <div class="text-[10px] text-slate-400">{{ $adv->employee->designation->title ?? '' }}</div>
                        </td>
                        <td class="py-3 px-3 text-right font-mono font-extrabold text-rose-400 text-sm">
                            ৳{{ number_format($adv->amount, 2) }}
                        </td>
                        <td class="py-3 px-3 text-center font-mono text-cyan-300 font-bold">
                            {{ $adv->deduct_month }}
                        </td>
                        <td class="py-3 px-3 text-slate-200">
                            {{ $adv->payment_method }}
                        </td>
                        <td class="py-3 px-3 text-slate-300 max-w-xs truncate" title="{{ $adv->reason }}">
                            {{ $adv->reason ?? '—' }}
                        </td>
                        <td class="py-3 px-3 text-center">
                            @if($adv->status === 'recovered')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    RECOVERED
                                </span>
                            @elseif($adv->status === 'approved')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                    APPROVED
                                </span>
                            @elseif($adv->status === 'rejected')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                    REJECTED
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-700 text-slate-300">
                                    PENDING
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-3 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                @if($adv->status === 'pending')
                                    <!-- Approve -->
                                    <form action="{{ route('factory.hr.advances.status.update', $adv->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" title="Approve Advance" class="p-1.5 rounded-lg bg-emerald-500/20 hover:bg-emerald-500/40 text-emerald-400 text-xs">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </form>
                                @endif

                                @if($adv->status === 'approved')
                                    <!-- Manually Mark Recovered -->
                                    <form action="{{ route('factory.hr.advances.status.update', $adv->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="recovered">
                                        <button type="submit" title="Mark as Recovered" class="p-1.5 rounded-lg bg-cyan-500/20 hover:bg-cyan-500/40 text-cyan-400 text-xs">
                                            <i class="fa-solid fa-handshake"></i>
                                        </button>
                                    </form>
                                @endif

                                <!-- Delete -->
                                <form action="{{ route('factory.hr.advances.destroy', $adv->id) }}" method="POST" onsubmit="return confirm('Delete advance record?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete" class="p-1.5 rounded-lg bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 text-xs">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="py-8 text-center text-slate-400 text-xs">
                            No advance salary records found for {{ $month }}.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal: Disburse Advance Salary -->
<div id="createAdvanceModal" class="fixed inset-0 z-50 hidden bg-black/75 backdrop-blur-sm overflow-y-auto p-4 flex items-center justify-center">
    <div class="relative w-full max-w-md bg-[#09112a] border border-rose-500/40 rounded-2xl shadow-2xl p-6 text-white">
        <div class="flex items-center justify-between pb-3 border-b border-slate-700/60 mb-4">
            <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-money-bill-transfer text-rose-400"></i>
                Disburse Advance Salary (অগ্রিম বেতন প্রদান)
            </h3>
            <button type="button" onclick="closeCreateAdvanceModal()" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('factory.hr.advances.store') }}" method="POST" class="space-y-3">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Select Employee *</label>
                <select name="employee_id" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-rose-400">
                    <option value="">Choose Worker</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->employee_code }} - {{ $emp->name }} (Gross: ৳{{ number_format($emp->gross_salary, 0) }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Advance Amount (৳) *</label>
                    <input type="number" step="0.01" min="1" name="amount" required placeholder="e.g. 2000" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:outline-none focus:border-rose-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Payment Date *</label>
                    <input type="date" name="advance_date" required value="{{ date('Y-m-d') }}" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-rose-400">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Deduct From Month *</label>
                    <input type="month" name="deduct_month" required value="{{ $month }}" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-rose-400 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Disbursed Via *</label>
                    <select name="payment_method" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-rose-400">
                        <option value="Cash">Cash (নগদ)</option>
                        <option value="bKash">bKash</option>
                        <option value="Nagad">Nagad</option>
                        <option value="Bank">Bank Account</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Status *</label>
                <select name="status" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-rose-400">
                    <option value="approved">Approved & Disbursed (অনুমোদিত)</option>
                    <option value="pending">Pending Approval (অপেক্ষমাণ)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Reason / Note</label>
                <textarea name="reason" rows="2" placeholder="e.g. Medical emergency / family urgent need..." class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-rose-400"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-700/60">
                <button type="button" onclick="closeCreateAdvanceModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs">Confirm Advance</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openCreateAdvanceModal() {
        document.getElementById('createAdvanceModal').classList.remove('hidden');
    }
    function closeCreateAdvanceModal() {
        document.getElementById('createAdvanceModal').classList.add('hidden');
    }
</script>
@endpush
@endsection

