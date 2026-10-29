@extends('layouts.app')

@section('title', 'Warehouse Stock Management | Factory ERP')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-warehouse text-amber-400"></i>
                Warehouse Inventory & Stock
            </h2>
            <p class="text-xs text-slate-400">Real-time inventory: automatically increased by Production, reduced by Deliveries, plus manual adjustments</p>
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

        <!-- Right: Real-Time Product Stock Balance Table -->
        <div class="lg:col-span-8 space-y-6">
            
            <div class="glass-panel rounded-3xl p-5 border border-slate-700/60">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-bold text-white">Current Warehouse Stock Balance</h3>
                    <span class="text-xs text-amber-300 font-mono">{{ $products->count() }} Products</span>
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
                            @foreach($products as $prod)
                            @php
                                $stk = $prod->stock;
                                $curr = $stk ? $stk->current_stock : 0;
                            @endphp
                            <tr class="hover:bg-slate-800/40 transition-colors">
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
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Movement Logs -->
            <div class="glass-panel rounded-3xl p-5 border border-slate-700/60">
                <h3 class="text-sm font-bold text-white mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-cyan-400"></i>
                    Recent Stock Movement History
                </h3>

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
                            <tr class="hover:bg-slate-800/40 transition-colors">
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
                                <td colspan="6" class="py-6 text-center text-slate-500">No stock movements logged yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
