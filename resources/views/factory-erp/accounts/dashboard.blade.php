@extends('layouts.app')

@section('title', 'Accounts & Financial Dashboard | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-amber-400"></i>
                Accounts & Financial Dashboard (হিসাব ও আর্থিক ড্যাশবোর্ড)
            </h2>
            <p class="text-xs text-slate-400">Complete double-entry accounting, income & expenses, bank/cash balances, and financial health</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Quick Link: New Income -->
            <a href="{{ route('factory.accounts.income.index') }}" 
               class="px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-plus-circle"></i>
                <span>+ Income (আয়)</span>
            </a>

            <!-- Quick Link: New Expense -->
            <a href="{{ route('factory.accounts.expense.index') }}" 
               class="px-3 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-lg transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-minus-circle"></i>
                <span>+ Expense (ব্যয়)</span>
            </a>

            <!-- Quick Link: Transfer -->
            <a href="{{ route('factory.accounts.transfers.index') }}" 
               class="px-3 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs shadow-lg transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-money-bill-transfer"></i>
                <span>Transfer (স্থানান্তর)</span>
            </a>

            <!-- Quick Link: Reports -->
            <a href="{{ route('factory.accounts.reports.index') }}" 
               class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold text-xs shadow-lg transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-file-invoice-dollar text-teal-400"></i>
                <span>Reports</span>
            </a>

            <!-- Sync Factory ERP Data -->
            <form action="{{ route('factory.accounts.sync-factory') }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                        class="px-3 py-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-xs shadow-lg shadow-blue-500/25 transition-all flex items-center gap-1.5"
                        title="Sync all orders sales, customer due collections, worker payrolls, and advances into Accounts">
                    <i class="fa-solid fa-arrows-rotate"></i>
                    <span>Sync Factory Data</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="p-3.5 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs flex items-center gap-2">
        <i class="fa-solid fa-circle-check text-sm"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="p-3.5 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs flex items-center gap-2">
        <i class="fa-solid fa-triangle-exclamation text-sm"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- Period Filter Bar -->
    <div class="glass-panel p-3.5 rounded-2xl border border-slate-800/80 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" action="{{ route('factory.accounts.dashboard') }}" class="flex flex-wrap items-center gap-2.5 text-xs">
            <span class="text-slate-400 font-medium flex items-center gap-1.5">
                <i class="fa-regular fa-calendar text-amber-400"></i> Filter Period:
            </span>
            <input type="date" name="from_date" value="{{ $fromDate }}" 
                   class="bg-slate-900/80 border border-slate-700/80 rounded-xl px-2.5 py-1.5 text-slate-200 focus:outline-none focus:border-amber-500 text-xs">
            <span class="text-slate-500">to</span>
            <input type="date" name="to_date" value="{{ $toDate }}" 
                   class="bg-slate-900/80 border border-slate-700/80 rounded-xl px-2.5 py-1.5 text-slate-200 focus:outline-none focus:border-amber-500 text-xs">
            <button type="submit" class="px-3 py-1.5 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 font-bold transition-all">
                <i class="fa-solid fa-filter text-[10px]"></i> Apply Filter
            </button>
            @if(request()->filled('from_date'))
            <a href="{{ route('factory.accounts.dashboard') }}" class="text-slate-400 hover:text-rose-400 text-xs underline ml-1">Reset</a>
            @endif
        </form>

        <div class="text-[11px] text-slate-400 font-mono">
            Showing stats: <span class="text-amber-400 font-bold">{{ date('d M, Y', strtotime($fromDate)) }}</span> to <span class="text-amber-400 font-bold">{{ date('d M, Y', strtotime($toDate)) }}</span>
        </div>
    </div>

    <!-- PRIMARY FINANCIAL STATS CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <!-- Card 1: Liquid Cash & Bank -->
        <div class="glass-panel rounded-2xl p-4 border border-cyan-500/20 bg-gradient-to-br from-cyan-950/20 to-slate-900/40 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-cyan-400/90 block">Liquid Funds (ক্যাশ ও ব্যাংক)</span>
                    <h3 class="text-2xl font-black text-white mt-1 font-mono">৳ {{ number_format($totalLiquidBalance, 2) }}</h3>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-cyan-500/15 border border-cyan-500/30 flex items-center justify-center text-cyan-400">
                    <i class="fa-solid fa-building-columns text-lg"></i>
                </div>
            </div>
            <div class="mt-3 text-[11px] text-slate-400 flex items-center justify-between border-t border-slate-800/80 pt-2 font-mono">
                <span>{{ $liquidAccounts->count() }} Liquid Accounts</span>
                <a href="{{ route('factory.accounts.chart.index') }}" class="text-cyan-400 hover:underline">View All &rarr;</a>
            </div>
        </div>

        <!-- Card 2: Income -->
        <div class="glass-panel rounded-2xl p-4 border border-emerald-500/20 bg-gradient-to-br from-emerald-950/20 to-slate-900/40 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-400/90 block">Total Income (মোট আয়)</span>
                    <h3 class="text-2xl font-black text-emerald-400 mt-1 font-mono">৳ {{ number_format($monthIncome, 2) }}</h3>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
                    <i class="fa-solid fa-arrow-trend-up text-lg"></i>
                </div>
            </div>
            <div class="mt-3 text-[11px] text-slate-400 flex items-center justify-between border-t border-slate-800/80 pt-2 font-mono">
                <span>All time: ৳ {{ number_format($allTimeIncome, 0) }}</span>
                <a href="{{ route('factory.accounts.income.index') }}" class="text-emerald-400 hover:underline">Manage &rarr;</a>
            </div>
        </div>

        <!-- Card 3: Expense -->
        <div class="glass-panel rounded-2xl p-4 border border-rose-500/20 bg-gradient-to-br from-rose-950/20 to-slate-900/40 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-rose-400/90 block">Total Expense (মোট খরচ)</span>
                    <h3 class="text-2xl font-black text-rose-400 mt-1 font-mono">৳ {{ number_format($monthExpense, 2) }}</h3>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-rose-500/15 border border-rose-500/30 flex items-center justify-center text-rose-400">
                    <i class="fa-solid fa-arrow-trend-down text-lg"></i>
                </div>
            </div>
            <div class="mt-3 text-[11px] text-slate-400 flex items-center justify-between border-t border-slate-800/80 pt-2 font-mono">
                <span>All time: ৳ {{ number_format($allTimeExpense, 0) }}</span>
                <a href="{{ route('factory.accounts.expense.index') }}" class="text-rose-400 hover:underline">Manage &rarr;</a>
            </div>
        </div>

        <!-- Card 4: Net Profit / Loss -->
        <div class="glass-panel rounded-2xl p-4 border {{ $netProfit >= 0 ? 'border-amber-500/30 bg-gradient-to-br from-amber-950/20 to-slate-900/40' : 'border-rose-500/40 bg-gradient-to-br from-rose-950/30 to-slate-900/40' }} relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider {{ $netProfit >= 0 ? 'text-amber-400' : 'text-rose-400' }} block">
                        Net {{ $netProfit >= 0 ? 'Profit (নিট লাভ)' : 'Loss (নিট ক্ষতি)' }}
                    </span>
                    <h3 class="text-2xl font-black {{ $netProfit >= 0 ? 'text-amber-300' : 'text-rose-400' }} mt-1 font-mono">
                        {{ $netProfit >= 0 ? '৳ ' . number_format($netProfit, 2) : '- ৳ ' . number_format(abs($netProfit), 2) }}
                    </h3>
                </div>
                <div class="w-11 h-11 rounded-2xl {{ $netProfit >= 0 ? 'bg-amber-500/15 border border-amber-500/30 text-amber-400' : 'bg-rose-500/15 border border-rose-500/30 text-rose-400' }} flex items-center justify-center">
                    <i class="fa-solid fa-scale-balanced text-lg"></i>
                </div>
            </div>
            <div class="mt-3 text-[11px] text-slate-400 flex items-center justify-between border-t border-slate-800/80 pt-2 font-mono">
                <span>Income vs Expense Margin</span>
                <a href="{{ route('factory.accounts.reports.index', ['tab' => 'profit_loss']) }}" class="text-amber-400 hover:underline">P&L Report &rarr;</a>
            </div>
        </div>

    </div>

    <!-- SECONDARY: RECEIVABLES, PAYABLES & BANK BALANCES -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Column 1 & 2: Cash & Bank Accounts Balance Board -->
        <div class="lg:col-span-2 glass-panel p-5 rounded-2xl border border-slate-800/80 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-cyan-400 animate-pulse"></span>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">Cash & Bank Accounts (তহবিল বিবরণী)</h3>
                </div>
                <a href="{{ route('factory.accounts.chart.index') }}" class="text-xs text-cyan-400 hover:text-cyan-300 font-semibold flex items-center gap-1">
                    <span>Manage Accounts</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($liquidAccounts as $acc)
                <div class="p-3.5 rounded-xl border border-slate-800 bg-slate-900/60 hover:border-slate-700 transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700">
                                #{{ $acc->account_code }}
                            </span>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-400/90">
                                {{ $acc->category }}
                            </span>
                        </div>
                        <h4 class="text-xs font-bold text-white truncate" title="{{ $acc->name }}">{{ $acc->name }}</h4>
                        @if($acc->account_number)
                        <p class="text-[11px] font-mono text-slate-400 mt-0.5">A/C: {{ $acc->account_number }}</p>
                        @endif
                    </div>
                    <div class="mt-3 pt-2 border-t border-slate-800/80 flex items-center justify-between">
                        <span class="text-[10px] text-slate-400 uppercase">Balance:</span>
                        <span class="text-sm font-black font-mono {{ $acc->current_balance >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                            ৳ {{ number_format($acc->current_balance, 2) }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Customer & Supplier Highlights -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                <div class="p-3.5 rounded-xl border border-rose-500/20 bg-rose-950/10 flex items-center justify-between">
                    <div>
                        <span class="text-[11px] text-rose-300 font-bold uppercase block">Customer Due (মার্কেট বাকি)</span>
                        <h4 class="text-lg font-black text-rose-400 font-mono mt-0.5">৳ {{ number_format($totalReceivable, 2) }}</h4>
                    </div>
                    <a href="{{ route('factory.customers.due.index') }}" class="px-2.5 py-1.5 rounded-lg bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/40 text-[11px] font-bold">
                        Collect Due &rarr;
                    </a>
                </div>

                <div class="p-3.5 rounded-xl border border-indigo-500/20 bg-indigo-950/10 flex items-center justify-between">
                    <div>
                        <span class="text-[11px] text-indigo-300 font-bold uppercase block">Supplier Payable (সাপ্লায়ার দেনা)</span>
                        <h4 class="text-lg font-black text-indigo-400 font-mono mt-0.5">৳ {{ number_format($totalPayable, 2) }}</h4>
                    </div>
                    <a href="{{ route('factory.suppliers.index') }}" class="px-2.5 py-1.5 rounded-lg bg-indigo-500/20 hover:bg-indigo-500/30 text-indigo-300 border border-indigo-500/40 text-[11px] font-bold">
                        Suppliers &rarr;
                    </a>
                </div>
            </div>
        </div>

        <!-- Column 3: Top Expense Categories -->
        <div class="glass-panel p-5 rounded-2xl border border-slate-800/80 space-y-4 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-chart-simple text-rose-400"></i>
                        Top Expenses (শীর্ষ খরচসমূহ)
                    </h3>
                    <a href="{{ route('factory.accounts.expense.index') }}" class="text-xs text-rose-400 hover:text-rose-300 font-semibold">View All</a>
                </div>

                <div class="space-y-3 mt-4">
                    @forelse($expenseBreakdown as $exp)
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-slate-300 font-medium truncate max-w-[180px]" title="{{ $exp->name }}">{{ $exp->name }}</span>
                            <span class="font-mono font-bold text-rose-400">৳ {{ number_format($exp->total_spent, 2) }}</span>
                        </div>
                        @php
                            $perc = $monthExpense > 0 ? min(100, round(($exp->total_spent / $monthExpense) * 100)) : 0;
                        @endphp
                        <div class="w-full h-1.5 rounded-full bg-slate-800 overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-rose-500 to-amber-500 rounded-full" style="width: {{ $perc }}%"></div>
                        </div>
                    </div>
                    @empty
                    <div class="py-8 text-center text-slate-500 text-xs">
                        <i class="fa-solid fa-receipt text-2xl mb-2 opacity-50 block"></i>
                        No expense records in this filtered period
                    </div>
                    @endforelse
                </div>
            </div>

            <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800 text-[11px] text-slate-400 flex items-center justify-between">
                <span>Need complete P&L analysis?</span>
                <a href="{{ route('factory.accounts.reports.index', ['tab' => 'profit_loss']) }}" class="text-teal-400 font-bold hover:underline">
                    View P&L Report
                </a>
            </div>
        </div>

    </div>

    <!-- RECENT VOUCHERS TABLE -->
    <div class="glass-panel rounded-2xl border border-slate-800/80 overflow-hidden">
        <div class="p-4 border-b border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice text-amber-400"></i>
                    Recent Vouchers & Transactions (সাম্প্রতিক ভাউচারসমূহ)
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Real-time audited transaction history with instant print and verification</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('factory.accounts.income.index') }}" class="text-xs px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium">Incomes</a>
                <a href="{{ route('factory.accounts.expense.index') }}" class="text-xs px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium">Expenses</a>
                <a href="{{ route('factory.accounts.transfers.index') }}" class="text-xs px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium">Transfers</a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/60 text-slate-400 uppercase text-[10px] font-bold border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Voucher No</th>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4">Type</th>
                        <th class="py-3 px-4">Particulars / Party</th>
                        <th class="py-3 px-4">Method</th>
                        <th class="py-3 px-4 text-right">Amount (৳)</th>
                        <th class="py-3 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($recentVouchers as $v)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="py-3 px-4 font-mono font-bold text-amber-400">
                            {{ $v->voucher_no }}
                        </td>
                        <td class="py-3 px-4 font-mono text-slate-400">
                            {{ $v->voucher_date->format('d M, Y') }}
                        </td>
                        <td class="py-3 px-4">
                            @if($v->voucher_type === 'income')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    <i class="fa-solid fa-arrow-down-long text-[9px] mr-1"></i>Income
                                </span>
                            @elseif($v->voucher_type === 'expense')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                    <i class="fa-solid fa-arrow-up-long text-[9px] mr-1"></i>Expense
                                </span>
                            @elseif($v->voucher_type === 'transfer')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">
                                    <i class="fa-solid fa-arrows-rotate text-[9px] mr-1"></i>Transfer
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-500/20 text-slate-300 border border-slate-500/30">
                                    {{ ucfirst($v->voucher_type) }}
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <span class="text-white font-medium block">
                                {{ $v->party_name ?: ($v->customer?->company_name ?: ($v->supplier?->name ?: ($v->employee?->name ?: 'N/A'))) }}
                            </span>
                            @if($v->notes)
                            <span class="text-[11px] text-slate-400 truncate max-w-xs block">{{ $v->notes }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 font-mono text-slate-300 text-[11px]">
                            {{ $v->payment_method_label }}
                            @if($v->reference_no)
                            <span class="text-[10px] text-slate-500 block">Ref: {{ $v->reference_no }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right font-mono font-black text-sm {{ $v->voucher_type === 'income' ? 'text-emerald-400' : ($v->voucher_type === 'expense' ? 'text-rose-400' : 'text-cyan-400') }}">
                            {{ $v->voucher_type === 'income' ? '+' : ($v->voucher_type === 'expense' ? '-' : '') }} ৳ {{ number_format($v->amount, 2) }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                @if($v->voucher_type === 'income')
                                <a href="{{ route('factory.accounts.income.print', $v->id) }}" target="_blank" 
                                   class="p-1.5 rounded-lg bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 border border-emerald-500/40 text-xs" title="Print Money Receipt">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                                @elseif($v->voucher_type === 'expense')
                                <a href="{{ route('factory.accounts.expense.print', $v->id) }}" target="_blank" 
                                   class="p-1.5 rounded-lg bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/40 text-xs" title="Print Debit Voucher">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                                @endif
                                
                                <form action="{{ route('factory.accounts.vouchers.destroy', $v->id) }}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete voucher {{ $v->voucher_no }}? Account balances will be reverted.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg bg-slate-800 hover:bg-rose-600/30 text-slate-400 hover:text-rose-400 text-xs transition-all" title="Delete Voucher">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-500">
                            <i class="fa-solid fa-folder-open text-2xl mb-2 opacity-50 block"></i>
                            No accounting vouchers recorded yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

