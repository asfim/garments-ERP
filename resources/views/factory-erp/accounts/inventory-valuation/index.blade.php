@extends('layouts.app')

@section('title', 'FIFO Inventory Valuation | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Header -->
    <div id="reportPageHeader" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked text-emerald-400"></i>
                FIFO Inventory Valuation
            </h2>
            <p class="text-xs text-slate-400">Cost valuation of finished goods stock based on unexhausted FIFO batches and unit production snapshots</p>
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

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="glass-panel p-4 rounded-2xl border border-emerald-500/30 bg-emerald-950/20">
            <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-400 block">Total Inventory Valuation</span>
            <h3 class="text-2xl font-black text-white font-mono mt-1">৳ {{ number_format($valuationData['grand_total_valuation'], 2) }}</h3>
            <span class="text-[10px] text-slate-400 font-mono mt-1 block">Active Finished Goods Asset (#1040)</span>
        </div>

        <div class="glass-panel p-4 rounded-2xl border border-cyan-500/30 bg-cyan-950/20">
            <span class="text-[11px] font-bold uppercase tracking-wider text-cyan-400 block">Total In-Stock Quantity</span>
            <h3 class="text-2xl font-black text-white font-mono mt-1">{{ number_format($valuationData['grand_total_qty'], 2) }} Units</h3>
            <span class="text-[10px] text-slate-400 font-mono mt-1 block">Across all active batches</span>
        </div>

        <div class="glass-panel p-4 rounded-2xl border border-indigo-500/30 bg-indigo-950/20">
            <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-400 block">Weighted Avg Cost / Unit</span>
            @php 
                $avgCost = $valuationData['grand_total_qty'] > 0 ? $valuationData['grand_total_valuation'] / $valuationData['grand_total_qty'] : 0;
            @endphp
            <h3 class="text-2xl font-black text-indigo-300 font-mono mt-1">৳ {{ number_format($avgCost, 2) }}</h3>
            <span class="text-[10px] text-slate-400 font-mono mt-1 block">Blended Production Cost</span>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="glass-panel p-3.5 rounded-2xl border border-slate-800/80 flex items-center justify-between">
        <form method="GET" action="{{ route('factory.accounts.inventory-valuation.index') }}" class="flex items-center gap-3 text-xs">
            <span class="text-slate-400 font-medium">Filter Product:</span>
            <select name="product_id" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-slate-200 text-xs">
                <option value="">All Products</option>
                @foreach($products as $p)
                <option value="{{ $p->id }}" {{ $productId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold transition-all">
                Filter
            </button>
            @if($productId)
            <a href="{{ route('factory.accounts.inventory-valuation.index') }}" class="text-xs text-slate-400 hover:text-white underline">Show All</a>
            @endif
        </form>

        <span class="text-xs text-slate-400 font-mono">
            Showing <strong class="text-white">{{ count($valuationData['products']) }}</strong> Products
        </span>
    </div>

    <!-- Valuation Breakdown Accordion / Cards -->
    <div class="space-y-4">
        @forelse($valuationData['products'] as $prod)
        <div class="glass-panel p-5 rounded-2xl border border-slate-800/80 space-y-3">
            <!-- Product Header Row -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800/80 pb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-box"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-white">{{ $prod['product_name'] }}</h4>
                        <div class="text-[11px] text-slate-400 font-mono">
                            Stock on Hand: <strong class="text-slate-200">{{ number_format($prod['stock_on_hand'], 2) }} {{ $prod['unit_name'] }}</strong> | 
                            Active FIFO Layers: <strong class="text-emerald-400">{{ count($prod['layers']) }} batches</strong>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4 text-xs font-mono">
                    <div class="text-right">
                        <span class="text-slate-400 uppercase text-[9px] block">FIFO Valuation</span>
                        <strong class="text-emerald-400 text-base">৳ {{ number_format($prod['fifo_valuation'], 2) }}</strong>
                    </div>

                    <div class="text-right">
                        <span class="text-slate-400 uppercase text-[9px] block">Avg Unit Cost</span>
                        <strong class="text-indigo-400 text-base">৳ {{ number_format($prod['weighted_avg_cost'], 2) }}</strong>
                    </div>

                    <div>
                        @if($prod['reconciliation_status'] === 'balanced')
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            <i class="fa-solid fa-check"></i> 100% In Sync
                        </span>
                        @else
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-rose-500/20 text-rose-300 border border-rose-500/30">
                            Mismatch
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- FIFO Layers Breakdown Table -->
            @if(count($prod['layers']) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950/60 text-slate-400 uppercase text-[9px] font-bold border-b border-slate-800">
                        <tr>
                            <th class="py-2 px-3">Batch Number</th>
                            <th class="py-2 px-3">Received Date</th>
                            <th class="py-2 px-3 text-center">Batch Age</th>
                            <th class="py-2 px-3 text-right">Initial Qty</th>
                            <th class="py-2 px-3 text-right">Remaining Qty</th>
                            <th class="py-2 px-3 text-right">Unit Cost Snapshot (৳)</th>
                            <th class="py-2 px-3 text-right">Batch Valuation (৳)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/40 font-mono text-[11px]">
                        @foreach($prod['layers'] as $layer)
                        <tr class="hover:bg-slate-800/30">
                            <td class="py-2 px-3 font-bold text-amber-400">
                                {{ $layer['batch_number'] }}
                                @if($layer['notes'])
                                <span class="block text-[9px] font-sans text-slate-500 font-normal">{{ $layer['notes'] }}</span>
                                @endif
                            </td>
                            <td class="py-2 px-3 text-slate-300">
                                {{ date('d M, Y', strtotime($layer['received_date'])) }}
                            </td>
                            <td class="py-2 px-3 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] {{ $layer['age_days'] > 60 ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : ($layer['age_days'] > 30 ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-slate-800 text-slate-300') }}">
                                    {{ $layer['age_days'] }} days
                                </span>
                            </td>
                            <td class="py-2 px-3 text-right text-slate-400">
                                {{ number_format($layer['initial_qty'], 2) }}
                            </td>
                            <td class="py-2 px-3 text-right font-black text-cyan-400">
                                {{ number_format($layer['remaining_qty'], 2) }}
                            </td>
                            <td class="py-2 px-3 text-right text-slate-200">
                                ৳ {{ number_format($layer['unit_cost'], 2) }}
                            </td>
                            <td class="py-2 px-3 text-right font-black text-emerald-400">
                                ৳ {{ number_format($layer['layer_valuation'], 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="py-4 text-center text-xs text-slate-500">
                No active FIFO cost layers for this product. Stock is either 0 or awaiting production batches.
            </div>
            @endif
        </div>
        @empty
        <div class="glass-panel p-8 text-center text-slate-500 text-xs">
            No products found.
        </div>
        @endforelse
    </div>
    </div> <!-- /#reportTabContent -->

</div>

@include('factory-erp.accounts.reports.ajax-script')
@endsection

