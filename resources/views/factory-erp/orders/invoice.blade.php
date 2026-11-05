@extends('layouts.app')

@section('title', 'Invoice ' . $order->order_number . ' | TBG ERP')

@push('styles')
<style>
@media print {
    /* Hide everything except the invoice card */
    header, aside, footer, .no-print, nav, #appSidebar, #topHeader, .mode-btn {
        display: none !important;
    }
    html, body {
        background: #ffffff !important;
        color: #0f172a !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        height: auto !important;
        overflow: visible !important;
    }
    div.flex.flex-1.overflow-hidden {
        overflow: visible !important;
        display: block !important;
    }
    main {
        overflow: visible !important;
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
    }
    #printableInvoiceCard {
        position: relative !important;
        background: #ffffff !important;
        color: #0f172a !important;
        box-shadow: none !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 12px !important;
        padding: 24px !important;
        margin: 0 auto !important;
        width: 100% !important;
        page-break-inside: avoid;
    }
    #printableInvoiceCard * {
        color: #0f172a !important;
        text-shadow: none !important;
    }
    #printableInvoiceCard .text-slate-400,
    #printableInvoiceCard .text-slate-500,
    #printableInvoiceCard .text-slate-300 {
        color: #64748b !important;
    }
    #printableInvoiceCard table th {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
        border-bottom: 2px solid #94a3b8 !important;
    }
    #printableInvoiceCard table td {
        border-bottom: 1px solid #e2e8f0 !important;
        color: #0f172a !important;
    }
    #printableInvoiceCard .invoice-watermark {
        display: flex !important;
        visibility: visible !important;
    }
    #printableInvoiceCard .invoice-watermark img {
        opacity: 0.20 !important;
        mix-blend-mode: multiply !important;
    }
    .global-print-watermark {
        display: none !important;
    }
    #printableInvoiceCard .doc-badge {
        background-color: #f1f5f9 !important;
        border: 1px solid #cbd5e1 !important;
        color: #0284c7 !important;
    }
}
</style>
@endpush

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    <!-- Top Action Bar (Completely Hidden on Print) -->
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('factory.orders.index') }}" class="text-xs text-slate-400 hover:text-white flex items-center gap-1 font-semibold">
            <i class="fa-solid fa-arrow-left"></i> Back to Orders
        </a>

        <div class="flex items-center gap-2.5">
            <!-- Print Button (Triggers window.print()) -->
            <button onclick="window.print()" 
                    class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition-colors flex items-center gap-1.5 cursor-pointer shadow-md">
                <i class="fa-solid fa-print"></i>
                <span>Print</span>
            </button>

            <!-- PDF Download Button -->
            <a href="{{ route('factory.orders.invoice.pdf', $order->id) }}" 
               class="px-4 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs shadow-md transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-file-pdf"></i>
                <span>Download PDF</span>
            </a>

            @if($order->delivery_status !== 'delivered')
            <a href="{{ route('factory.deliveries.create', $order->id) }}" 
               class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-truck-fast"></i>
                <span>Process Delivery</span>
            </a>
            @endif
        </div>
    </div>

    @php
        $watermarkSrc = !empty($company->print_logo) && file_exists(public_path('storage/' . $company->print_logo))
            ? asset('storage/' . $company->print_logo)
            : (!empty($company->logo) && file_exists(public_path('storage/' . $company->logo)) ? asset('storage/' . $company->logo) : null);
    @endphp

    <!-- Printable Invoice Paper Card (ONLY THIS PRINTS) -->
    <div id="printableInvoiceCard" class="glass-panel rounded-3xl p-8 border border-slate-700/60 text-slate-200 space-y-6 bg-[#0a1128]/95 relative overflow-hidden">
        
        <!-- Center Page Watermark / Jolchap (জলছাপ) -->
        @if($watermarkSrc)
        <div class="invoice-watermark pointer-events-none select-none absolute inset-0 flex items-center justify-center overflow-hidden z-0" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; display: flex; align-items: center; justify-content: center; pointer-events: none; z-index: 0;">
            <img src="{{ $watermarkSrc }}" alt="Watermark" class="w-96 max-w-[70%] max-h-[70%] object-contain" style="opacity: 0.20; mix-blend-mode: multiply;">
        </div>
        @endif

        <div class="relative z-10 space-y-6">

            <!-- Official Header Matching Image 2 -->
            @include('factory-erp.common.web-header')

            <div class="flex items-center justify-between pb-3 border-b border-slate-700/60">
                <span class="doc-badge px-3 py-1 rounded-xl bg-cyan-500/20 text-cyan-300 font-mono font-bold text-xs border border-cyan-500/30">
                    TAX INVOICE
                </span>
                <div class="text-right text-xs">
                    <span class="font-mono font-bold text-white">Invoice #{{ $order->order_number }}</span>
                    <span class="text-slate-400 ml-2">Date: {{ date('d M, Y', strtotime($order->order_date)) }}</span>
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

            <!-- Official Footer Matching Image 2 -->
            @include('factory-erp.common.web-footer')

        </div>

    </div>

</div>
@endsection
