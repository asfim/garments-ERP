@extends('layouts.app')

@section('title', 'Employee Advance Ledger | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-user-tie text-purple-400"></i>
                Employee Advance Ledger (কর্মচারী অগ্রিম খতিয়ান)
            </h2>
            <p class="text-xs text-slate-400">Track advance salary disbursements, payroll recovery deductions, and net advance receivable balance</p>
        </div>

        <div class="flex items-center gap-2.5">
            <button onclick="window.print()" 
                    class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold text-xs shadow-lg transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-print"></i>
                <span>Print Ledger</span>
            </button>
            <a href="{{ route('factory.accounts.dashboard') }}" 
               class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Dashboard</span>
            </a>
        </div>
    </div>

    <!-- LEDGER NAVIGATION TABS -->
    <div class="glass-panel p-2 rounded-2xl border border-slate-800/80 flex flex-wrap gap-2">
        <a href="{{ route('factory.accounts.ledger.index') }}" 
           class="px-4 py-2 rounded-xl font-bold text-xs transition-all flex items-center gap-2 text-slate-400 hover:text-white hover:bg-slate-800/60">
            <i class="fa-solid fa-receipt text-sm"></i>
            <span>1. General Ledger (সাধারণ খতিয়ান)</span>
        </a>
        <a href="{{ route('factory.accounts.ledgers.customer') }}" 
           class="px-4 py-2 rounded-xl font-bold text-xs transition-all flex items-center gap-2 text-slate-400 hover:text-white hover:bg-slate-800/60">
            <i class="fa-solid fa-user text-sm"></i>
            <span>2. Customer Ledger (গ্রাহক খতিয়ান)</span>
        </a>
        <a href="{{ route('factory.accounts.ledgers.supplier') }}" 
           class="px-4 py-2 rounded-xl font-bold text-xs transition-all flex items-center gap-2 text-slate-400 hover:text-white hover:bg-slate-800/60">
            <i class="fa-solid fa-truck text-sm"></i>
            <span>3. Supplier Ledger (সরবরাহকারী খতিয়ান)</span>
        </a>
        <a href="{{ route('factory.accounts.ledgers.employee') }}" 
           class="px-4 py-2 rounded-xl font-bold text-xs transition-all flex items-center gap-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white shadow-lg shadow-purple-500/25">
            <i class="fa-solid fa-user-tie text-sm"></i>
            <span>4. Employee Advance Ledger (কর্মচারী অগ্রিম)</span>
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="glass-panel p-4 rounded-2xl border border-slate-800/80">
        <form method="GET" action="{{ route('factory.accounts.ledgers.employee') }}" class="flex flex-wrap items-center gap-3 text-xs">
            <div>
                <label class="block text-slate-400 text-[10px] uppercase font-bold mb-1">Select Employee:</label>
                <select name="employee_id" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-slate-200 text-xs min-w-[200px]">
                    @foreach($employees as $e)
                    <option value="{{ $e->id }}" {{ $selectedEmployee?->id == $e->id ? 'selected' : '' }}>
                        {{ $e->first_name }} {{ $e->last_name }} ({{ $e->employee_id }} - {{ $e->designation?->name }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-slate-400 text-[10px] uppercase font-bold mb-1">From Date:</label>
                <input type="date" name="from_date" value="{{ $fromDate }}" 
                       class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-slate-200 text-xs">
            </div>

            <div>
                <label class="block text-slate-400 text-[10px] uppercase font-bold mb-1">To Date:</label>
                <input type="date" name="to_date" value="{{ $toDate }}" 
                       class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-slate-200 text-xs">
            </div>

            <div class="pt-4">
                <button type="submit" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold transition-all">
                    Load Ledger
                </button>
            </div>
        </form>
    </div>

    @if($ledgerData && $selectedEmployee)
    <!-- Employee Info Card & Closing Balance -->
    <div class="glass-panel p-5 rounded-2xl border border-purple-500/30 bg-purple-950/10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-user-gear text-purple-400"></i>
                {{ $selectedEmployee->first_name }} {{ $selectedEmployee->last_name }}
            </h3>
            <div class="text-xs text-slate-400 mt-1 space-x-3">
                <span>ID: <strong class="text-purple-300 font-mono">{{ $selectedEmployee->employee_id }}</strong></span>
                <span>Designation: <strong class="text-slate-200">{{ $selectedEmployee->designation?->name ?? 'Staff' }}</strong></span>
                <span>Department: <strong class="text-slate-200">{{ $selectedEmployee->department?->name ?? 'General' }}</strong></span>
            </div>
        </div>

        <div class="text-right font-mono">
            <span class="text-slate-400 uppercase text-[10px] block">Current Advance Receivable Balance</span>
            <h3 class="text-2xl font-black {{ $ledgerData['closing_advance_balance'] > 0 ? 'text-amber-400' : 'text-emerald-400' }}">
                ৳ {{ number_format($ledgerData['closing_advance_balance'], 2) }}
            </h3>
            <span class="text-[10px] text-slate-500 font-sans block">
                {{ $ledgerData['closing_advance_balance'] > 0 ? 'Outstanding Advance Asset (#1060)' : 'No Advance Outstanding' }}
            </span>
        </div>
    </div>

    <!-- Ledger Table -->
    <div class="glass-panel rounded-2xl border border-slate-800/80 overflow-hidden">
        <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-950/60 text-slate-400 uppercase text-[10px] font-bold border-b border-slate-800">
                <tr>
                    <th class="py-3 px-4">Date</th>
                    <th class="py-3 px-4">Voucher No</th>
                    <th class="py-3 px-4">Particulars</th>
                    <th class="py-3 px-4">Mode / Ref</th>
                    <th class="py-3 px-4 text-right">Advance Given (৳)</th>
                    <th class="py-3 px-4 text-right">Advance Recovered (৳)</th>
                    <th class="py-3 px-4 text-right">Remaining Advance (৳)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60 font-mono">
                @forelse($ledgerData['rows'] as $r)
                <tr class="hover:bg-slate-800/30">
                    <td class="py-2.5 px-4 text-slate-300">
                        {{ date('d M, Y', strtotime($r['date'])) }}
                    </td>
                    <td class="py-2.5 px-4 font-bold text-purple-400">
                        {{ $r['voucher_no'] }}
                    </td>
                    <td class="py-2.5 px-4 font-sans text-white">
                        {{ $r['description'] }}
                    </td>
                    <td class="py-2.5 px-4 font-sans text-slate-400 text-[11px]">
                        {{ ucfirst($r['payment_method'] ?? 'N/A') }} {{ $r['reference_no'] ? "({$r['reference_no']})" : '' }}
                    </td>
                    <td class="py-2.5 px-4 text-right font-black {{ $r['advance_given'] > 0 ? 'text-amber-400' : 'text-slate-600' }}">
                        {{ $r['advance_given'] > 0 ? '৳ ' . number_format($r['advance_given'], 2) : '-' }}
                    </td>
                    <td class="py-2.5 px-4 text-right font-black {{ $r['advance_recovered'] > 0 ? 'text-emerald-400' : 'text-slate-600' }}">
                        {{ $r['advance_recovered'] > 0 ? '৳ ' . number_format($r['advance_recovered'], 2) : '-' }}
                    </td>
                    <td class="py-2.5 px-4 text-right font-black {{ $r['running_advance_balance'] > 0 ? 'text-amber-400' : 'text-emerald-400' }}">
                        ৳ {{ number_format($r['running_advance_balance'], 2) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-8 text-center text-slate-500 font-sans">
                        No advance salary or recovery records found for this employee in selected period.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif

</div>
@endsection

