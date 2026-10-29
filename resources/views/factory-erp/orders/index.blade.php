@extends('layouts.app')

@section('title', 'Orders Management | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Controls -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-cart-shopping text-cyan-400"></i>
                Orders & Sales Invoices
            </h2>
            <p class="text-xs text-slate-400">Manage client orders, invoice amounts, paid/due tracking, and delivery progress</p>
        </div>

        <a href="{{ route('factory.orders.create') }}" 
           class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-bold text-xs shadow-lg shadow-cyan-500/25 hover:from-cyan-400 hover:to-blue-500 transition-all flex items-center gap-2">
            <i class="fa-solid fa-cart-plus text-sm"></i>
            <span>+ Create New Order</span>
        </a>
    </div>

    @if(session('success'))
    <div class="p-3 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs flex items-center gap-2">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <div class="glass-panel rounded-3xl p-5 border border-slate-700/60">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-white">All Recorded Orders</h3>
            <span class="text-xs text-cyan-400 font-mono">{{ $orders->count() }} Orders</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-700/80 font-semibold">
                        <th class="pb-2.5">Order / Inv #</th>
                        <th class="pb-2.5">Customer Name</th>
                        <th class="pb-2.5">Date</th>
                        <th class="pb-2.5">Ordered Items</th>
                        <th class="pb-2.5 text-right">Total Price</th>
                        <th class="pb-2.5 text-right">Paid</th>
                        <th class="pb-2.5 text-right">Due</th>
                        <th class="pb-2.5 text-center">Delivery Status</th>
                        <th class="pb-2.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($orders as $ord)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="py-3 font-mono font-bold text-cyan-300">
                            {{ $ord->order_number }}
                        </td>
                        <td class="py-3">
                            <a href="{{ route('factory.customers.details', $ord->customer_id) }}" class="font-bold text-white hover:text-cyan-400 block transition-colors">
                                {{ $ord->customer->company_name ?? 'N/A' }}
                            </a>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $ord->customer->phone ?? '' }}</span>
                        </td>
                        <td class="py-3 text-slate-300">
                            {{ date('d M, Y', strtotime($ord->order_date)) }}
                        </td>
                        <td class="py-3">
                            <span class="text-slate-300">{{ $ord->items->count() }} Product types</span>
                        </td>
                        <td class="py-3 text-right font-mono font-bold text-white">
                            ৳ {{ number_format($ord->total_amount, 2) }}
                        </td>
                        <td class="py-3 text-right font-mono font-bold text-emerald-400">
                            ৳ {{ number_format($ord->paid_amount, 2) }}
                        </td>
                        <td class="py-3 text-right font-mono font-bold {{ $ord->due_amount > 0 ? 'text-rose-400' : 'text-slate-400' }}">
                            ৳ {{ number_format($ord->due_amount, 2) }}
                        </td>
                        <td class="py-3 text-center">
                            @if($ord->delivery_status === 'delivered')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Delivered</span>
                            @elseif($ord->delivery_status === 'partial')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">Partial</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">Pending</span>
                            @endif
                        </td>
                        <td class="py-3 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('factory.orders.invoice', $ord->id) }}" title="View Invoice"
                                   class="px-2.5 py-1 rounded-lg bg-blue-600/30 hover:bg-blue-600/50 text-cyan-300 border border-blue-500/30 text-[11px] font-semibold transition-all">
                                    <i class="fa-solid fa-file-invoice"></i> Invoice
                                </a>

                                @if($ord->delivery_status !== 'delivered')
                                <a href="{{ route('factory.deliveries.create', $ord->id) }}" title="Process Delivery"
                                   class="px-2.5 py-1 rounded-lg bg-emerald-600/30 hover:bg-emerald-600/50 text-emerald-300 border border-emerald-500/30 text-[11px] font-semibold transition-all">
                                    <i class="fa-solid fa-truck-fast"></i> Deliver
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-8 text-center text-slate-500">No orders created yet. Click "+ Create New Order" to start!</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
