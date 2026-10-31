@extends('layouts.app')

@section('title', 'Warehouse Stock Management | Factory ERP')

@push('styles')
<style>
    input[type="date"]::-webkit-calendar-picker-indicator {
        filter: brightness(0) invert(1) !important;
        cursor: pointer !important;
        opacity: 1 !important;
    }
    input[type="date"]::-webkit-calendar-picker-indicator:hover {
        opacity: 0.8 !important;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-warehouse text-amber-400"></i>
                Warehouse Inventory & Stock
            </h2>
            <p class="text-xs text-slate-400">Real-time inventory: automatically increased by Production, reduced by Deliveries, plus manual adjustments</p>
        </div>

        <div class="flex items-center gap-2.5">
            <!-- Manual Stock Adjustment Button (Opens Modal) -->
            <button type="button" onclick="openManualAdjustModal()"
                    class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 text-white font-bold text-xs shadow-lg shadow-amber-500/25 transition-all flex items-center gap-2">
                <i class="fa-solid fa-sliders"></i>
                <span>Manual Stock Adjustment</span>
            </button>

            <!-- CSV Download Button -->
            <a id="csvDownloadBtn" 
               href="{{ route('factory.stocks.export.csv', ['range' => $range, 'start_date' => $startDate, 'end_date' => $endDate, 'tab' => $tab]) }}" 
               title="Export Active Stock Data to CSV"
               class="px-3.5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-emerald-400 hover:text-emerald-300 border border-slate-700 hover:border-emerald-500/40 font-bold text-xs shadow-lg transition-all flex items-center gap-2">
                <i class="fa-solid fa-file-csv text-sm"></i>
                <span>Download CSV</span>
            </a>
        </div>
    </div>

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

    <!-- Date Filter Bar (Date-wise Presets & Custom Range) -->
    <div class="glass-panel rounded-2xl p-3 border border-slate-700/60 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-1.5 flex-wrap">
            <span class="text-xs font-semibold text-slate-400 mr-1 flex items-center gap-1.5">
                <i class="fa-solid fa-calendar-days text-amber-400"></i>
                Date Filter:
            </span>

            <a href="{{ route('factory.stocks.index', ['range' => 'this_month', 'tab' => $tab]) }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all {{ $range === 'this_month' ? 'bg-gradient-to-r from-amber-500 to-orange-600 text-white shadow-md shadow-amber-500/20' : 'bg-slate-800/80 text-slate-300 hover:text-white hover:bg-slate-700 border border-slate-700' }}">
                This Month (Default)
            </a>

            <a href="{{ route('factory.stocks.index', ['range' => 'today', 'tab' => $tab]) }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all {{ $range === 'today' ? 'bg-gradient-to-r from-amber-500 to-orange-600 text-white shadow-md shadow-amber-500/20' : 'bg-slate-800/80 text-slate-300 hover:text-white hover:bg-slate-700 border border-slate-700' }}">
                Today
            </a>

            <a href="{{ route('factory.stocks.index', ['range' => 'last_7_days', 'tab' => $tab]) }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all {{ $range === 'last_7_days' ? 'bg-gradient-to-r from-amber-500 to-orange-600 text-white shadow-md shadow-amber-500/20' : 'bg-slate-800/80 text-slate-300 hover:text-white hover:bg-slate-700 border border-slate-700' }}">
                Last 7 Days
            </a>

            <a href="{{ route('factory.stocks.index', ['range' => 'last_month', 'tab' => $tab]) }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all {{ $range === 'last_month' ? 'bg-gradient-to-r from-amber-500 to-orange-600 text-white shadow-md shadow-amber-500/20' : 'bg-slate-800/80 text-slate-300 hover:text-white hover:bg-slate-700 border border-slate-700' }}">
                Last Month
            </a>

            <a href="{{ route('factory.stocks.index', ['range' => 'all', 'tab' => $tab]) }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all {{ $range === 'all' ? 'bg-gradient-to-r from-amber-500 to-orange-600 text-white shadow-md shadow-amber-500/20' : 'bg-slate-800/80 text-slate-300 hover:text-white hover:bg-slate-700 border border-slate-700' }}">
                All Time
            </a>
        </div>

        <!-- Custom Date Range Form -->
        <form action="{{ route('factory.stocks.index') }}" method="GET" class="flex items-center gap-2">
            <input type="hidden" name="range" value="custom">
            <input type="hidden" name="tab" id="dateFilterTab" value="{{ $tab }}">
            <div class="flex items-center gap-1.5 bg-[#070d1e] border border-slate-700/80 rounded-xl px-2.5 py-1">
                <span class="text-[10px] text-slate-400 font-semibold">From:</span>
                <input type="date" name="start_date" value="{{ $startDate ?? '' }}" required
                       style="color-scheme: dark;"
                       class="bg-transparent text-xs text-white focus:outline-none cursor-pointer w-[115px] sm:w-[130px]">
            </div>
            <div class="flex items-center gap-1.5 bg-[#070d1e] border border-slate-700/80 rounded-xl px-2.5 py-1">
                <span class="text-[10px] text-slate-400 font-semibold">To:</span>
                <input type="date" name="end_date" value="{{ $endDate ?? '' }}" required
                       style="color-scheme: dark;"
                       class="bg-transparent text-xs text-white focus:outline-none cursor-pointer w-[115px] sm:w-[130px]">
            </div>
            <button type="submit" 
                    class="px-3 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-semibold transition-all shadow-md">
                Filter
            </button>
            @if($range !== 'this_month')
            <a href="{{ route('factory.stocks.index', ['tab' => $tab]) }}" title="Reset to This Month"
               class="p-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white text-xs transition-colors">
                <i class="fa-solid fa-rotate-left"></i>
            </a>
            @endif
        </form>
    </div>

    <!-- Main Full-Width Content: Tabs & Tables -->
    <div class="space-y-4">
        
        <!-- Section Tabs Toggle (Current Stock Balance vs Movement History) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-700/70 pb-3">
            <div class="flex items-center gap-2">
                <!-- Tab 1: Current Warehouse Stock Balance (Initial Active) -->
                <button type="button" id="tabBtnBalance" onclick="switchStockTab('balance')"
                        class="px-3.5 py-2 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 {{ $tab === 'balance' ? 'bg-gradient-to-r from-amber-500 to-orange-600 text-white shadow-lg shadow-amber-500/25' : 'bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700' }}">
                    <i class="fa-solid fa-boxes-stacked"></i>
                    <span>Current Stock Balance</span>
                    <span class="ml-1 px-2 py-0.5 rounded-full text-[10px] bg-black/30 font-mono">
                        {{ $products->count() }}
                    </span>
                </button>

                <!-- Tab 2: Recent Stock Movement History -->
                <button type="button" id="tabBtnHistory" onclick="switchStockTab('history')"
                        class="px-3.5 py-2 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 {{ $tab === 'history' ? 'bg-gradient-to-r from-cyan-500 to-blue-600 text-white shadow-lg shadow-cyan-500/25' : 'bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700' }}">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <span>Movement History</span>
                    <span class="ml-1 px-2 py-0.5 rounded-full text-[10px] bg-black/30 font-mono">
                        {{ $stockLogs->count() }}
                    </span>
                </button>
            </div>

            <!-- Live Search Box -->
            <div class="relative w-full sm:w-80">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="stockSearchInput" placeholder="Search product, unit, stock..." 
                       class="w-full bg-[#070d1e] text-xs text-white placeholder-slate-500 rounded-xl pl-8 pr-7 py-2 border border-slate-700 focus:border-amber-400 focus:outline-none transition-all">
                <button type="button" id="clearStockSearchBtn" onclick="clearStockSearch()" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white hidden text-xs">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

            <!-- ==========================================
                 TAB 1: Current Warehouse Stock Balance Table
                 ========================================== -->
            <div id="sectionBalance" class="{{ $tab === 'balance' ? '' : 'hidden' }} space-y-4">
                <div class="glass-panel rounded-3xl p-5 border border-slate-700/60">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-cubes-stacked text-amber-400"></i>
                            Current Warehouse Stock Balance
                        </h3>
                        <span id="balanceCounterDisplay" class="text-xs text-amber-300 font-mono">
                            Showing <span id="balanceVisibleCount">{{ min(10, $products->count()) }}</span> of <span id="balanceTotalCount">{{ $products->count() }}</span> Products
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[760px] text-left text-xs">
                            <thead>
                                <tr class="text-slate-400 border-b border-slate-700/80 font-semibold bg-slate-800/20">
                                    <th class="py-3 px-3.5 whitespace-nowrap">Product Name</th>
                                    <th class="py-3 px-3.5 whitespace-nowrap text-center">Unit</th>
                                    <th class="py-3 px-3.5 whitespace-nowrap text-right">Total Produced (+)</th>
                                    <th class="py-3 px-3.5 whitespace-nowrap text-right">Total Delivered (-)</th>
                                    <th class="py-3 px-3.5 whitespace-nowrap text-right">Manual Adj</th>
                                    <th class="py-3 px-3.5 whitespace-nowrap text-right font-bold text-cyan-300">Available Stock</th>
                                    <th class="py-3 px-3.5 whitespace-nowrap text-right min-w-[130px]">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/80">
                                @forelse($products as $prod)
                                @php
                                    $stk = $prod->stock;
                                    $curr = $stk ? $stk->current_stock : 0;
                                @endphp
                                <tr class="stock-balance-row hover:bg-slate-800/40 transition-colors"
                                    data-search="{{ strtolower($prod->name . ' ' . ($prod->unit->short_code ?? '') . ' ' . ($prod->unit->name ?? '') . ' stock ' . $curr) }}">
                                    <td class="py-3 px-3.5 font-bold text-white whitespace-nowrap">
                                        {{ $prod->name }}
                                    </td>
                                    <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                        <span class="px-2 py-0.5 rounded bg-blue-500/10 text-cyan-300 font-mono text-[11px]">
                                            {{ $prod->unit->short_code ?? 'Pcs' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3.5 text-right font-mono text-emerald-400 whitespace-nowrap">
                                        + {{ number_format($stk ? $stk->total_produced : 0) }}
                                    </td>
                                    <td class="py-3 px-3.5 text-right font-mono text-rose-400 whitespace-nowrap">
                                        - {{ number_format($stk ? $stk->total_delivered : 0) }}
                                    </td>
                                    <td class="py-3 px-3.5 text-right font-mono text-slate-300 whitespace-nowrap">
                                        {{ $stk && $stk->manual_adjusted != 0 ? ($stk->manual_adjusted > 0 ? '+'.number_format($stk->manual_adjusted) : number_format($stk->manual_adjusted)) : '0' }}
                                    </td>
                                    <td class="py-3 px-3.5 text-right whitespace-nowrap">
                                        <span class="px-3 py-1 rounded-full text-xs font-black font-mono {{ $curr > 50 ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30' }}">
                                            {{ number_format($curr) }} {{ $prod->unit->short_code ?? '' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3.5 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button" 
                                                    onclick="openEditStockModal({{ $prod->id }}, '{{ addslashes($prod->name) }}', {{ $curr }}, '{{ $prod->unit->short_code ?? '' }}')"
                                                    class="px-2.5 py-1 rounded-lg bg-cyan-600/20 hover:bg-cyan-600/40 text-cyan-300 border border-cyan-500/30 text-[11px] font-semibold transition-all inline-flex items-center gap-1"
                                                    title="Edit Stock Balance">
                                                <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                                <span>Edit</span>
                                            </button>

                                            <form action="{{ route('factory.stocks.balance.reset', $prod->id) }}" method="POST" 
                                                  onsubmit="return confirm('Reset available stock for \'{{ addslashes($prod->name) }}\' to 0?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="px-2.5 py-1 rounded-lg bg-rose-600/20 hover:bg-rose-600/40 text-rose-300 border border-rose-500/30 text-[11px] font-semibold transition-all inline-flex items-center gap-1"
                                                        title="Reset Stock to 0">
                                                    <i class="fa-solid fa-trash-can text-[10px]"></i>
                                                    <span>Reset</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-slate-500">No products recorded in warehouse inventory.</td>
                                </tr>
                                @endforelse

                                <!-- No Search Results Row -->
                                <tr id="noBalanceSearchResultRow" class="hidden">
                                    <td colspan="7" class="py-8 text-center text-slate-400">
                                        <div class="flex flex-col items-center justify-center gap-1.5">
                                            <i class="fa-solid fa-box-open text-2xl text-slate-600"></i>
                                            <span class="text-xs">No products match your inventory search.</span>
                                            <button type="button" onclick="clearStockSearch()" class="text-amber-400 text-xs underline mt-1">Clear search</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Load More Stock Products Button -->
                    <div id="loadMoreBalanceContainer" class="mt-4 pt-3 border-t border-slate-800 text-center {{ $products->count() > 10 ? '' : 'hidden' }}">
                        <button type="button" onclick="loadMoreBalance()"
                                class="px-5 py-2 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-amber-300 hover:text-white border border-slate-700 hover:border-amber-500/40 text-xs font-semibold transition-all inline-flex items-center gap-2 shadow-lg">
                            <i class="fa-solid fa-angles-down text-amber-400"></i>
                            <span>Load More Products (+10)</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 TAB 2: Recent Stock Movement History Table
                 ========================================== -->
            <div id="sectionHistory" class="{{ $tab === 'history' ? '' : 'hidden' }} space-y-4">
                <div class="glass-panel rounded-3xl p-5 border border-slate-700/60">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-clock-rotate-left text-cyan-400"></i>
                            Recent Stock Movement History
                        </h3>
                        <span id="historyCounterDisplay" class="text-xs text-cyan-300 font-mono">
                            Showing <span id="historyVisibleCount">{{ min(10, $stockLogs->count()) }}</span> of <span id="historyTotalCount">{{ $stockLogs->count() }}</span> Logs
                            @if($range === 'this_month')
                                <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 font-sans font-semibold">This Month</span>
                            @elseif($range === 'today')
                                <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 font-sans font-semibold">Today</span>
                            @elseif($range === 'last_7_days')
                                <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 font-sans font-semibold">Last 7 Days</span>
                            @elseif($range === 'last_month')
                                <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 font-sans font-semibold">Last Month</span>
                            @elseif($range === 'custom')
                                <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 font-sans font-semibold">{{ $startDate }} to {{ $endDate }}</span>
                            @else
                                <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] bg-slate-700 text-slate-300 font-sans font-semibold">All Time</span>
                            @endif
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[780px] text-left text-xs">
                            <thead>
                                <tr class="text-slate-400 border-b border-slate-700/80 font-semibold bg-slate-800/20">
                                    <th class="py-3 px-3.5 whitespace-nowrap">Date & Time</th>
                                    <th class="py-3 px-3.5 whitespace-nowrap">Product</th>
                                    <th class="py-3 px-3.5 whitespace-nowrap text-center">Movement Type</th>
                                    <th class="py-3 px-3.5 whitespace-nowrap text-right">Quantity</th>
                                    <th class="py-3 px-3.5 whitespace-nowrap text-right">Balance</th>
                                    <th class="py-3 px-3.5 min-w-[180px]">Ref / Notes</th>
                                    <th class="py-3 px-3.5 whitespace-nowrap text-right min-w-[130px]">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/80">
                                @forelse($stockLogs as $log)
                                @php
                                    $typeSearchText = 'adjustment';
                                    if ($log->type === 'production') $typeSearchText = 'production in';
                                    elseif ($log->type === 'delivery') $typeSearchText = 'delivery out';
                                    elseif ($log->type === 'manual_add') $typeSearchText = 'manual add';
                                    elseif ($log->type === 'manual_subtract') $typeSearchText = 'manual subtract';
                                @endphp
                                <tr class="stock-history-row hover:bg-slate-800/40 transition-colors">
                                    <td class="py-3 px-3.5 text-slate-400 font-mono whitespace-nowrap">
                                        {{ date('d M, h:i A', strtotime($log->created_at)) }}
                                    </td>
                                    <td class="py-3 px-3.5 font-bold text-white whitespace-nowrap">
                                        {{ $log->product->name ?? 'N/A' }}
                                    </td>
                                    <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                        @if($log->type === 'production')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                                Production In (+)
                                            </span>
                                        @elseif($log->type === 'delivery')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/15 text-rose-400 border border-rose-500/30">
                                                Delivery Out (-)
                                            </span>
                                        @elseif($log->type === 'manual_add')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-cyan-500/15 text-cyan-300 border border-cyan-500/30">
                                                Manual Add (+)
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30">
                                                Manual Subtract (-)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3.5 text-right font-mono font-bold text-white whitespace-nowrap">
                                        {{ number_format($log->qty) }}
                                    </td>
                                    <td class="py-3 px-3.5 text-right font-mono font-bold text-cyan-300 whitespace-nowrap">
                                        {{ number_format($log->new_stock) }}
                                    </td>
                                    <td class="py-3 px-3.5 text-slate-300 min-w-[180px]">
                                        <span class="text-xs">{{ $log->notes }}</span>
                                    </td>
                                    <td class="py-3 px-3.5 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button" 
                                                    onclick="openEditLogModal({{ $log->id }}, '{{ addslashes($log->product->name ?? 'Product') }}', '{{ addslashes($log->notes ?? '') }}')"
                                                    class="px-2.5 py-1 rounded-lg bg-cyan-600/20 hover:bg-cyan-600/40 text-cyan-300 border border-cyan-500/30 text-[11px] font-semibold transition-all inline-flex items-center gap-1"
                                                    title="Edit Log Notes">
                                                <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                                <span>Edit</span>
                                            </button>

                                            <form action="{{ route('factory.stocks.logs.destroy', $log->id) }}" method="POST" 
                                                  onsubmit="return confirm('Delete this movement log and reverse the stock balance for \'{{ addslashes($log->product->name ?? 'Product') }}\'?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="px-2.5 py-1 rounded-lg bg-rose-600/20 hover:bg-rose-600/40 text-rose-300 border border-rose-500/30 text-[11px] font-semibold transition-all inline-flex items-center gap-1"
                                                        title="Delete Log & Reverse Stock">
                                                    <i class="fa-solid fa-trash-can text-[10px]"></i>
                                                    <span>Delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="py-10 text-center">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <i class="fa-solid fa-calendar-xmark text-3xl text-slate-600"></i>
                                            <span class="text-slate-400 font-medium">No stock movements found for this date range.</span>
                                            @if($range !== 'all')
                                            <a href="{{ route('factory.stocks.index', ['range' => 'all', 'tab' => 'history']) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 mt-2 rounded-xl bg-cyan-600/20 text-cyan-300 border border-cyan-500/30 hover:bg-cyan-600/30 text-xs font-semibold transition-all">
                                                <i class="fa-solid fa-arrows-rotate text-[11px]"></i>
                                                <span>View All Stock Logs ({{ $totalStockLogsCount }})</span>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforelse

                                <!-- No Search Results Row -->
                                <tr id="noHistorySearchResultRow" class="hidden">
                                    <td colspan="7" class="py-8 text-center text-slate-400">
                                        <div class="flex flex-col items-center justify-center gap-1.5">
                                            <i class="fa-solid fa-clock-rotate-left text-2xl text-slate-600"></i>
                                            <span class="text-xs">No movement logs match your search.</span>
                                            <button type="button" onclick="clearStockSearch()" class="text-cyan-400 text-xs underline mt-1">Clear search</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Load More History Logs Button -->
                    <div id="loadMoreHistoryContainer" class="mt-4 pt-3 border-t border-slate-800 text-center {{ $stockLogs->count() > 10 ? '' : 'hidden' }}">
                        <button type="button" onclick="loadMoreHistory()"
                                class="px-5 py-2 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-cyan-300 hover:text-white border border-slate-700 hover:border-cyan-500/40 text-xs font-semibold transition-all inline-flex items-center gap-2 shadow-lg">
                            <i class="fa-solid fa-angles-down text-cyan-400"></i>
                            <span>Load More Logs (+10)</span>
                        </button>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- MANUAL STOCK ADJUSTMENT POPUP MODAL -->
<div id="manualAdjustModal" class="fixed inset-0 z-50 bg-black/75 backdrop-blur-md hidden flex items-center justify-center p-4">
    <div class="glass-panel rounded-3xl p-6 border border-slate-700/80 max-w-md w-full relative shadow-2xl bg-[#0a1228]/95">
        <div class="flex items-center justify-between pb-3 border-b border-slate-700/80 mb-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-sliders text-amber-400"></i>
                Manual Stock Adjustment
            </h3>
            <button type="button" onclick="closeManualAdjustModal()" class="text-slate-400 hover:text-white p-1 text-base">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('factory.stocks.adjust') }}" method="POST" class="space-y-3.5">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Select Product *</label>
                <select name="product_id" required class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-amber-400 focus:outline-none">
                    <option value="">-- Choose Product --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} (Available: {{ number_format($p->current_stock) }} {{ $p->unit->short_code ?? '' }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Action Type *</label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="flex items-center gap-2 p-2 rounded-xl bg-[#070d1e] border border-slate-700 cursor-pointer text-xs">
                        <input type="radio" name="type" value="manual_add" checked class="text-amber-400">
                        <span class="text-emerald-400 font-bold">+ Add Stock</span>
                    </label>
                    <label class="flex items-center gap-2 p-2 rounded-xl bg-[#070d1e] border border-slate-700 cursor-pointer text-xs">
                        <input type="radio" name="type" value="manual_subtract" class="text-amber-400">
                        <span class="text-rose-400 font-bold">- Subtract</span>
                    </label>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Adjustment Quantity *</label>
                <input type="number" step="1" min="1" name="qty" placeholder="e.g. 50" required class="w-full bg-[#070d1e] text-xs font-mono font-bold text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-amber-400 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Reason / Notes</label>
                <textarea name="notes" rows="2" placeholder="e.g. Physical audit recount, sample deduction..." class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3 py-2 border border-slate-700 focus:border-amber-400 focus:outline-none"></textarea>
            </div>
            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-700/80">
                <button type="button" onclick="closeManualAdjustModal()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold text-xs shadow-lg shadow-amber-500/25 hover:from-amber-400 hover:to-orange-500 transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-check"></i>
                    <span>Apply Adjustment</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT STOCK MODAL -->
<div id="editStockModal" class="fixed inset-0 z-50 bg-black/75 backdrop-blur-md hidden flex items-center justify-center p-4">
    <div class="glass-panel rounded-3xl p-6 border border-slate-700/80 max-w-md w-full relative shadow-2xl bg-[#0a1228]/95">
        <div class="flex items-center justify-between pb-3 border-b border-slate-700/80 mb-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-amber-400"></i>
                Edit Available Stock Balance
            </h3>
            <button type="button" onclick="closeEditStockModal()" class="text-slate-400 hover:text-white p-1 text-base">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="editStockForm" method="POST" class="space-y-3.5">
            @csrf
            @method('PUT')

            <div class="bg-[#070d1e] p-3 rounded-xl border border-slate-800 text-xs">
                <span class="text-slate-400">Product:</span>
                <span id="modalStockProdName" class="text-white font-bold block mt-0.5"></span>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Set New Available Stock *</label>
                <div class="flex items-center gap-2">
                    <input type="number" step="1" min="0" name="current_stock" id="modalStockInput" required
                           class="w-full bg-[#070d1e] text-sm font-mono font-bold text-emerald-400 rounded-xl px-3.5 py-2 border border-slate-700 focus:border-amber-400 focus:outline-none">
                    <span id="modalStockUnit" class="text-xs text-slate-400 font-mono"></span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Reason / Notes</label>
                <textarea name="notes" id="modalStockNotes" rows="2" placeholder="Audit recount, physical inventory adjustment..."
                          class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3.5 py-2 border border-slate-700 focus:border-amber-400 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-700/80">
                <button type="button" onclick="closeEditStockModal()" 
                        class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-5 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold text-xs shadow-lg shadow-amber-500/25 hover:from-amber-400 hover:to-orange-500 transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-check"></i>
                    <span>Save Balance</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT LOG NOTES MODAL -->
<div id="editLogModal" class="fixed inset-0 z-50 bg-black/75 backdrop-blur-md hidden flex items-center justify-center p-4">
    <div class="glass-panel rounded-3xl p-6 border border-slate-700/80 max-w-md w-full relative shadow-2xl bg-[#0a1228]/95">
        <div class="flex items-center justify-between pb-3 border-b border-slate-700/80 mb-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-cyan-400"></i>
                Edit Movement Log Notes
            </h3>
            <button type="button" onclick="closeEditLogModal()" class="text-slate-400 hover:text-white p-1 text-base">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="editLogForm" method="POST" class="space-y-3.5">
            @csrf
            @method('PUT')

            <div class="bg-[#070d1e] p-3 rounded-xl border border-slate-800 text-xs">
                <span class="text-slate-400">Product:</span>
                <span id="modalLogProdName" class="text-white font-bold block mt-0.5"></span>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Reason / Notes</label>
                <textarea name="notes" id="modalLogNotes" rows="3" required
                          class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3.5 py-2 border border-slate-700 focus:border-cyan-400 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-700/80">
                <button type="button" onclick="closeEditLogModal()" 
                        class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-5 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-bold text-xs shadow-lg shadow-cyan-500/25 hover:from-cyan-400 hover:to-blue-500 transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-check"></i>
                    <span>Update Notes</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Global State
    let activeStockTab = '{{ $tab }}';
    const batchSize = 10;

    // Balance state
    let allBalanceRows = [];
    let balanceVisibleLimit = 10;

    // History state
    let allHistoryRows = [];
    let historyVisibleLimit = 10;

    document.addEventListener('DOMContentLoaded', () => {
        allBalanceRows = Array.from(document.querySelectorAll('.stock-balance-row'));
        allHistoryRows = Array.from(document.querySelectorAll('.stock-history-row'));

        applyBalanceVisibility();
        applyHistoryVisibility();

        // Search Input Handler
        const searchInput = document.getElementById('stockSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.trim().toLowerCase();
                const clearBtn = document.getElementById('clearStockSearchBtn');
                if (clearBtn) {
                    clearBtn.classList.toggle('hidden', query.length === 0);
                }

                if (activeStockTab === 'balance') {
                    filterBalanceByQuery(query);
                } else {
                    filterHistoryByQuery(query);
                }
            });
        }
    });

    // ==========================================
    // TAB SWITCHING (Current Balance vs Movement History)
    // ==========================================
    function switchStockTab(tab) {
        activeStockTab = tab;

        const btnBalance = document.getElementById('tabBtnBalance');
        const btnHistory = document.getElementById('tabBtnHistory');
        const secBalance = document.getElementById('sectionBalance');
        const secHistory = document.getElementById('sectionHistory');
        const searchInput = document.getElementById('stockSearchInput');
        const csvBtn = document.getElementById('csvDownloadBtn');
        const dateFilterTab = document.getElementById('dateFilterTab');

        if (dateFilterTab) dateFilterTab.value = tab;

        // Update CSV link query
        if (csvBtn) {
            const currentUrl = new URL(csvBtn.href);
            currentUrl.searchParams.set('tab', tab);
            csvBtn.href = currentUrl.toString();
        }

        if (tab === 'balance') {
            btnBalance.className = "px-3.5 py-2 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 bg-gradient-to-r from-amber-500 to-orange-600 text-white shadow-lg shadow-amber-500/25";
            btnHistory.className = "px-3.5 py-2 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700";
            
            secBalance.classList.remove('hidden');
            secHistory.classList.add('hidden');

            if (searchInput) {
                searchInput.placeholder = "Search product, unit, stock...";
                searchInput.focus();
            }
        } else {
            btnHistory.className = "px-3.5 py-2 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 bg-gradient-to-r from-cyan-500 to-blue-600 text-white shadow-lg shadow-cyan-500/25";
            btnBalance.className = "px-3.5 py-2 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700";
            
            secHistory.classList.remove('hidden');
            secBalance.classList.add('hidden');

            if (searchInput) {
                searchInput.placeholder = "Search by product, movement type, notes...";
                searchInput.focus();
            }
        }

        // Re-run search for newly active tab
        if (searchInput && searchInput.value.trim().length > 0) {
            const query = searchInput.value.trim().toLowerCase();
            if (activeStockTab === 'balance') filterBalanceByQuery(query);
            else filterHistoryByQuery(query);
        } else {
            if (activeStockTab === 'balance') applyBalanceVisibility();
            else applyHistoryVisibility();
        }
    }

    // ==========================================
    // BALANCE: Visibility, Search & Load More
    // ==========================================
    function applyBalanceVisibility() {
        allBalanceRows.forEach((row, idx) => {
            row.style.display = idx < balanceVisibleLimit ? '' : 'none';
        });

        const currentVisible = Math.min(balanceVisibleLimit, allBalanceRows.length);
        const countEl = document.getElementById('balanceVisibleCount');
        if (countEl) countEl.innerText = currentVisible;

        const loadMore = document.getElementById('loadMoreBalanceContainer');
        if (loadMore) {
            loadMore.classList.toggle('hidden', balanceVisibleLimit >= allBalanceRows.length);
        }
    }

    function loadMoreBalance() {
        balanceVisibleLimit += batchSize;
        applyBalanceVisibility();
    }

    function filterBalanceByQuery(query) {
        if (!query) {
            document.getElementById('noBalanceSearchResultRow').classList.add('hidden');
            applyBalanceVisibility();
            return;
        }

        let matched = 0;
        allBalanceRows.forEach(row => {
            const text = (row.getAttribute('data-search') || '').toLowerCase();
            if (text.includes(query)) {
                row.style.display = '';
                matched++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('noBalanceSearchResultRow').classList.toggle('hidden', matched > 0);
        document.getElementById('loadMoreBalanceContainer').classList.add('hidden');
        document.getElementById('balanceVisibleCount').innerText = matched;
    }

    // ==========================================
    // HISTORY: Visibility, Search & Load More
    // ==========================================
    function applyHistoryVisibility() {
        allHistoryRows.forEach((row, idx) => {
            row.style.display = idx < historyVisibleLimit ? '' : 'none';
        });

        const currentVisible = Math.min(historyVisibleLimit, allHistoryRows.length);
        const countEl = document.getElementById('historyVisibleCount');
        if (countEl) countEl.innerText = currentVisible;

        const loadMore = document.getElementById('loadMoreHistoryContainer');
        if (loadMore) {
            loadMore.classList.toggle('hidden', historyVisibleLimit >= allHistoryRows.length);
        }
    }

    function loadMoreHistory() {
        historyVisibleLimit += batchSize;
        applyHistoryVisibility();
    }

    function filterHistoryByQuery(query) {
        if (!query) {
            document.getElementById('noHistorySearchResultRow').classList.add('hidden');
            applyHistoryVisibility();
            return;
        }

        let matched = 0;
        allHistoryRows.forEach(row => {
            const text = (row.getAttribute('data-search') || '').toLowerCase();
            if (text.includes(query)) {
                row.style.display = '';
                matched++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('noHistorySearchResultRow').classList.toggle('hidden', matched > 0);
        document.getElementById('loadMoreHistoryContainer').classList.add('hidden');
        document.getElementById('historyVisibleCount').innerText = matched;
    }

    // Clear Search Input
    function clearStockSearch() {
        const searchInput = document.getElementById('stockSearchInput');
        if (searchInput) {
            searchInput.value = '';
            document.getElementById('clearStockSearchBtn').classList.add('hidden');
            document.getElementById('noBalanceSearchResultRow').classList.add('hidden');
            document.getElementById('noHistorySearchResultRow').classList.add('hidden');
            
            if (activeStockTab === 'balance') applyBalanceVisibility();
            else applyHistoryVisibility();

            searchInput.focus();
        }
    }
    // ==========================================
    // Modals: Manual Adjust, Edit Stock & Edit Log
    // ==========================================
    function openManualAdjustModal() {
        document.getElementById('manualAdjustModal').classList.remove('hidden');
    }

    function closeManualAdjustModal() {
        document.getElementById('manualAdjustModal').classList.add('hidden');
    }

    function openEditStockModal(id, name, currStock, unit) {
        document.getElementById('modalStockProdName').innerText = name + (unit ? ' (' + unit + ')' : '');
        document.getElementById('modalStockInput').value = currStock;
        document.getElementById('modalStockUnit').innerText = unit || '';
        document.getElementById('modalStockNotes').value = '';
        document.getElementById('editStockForm').action = `/factory/stocks/balance/${id}`;
        document.getElementById('editStockModal').classList.remove('hidden');
    }

    function closeEditStockModal() {
        document.getElementById('editStockModal').classList.add('hidden');
    }

    function openEditLogModal(id, prodName, notes) {
        document.getElementById('modalLogProdName').innerText = prodName;
        document.getElementById('modalLogNotes').value = notes;
        document.getElementById('editLogForm').action = `/factory/stocks/logs/${id}`;
        document.getElementById('editLogModal').classList.remove('hidden');
    }

    function closeEditLogModal() {
        document.getElementById('editLogModal').classList.add('hidden');
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeManualAdjustModal();
            closeEditStockModal();
            closeEditLogModal();
        }
    });
</script>
@endsection
