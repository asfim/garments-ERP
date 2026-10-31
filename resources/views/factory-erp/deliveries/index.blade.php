@extends('layouts.app')

@section('title', 'Delivery Management | Factory ERP')

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
                <i class="fa-solid fa-truck-fast text-emerald-400"></i>
                Shipments & Delivery Center
            </h2>
            <p class="text-xs text-slate-400">Track pending client orders ready for shipment, process challans & update factory stocks</p>
        </div>

        <div class="flex items-center gap-2.5">
            <!-- CSV Download Button -->
            <a id="csvDownloadBtn" 
               href="{{ route('factory.deliveries.export.csv', ['range' => $range, 'start_date' => $startDate, 'end_date' => $endDate, 'tab' => $tab]) }}" 
               title="Export Active Deliveries to CSV"
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
    <div class="p-3.5 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs flex items-center gap-2">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- Date Filter Bar (Date-wise Presets & Custom Range) -->
    <div class="glass-panel rounded-2xl p-3 border border-slate-700/60 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-1.5 flex-wrap">
            <span class="text-xs font-semibold text-slate-400 mr-1 flex items-center gap-1.5">
                <i class="fa-solid fa-calendar-days text-emerald-400"></i>
                Date Filter:
            </span>

            <a href="{{ route('factory.deliveries.index', ['range' => 'this_month', 'tab' => $tab]) }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all {{ $range === 'this_month' ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-md shadow-emerald-500/20' : 'bg-slate-800/80 text-slate-300 hover:text-white hover:bg-slate-700 border border-slate-700' }}">
                This Month (Default)
            </a>

            <a href="{{ route('factory.deliveries.index', ['range' => 'today', 'tab' => $tab]) }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all {{ $range === 'today' ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-md shadow-emerald-500/20' : 'bg-slate-800/80 text-slate-300 hover:text-white hover:bg-slate-700 border border-slate-700' }}">
                Today
            </a>

            <a href="{{ route('factory.deliveries.index', ['range' => 'last_7_days', 'tab' => $tab]) }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all {{ $range === 'last_7_days' ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-md shadow-emerald-500/20' : 'bg-slate-800/80 text-slate-300 hover:text-white hover:bg-slate-700 border border-slate-700' }}">
                Last 7 Days
            </a>

            <a href="{{ route('factory.deliveries.index', ['range' => 'last_month', 'tab' => $tab]) }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all {{ $range === 'last_month' ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-md shadow-emerald-500/20' : 'bg-slate-800/80 text-slate-300 hover:text-white hover:bg-slate-700 border border-slate-700' }}">
                Last Month
            </a>

            <a href="{{ route('factory.deliveries.index', ['range' => 'all', 'tab' => $tab]) }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all {{ $range === 'all' ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-md shadow-emerald-500/20' : 'bg-slate-800/80 text-slate-300 hover:text-white hover:bg-slate-700 border border-slate-700' }}">
                All Time
            </a>
        </div>

        <!-- Custom Date Range Form -->
        <form action="{{ route('factory.deliveries.index') }}" method="GET" class="flex items-center gap-2">
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
                    class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold transition-all shadow-md">
                Filter
            </button>
            @if($range !== 'this_month')
            <a href="{{ route('factory.deliveries.index', ['tab' => $tab]) }}" title="Reset to This Month"
               class="p-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white text-xs transition-colors">
                <i class="fa-solid fa-rotate-left"></i>
            </a>
            @endif
        </form>
    </div>

    <!-- Section Tabs Toggle (Orders Awaiting Shipment vs Delivered Challans) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-700/70 pb-3">
        <div class="flex items-center gap-2.5">
            <!-- Tab 1: Orders Awaiting Shipment (Initial Active) -->
            <button type="button" id="tabBtnOrders" onclick="switchDeliveryTab('orders')"
                    class="px-4 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 {{ $tab === 'orders' ? 'bg-gradient-to-r from-amber-500 to-orange-600 text-white shadow-lg shadow-amber-500/25' : 'bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700' }}">
                <i class="fa-solid fa-box-open"></i>
                <span>Orders Awaiting Shipment (Click to Ship)</span>
                <span class="ml-1 px-2 py-0.5 rounded-full text-[10px] bg-black/30 font-mono">
                    {{ $pendingOrders->count() }}
                </span>
            </button>

            <!-- Tab 2: Delivered Challans / Shipment Logs -->
            <button type="button" id="tabBtnChallans" onclick="switchDeliveryTab('challans')"
                    class="px-4 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 {{ $tab === 'challans' ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/25' : 'bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700' }}">
                <i class="fa-solid fa-file-circle-check"></i>
                <span>Delivered Challans / Shipment Logs</span>
                <span class="ml-1 px-2 py-0.5 rounded-full text-[10px] bg-black/30 font-mono">
                    {{ $deliveries->count() }}
                </span>
            </button>
        </div>

        <!-- Live Search Box (Order #, Challan #, Customer, Phone) -->
        <div class="relative w-full sm:w-80">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" id="deliverySearchInput" placeholder="Search by Order #, Customer, Phone, Items..." 
                   class="w-full bg-[#070d1e] text-xs text-white placeholder-slate-500 rounded-xl pl-8 pr-7 py-2 border border-slate-700 focus:border-emerald-400 focus:outline-none transition-all">
            <button type="button" id="clearDeliverySearchBtn" onclick="clearDeliverySearch()" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white hidden text-xs">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>

    <!-- ==========================================
         SECTION 1: Orders Pending Delivery
         ========================================== -->
    <div id="sectionOrders" class="{{ $tab === 'orders' ? '' : 'hidden' }} space-y-4">
        <div class="glass-panel rounded-3xl p-5 border border-slate-700/60">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-box-open text-amber-400"></i>
                    Orders Awaiting Shipment (Click to Ship)
                </h3>
                <span id="ordersCounterDisplay" class="text-xs text-amber-300 font-mono">
                    Showing <span id="ordersVisibleCount">{{ min(10, $pendingOrders->count()) }}</span> of <span id="ordersTotalCount">{{ $pendingOrders->count() }}</span> Pending Orders
                    @if($range === 'this_month')
                        <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] bg-amber-500/20 text-amber-300 border border-amber-500/30 font-sans font-semibold">This Month</span>
                    @elseif($range === 'today')
                        <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] bg-amber-500/20 text-amber-300 border border-amber-500/30 font-sans font-semibold">Today</span>
                    @elseif($range === 'last_7_days')
                        <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] bg-amber-500/20 text-amber-300 border border-amber-500/30 font-sans font-semibold">Last 7 Days</span>
                    @elseif($range === 'last_month')
                        <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] bg-amber-500/20 text-amber-300 border border-amber-500/30 font-sans font-semibold">Last Month</span>
                    @elseif($range === 'custom')
                        <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] bg-amber-500/20 text-amber-300 border border-amber-500/30 font-sans font-semibold">{{ $startDate }} to {{ $endDate }}</span>
                    @else
                        <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] bg-slate-700 text-slate-300 font-sans font-semibold">All Time</span>
                    @endif
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 border-b border-slate-700/80 font-semibold">
                            <th class="pb-2.5">Order #</th>
                            <th class="pb-2.5">Customer Name</th>
                            <th class="pb-2.5">Order Date</th>
                            <th class="pb-2.5">Items & Remaining Due Qty</th>
                            <th class="pb-2.5 text-right">Order Amount</th>
                            <th class="pb-2.5 text-center">Status</th>
                            <th class="pb-2.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @forelse($pendingOrders as $ord)
                        @php
                            $searchItemString = '';
                            foreach($ord->items as $itm) {
                                $searchItemString .= ' ' . ($itm->product->name ?? '') . ' due ' . $itm->due_qty;
                            }
                        @endphp
                        <tr class="order-ship-row hover:bg-slate-800/40 transition-colors"
                            data-search="{{ strtolower($ord->order_number . ' ' . ($ord->customer->company_name ?? '') . ' ' . ($ord->customer->phone ?? '') . ' ' . $searchItemString . ' ' . date('d M, Y', strtotime($ord->order_date))) }}">
                            <td class="py-3 font-mono font-bold text-cyan-300">
                                {{ $ord->order_number }}
                            </td>
                            <td class="py-3">
                                <span class="font-bold text-white block">{{ $ord->customer->company_name }}</span>
                                <span class="text-[10px] text-slate-400">{{ $ord->customer->phone }}</span>
                            </td>
                            <td class="py-3 text-slate-300">
                                {{ date('d M, Y', strtotime($ord->order_date)) }}
                            </td>
                            <td class="py-3">
                                <div class="space-y-1">
                                    @foreach($ord->items as $itm)
                                    <div class="text-[11px] flex items-center justify-between gap-4 max-w-sm">
                                        <span class="text-slate-300 truncate">• {{ $itm->product->name }}</span>
                                        <span class="font-mono {{ $itm->due_qty > 0 ? 'text-amber-300 font-bold' : 'text-slate-500' }}">
                                            Due: {{ number_format($itm->due_qty) }} {{ $itm->product->unit->short_code ?? '' }}
                                        </span>
                                    </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="py-3 text-right font-mono font-bold text-white">
                                ৳ {{ number_format($ord->total_amount, 2) }}
                            </td>
                            <td class="py-3 text-center">
                                @if($ord->delivery_status === 'partial')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">Partial</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">Pending</span>
                                @endif
                            </td>
                            <td class="py-3 text-right">
                                <a href="{{ route('factory.deliveries.create', $ord->id) }}" 
                                   class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold text-xs shadow-md shadow-emerald-500/20 hover:from-emerald-400 hover:to-teal-500 transition-all inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-truck-fast"></i>
                                    <span>Dispatch Delivery</span>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fa-solid fa-calendar-xmark text-3xl text-slate-600"></i>
                                    <span class="text-slate-400 font-medium">No orders awaiting shipment found for this date range.</span>
                                    @if($range !== 'all')
                                    <a href="{{ route('factory.deliveries.index', ['range' => 'all', 'tab' => 'orders']) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 mt-2 rounded-xl bg-amber-600/20 text-amber-300 border border-amber-500/30 hover:bg-amber-600/30 text-xs font-semibold transition-all">
                                        <i class="fa-solid fa-arrows-rotate text-[11px]"></i>
                                        <span>View All Pending Shipments ({{ $totalPendingOrdersCount }})</span>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse

                        <!-- No Search Results Row -->
                        <tr id="noOrdersSearchResultRow" class="hidden">
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-box-open text-2xl text-slate-600"></i>
                                    <span class="text-xs">No pending orders match your search.</span>
                                    <button type="button" onclick="clearDeliverySearch()" class="text-amber-400 text-xs underline mt-1">Clear search</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Load More Pending Orders Button -->
            <div id="loadMoreOrdersContainer" class="mt-4 pt-3 border-t border-slate-800 text-center {{ $pendingOrders->count() > 10 ? '' : 'hidden' }}">
                <button type="button" onclick="loadMoreOrders()"
                        class="px-5 py-2 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-amber-300 hover:text-white border border-slate-700 hover:border-amber-500/40 text-xs font-semibold transition-all inline-flex items-center gap-2 shadow-lg">
                    <i class="fa-solid fa-angles-down text-amber-400"></i>
                    <span>Load More Orders (+10)</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ==========================================
         SECTION 2: Completed Delivery Shipments
         ========================================== -->
    <div id="sectionChallans" class="{{ $tab === 'challans' ? '' : 'hidden' }} space-y-4">
        <div class="glass-panel rounded-3xl p-5 border border-slate-700/60">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-file-circle-check text-emerald-400"></i>
                    Delivered Challans / Shipment Logs
                </h3>
                <span id="challansCounterDisplay" class="text-xs text-emerald-300 font-mono">
                    Showing <span id="challansVisibleCount">{{ min(10, $deliveries->count()) }}</span> of <span id="challansTotalCount">{{ $deliveries->count() }}</span> Challans
                    @if($range === 'this_month')
                        <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-sans font-semibold">This Month</span>
                    @elseif($range === 'today')
                        <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-sans font-semibold">Today</span>
                    @elseif($range === 'last_7_days')
                        <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-sans font-semibold">Last 7 Days</span>
                    @elseif($range === 'last_month')
                        <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-sans font-semibold">Last Month</span>
                    @elseif($range === 'custom')
                        <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-sans font-semibold">{{ $startDate }} to {{ $endDate }}</span>
                    @else
                        <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] bg-slate-700 text-slate-300 font-sans font-semibold">All Time</span>
                    @endif
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 border-b border-slate-700/80 font-semibold">
                            <th class="pb-2.5">Challan #</th>
                            <th class="pb-2.5">Order Ref</th>
                            <th class="pb-2.5">Customer Name</th>
                            <th class="pb-2.5">Date</th>
                            <th class="pb-2.5">Delivered Products & Quantities</th>
                            <th class="pb-2.5 text-right">Delivered Value</th>
                            <th class="pb-2.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @forelse($deliveries as $del)
                        @php
                            $searchChallanItemString = '';
                            foreach($del->items as $dItem) {
                                $searchChallanItemString .= ' ' . ($dItem->product->name ?? '') . ' ' . $dItem->qty;
                            }
                        @endphp
                        <tr class="challan-row hover:bg-slate-800/40 transition-colors"
                            data-search="{{ strtolower($del->delivery_number . ' ' . ($del->order->order_number ?? '') . ' ' . ($del->customer->company_name ?? '') . ' ' . ($del->customer->phone ?? '') . ' ' . $searchChallanItemString . ' ' . date('d M, Y', strtotime($del->delivery_date))) }}">
                            <td class="py-3 font-mono font-bold text-emerald-400">
                                {{ $del->delivery_number }}
                            </td>
                            <td class="py-3 font-mono text-cyan-300">
                                {{ $del->order->order_number ?? 'N/A' }}
                            </td>
                            <td class="py-3 font-bold text-white">
                                {{ $del->customer->company_name ?? 'N/A' }}
                            </td>
                            <td class="py-3 text-slate-300">
                                {{ date('d M, Y', strtotime($del->delivery_date)) }}
                            </td>
                            <td class="py-3">
                                <div class="space-y-1">
                                    @foreach($del->items as $dItem)
                                    <div class="text-[11px] text-slate-300">
                                        • {{ $dItem->product->name }}: <strong class="text-white font-mono">{{ number_format($dItem->qty) }} {{ $dItem->product->unit->short_code ?? '' }}</strong>
                                    </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="py-3 text-right font-mono font-bold text-white">
                                ৳ {{ number_format($del->total_delivered_amount, 2) }}
                            </td>
                            <td class="py-3 text-right">
                                <form action="{{ route('factory.deliveries.destroy', $del->id) }}" method="POST" 
                                      onsubmit="return confirm('Cancel and delete Challan \'{{ $del->delivery_number }}\'? This will return delivered quantities back into warehouse stock and restore order pending dues.');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="px-2 py-1 rounded-lg bg-rose-600/20 hover:bg-rose-600/40 text-rose-300 border border-rose-500/30 text-[11px] font-semibold transition-all inline-flex items-center gap-1"
                                            title="Cancel Challan & Revert Stock">
                                        <i class="fa-solid fa-trash-can text-[10px]"></i>
                                        <span>Delete</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fa-solid fa-calendar-xmark text-3xl text-slate-600"></i>
                                    <span class="text-slate-400 font-medium">No delivery challans recorded for this date range.</span>
                                    @if($range !== 'all')
                                    <a href="{{ route('factory.deliveries.index', ['range' => 'all', 'tab' => 'challans']) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 mt-2 rounded-xl bg-emerald-600/20 text-emerald-300 border border-emerald-500/30 hover:bg-emerald-600/30 text-xs font-semibold transition-all">
                                        <i class="fa-solid fa-arrows-rotate text-[11px]"></i>
                                        <span>View All Challans ({{ $totalDeliveriesCount }})</span>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse

                        <!-- No Search Results Row -->
                        <tr id="noChallansSearchResultRow" class="hidden">
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-file-circle-xmark text-2xl text-slate-600"></i>
                                    <span class="text-xs">No delivery challans match your search.</span>
                                    <button type="button" onclick="clearDeliverySearch()" class="text-emerald-400 text-xs underline mt-1">Clear search</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Load More Challans Button -->
            <div id="loadMoreChallansContainer" class="mt-4 pt-3 border-t border-slate-800 text-center {{ $deliveries->count() > 10 ? '' : 'hidden' }}">
                <button type="button" onclick="loadMoreChallans()"
                        class="px-5 py-2 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-emerald-300 hover:text-white border border-slate-700 hover:border-emerald-500/40 text-xs font-semibold transition-all inline-flex items-center gap-2 shadow-lg">
                    <i class="fa-solid fa-angles-down text-emerald-400"></i>
                    <span>Load More Challans (+10)</span>
                </button>
            </div>
        </div>
    </div>

