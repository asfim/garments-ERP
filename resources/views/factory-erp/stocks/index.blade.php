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

    <!-- Main Grid: Left Adjustment Form + Right Tabs & Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left: Manual Stock Adjustment Form -->
        <div class="lg:col-span-4 glass-panel rounded-3xl p-5 border border-slate-700/60 h-fit">
            <h3 class="text-sm font-bold text-white mb-3 flex items-center gap-2">
                <i class="fa-solid fa-sliders text-amber-400"></i>
                Manual Stock Adjustment
            </h3>

            <form action="{{ route('factory.stocks.adjust') }}" method="POST" class="space-y-3.5">
                @csrf
                
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Select Product *</label>
                    <select name="product_id" required
                            class="w-full bg-[#0a1228] text-xs text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                        <option value="">-- Choose Product --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} (Available: {{ number_format($p->current_stock) }} {{ $p->unit->short_code ?? '' }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Action Type *</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center gap-2 p-2 rounded-xl bg-[#0a1228] border border-slate-700 cursor-pointer text-xs">
                            <input type="radio" name="type" value="manual_add" checked class="text-cyan-400">
                            <span class="text-emerald-400 font-bold">+ Add Stock</span>
                        </label>
                        <label class="flex items-center gap-2 p-2 rounded-xl bg-[#0a1228] border border-slate-700 cursor-pointer text-xs">
                            <input type="radio" name="type" value="manual_subtract" class="text-cyan-400">
                            <span class="text-rose-400 font-bold">- Subtract</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Adjustment Quantity *</label>
                    <input type="number" step="1" min="1" name="qty" placeholder="e.g. 50" required
                           class="w-full bg-[#0a1228] text-xs font-mono font-bold text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Reason / Notes</label>
                    <textarea name="notes" rows="2" placeholder="e.g. Physical audit recount, sample deduction..."
                              class="w-full bg-[#0a1228] text-xs text-white rounded-xl px-3 py-2 border border-slate-700 focus:border-cyan-400 focus:outline-none"></textarea>
                </div>

                <button type="submit" 
                        class="w-full py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold text-xs shadow-lg shadow-amber-500/25 hover:from-amber-400 hover:to-orange-500 transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-check"></i>
                    <span>Apply Adjustment</span>
                </button>
            </form>
        </div>

        <!-- Right: Tab Switcher & Display Panes -->
        <div class="lg:col-span-8 space-y-4">
            
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
                <div class="relative w-full sm:w-64">
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
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="text-slate-400 border-b border-slate-700/80 font-semibold">
                                    <th class="pb-2.5">Product Name</th>
                                    <th class="pb-2.5 text-center">Unit</th>
                                    <th class="pb-2.5 text-right">Total Produced (+)</th>
                                    <th class="pb-2.5 text-right">Total Delivered (-)</th>
                                    <th class="pb-2.5 text-right">Manual Adj</th>
                                    <th class="pb-2.5 text-right font-bold text-cyan-300">Available Stock</th>
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
                                    <td class="py-3 font-bold text-white">
                                        {{ $prod->name }}
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="px-2 py-0.5 rounded bg-blue-500/10 text-cyan-300 font-mono text-[11px]">
                                            {{ $prod->unit->short_code ?? 'Pcs' }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-right font-mono text-emerald-400">
                                        + {{ number_format($stk ? $stk->total_produced : 0) }}
                                    </td>
                                    <td class="py-3 text-right font-mono text-rose-400">
                                        - {{ number_format($stk ? $stk->total_delivered : 0) }}
                                    </td>
                                    <td class="py-3 text-right font-mono text-slate-300">
                                        {{ $stk && $stk->manual_adjusted != 0 ? ($stk->manual_adjusted > 0 ? '+'.number_format($stk->manual_adjusted) : number_format($stk->manual_adjusted)) : '0' }}
                                    </td>
                                    <td class="py-3 text-right">
                                        <span class="px-3 py-1 rounded-full text-xs font-black font-mono {{ $curr > 50 ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30' }}">
                                            {{ number_format($curr) }} {{ $prod->unit->short_code ?? '' }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-500">No products recorded in warehouse inventory.</td>
                                </tr>
                                @endforelse

                                <!-- No Search Results Row -->
                                <tr id="noBalanceSearchResultRow" class="hidden">
                                    <td colspan="6" class="py-8 text-center text-slate-400">
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
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="text-slate-400 border-b border-slate-700/80 font-semibold">
                                    <th class="pb-2.5">Date</th>
                                    <th class="pb-2.5">Product</th>
                                    <th class="pb-2.5">Movement Type</th>
                                    <th class="pb-2.5 text-right">Quantity</th>
                                    <th class="pb-2.5 text-right">Balance</th>
                                    <th class="pb-2.5">Ref / Notes</th>
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
                                <tr class="stock-history-row hover:bg-slate-800/40 transition-colors"
                                    data-search="{{ strtolower(($log->product->name ?? '') . ' ' . $typeSearchText . ' ' . ($log->reference_id ?? '') . ' ' . ($log->notes ?? '') . ' ' . date('d M, Y', strtotime($log->created_at))) }}">
                                    <td class="py-2.5 text-slate-400 font-mono">{{ date('d M, h:i A', strtotime($log->created_at)) }}</td>
                                    <td class="py-2.5 font-medium text-white">{{ $log->product->name ?? 'N/A' }}</td>
                                    <td class="py-2.5">
                                        @if($log->type === 'production')
                                            <span class="text-emerald-400 font-semibold">Production In (+)</span>
                                        @elseif($log->type === 'delivery')
                                            <span class="text-rose-400 font-semibold">Delivery Out (-)</span>
                                        @elseif($log->type === 'manual_add')
                                            <span class="text-cyan-300 font-semibold">Manual Add (+)</span>
                                        @else
                                            <span class="text-amber-300 font-semibold">Manual Subtract (-)</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 text-right font-mono font-bold text-white">
                                        {{ number_format($log->qty) }}
                                    </td>
                                    <td class="py-2.5 text-right font-mono text-cyan-300">
                                        {{ number_format($log->new_stock) }}
                                    </td>
                                    <td class="py-2.5 text-slate-400 truncate max-w-xs">{{ $log->notes }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center">
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
                                    <td colspan="6" class="py-8 text-center text-slate-400">
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
</script>
@endsection
