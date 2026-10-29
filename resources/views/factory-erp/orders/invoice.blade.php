@extends('layouts.app')

@section('title', 'Invoice ' . $order->order_number . ' | TBG ERP')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    <!-- Top Action Bar -->
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('factory.orders.index') }}" class="text-xs text-slate-400 hover:text-white flex items-center gap-1">
            <i class="fa-solid fa-arrow-left"></i> Back to Orders
        </a>

        <div class="flex items-center gap-2.5">
            <button onclick="window.print()" 
                    class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-print"></i> Print
            </button>
            <a href="{{ route('factory.orders.invoice.pdf', $order->id) }}" 
               class="px-4 py-2 rounded-xl bg-gradient-to-r from-rose-500 to-pink-600 text-white font-bold text-xs shadow-lg shadow-rose-500/25 hover:from-rose-400 hover:to-pink-500 transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-file-pdf"></i> Download PDF
            </a>
            @if($order->delivery_status !== 'delivered')
            <a href="{{ route('factory.deliveries.create', $order->id) }}" 
               class="px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold text-xs shadow-lg shadow-emerald-500/25 hover:from-emerald-400 hover:to-teal-500 transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-truck-fast"></i> Process Delivery
            </a>
            @endif
        </div>
    </div>

    <!-- Printable Invoice Paper Card -->
    <div class="glass-panel rounded-3xl p-8 border border-slate-700/60 text-slate-200 space-y-6 bg-[#0a1128]/90">
        
        <!-- Header (Bound to Company Settings) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-700/80">
            <div>
                <h1 class="text-2xl font-black text-white tracking-wide">
                    {{ $company->company_name ?? 'Turkey Bangla Garments Ltd' }}
                </h1>
                <p class="text-xs text-slate-400 mt-1">{{ $company->address ?? 'Tongi Industrial Area, Gazipur, Dhaka' }}</p>
                <p class="text-xs text-slate-400">Phone: {{ $company->phone ?? '+880 1712-345678' }} | Email: {{ $company->email ?? 'info@turkeybangla.com' }}</p>
            </div>

            <div class="text-right">
                <span class="px-3 py-1 rounded-xl bg-cyan-500/20 text-cyan-300 font-mono font-bold text-sm border border-cyan-500/30">
                    INVOICE
                </span>
                <p class="text-sm font-mono font-bold text-white mt-2">{{ $order->order_number }}</p>
                <p class="text-xs text-slate-400">Date: {{ date('d M, Y', strtotime($order->order_date)) }}</p>
            </div>
        </div>

        <!-- Bill To / Customer Details -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-4 border-b border-slate-700/80">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Billed To (Client):</span>
                <h3 class="text-base font-bold text-white mt-1">{{ $order->customer->company_name }}</h3>
                <p class="text-xs text-slate-300">{{ $order->customer->address }}</p>
                <p class="text-xs text-cyan-300 font-mono mt-0.5">Contact: {{ $order->customer->phone }} ({{ $order->customer->contact_person ?? 'Owner' }})</p>
            </div>
            <div class="sm:text-right">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Delivery Status:</span>
                <p class="mt-1">
                    @if($order->delivery_status === 'delivered')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Delivered</span>
                    @elseif($order->delivery_status === 'partial')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">Partial Delivered</span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">Pending Delivery</span>
                    @endif
                </p>
            </div>
        </div>

        <!-- Ordered Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-700 font-semibold uppercase tracking-wider">
                        <th class="pb-2.5">#</th>
                        <th class="pb-2.5">Item Description</th>
                        <th class="pb-2.5 text-center">Unit</th>
                        <th class="pb-2.5 text-center">Order Qty</th>
                        <th class="pb-2.5 text-center">Delivered</th>
                        <th class="pb-2.5 text-center">Due Qty</th>
                        <th class="pb-2.5 text-right">Unit Price</th>
                        <th class="pb-2.5 text-right">Total (৳)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach($order->items as $idx => $item)
                    <tr>
                        <td class="py-3 text-slate-400">{{ $idx + 1 }}</td>
                        <td class="py-3 font-semibold text-white">{{ $item->product->name }}</td>
                        <td class="py-3 text-center text-slate-300">{{ $item->product->unit->short_code ?? 'Pcs' }}</td>
                        <td class="py-3 text-center font-mono font-bold text-white">{{ number_format($item->qty) }}</td>
                        <td class="py-3 text-center font-mono text-emerald-400">{{ number_format($item->delivered_qty) }}</td>
                        <td class="py-3 text-center font-mono font-bold {{ $item->due_qty > 0 ? 'text-amber-400' : 'text-slate-500' }}">{{ number_format($item->due_qty) }}</td>
                        <td class="py-3 text-right font-mono text-slate-300">৳ {{ number_format($item->sell_price, 2) }}</td>
                        <td class="py-3 text-right font-mono font-bold text-cyan-300">৳ {{ number_format($item->total_price, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Bottom Totals & Payment Status -->
        <div class="flex flex-col sm:flex-row justify-between items-start pt-4 border-t border-slate-700/80 gap-6">
            <div class="text-xs text-slate-400 max-w-sm">
                @if($order->notes)
                    <p class="font-semibold text-slate-300">Notes:</p>
                    <p>{{ $order->notes }}</p>
                @endif
                <p class="mt-2 text-[11px] text-slate-500">Thank you for your business with {{ $company->company_name ?? 'Turkey Bangla Garments Ltd' }}.</p>
            </div>

            <div class="w-full sm:w-64 space-y-2 text-xs">
                <div class="flex justify-between text-slate-300">
                    <span>Subtotal:</span>
                    <span class="font-mono font-bold text-white">৳ {{ number_format($order->total_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-emerald-400">
                    <span>Paid / Advance:</span>
                    <span class="font-mono font-bold">৳ {{ number_format($order->paid_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-base font-bold text-rose-400 pt-2 border-t border-slate-700">
                    <span>Total Due:</span>
                    <span class="font-mono">৳ {{ number_format($order->due_amount, 2) }}</span>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