</div>

<script>
    // Global State
    let activeTab = '{{ $tab }}';
    const batchSize = 10;

    // Orders state
    let allOrderRows = [];
    let ordersVisibleLimit = 10;

    // Challans state
    let allChallanRows = [];
    let challansVisibleLimit = 10;

    document.addEventListener('DOMContentLoaded', () => {
        allOrderRows = Array.from(document.querySelectorAll('.order-ship-row'));
        allChallanRows = Array.from(document.querySelectorAll('.challan-row'));

        applyOrdersVisibility();
        applyChallansVisibility();

        // Search Input Handler
        const searchInput = document.getElementById('deliverySearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.trim().toLowerCase();
                const clearBtn = document.getElementById('clearDeliverySearchBtn');
                if (clearBtn) {
                    clearBtn.classList.toggle('hidden', query.length === 0);
                }

                if (activeTab === 'orders') {
                    filterOrdersByQuery(query);
                } else {
                    filterChallansByQuery(query);
                }
            });
        }
    });

    // ==========================================
    // TAB SWITCHING (Orders Awaiting vs Challans)
    // ==========================================
    function switchDeliveryTab(tab) {
        activeTab = tab;

        const btnOrders = document.getElementById('tabBtnOrders');
        const btnChallans = document.getElementById('tabBtnChallans');
        const secOrders = document.getElementById('sectionOrders');
        const secChallans = document.getElementById('sectionChallans');
        const searchInput = document.getElementById('deliverySearchInput');
        const csvBtn = document.getElementById('csvDownloadBtn');
        const dateFilterTab = document.getElementById('dateFilterTab');

        if (dateFilterTab) dateFilterTab.value = tab;

        // Update CSV link query
        if (csvBtn) {
            const currentUrl = new URL(csvBtn.href);
            currentUrl.searchParams.set('tab', tab);
            csvBtn.href = currentUrl.toString();
        }

        if (tab === 'orders') {
            btnOrders.className = "px-4 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 bg-gradient-to-r from-amber-500 to-orange-600 text-white shadow-lg shadow-amber-500/25";
            btnChallans.className = "px-4 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700";
            
            secOrders.classList.remove('hidden');
            secChallans.classList.add('hidden');

            if (searchInput) {
                searchInput.placeholder = "Search by Order #, Customer, Phone, Items...";
                searchInput.focus();
            }
        } else {
            btnChallans.className = "px-4 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/25";
            btnOrders.className = "px-4 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700";
            
            secChallans.classList.remove('hidden');
            secOrders.classList.add('hidden');

            if (searchInput) {
                searchInput.placeholder = "Search by Challan #, Order Ref, Customer...";
                searchInput.focus();
            }
        }

        // Re-run search for newly active tab
        if (searchInput && searchInput.value.trim().length > 0) {
            const query = searchInput.value.trim().toLowerCase();
            if (activeTab === 'orders') filterOrdersByQuery(query);
            else filterChallansByQuery(query);
        } else {
            if (activeTab === 'orders') applyOrdersVisibility();
            else applyChallansVisibility();
        }
    }

    // ==========================================
    // ORDERS: Visibility, Search & Load More
    // ==========================================
    function applyOrdersVisibility() {
        allOrderRows.forEach((row, idx) => {
            row.style.display = idx < ordersVisibleLimit ? '' : 'none';
        });

        const currentVisible = Math.min(ordersVisibleLimit, allOrderRows.length);
        const countEl = document.getElementById('ordersVisibleCount');
        if (countEl) countEl.innerText = currentVisible;

        const loadMore = document.getElementById('loadMoreOrdersContainer');
        if (loadMore) {
            loadMore.classList.toggle('hidden', ordersVisibleLimit >= allOrderRows.length);
        }
    }

    function loadMoreOrders() {
        ordersVisibleLimit += batchSize;
        applyOrdersVisibility();
    }

    function filterOrdersByQuery(query) {
        if (!query) {
            document.getElementById('noOrdersSearchResultRow').classList.add('hidden');
            applyOrdersVisibility();
            return;
        }

        let matched = 0;
        allOrderRows.forEach(row => {
            const text = (row.getAttribute('data-search') || '').toLowerCase();
            if (text.includes(query)) {
                row.style.display = '';
                matched++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('noOrdersSearchResultRow').classList.toggle('hidden', matched > 0);
        document.getElementById('loadMoreOrdersContainer').classList.add('hidden');
        document.getElementById('ordersVisibleCount').innerText = matched;
    }

    // ==========================================
    // CHALLANS: Visibility, Search & Load More
    // ==========================================
    function applyChallansVisibility() {
        allChallanRows.forEach((row, idx) => {
            row.style.display = idx < challansVisibleLimit ? '' : 'none';
        });

        const currentVisible = Math.min(challansVisibleLimit, allChallanRows.length);
        const countEl = document.getElementById('challansVisibleCount');
        if (countEl) countEl.innerText = currentVisible;

        const loadMore = document.getElementById('loadMoreChallansContainer');
        if (loadMore) {
            loadMore.classList.toggle('hidden', challansVisibleLimit >= allChallanRows.length);
        }
    }

    function loadMoreChallans() {
        challansVisibleLimit += batchSize;
        applyChallansVisibility();
    }

    function filterChallansByQuery(query) {
        if (!query) {
            document.getElementById('noChallansSearchResultRow').classList.add('hidden');
            applyChallansVisibility();
            return;
        }

        let matched = 0;
        allChallanRows.forEach(row => {
            const text = (row.getAttribute('data-search') || '').toLowerCase();
            if (text.includes(query)) {
                row.style.display = '';
                matched++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('noChallansSearchResultRow').classList.toggle('hidden', matched > 0);
        document.getElementById('loadMoreChallansContainer').classList.add('hidden');
        document.getElementById('challansVisibleCount').innerText = matched;
    }

    // Clear Search Input
    function clearDeliverySearch() {
        const searchInput = document.getElementById('deliverySearchInput');
        if (searchInput) {
            searchInput.value = '';
            document.getElementById('clearDeliverySearchBtn').classList.add('hidden');
            document.getElementById('noOrdersSearchResultRow').classList.add('hidden');
            document.getElementById('noChallansSearchResultRow').classList.add('hidden');
            
            if (activeTab === 'orders') applyOrdersVisibility();
            else applyChallansVisibility();

            searchInput.focus();
        }
    }
</script>
@endsection
