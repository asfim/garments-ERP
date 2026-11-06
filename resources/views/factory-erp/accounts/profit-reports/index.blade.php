@extends('layouts.app')

@section('title', 'Profitability Reports | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Header -->
    <div id="reportPageHeader" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-chart-column text-cyan-400"></i>
                Profitability Reports
            </h2>
            <p class="text-xs text-slate-400">Audited Product-wise and Delivery-wise Gross Margins powered by exact FIFO cost layer allocations</p>
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

    <!-- REPORT TABS -->
    <div class="glass-panel p-2 rounded-2xl border border-slate-800/80 flex flex-wrap gap-2">
        <a href="{{ route('factory.accounts.profitability.index', ['tab' => 'product', 'from_date' => $fromDate, 'to_date' => $toDate]) }}" 
           class="px-4 py-2.5 rounded-xl font-bold text-xs transition-all flex items-center gap-2 {{ $tab === 'product' ? 'bg-gradient-to-r from-cyan-600 to-blue-600 text-white shadow-lg shadow-cyan-500/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
            <i class="fa-solid fa-box text-sm"></i>
            <span>1. Product-wise Gross Profitability</span>
        </a>

        <a href="{{ route('factory.accounts.profitability.index', ['tab' => 'delivery', 'from_date' => $fromDate, 'to_date' => $toDate]) }}" 
           class="px-4 py-2.5 rounded-xl font-bold text-xs transition-all flex items-center gap-2 {{ $tab === 'delivery' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-lg shadow-emerald-500/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
            <i class="fa-solid fa-truck-ramp-box text-sm"></i>
            <span>2. Delivery Invoice-wise Profitability</span>
        </a>
    </div>

    <!-- ========================================== -->
    <!-- TAB 1: PRODUCT-WISE PROFITABILITY -->
    <!-- ========================================== -->
    @if($tab === 'product' && $productData)
    <div class="space-y-6">
        <!-- Filter Bar -->
        <div class="glass-panel p-3.5 rounded-2xl border border-slate-800/80 flex flex-wrap items-center justify-between gap-4">
            <form method="GET" action="{{ route('factory.accounts.profitability.index') }}" class="flex flex-wrap items-center gap-3 text-xs">
                <input type="hidden" name="tab" value="product">
                <span class="text-slate-400 font-medium">Period:</span>
                <input type="date" name="from_date" value="{{ $fromDate }}" 
                       class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-slate-200 text-xs">
                <span class="text-slate-500">to</span>
                <input type="date" name="to_date" value="{{ $toDate }}" 
                       class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-slate-200 text-xs">

                <select name="product_id" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-slate-200 text-xs">
                    <option value="">All Products</option>
                    @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ $productId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>

                <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold transition-all">
                    Generate Report
                </button>
            </form>

            <div class="text-xs font-mono text-slate-400">
                Period: <strong class="text-cyan-400">{{ date('d M Y', strtotime($fromDate)) }} - {{ date('d M Y', strtotime($toDate)) }}</strong>
            </div>
        </div>

        <!-- KPI Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="glass-panel p-4 rounded-2xl border border-blue-500/30 bg-blue-950/20">
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-400 block">Total Sales Revenue</span>
                <h3 class="text-2xl font-black text-white font-mono mt-1">৳ {{ number_format($productData['total_sales'], 2) }}</h3>
                <span class="text-[10px] text-slate-400 font-mono mt-1 block">{{ number_format($productData['total_delivered_qty'], 2) }} Units Delivered</span>
            </div>

            <div class="glass-panel p-4 rounded-2xl border border-amber-500/30 bg-amber-950/20">
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-400 block">Total FIFO COGS</span>
                <h3 class="text-2xl font-black text-white font-mono mt-1">৳ {{ number_format($productData['total_cogs'], 2) }}</h3>
                <span class="text-[10px] text-slate-400 font-mono mt-1 block">Account #5100 Direct Cost</span>
            </div>

            <div class="glass-panel p-4 rounded-2xl border border-emerald-500/30 bg-emerald-950/20">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-400 block">Total Gross Profit</span>
                <h3 class="text-2xl font-black text-emerald-400 font-mono mt-1">৳ {{ number_format($productData['total_gross_profit'], 2) }}</h3>
                <span class="text-[10px] text-slate-400 font-mono mt-1 block">Revenue - FIFO COGS</span>
            </div>

            <div class="glass-panel p-4 rounded-2xl border border-indigo-500/30 bg-indigo-950/20">
                <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-400 block">Overall Gross Margin</span>
                <h3 class="text-2xl font-black text-indigo-300 font-mono mt-1">{{ number_format($productData['overall_margin'], 2) }}%</h3>
                <span class="text-[10px] text-slate-400 font-mono mt-1 block">Average Gross Margin</span>
            </div>
        </div>

        <!-- Product Profitability Table -->
        <div class="glass-panel rounded-2xl border border-slate-800/80 overflow-hidden">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/60 text-slate-400 uppercase text-[10px] font-bold border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Product Name</th>
                        <th class="py-3 px-4 text-right">Delivered Qty</th>
                        <th class="py-3 px-4 text-right">Sales Revenue (৳)</th>
                        <th class="py-3 px-4 text-right">FIFO COGS (৳)</th>
                        <th class="py-3 px-4 text-right">Gross Profit (৳)</th>
                        <th class="py-3 px-4 text-right">Margin (%)</th>
                        <th class="py-3 px-4 text-right">Remaining Stock</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-mono">
                    @forelse($productData['rows'] as $row)
                    <tr class="product-profit-row hover:bg-slate-800/40 {{ $loop->index >= 20 ? 'hidden' : '' }}" data-index="{{ $loop->index }}">
                        <td class="py-3 px-4 font-sans font-bold text-white">
                            {{ $row['product_name'] }}
                            <span class="block text-[10px] text-slate-400 font-mono font-normal">Catalog Cost: ৳ {{ number_format($row['production_price'], 2) }} / {{ $row['unit_name'] }}</span>
                        </td>
                        <td class="py-3 px-4 text-right text-slate-200">
                            {{ number_format($row['delivered_qty'], 2) }}
                        </td>
                        <td class="py-3 px-4 text-right font-black text-white">
                            ৳ {{ number_format($row['sales_amount'], 2) }}
                        </td>
                        <td class="py-3 px-4 text-right font-black text-amber-400">
                            ৳ {{ number_format($row['fifo_cogs'], 2) }}
                        </td>
                        <td class="py-3 px-4 text-right font-black {{ $row['gross_profit'] >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                            ৳ {{ number_format($row['gross_profit'], 2) }}
                        </td>
                        <td class="py-3 px-4 text-right">
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $row['gross_margin'] >= 25 ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : ($row['gross_margin'] > 0 ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30') }}">
                                {{ number_format($row['gross_margin'], 1) }}%
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right text-slate-300 text-[11px]">
                            {{ number_format($row['current_stock_qty'], 2) }} (৳ {{ number_format($row['current_stock_valuation'], 2) }})
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-500 font-sans">
                            No product delivery transactions recorded in this period.
                        </td>
                    </tr>
                    @endforelse

                    <!-- Totals Row -->
                    <tr class="bg-slate-950 border-t-2 border-slate-700 font-black text-sm">
                        <td class="py-3 px-4 uppercase tracking-wider font-sans text-slate-400">Total:</td>
                        <td class="py-3 px-4 text-right text-slate-200">{{ number_format($productData['total_delivered_qty'], 2) }}</td>
                        <td class="py-3 px-4 text-right text-white">৳ {{ number_format($productData['total_sales'], 2) }}</td>
                        <td class="py-3 px-4 text-right text-amber-400">৳ {{ number_format($productData['total_cogs'], 2) }}</td>
                        <td class="py-3 px-4 text-right text-emerald-400">৳ {{ number_format($productData['total_gross_profit'], 2) }}</td>
                        <td class="py-3 px-4 text-right text-indigo-300">{{ number_format($productData['overall_margin'], 1) }}%</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>

            <!-- Product Profit Load More -->
            @if(!empty($productData['rows']) && count($productData['rows']) > 0)
            <div id="productProfitLoadMoreContainer" class="p-4 border-t border-slate-800 bg-slate-950/40 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-xs text-slate-400 font-mono">
                    Showing <span id="productProfitShowingCount" class="text-cyan-400 font-bold">{{ min(20, count($productData['rows'])) }}</span> of <span id="productProfitTotalCount" class="text-white font-bold">{{ count($productData['rows']) }}</span> products
                </div>

                @if(count($productData['rows']) > 20)
                <button type="button" 
                        id="productProfitLoadMoreBtn"
                        onclick="loadMoreProductProfitRows()"
                        class="px-5 py-2 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white text-xs font-bold shadow-lg shadow-cyan-950/50 flex items-center gap-2 transition-all cursor-pointer">
                    <i class="fa-solid fa-circle-down"></i>
                    <span>Load More Products</span>
                </button>
                @else
                <div class="text-xs text-slate-500 font-semibold italic flex items-center gap-1.5">
                    <i class="fa-solid fa-check text-emerald-400"></i> All products loaded
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- ========================================== -->
    <!-- TAB 2: DELIVERY INVOICE-WISE PROFITABILITY -->
    <!-- ========================================== -->
    @if($tab === 'delivery' && $deliveryData)
    <div class="space-y-6">
        <!-- Filter Bar -->
        <div class="glass-panel p-3.5 rounded-2xl border border-slate-800/80 flex flex-wrap items-center justify-between gap-4">
            <form method="GET" action="{{ route('factory.accounts.profitability.index') }}" class="flex flex-wrap items-center gap-3 text-xs">
                <input type="hidden" name="tab" value="delivery">
                <span class="text-slate-400 font-medium">Period:</span>
                <input type="date" name="from_date" value="{{ $fromDate }}" 
                       class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-slate-200 text-xs">
                <span class="text-slate-500">to</span>
                <input type="date" name="to_date" value="{{ $toDate }}" 
                       class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-slate-200 text-xs">

                <select name="customer_id" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-slate-200 text-xs">
                    <option value="">All Customers</option>
                    @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ $customerId == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                    @endforeach
                </select>

                <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold transition-all">
                    Filter Deliveries
                </button>
            </form>

            <div class="text-xs font-mono text-slate-400">
                Deliveries: <strong class="text-white">{{ count($deliveryData['rows']) }}</strong> | Gross Margin: <strong class="text-emerald-400">{{ number_format($deliveryData['overall_margin'], 1) }}%</strong>
            </div>
        </div>

        <!-- Deliveries Table with Item Accordion -->
        <div class="space-y-4">
            @forelse($deliveryData['rows'] as $del)
            <div class="delivery-card-item glass-panel p-5 rounded-2xl border border-slate-800/80 space-y-3 {{ $loop->index >= 20 ? 'hidden' : '' }}" x-data="{ open: false }">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800/80 pb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-truck"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-sm font-black text-cyan-400 font-mono">{{ $del['delivery_number'] }}</h4>
                                <span class="text-slate-500 text-xs font-mono">({{ date('d M, Y', strtotime($del['delivery_date'])) }})</span>
                            </div>
                            <div class="text-[11px] text-slate-300">
                                Customer: <strong class="text-white">{{ $del['customer_name'] }}</strong> | Order: <span class="font-mono text-amber-400">{{ $del['order_number'] }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-5 text-xs font-mono">
                        <div class="text-right">
                            <span class="text-slate-400 uppercase text-[9px] block">Revenue</span>
                            <strong class="text-white text-sm">৳ {{ number_format($del['total_revenue'], 2) }}</strong>
                        </div>
                        <div class="text-right">
                            <span class="text-slate-400 uppercase text-[9px] block">FIFO COGS</span>
                            <strong class="text-amber-400 text-sm">৳ {{ number_format($del['total_cogs'], 2) }}</strong>
                        </div>
                        <div class="text-right">
                            <span class="text-slate-400 uppercase text-[9px] block">Gross Profit</span>
                            <strong class="text-emerald-400 text-sm">৳ {{ number_format($del['gross_profit'], 2) }}</strong>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $del['gross_margin'] >= 25 ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30' }}">
                                {{ number_format($del['gross_margin'], 1) }}%
                            </span>
                        </div>
                        <button @click="open = !open" class="p-2 rounded-lg bg-slate-800 text-slate-400 hover:text-white">
                            <i class="fa-solid fa-chevron-down transition-transform" :class="{ 'rotate-180': open }"></i>
                        </button>
                    </div>
                </div>

                <!-- Delivered Item Details -->
                <div x-show="open" x-cloak class="pt-2">
                    <table class="w-full text-left text-xs text-slate-300">
                        <thead class="bg-slate-950/60 text-slate-400 uppercase text-[9px] font-bold border-b border-slate-800">
                            <tr>
                                <th class="py-2 px-3">Delivered Item</th>
                                <th class="py-2 px-3 text-right">Delivered Qty</th>
                                <th class="py-2 px-3 text-right">Selling Price (৳)</th>
                                <th class="py-2 px-3 text-right">Item Revenue (৳)</th>
                                <th class="py-2 px-3 text-right">FIFO Unit Cost (৳)</th>
                                <th class="py-2 px-3 text-right">FIFO Total COGS (৳)</th>
                                <th class="py-2 px-3 text-right">Item Margin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/40 font-mono text-[11px]">
                            @foreach($del['items'] as $item)
                            <tr>
                                <td class="py-2 px-3 font-sans font-bold text-white">{{ $item['product_name'] }}</td>
                                <td class="py-2 px-3 text-right text-slate-200">{{ number_format($item['qty'], 2) }} {{ $item['unit_name'] }}</td>
                                <td class="py-2 px-3 text-right text-slate-300">৳ {{ number_format($item['sell_price'], 2) }}</td>
                                <td class="py-2 px-3 text-right text-white font-bold">৳ {{ number_format($item['total_price'], 2) }}</td>
                                <td class="py-2 px-3 text-right text-amber-400">৳ {{ number_format($item['fifo_unit_cost'], 2) }}</td>
                                <td class="py-2 px-3 text-right text-amber-400 font-bold">৳ {{ number_format($item['fifo_total_cost'], 2) }}</td>
                                <td class="py-2 px-3 text-right">
                                    <span class="text-emerald-400 font-bold">{{ number_format($item['gross_margin'], 1) }}%</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @empty
            <div class="glass-panel p-8 text-center text-slate-500 text-xs">
                No delivery records found matching the filter criteria.
            </div>
            @endforelse

            <!-- Delivery Profit Load More -->
            @if(!empty($deliveryData['rows']) && count($deliveryData['rows']) > 0)
            <div id="deliveryProfitLoadMoreContainer" class="p-4 rounded-2xl border border-slate-800 bg-slate-950/40 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-xs text-slate-400 font-mono">
                    Showing <span id="deliveryProfitShowingCount" class="text-cyan-400 font-bold">{{ min(20, count($deliveryData['rows'])) }}</span> of <span id="deliveryProfitTotalCount" class="text-white font-bold">{{ count($deliveryData['rows']) }}</span> deliveries
                </div>

                @if(count($deliveryData['rows']) > 20)
                <button type="button" 
                        id="deliveryProfitLoadMoreBtn"
                        onclick="loadMoreDeliveryProfitCards()"
                        class="px-5 py-2 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white text-xs font-bold shadow-lg shadow-cyan-950/50 flex items-center gap-2 transition-all cursor-pointer">
                    <i class="fa-solid fa-circle-down"></i>
                    <span>Load More Deliveries</span>
                </button>
                @else
                <div class="text-xs text-slate-500 font-semibold italic flex items-center gap-1.5">
                    <i class="fa-solid fa-check text-emerald-400"></i> All deliveries loaded
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
    @endif
    </div> <!-- /#reportTabContent -->

</div>

@include('factory-erp.accounts.reports.ajax-script')

<script>
// Product Profit Progressive Reveal
let prodPageSize = 20;
let currentProdVisible = Math.min(20, {{ !empty($productData['rows']) ? count($productData['rows']) : 0 }});
const totalProdRows = {{ !empty($productData['rows']) ? count($productData['rows']) : 0 }};

function loadMoreProductProfitRows() {
    const hiddenRows = document.querySelectorAll('.product-profit-row.hidden');
    let toShow = Math.min(prodPageSize, hiddenRows.length);
    for (let i = 0; i < toShow; i++) {
        hiddenRows[i].classList.remove('hidden');
    }
    currentProdVisible += toShow;
    const countEl = document.getElementById('productProfitShowingCount');
    if (countEl) countEl.textContent = currentProdVisible;

    if (currentProdVisible >= totalProdRows || hiddenRows.length <= toShow) {
        const btn = document.getElementById('productProfitLoadMoreBtn');
        if (btn) btn.remove();
        const container = document.getElementById('productProfitLoadMoreContainer');
        if (container) {
            const doneDiv = document.createElement('div');
            doneDiv.className = 'text-xs text-slate-500 font-semibold italic flex items-center gap-1.5';
            doneDiv.innerHTML = '<i class="fa-solid fa-check text-emerald-400"></i> All products loaded';
            container.appendChild(doneDiv);
        }
    }
}

// Delivery Profit Progressive Reveal
let delPageSize = 20;
let currentDelVisible = Math.min(20, {{ !empty($deliveryData['rows']) ? count($deliveryData['rows']) : 0 }});
const totalDelRows = {{ !empty($deliveryData['rows']) ? count($deliveryData['rows']) : 0 }};

function loadMoreDeliveryProfitCards() {
    const hiddenCards = document.querySelectorAll('.delivery-card-item.hidden');
    let toShow = Math.min(delPageSize, hiddenCards.length);
    for (let i = 0; i < toShow; i++) {
        hiddenCards[i].classList.remove('hidden');
    }
    currentDelVisible += toShow;
    const countEl = document.getElementById('deliveryProfitShowingCount');
    if (countEl) countEl.textContent = currentDelVisible;

    if (currentDelVisible >= totalDelRows || hiddenCards.length <= toShow) {
        const btn = document.getElementById('deliveryProfitLoadMoreBtn');
        if (btn) btn.remove();
        const container = document.getElementById('deliveryProfitLoadMoreContainer');
        if (container) {
            const doneDiv = document.createElement('div');
            doneDiv.className = 'text-xs text-slate-500 font-semibold italic flex items-center gap-1.5';
            doneDiv.innerHTML = '<i class="fa-solid fa-check text-emerald-400"></i> All deliveries loaded';
            container.appendChild(doneDiv);
        }
    }
}
</script>
@endsection

