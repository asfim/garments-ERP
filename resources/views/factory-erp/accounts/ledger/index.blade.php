@extends('layouts.app')

@section('title', 'General Ledger (খতিয়ান) | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-receipt text-purple-400"></i>
                General Account Ledger (হিসাবের খতিয়ান বিবরণী)
            </h2>
            <p class="text-xs text-slate-400">Statement of individual account movements, debits, credits, and continuous running balances</p>
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

    <!-- ACCOUNT SELECTOR & DATE FILTER -->
    <div class="glass-panel p-4 rounded-2xl border border-slate-800/80">
        <form method="GET" action="{{ route('factory.accounts.ledger.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
            <!-- Account Dropdown -->
            <div class="lg:col-span-2">
                <label class="block text-slate-400 text-[10px] uppercase font-bold mb-1">Select Account (হিসাবের খাত নির্বাচন করুন) *</label>
                <select name="account_id" required 
                        class="w-full bg-slate-900/80 border border-slate-700/80 rounded-xl px-3 py-2 text-slate-200 focus:outline-none focus:border-purple-500 text-xs">
                    @foreach($accounts as $acc)
                    <option value="{{ $acc->id }}" {{ $selectedAccount?->id == $acc->id ? 'selected' : '' }}>
                        [{{ $acc->account_code }}] {{ $acc->name }} ({{ ucfirst($acc->type) }})
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- From Date -->
            <div>
                <label class="block text-slate-400 text-[10px] uppercase font-bold mb-1">From Date</label>
                <input type="date" name="from_date" value="{{ $fromDate }}" 
                       class="w-full bg-slate-900/80 border border-slate-700/80 rounded-xl px-3 py-2 text-slate-200 focus:outline-none focus:border-purple-500 text-xs">
            </div>

            <!-- To Date -->
            <div>
                <label class="block text-slate-400 text-[10px] uppercase font-bold mb-1">To Date</label>
                <input type="date" name="to_date" value="{{ $toDate }}" 
                       class="w-full bg-slate-900/80 border border-slate-700/80 rounded-xl px-3 py-2 text-slate-200 focus:outline-none focus:border-purple-500 text-xs">
            </div>

            <!-- Submit Button -->
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold transition-all flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    <span>Generate Ledger</span>
                </button>
            </div>
        </form>
    </div>

    @if($ledgerData)
    <!-- LEDGER ACCOUNT SUMMARY CARD -->
    <div class="glass-panel p-5 rounded-2xl border border-purple-500/30 bg-gradient-to-br from-purple-950/20 to-slate-900/40">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800/80 pb-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-xs font-mono font-bold px-2.5 py-0.5 rounded-lg bg-purple-500/20 text-purple-300 border border-purple-500/30">
                        Code #{{ $ledgerData['account']->account_code }}
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase border {{ $ledgerData['account']->type_badge_class }}">
                        {{ ucfirst($ledgerData['account']->type) }}
                    </span>
                    <span class="text-xs text-slate-400">| {{ $ledgerData['account']->category }}</span>
                </div>
                <h3 class="text-lg font-black text-white">{{ $ledgerData['account']->name }}</h3>
                @if($ledgerData['account']->account_number)
                <p class="text-xs text-slate-400 font-mono">Bank A/C: {{ $ledgerData['account']->account_number }} ({{ $ledgerData['account']->bank_name }} - {{ $ledgerData['account']->branch_name }})</p>
                @endif
            </div>

            <div class="text-right text-xs text-slate-400 font-mono">
                <span>Period: <strong class="text-purple-300">{{ date('d M, Y', strtotime($ledgerData['from_date'])) }}</strong> to <strong class="text-purple-300">{{ date('d M, Y', strtotime($ledgerData['to_date'])) }}</strong></span>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4 text-center">
            <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800">
                <span class="text-[10px] text-slate-400 uppercase font-bold block">Opening Balance</span>
                <span class="text-base font-black font-mono text-slate-200 mt-1 block">
                    ৳ {{ number_format($ledgerData['opening_balance'], 2) }}
                </span>
            </div>
            <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800">
                <span class="text-[10px] text-slate-400 uppercase font-bold block">Total Debit (Dr)</span>
                <span class="text-base font-black font-mono text-cyan-400 mt-1 block">
                    ৳ {{ number_format($ledgerData['total_debit'], 2) }}
                </span>
            </div>
            <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800">
                <span class="text-[10px] text-slate-400 uppercase font-bold block">Total Credit (Cr)</span>
                <span class="text-base font-black font-mono text-amber-400 mt-1 block">
                    ৳ {{ number_format($ledgerData['total_credit'], 2) }}
                </span>
            </div>
            <div class="p-3 rounded-xl bg-purple-950/40 border border-purple-500/40">
                <span class="text-[10px] text-purple-300 uppercase font-bold block">Closing Balance</span>
                <span class="text-base font-black font-mono text-emerald-400 mt-1 block">
                    ৳ {{ number_format($ledgerData['closing_balance'], 2) }}
                </span>
            </div>
        </div>
    </div>

    <!-- LEDGER ENTRIES TABLE -->
    <div class="glass-panel rounded-2xl border border-slate-800/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/60 text-slate-400 uppercase text-[10px] font-bold border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4">Voucher No</th>
                        <th class="py-3 px-4">Type</th>
                        <th class="py-3 px-4">Particulars / Narration</th>
                        <th class="py-3 px-4">Ref No</th>
                        <th class="py-3 px-4 text-right">Debit (Dr) ৳</th>
                        <th class="py-3 px-4 text-right">Credit (Cr) ৳</th>
                        <th class="py-3 px-4 text-right">Balance ৳</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-mono">
                    <!-- Opening Balance Row -->
                    <tr class="bg-slate-900/40 font-bold">
                        <td class="py-3 px-4 text-slate-400">{{ date('d M, Y', strtotime($ledgerData['from_date'])) }}</td>
                        <td class="py-3 px-4 text-slate-500">-</td>
                        <td class="py-3 px-4 text-slate-500">-</td>
                        <td class="py-3 px-4 text-purple-300 font-sans italic">** Opening Balance Brought Forward **</td>
                        <td class="py-3 px-4 text-slate-500">-</td>
                        <td class="py-3 px-4 text-right text-slate-500">-</td>
                        <td class="py-3 px-4 text-right text-slate-500">-</td>
                        <td class="py-3 px-4 text-right font-black text-slate-200">
                            ৳ {{ number_format($ledgerData['opening_balance'], 2) }}
                        </td>
                    </tr>

                    @forelse($ledgerData['entries'] as $entry)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="py-3 px-4 text-slate-300">
                            {{ date('d M, Y', strtotime($entry['date'])) }}
                        </td>
                        <td class="py-3 px-4 font-bold text-amber-400">
                            @if($entry['voucher_type'] === 'income')
                            <a href="{{ route('factory.accounts.income.print', $entry['voucher_id']) }}" target="_blank" class="hover:underline">
                                {{ $entry['voucher_no'] }}
                            </a>
                            @elseif($entry['voucher_type'] === 'expense')
                            <a href="{{ route('factory.accounts.expense.print', $entry['voucher_id']) }}" target="_blank" class="hover:underline">
                                {{ $entry['voucher_no'] }}
                            </a>
                            @else
                                {{ $entry['voucher_no'] }}
                            @endif
                        </td>
                        <td class="py-3 px-4 font-sans uppercase text-[10px]">
                            <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700">
                                {{ $entry['voucher_type'] }}
                            </span>
                        </td>
                        <td class="py-3 px-4 font-sans text-slate-200">
                            {{ $entry['narration'] ?: 'Accounting transaction' }}
                        </td>
                        <td class="py-3 px-4 text-slate-400 text-[11px]">
                            {{ $entry['reference_no'] ?: '-' }}
                        </td>
                        <td class="py-3 px-4 text-right font-black {{ $entry['debit'] > 0 ? 'text-cyan-400' : 'text-slate-600' }}">
                            {{ $entry['debit'] > 0 ? '৳ ' . number_format($entry['debit'], 2) : '-' }}
                        </td>
                        <td class="py-3 px-4 text-right font-black {{ $entry['credit'] > 0 ? 'text-amber-400' : 'text-slate-600' }}">
                            {{ $entry['credit'] > 0 ? '৳ ' . number_format($entry['credit'], 2) : '-' }}
                        </td>
                        <td class="py-3 px-4 text-right font-black text-emerald-400">
                            ৳ {{ number_format($entry['running_balance'], 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-slate-500 font-sans">
                            No ledger transactions recorded for this account during this period.
                        </td>
                    </tr>
                    @endforelse

                    <!-- Total Row -->
                    <tr class="bg-slate-950 font-black border-t-2 border-slate-700">
                        <td colspan="5" class="py-3 px-4 text-right uppercase tracking-wider text-slate-400 font-sans">
                            Total Movements in Period:
                        </td>
                        <td class="py-3 px-4 text-right text-cyan-400">
                            ৳ {{ number_format($ledgerData['total_debit'], 2) }}
                        </td>
                        <td class="py-3 px-4 text-right text-amber-400">
                            ৳ {{ number_format($ledgerData['total_credit'], 2) }}
                        </td>
                        <td class="py-3 px-4 text-right text-emerald-400">
                            ৳ {{ number_format($ledgerData['closing_balance'], 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection
