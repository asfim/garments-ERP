@extends('layouts.app')

@section('title', $customer->company_name . ' - Ledger History | TBG ERP')

@section('content')
<div class="space-y-6">

    <!-- Header Breadcrumb & Back -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('factory.customers.index') }}" class="text-xs text-cyan-400 hover:underline flex items-center gap-1">
                    <i class="fa-solid fa-arrow-left"></i> Back to Customers
                </a>
            </div>
            <h2 class="text-2xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-building text-cyan-400"></i>
                {{ $customer->company_name }}
            </h2>
            <p class="text-xs text-slate-400">Phone: {{ $customer->phone }} | Address: {{ $customer->address }}</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('factory.orders.create') }}" 
               class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-bold text-xs shadow-lg shadow-cyan-500/25 hover:from-cyan-400 hover:to-blue-500 transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-cart-plus"></i>
                <span>Create New Order</span>
            </a>
        </div>
    </div>

    <!-- Financial KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="glass-panel rounded-2xl p-4 border border-blue-500/30">
            <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider">Total Orders Amount</p>
            <h3 class="text-xl font-black text-white mt-1">৳ {{ number_format($customer->total_ordered, 2) }}</h3>
            <p class="text-[10px] text-cyan-300 mt-0.5">{{ $customer->orders->count() }} Orders placed</p>
        </div>
        <div class="glass-panel rounded-2xl p-4 border border-emerald-500/30">
            <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider">Total Paid Amount</p>
            <h3 class="text-xl font-black text-emerald-400 mt-1">৳ {{ number_format($customer->total_paid, 2) }}</h3>
            <p class="text-[10px] text-emerald-300 mt-0.5">Received Payment</p>
        </div>
        <div class="glass-panel rounded-2xl p-4 border border-rose-500/30">
            <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider">Outstanding Total Due</p>
            <h3 class="text-xl font-black text-rose-400 mt-1">৳ {{ number_format($customer->total_due, 2) }}</h3>
            <p class="text-[10px] text-rose-300 mt-0.5">Pending collection</p>
        </div>
    </div>

    <!-- Full Month-by-Month Order & Invoice History -->
    <div class="glass-panel rounded-3xl p-5 border border-slate-700/60">
        <h3 class="text-sm font-bold text-white mb-3 flex items-center justify-between">
            <span class="flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-cyan-400"></i>
                Complete Order & Delivery Timeline
            </span>
            <span class="text-xs text-slate-400 font-mono">{{ $customer->orders->count() }} Records</span>
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-700/80 font-semibold">
                        <th class="pb-2.5">Date / Month</th>
                        <th class="pb-2.5">Invoice #</th>
                        <th class="pb-2.5">Products & Quantities</th>
                        <th class="pb-2.5 text-right">Invoice Amount</th>
                        <th class="pb-2.5 text-right">Paid</th>
                        <th class="pb-2.5 text-right">Due Balance</th>
                        <th class="pb-2.5 text-center">Delivery Status</th>
                        <th class="pb-2.5 text-right">Invoice</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($customer->orders as $ord)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="py-3 text-slate-300 font-medium">
                            <span class="font-bold text-white block">{{ date('d M, Y', strtotime($ord->order_date)) }}</span>
                            <span class="text-[10px] text-cyan-400 font-mono">{{ date('F Y', strtotime($ord->order_date)) }}</span>
                        </td>
                        <td class="py-3 font-mono font-bold text-cyan-300">
                            {{ $ord->order_number }}
                        </td>
                        <td class="py-3">
                            <div class="space-y-1 max-w-sm">
                                @foreach($ord->items as $itm)
                                <div class="text-[11px] text-slate-300 flex items-center justify-between">
                                    <span>• {{ $itm->product->name }}</span>
                                    <span class="text-slate-400 font-mono ml-2">({{ number_format($itm->qty) }} {{ $itm->product->unit->short_code ?? '' }})</span>
                                </div>
                                @endforeach
                            </div>
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
                            <a href="{{ route('factory.orders.invoice', $ord->id) }}" 
                               class="px-2.5 py-1 rounded-lg bg-blue-600/30 hover:bg-blue-600/50 text-cyan-300 border border-blue-500/30 text-[11px] font-semibold transition-all inline-flex items-center gap-1">
                                <i class="fa-solid fa-file-invoice"></i>
                                <span>Invoice</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-slate-500">No orders recorded for this customer yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

