@extends('layouts.app')

@section('title', 'Delivery Management | Factory ERP')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-truck-fast text-emerald-400"></i>
                Shipments & Delivery Center
            </h2>
            <p class="text-xs text-slate-400">Track pending client orders ready for shipment, process challans & update factory stocks</p>
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

    <!-- SECTION 1: Orders Pending Delivery -->
    <div class="glass-panel rounded-3xl p-5 border border-slate-700/60">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-box-open text-amber-400"></i>
                Orders Awaiting Shipment (Click to Ship)
            </h3>
            <span class="text-xs text-amber-300 font-mono">{{ $pendingOrders->count() }} Orders Pending</span>
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
                    <tr class="hover:bg-slate-800/40 transition-colors">
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
                        <td colspan="7" class="py-6 text-center text-slate-500">All current orders have been delivered! No shipments pending.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- SECTION 2: Completed Delivery Shipments -->
    <div class="glass-panel rounded-3xl p-5 border border-slate-700/60">
        <h3 class="text-sm font-bold text-white mb-3 flex items-center justify-between">
            <span>Delivered Challans / Shipment Logs</span>
            <span class="text-xs text-cyan-400 font-mono">{{ $deliveries->count() }} Challans</span>
        </h3>

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
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($deliveries as $del)
                    <tr class="hover:bg-slate-800/40 transition-colors">
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
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-slate-500">No delivery challans recorded yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
