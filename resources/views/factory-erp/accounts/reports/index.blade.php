@extends('layouts.app')

@section('title', 'Financial Reports | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Header -->
    <div id="reportPageHeader" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-file-invoice-dollar text-teal-400"></i>
                Financial Statements & Reports
            </h2>
            <p class="text-xs text-slate-400">Audited Profit & Loss (Income Statement), Trial Balance, Day Book, and Balance Sheet</p>
        </div>

        <div class="flex items-center gap-2.5">
            <button onclick="window.print()" 
                    class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold text-xs shadow-lg transition-all flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-print"></i>
                <span>Print Report</span>
            </button>
            <a href="{{ route('factory.accounts.dashboard') }}" 
               class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Dashboard</span>
            </a>
        </div>
    </div>

    <!-- UNIFIED REPORT NAVIGATION TABS -->
    @include('factory-erp.accounts.reports.tabs-nav')

    <!-- REPORT CONTENT CONTAINER (AJAX SWAPPED) -->
    <div id="reportTabContent" class="space-y-6">

    <!-- ========================================== -->
    <!-- TAB 1: PROFIT & LOSS STATEMENT -->
    <!-- ========================================== -->
    @if($tab === 'profit_loss' && $profitLossData)
    <div class="space-y-6">
        <!-- Date Filter -->
        <div class="glass-panel p-3.5 rounded-2xl border border-slate-800/80">
            <form method="GET" action="{{ route('factory.accounts.reports.index') }}" class="flex flex-wrap items-center gap-3 text-xs">
                <input type="hidden" name="tab" value="profit_loss">
                <span class="text-slate-400 font-medium flex items-center gap-1.5">
                    <i class="fa-regular fa-calendar text-emerald-400"></i> Statement Period:
                </span>
                <input type="date" name="from_date" value="{{ $fromDate }}" 
                       class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-slate-200 text-xs">
                <span class="text-slate-500">to</span>
                <input type="date" name="to_date" value="{{ $toDate }}" 
                       class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-slate-200 text-xs">
                <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold transition-all">
                    Generate P&L
                </button>
            </form>
        </div>

        <!-- Profit & Loss Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="glass-panel p-4 rounded-2xl border border-emerald-500/30 bg-emerald-950/20">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-400 block">Total Revenue / Income</span>
                <h3 class="text-2xl font-black text-white font-mono mt-1">৳ {{ number_format($profitLossData['total_income'], 2) }}</h3>
                <span class="text-[10px] text-slate-400 font-mono mt-1 block">From {{ count($profitLossData['income_details']) }} Income Heads</span>
            </div>

            <div class="glass-panel p-4 rounded-2xl border border-rose-500/30 bg-rose-950/20">
                <span class="text-[11px] font-bold uppercase tracking-wider text-rose-400 block">Total Operating Expenses</span>
                <h3 class="text-2xl font-black text-white font-mono mt-1">৳ {{ number_format($profitLossData['total_expense'], 2) }}</h3>
                <span class="text-[10px] text-slate-400 font-mono mt-1 block">From {{ count($profitLossData['expense_details']) }} Expense Heads</span>
            </div>

            <div class="glass-panel p-4 rounded-2xl border {{ $profitLossData['net_profit'] >= 0 ? 'border-amber-500/40 bg-amber-950/20' : 'border-rose-500/40 bg-rose-950/20' }}">
                <span class="text-[11px] font-bold uppercase tracking-wider {{ $profitLossData['net_profit'] >= 0 ? 'text-amber-400' : 'text-rose-400' }} block">
                    Net {{ $profitLossData['net_profit'] >= 0 ? 'Profit' : 'Loss' }}
                </span>
                <h3 class="text-2xl font-black {{ $profitLossData['net_profit'] >= 0 ? 'text-amber-300' : 'text-rose-400' }} font-mono mt-1">
                    {{ $profitLossData['net_profit'] >= 0 ? '৳ ' . number_format($profitLossData['net_profit'], 2) : '- ৳ ' . number_format(abs($profitLossData['net_profit']), 2) }}
                </h3>
                <span class="text-[10px] text-slate-400 font-mono mt-1 block">
                    Margin: {{ $profitLossData['total_income'] > 0 ? round(($profitLossData['net_profit'] / $profitLossData['total_income']) * 100, 1) : 0 }}%
                </span>
            </div>
        </div>

        <!-- Profit & Loss Detailed Table -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Incomes Column -->
            <div class="glass-panel rounded-2xl border border-emerald-500/20 overflow-hidden">
                <div class="p-3.5 bg-emerald-950/40 border-b border-emerald-500/30 flex items-center justify-between">
                    <h4 class="text-xs font-bold text-emerald-300 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-arrow-trend-up"></i>
                        A. Revenues & Incomes
                    </h4>
                    <span class="font-mono font-bold text-xs text-white">৳ {{ number_format($profitLossData['total_income'], 2) }}</span>
                </div>
                <table class="w-full text-left text-xs text-slate-300">
                    <tbody class="divide-y divide-slate-800/60 font-mono">
                        @forelse($profitLossData['income_details'] as $item)
                        <tr class="hover:bg-slate-800/40">
                            <td class="py-2.5 px-4 font-sans text-white">
                                <span class="font-bold">[{{ $item['account']->account_code }}] {{ $item['account']->name }}</span>
                                <span class="text-[10px] text-slate-400 block font-sans">{{ $item['account']->category }}</span>
                            </td>
                            <td class="py-2.5 px-4 text-right font-black text-emerald-400">
                                ৳ {{ number_format($item['amount'], 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="py-6 text-center text-slate-500 font-sans">
                                No income recorded in this period.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="border-t-2 border-emerald-500/40 bg-slate-950/60 font-mono font-bold">
                        <tr>
                            <td class="py-3 px-4 font-sans uppercase text-emerald-300">Total Operating Income</td>
                            <td class="py-3 px-4 text-right text-emerald-400 font-black text-sm">
                                ৳ {{ number_format($profitLossData['total_income'], 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Expenses Column -->
            <div class="glass-panel rounded-2xl border border-rose-500/20 overflow-hidden">
                <div class="p-3.5 bg-rose-950/40 border-b border-rose-500/30 flex items-center justify-between">
                    <h4 class="text-xs font-bold text-rose-300 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-arrow-trend-down"></i>
                        B. Operating Expenses
                    </h4>
                    <span class="font-mono font-bold text-xs text-white">৳ {{ number_format($profitLossData['total_expense'], 2) }}</span>
                </div>
                <table class="w-full text-left text-xs text-slate-300">
                    <tbody class="divide-y divide-slate-800/60 font-mono">
                        @forelse($profitLossData['expense_details'] as $item)
                        <tr class="hover:bg-slate-800/40">
                            <td class="py-2.5 px-4 font-sans text-white">
                                <span class="font-bold">[{{ $item['account']->account_code }}] {{ $item['account']->name }}</span>
                                <span class="text-[10px] text-slate-400 block font-sans">{{ $item['account']->category }}</span>
                            </td>
                            <td class="py-2.5 px-4 text-right font-black text-rose-400">
                                ৳ {{ number_format($item['amount'], 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="py-6 text-center text-slate-500 font-sans">
                                No expenses recorded in this period.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="border-t-2 border-rose-500/40 bg-slate-950/60 font-mono font-bold">
                        <tr>
                            <td class="py-3 px-4 font-sans uppercase text-rose-300">Total Expenses</td>
                            <td class="py-3 px-4 text-right text-rose-400 font-black text-sm">
                                ৳ {{ number_format($profitLossData['total_expense'], 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- ========================================== -->
    <!-- TAB 2: TRIAL BALANCE -->
    <!-- ========================================== -->
    @if($tab === 'trial_balance' && $trialBalanceData)
    <div class="space-y-6">
        <!-- Date Filter -->
        <div class="glass-panel p-3.5 rounded-2xl border border-slate-800/80 flex items-center justify-between">
            <form method="GET" action="{{ route('factory.accounts.reports.index') }}" class="flex items-center gap-3 text-xs">
                <input type="hidden" name="tab" value="trial_balance">
                <span class="text-slate-400 font-medium">As of Date:</span>
                <input type="date" name="as_of_date" value="{{ $asOfDate }}" 
                       class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-slate-200 text-xs">
                <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold transition-all">
                    Generate Trial Balance
                </button>
            </form>

            <div>
                @if($trialBalanceData['is_balanced'])
                <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-xs font-bold flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-check"></i>
                    Trial Balance is Balanced (Dr = Cr)
                </span>
                @else
                <span class="px-3 py-1 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/40 text-xs font-bold flex items-center gap-1.5">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    Difference: ৳ {{ number_format(abs($trialBalanceData['total_debit'] - $trialBalanceData['total_credit']), 2) }}
                </span>
                @endif
            </div>
        </div>

        <!-- Trial Balance Table -->
        <div class="glass-panel rounded-2xl border border-slate-800/80 overflow-hidden">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/60 text-slate-400 uppercase text-[10px] font-bold border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Code</th>
                        <th class="py-3 px-4">Account Title</th>
                        <th class="py-3 px-4">Type</th>
                        <th class="py-3 px-4 text-right">Debit (Dr) ৳</th>
                        <th class="py-3 px-4 text-right">Credit (Cr) ৳</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-mono">
                    @forelse($trialBalanceData['rows'] as $r)
                    <tr class="hover:bg-slate-800/40">
                        <td class="py-2.5 px-4 font-bold text-amber-400">
                            #{{ $r['account']->account_code }}
                        </td>
                        <td class="py-2.5 px-4 font-sans text-white font-medium">
                            {{ $r['account']->name }}
                        </td>
                        <td class="py-2.5 px-4 font-sans">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border {{ $r['account']->type_badge_class }}">
                                {{ ucfirst($r['account']->type) }}
                            </span>
                        </td>
                        <td class="py-2.5 px-4 text-right font-black {{ $r['debit'] > 0 ? 'text-cyan-400' : 'text-slate-600' }}">
                            {{ $r['debit'] > 0 ? '৳ ' . number_format($r['debit'], 2) : '-' }}
                        </td>
                        <td class="py-2.5 px-4 text-right font-black {{ $r['credit'] > 0 ? 'text-amber-400' : 'text-slate-600' }}">
                            {{ $r['credit'] > 0 ? '৳ ' . number_format($r['credit'], 2) : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-500 font-sans">
                            No active account transactions to display in trial balance.
                        </td>
                    </tr>
                    @endforelse

                    <!-- Totals Row -->
                    <tr class="bg-slate-950 border-t-2 border-slate-700 font-black text-sm">
                        <td colspan="3" class="py-3 px-4 text-right uppercase tracking-wider font-sans text-slate-400">
                            Total:
                        </td>
                        <td class="py-3 px-4 text-right text-cyan-400">
                            ৳ {{ number_format($trialBalanceData['total_debit'], 2) }}
                        </td>
                        <td class="py-3 px-4 text-right text-amber-400">
                            ৳ {{ number_format($trialBalanceData['total_credit'], 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- ========================================== -->
    <!-- TAB 3: DAY BOOK / CASH BOOK -->
    <!-- ========================================== -->
    @if($tab === 'day_book' && $dayBookData)
    <div class="space-y-6">
        <!-- Date Selector -->
        <div class="glass-panel p-3.5 rounded-2xl border border-slate-800/80 flex items-center justify-between">
            <form method="GET" action="{{ route('factory.accounts.reports.index') }}" class="flex items-center gap-3 text-xs">
                <input type="hidden" name="tab" value="day_book">
                <span class="text-slate-400 font-medium">Select Date:</span>
                <input type="date" name="date" value="{{ $dayBookDate }}" 
                       class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-slate-200 text-xs">
                <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold transition-all">
                    View Day Book
                </button>
            </form>

            <div class="text-xs font-mono text-slate-400">
                Date: <strong class="text-amber-400">{{ date('d M, Y', strtotime($dayBookDate)) }}</strong>
            </div>
        </div>

        <!-- Day Summary Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="glass-panel p-4 rounded-2xl border border-emerald-500/30 bg-emerald-950/20">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-400 block">Today's Inflow</span>
                <h3 class="text-2xl font-black text-emerald-400 font-mono mt-1">+ ৳ {{ number_format($dayBookData['total_income'], 2) }}</h3>
            </div>
            <div class="glass-panel p-4 rounded-2xl border border-rose-500/30 bg-rose-950/20">
                <span class="text-[11px] font-bold uppercase tracking-wider text-rose-400 block">Today's Outflow</span>
                <h3 class="text-2xl font-black text-rose-400 font-mono mt-1">- ৳ {{ number_format($dayBookData['total_expense'], 2) }}</h3>
            </div>
            <div class="glass-panel p-4 rounded-2xl border border-cyan-500/30 bg-cyan-950/20">
                <span class="text-[11px] font-bold uppercase tracking-wider text-cyan-400 block">Today's Net Cash Movement</span>
                @php $dayNet = $dayBookData['total_income'] - $dayBookData['total_expense']; @endphp
                <h3 class="text-2xl font-black font-mono mt-1 {{ $dayNet >= 0 ? 'text-cyan-400' : 'text-rose-400' }}">
                    {{ $dayNet >= 0 ? '+ ৳ ' . number_format($dayNet, 2) : '- ৳ ' . number_format(abs($dayNet), 2) }}
                </h3>
            </div>
        </div>

        <!-- Day Vouchers Table -->
        <div class="glass-panel rounded-2xl border border-slate-800/80 overflow-hidden">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/60 text-slate-400 uppercase text-[10px] font-bold border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Voucher No</th>
                        <th class="py-3 px-4">Type</th>
                        <th class="py-3 px-4">Particulars / Party</th>
                        <th class="py-3 px-4">Payment Method & Ref</th>
                        <th class="py-3 px-4 text-right">Inflow (৳)</th>
                        <th class="py-3 px-4 text-right">Outflow (৳)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-mono">
                    @forelse($dayBookData['vouchers'] as $v)
                    <tr class="daybook-entry-row hover:bg-slate-800/40 {{ $loop->index >= 20 ? 'hidden' : '' }}" data-index="{{ $loop->index }}">
                        <td class="py-2.5 px-4 font-bold text-amber-400">
                            {{ $v->voucher_no }}
                        </td>
                        <td class="py-2.5 px-4 font-sans uppercase text-[10px]">
                            @if($v->voucher_type === 'income')
                            <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Income</span>
                            @elseif($v->voucher_type === 'expense')
                            <span class="px-2 py-0.5 rounded bg-rose-500/20 text-rose-300 border border-rose-500/30">Expense</span>
                            @elseif($v->voucher_type === 'transfer')
                            <span class="px-2 py-0.5 rounded bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">Transfer</span>
                            @endif
                        </td>
                        <td class="py-2.5 px-4 font-sans text-white">
                            {{ $v->party_name ?: ($v->notes ?: 'General Transaction') }}
                        </td>
                        <td class="py-2.5 px-4 font-sans text-slate-400 text-[11px]">
                            {{ $v->payment_method_label }} {{ $v->reference_no ? "({$v->reference_no})" : '' }}
                        </td>
                        <td class="py-2.5 px-4 text-right font-black text-emerald-400">
                            {{ $v->voucher_type === 'income' ? '৳ ' . number_format($v->amount, 2) : '-' }}
                        </td>
                        <td class="py-2.5 px-4 text-right font-black text-rose-400">
                            {{ $v->voucher_type === 'expense' ? '৳ ' . number_format($v->amount, 2) : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-500 font-sans">
                            No vouchers recorded for this date.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Day Book Load More Container -->
            @if(!empty($dayBookData['vouchers']) && count($dayBookData['vouchers']) > 0)
            <div id="dayBookLoadMoreContainer" class="p-4 border-t border-slate-800 bg-slate-950/40 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-xs text-slate-400 font-mono">
                    Showing <span id="dayBookShowingCount" class="text-amber-400 font-bold">{{ min(20, count($dayBookData['vouchers'])) }}</span> of <span id="dayBookTotalCount" class="text-white font-bold">{{ count($dayBookData['vouchers']) }}</span> entries
                </div>

                @if(count($dayBookData['vouchers']) > 20)
                <button type="button" 
                        id="dayBookLoadMoreBtn"
                        onclick="loadMoreDayBookRows()"
                        class="px-5 py-2 rounded-xl bg-gradient-to-r from-amber-600 to-yellow-600 hover:from-amber-500 hover:to-yellow-500 text-white text-xs font-bold shadow-lg shadow-amber-950/50 flex items-center gap-2 transition-all cursor-pointer">
                    <i class="fa-solid fa-circle-down"></i>
                    <span>Load More Entries</span>
                </button>
                @else
                <div class="text-xs text-slate-500 font-semibold italic flex items-center gap-1.5">
                    <i class="fa-solid fa-check text-emerald-400"></i> All entries loaded
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- ========================================== -->
    <!-- TAB 4: BALANCE SHEET -->
    <!-- ========================================== -->
    @if($tab === 'balance_sheet' && $balanceSheetData)
    <div class="space-y-6">
        <!-- As of Date Selector & Balance Check -->
        <div class="glass-panel p-3.5 rounded-2xl border border-slate-800/80 flex flex-wrap items-center justify-between gap-4">
            <form method="GET" action="{{ route('factory.accounts.reports.index') }}" class="flex items-center gap-3 text-xs">
                <input type="hidden" name="tab" value="balance_sheet">
                <span class="text-slate-400 font-medium">As of Date:</span>
                <input type="date" name="as_of_date" value="{{ $asOfDate }}" 
                       class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-slate-200 text-xs">
                <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-bold transition-all">
                    Generate Balance Sheet
                </button>
            </form>

            <div>
                @if($balanceSheetData['is_balanced'])
                <span class="px-3 py-1.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-xs font-bold flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-check"></i>
                    Balance Sheet is Balanced (Assets = Liabilities + Equity)
                </span>
                @else
                <span class="px-3 py-1.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/40 text-xs font-bold flex items-center gap-1.5">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    Variance: ৳ {{ number_format(abs($balanceSheetData['total_assets'] - $balanceSheetData['total_liabilities_and_equity']), 2) }}
                </span>
                @endif
            </div>
        </div>

        <!-- Balance Sheet Split Cards (2 Columns: Assets vs Liabilities & Equity) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- LEFT COLUMN: ASSETS -->
            <div class="glass-panel p-5 rounded-2xl border border-slate-800/80 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-cyan-400 flex items-center gap-2">
                        <i class="fa-solid fa-vault"></i> Assets
                    </h3>
                    <span class="font-mono text-cyan-400 font-black text-sm">৳ {{ number_format($balanceSheetData['total_assets'], 2) }}</span>
                </div>

                <div class="space-y-2">
                    <table class="w-full text-left text-xs text-slate-300">
                        <tbody class="divide-y divide-slate-800/50 font-mono">
                            @foreach($balanceSheetData['assets'] as $item)
                            <tr class="hover:bg-slate-800/30">
                                <td class="py-2 text-white font-sans font-medium">
                                    <span class="text-amber-400 font-mono text-[11px] mr-1">#{{ $item['account']->account_code }}</span>
                                    {{ $item['account']->name }}
                                </td>
                                <td class="py-2 text-right font-black text-cyan-400">
                                    ৳ {{ number_format($item['balance'], 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pt-3 border-t-2 border-slate-700 flex items-center justify-between font-black text-sm">
                    <span class="text-white font-sans uppercase">Total Assets:</span>
                    <span class="text-cyan-400 font-mono">৳ {{ number_format($balanceSheetData['total_assets'], 2) }}</span>
                </div>
            </div>

            <!-- RIGHT COLUMN: LIABILITIES & EQUITY -->
            <div class="space-y-6">

                <!-- Liabilities Box -->
                <div class="glass-panel p-5 rounded-2xl border border-slate-800/80 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-rose-400 flex items-center gap-2">
                            <i class="fa-solid fa-file-invoice"></i> Liabilities
                        </h3>
                        <span class="font-mono text-rose-400 font-black text-sm">৳ {{ number_format($balanceSheetData['total_liabilities'], 2) }}</span>
                    </div>

                    <table class="w-full text-left text-xs text-slate-300">
                        <tbody class="divide-y divide-slate-800/50 font-mono">
                            @forelse($balanceSheetData['liabilities'] as $item)
                            <tr class="hover:bg-slate-800/30">
                                <td class="py-2 text-white font-sans font-medium">
                                    <span class="text-amber-400 font-mono text-[11px] mr-1">#{{ $item['account']->account_code }}</span>
                                    {{ $item['account']->name }}
                                </td>
                                <td class="py-2 text-right font-black text-rose-400">
                                    ৳ {{ number_format($item['balance'], 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="py-2 text-slate-500 font-sans text-center">No outstanding liabilities.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Equity & Retained Earnings Box -->
                <div class="glass-panel p-5 rounded-2xl border border-slate-800/80 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-400 flex items-center gap-2">
                            <i class="fa-solid fa-scale-balanced"></i> Equity & Retained Earnings
                        </h3>
                        <span class="font-mono text-emerald-400 font-black text-sm">৳ {{ number_format($balanceSheetData['total_equity'] + $balanceSheetData['period_earnings'], 2) }}</span>
                    </div>

                    <table class="w-full text-left text-xs text-slate-300">
                        <tbody class="divide-y divide-slate-800/50 font-mono">
                            @foreach($balanceSheetData['equity'] as $item)
                            <tr class="hover:bg-slate-800/30">
                                <td class="py-2 text-white font-sans font-medium">
                                    <span class="text-amber-400 font-mono text-[11px] mr-1">#{{ $item['account']->account_code }}</span>
                                    {{ $item['account']->name }}
                                </td>
                                <td class="py-2 text-right font-black text-emerald-400">
                                    ৳ {{ number_format($item['balance'], 2) }}
                                </td>
                            </tr>
                            @endforeach

                            <tr class="hover:bg-slate-800/30">
                                <td class="py-2 text-amber-300 font-sans font-bold">
                                    <i class="fa-solid fa-chart-line text-[10px] mr-1"></i> Cumulative Net Profit / Retained Earnings
                                </td>
                                <td class="py-2 text-right font-black {{ $balanceSheetData['period_earnings'] >= 0 ? 'text-amber-300' : 'text-rose-400' }}">
                                    ৳ {{ number_format($balanceSheetData['period_earnings'], 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="pt-3 border-t-2 border-slate-700 flex items-center justify-between font-black text-sm">
                        <span class="text-white font-sans uppercase">Total Liabilities & Equity:</span>
                        <span class="text-emerald-400 font-mono">৳ {{ number_format($balanceSheetData['total_liabilities_and_equity'], 2) }}</span>
                    </div>
                </div>

            </div>
        </div>
    </div>
    @endif
    </div> <!-- /#reportTabContent -->

</div>

@include('factory-erp.accounts.reports.ajax-script')

@if($tab === 'day_book' && $dayBookData && !empty($dayBookData['vouchers']))
<script>
let dayBookPageSize = 20;
let currentDayBookVisible = Math.min(20, {{ count($dayBookData['vouchers']) }});
const totalDayBookEntries = {{ count($dayBookData['vouchers']) }};

function loadMoreDayBookRows() {
    const hiddenRows = document.querySelectorAll('.daybook-entry-row.hidden');
    let toShow = Math.min(dayBookPageSize, hiddenRows.length);
    for (let i = 0; i < toShow; i++) {
        hiddenRows[i].classList.remove('hidden');
    }
    currentDayBookVisible += toShow;
    const countEl = document.getElementById('dayBookShowingCount');
    if (countEl) countEl.textContent = currentDayBookVisible;

    if (currentDayBookVisible >= totalDayBookEntries || hiddenRows.length <= toShow) {
        const btn = document.getElementById('dayBookLoadMoreBtn');
        if (btn) btn.remove();
        const container = document.getElementById('dayBookLoadMoreContainer');
        if (container) {
            const doneDiv = document.createElement('div');
            doneDiv.className = 'text-xs text-slate-500 font-semibold italic flex items-center gap-1.5';
            doneDiv.innerHTML = '<i class="fa-solid fa-check text-emerald-400"></i> All entries loaded';
            container.appendChild(doneDiv);
        }
    }
}
</script>
@endif
@endsection

