@extends('layouts.app')

@section('title', $customer->company_name . ' - Due Invoices & Payment | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Header Breadcrumb & Customer Info -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('factory.customers.due.index') }}" class="text-xs text-rose-400 hover:underline flex items-center gap-1 font-semibold">
                    <i class="fa-solid fa-arrow-left"></i> Due Payment Registry
                </a>
                <span class="text-slate-600">/</span>
                <a href="{{ route('factory.customers.index') }}" class="text-xs text-slate-400 hover:underline">
                    All Customers
                </a>
            </div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-building text-rose-400"></i>
                <span>{{ $customer->company_name }}</span>
            </h2>
            <p class="text-xs text-slate-400 flex flex-wrap items-center gap-x-3 gap-y-1 mt-0.5">
                <span class="inline-flex items-center gap-1 text-cyan-300 font-mono"><i class="fa-solid fa-phone text-[10px]"></i> {{ $customer->phone }}</span>
                <span>•</span>
                <span>Contact: {{ $customer->contact_person ?? 'Owner' }}</span>
                <span>•</span>
                <span>Address: {{ $customer->address ?? 'N/A' }}</span>
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <!-- Full Ledger Link -->
            <a href="{{ route('factory.customers.details', $customer->id) }}" 
               class="px-3.5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-cyan-300 hover:text-white border border-slate-700 text-xs font-semibold transition-all flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>Full Order History</span>
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="p-3.5 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs flex items-center gap-2 shadow-lg">
        <i class="fa-solid fa-circle-check text-sm"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="p-3.5 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs flex items-center gap-2 shadow-lg">
        <i class="fa-solid fa-circle-exclamation text-sm"></i>
        <span class="font-medium">{{ session('error') }}</span>
    </div>
    @endif
    @if($errors->any())
    <div class="p-3 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Financial KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <!-- 1. Outstanding Due -->
        <div class="glass-panel rounded-2xl p-4 border border-rose-500/40 bg-gradient-to-br from-rose-950/40 via-red-950/20 to-[#0a1228] flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-rose-300 uppercase tracking-wider block">Total Outstanding Due</span>
                <span class="text-2xl font-mono font-black text-rose-400 mt-1 block">৳ {{ number_format($totalDue, 2) }}</span>
                <span class="text-[10px] text-rose-300/80 font-mono mt-0.5 block">Pending across {{ $dueOrders->count() }} invoice(s)</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-rose-500/20 border border-rose-500/40 text-rose-400 flex items-center justify-center text-lg">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
        </div>

        <!-- 2. Due Invoices Count -->
        <div class="glass-panel rounded-2xl p-4 border border-amber-500/40 bg-gradient-to-br from-amber-950/20 to-[#0a1228] flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-amber-300 uppercase tracking-wider block">Due Invoices</span>
                <span class="text-2xl font-mono font-black text-amber-400 mt-1 block">{{ $dueOrders->count() }}</span>
                <span class="text-[10px] text-amber-300/80 mt-0.5 block">Arranged oldest to newest</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-400 flex items-center justify-center text-lg">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
        </div>

        <!-- 3. Already Paid Amount -->
        <div class="glass-panel rounded-2xl p-4 border border-emerald-500/40 bg-gradient-to-br from-emerald-950/20 to-[#0a1228] flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-emerald-300 uppercase tracking-wider block">Total Paid So Far</span>
                <span class="text-2xl font-mono font-black text-emerald-400 mt-1 block">৳ {{ number_format($totalPaid, 2) }}</span>
                <span class="text-[10px] text-emerald-300/80 mt-0.5 block">Collected payments</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 flex items-center justify-center text-lg">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <!-- 4. Total Invoiced Amount -->
        <div class="glass-panel rounded-2xl p-4 border border-blue-500/40 bg-gradient-to-br from-blue-950/20 to-[#0a1228] flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-blue-300 uppercase tracking-wider block">Total Invoiced Amount</span>
                <span class="text-2xl font-mono font-black text-blue-400 mt-1 block">৳ {{ number_format($totalOrdered, 2) }}</span>
                <span class="text-[10px] text-blue-300/80 mt-0.5 block">Lifetime orders total</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-500/15 border border-blue-500/30 text-blue-400 flex items-center justify-center text-lg">
                <i class="fa-solid fa-receipt"></i>
            </div>
        </div>
    </div>

    <!-- TOP OVERARCHING PAYMENT CARD (KEY REQUIREMENT: FIFO AUTO-ALLOCATION ENGINE) -->
    <div class="glass-panel rounded-3xl p-5 border border-amber-500/50 bg-gradient-to-br from-[#0a1636]/90 via-[#0d1c44]/80 to-[#12193b]/90 shadow-2xl relative overflow-hidden">
        <div class="absolute -top-16 -right-16 w-48 h-48 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 pb-3.5 mb-4 border-b border-slate-700/80">
            <div>
                <h3 class="text-sm sm:text-base font-extrabold text-white flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-cyan-600/25 text-cyan-400 border border-cyan-500/40 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-bolt"></i>
                    </span>
                    <span>Receive Due Payment (FIFO Auto-Allocation)</span>
                </h3>
                <p class="text-xs text-slate-300 mt-1">
                    টাকা ইনপুট করলে স্বয়ংক্রিয়ভাবে <strong class="text-amber-300">সবচেয়ে পুরনো বকেয়া ইনভয়েস থেকে শুরু করে ক্রমানুসারে</strong> টাকা পেইড হবে।
                </p>
            </div>
            
            <div class="flex items-center gap-2.5">
                <span class="px-3 py-1.5 rounded-xl bg-slate-800/80 text-cyan-300 font-mono text-xs border border-slate-700 flex items-center gap-1.5 font-bold">
                    <i class="fa-solid fa-layer-group text-[11px] text-cyan-400"></i>
                    <span>Max Due: ৳ {{ number_format($totalDue, 2) }}</span>
                </span>
            </div>
        </div>

        <form action="{{ route('factory.customers.due.payment.store', $customer->id) }}" method="POST" id="fifoPaymentForm" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                
                <!-- 1. Payment Amount -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold text-amber-300">
                            Payment Amount (৳) *
                        </label>
                        <button type="button" onclick="setFullDueAmount({{ $totalDue }})" class="text-[10px] text-cyan-400 hover:text-cyan-300 font-semibold hover:underline">
                            Full Due (৳{{ number_format($totalDue, 2) }})
                        </button>
                    </div>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-bold text-amber-400">৳</span>
                        <input type="number" step="0.01" min="0.01" max="{{ $totalDue }}" name="amount" id="fifoPaymentAmountInput" required
                               placeholder="e.g. 700"
                               oninput="updateFifoPreview(this.value)"
                               class="w-full bg-[#070d1e] text-base font-mono font-black text-amber-300 rounded-xl pl-8 pr-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                    </div>
                </div>

                <!-- 2. Payment Date -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Payment Date *</label>
                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                           style="color-scheme: dark;"
                           class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none cursor-pointer">
                </div>

                <!-- 3. Payment Method -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Payment Method</label>
                    <select name="payment_method" class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                        <option value="Cash">Cash (নগদ)</option>
                        <option value="Bank Transfer">Bank Transfer / Cheque</option>
                        <option value="bKash / Nagad">Mobile Banking (bKash/Nagad)</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <!-- 4. Notes / Remarks -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Reference / Note</label>
                    <input type="text" name="notes" placeholder="e.g. Received by Manager, Slip #102"
                           class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                </div>

            </div>

            <!-- Interactive Live FIFO Allocation Preview -->
            <div id="fifoPreviewBox" class="hidden p-3.5 rounded-2xl bg-[#070e24] border border-amber-500/30 text-xs space-y-2">
                <div class="flex items-center justify-between text-[11px] font-bold text-amber-300">
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-calculator text-amber-400"></i>
                        <span>Live FIFO Allocation Breakdown (স্বয়ংক্রিয় বন্টন হিসাব)</span>
                    </span>
                    <span id="previewTotalAllocated" class="font-mono text-emerald-400">Allocated: ৳ 0.00</span>
                </div>
                <div id="previewItemsList" class="space-y-1.5 max-h-36 overflow-y-auto pr-1 text-slate-300 text-[11px]">
                    <!-- Live injected rows -->
                </div>
            </div>

            <!-- Submit Button (Site Color: Solid Cyan, No Gradient) -->
            <div class="flex items-center justify-end pt-1">
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs shadow-lg shadow-cyan-600/20 transition-all flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-check-double text-sm"></i>
                    <span>Apply Due Payment Across Invoices (FIFO)</span>
                </button>
            </div>
        </form>
    </div>

    <!-- DUE INVOICES TABLE (DATE-WISE, WITH PRODUCT NAMES & SINGLE PAY OPTION) -->
    <div class="glass-panel rounded-3xl p-5 border border-slate-700/60 w-full space-y-4">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-700/70">
            <div>
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-list-ol text-cyan-400"></i>
                    <span>Due Invoices Breakdown (Date-wise: Oldest to Newest)</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">
                    প্রতিটি ইনভয়েসের তারিখ, অন্তর্ভুক্ত প্রোডাক্টসমূহ এবং আলাদাভাবে পেমেন্ট করার অপশন
                </p>
            </div>

            <span class="px-3 py-1 rounded-xl bg-slate-800 text-cyan-300 font-mono text-xs border border-slate-700">
                Total Invoices: {{ $dueOrders->count() }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs min-w-[850px]">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-700/80 font-bold uppercase tracking-wider text-[11px]">
                        <th class="py-3 px-3.5">Priority</th>
                        <th class="py-3 px-3.5">Order Date</th>
                        <th class="py-3 px-3.5">Invoice #</th>
                        <th class="py-3 px-3.5" style="width: 28%;">Products in Invoice</th>
                        <th class="py-3 px-3.5 text-right">Total (৳)</th>
                        <th class="py-3 px-3.5 text-right">Paid (৳)</th>
                        <th class="py-3 px-3.5 text-right">Due Balance (৳)</th>
                        <th class="py-3 px-3.5 text-right min-w-[170px]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($dueOrders as $idx => $order)
                    @php
                        $isOldest = $loop->first;
                    @endphp
                    <tr class="hover:bg-slate-800/40 transition-colors {{ $isOldest ? 'bg-amber-950/15' : '' }}">
                        
                        <!-- Priority Queue Tag (Site Color: Solid Cyan Border/Bg, No Gradient) -->
                        <td class="py-3.5 px-3.5 whitespace-nowrap">
                            @if($isOldest)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 flex items-center gap-1 w-fit shadow-sm">
                                    <i class="fa-solid fa-star text-[9px] text-cyan-400"></i> #1 1st in FIFO
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono text-slate-400 bg-slate-800 border border-slate-700/60 w-fit">
                                    #{{ $idx + 1 }} in queue
                                </span>
                            @endif
                        </td>

                        <!-- Date -->
                        <td class="py-3.5 px-3.5 whitespace-nowrap">
                            <span class="font-bold text-white block">{{ date('d M, Y', strtotime($order->order_date)) }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">{{ date('h:i A', strtotime($order->created_at)) }}</span>
                        </td>

                        <!-- Invoice Number -->
                        <td class="py-3.5 px-3.5 whitespace-nowrap font-mono font-bold text-cyan-300">
                            {{ $order->order_number }}
                        </td>

                        <!-- Products in Invoice (KEY REQUIREMENT: product name soho) -->
                        <td class="py-3.5 px-3.5">
                            <div class="space-y-1.5 max-w-sm">
                                @forelse($order->items as $item)
                                <div class="flex items-center justify-between text-[11px] bg-[#070d1e]/80 p-1.5 rounded-lg border border-slate-800">
                                    <div class="flex items-center gap-1.5 overflow-hidden">
                                        <i class="fa-solid fa-box text-cyan-400 text-[10px] flex-shrink-0"></i>
                                        <span class="text-white font-medium truncate" title="{{ $item->product->name ?? 'Product' }}">
                                            {{ $item->product->name ?? 'Product' }}
                                        </span>
                                    </div>
                                    <span class="text-slate-400 font-mono text-[10px] whitespace-nowrap ml-2">
                                        {{ number_format($item->qty) }} {{ $item->product->unit->short_code ?? ($item->product->unit->name ?? 'Pcs') }}
                                    </span>
                                </div>
                                @empty
                                <span class="text-slate-500 italic text-[11px]">No items recorded</span>
                                @endforelse
                            </div>
                        </td>

                        <!-- Total Amount -->
                        <td class="py-3.5 px-3.5 text-right font-mono font-bold text-white whitespace-nowrap">
                            ৳ {{ number_format($order->total_amount, 2) }}
                        </td>

                        <!-- Paid Amount -->
                        <td class="py-3.5 px-3.5 text-right font-mono font-bold text-emerald-400 whitespace-nowrap">
                            ৳ {{ number_format($order->paid_amount, 2) }}
                        </td>

                        <!-- Due Balance -->
                        <td class="py-3.5 px-3.5 text-right font-mono font-black text-rose-400 whitespace-nowrap text-sm">
                            ৳ {{ number_format($order->due_amount, 2) }}
                        </td>

                        <!-- Actions (Pay This Invoice / View Invoice) -->
                        <td class="py-3.5 px-3.5 text-right whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5">
                                
                                <!-- Pay This Single Invoice Button (Site Color: Solid Cyan, No Gradient) -->
                                <button type="button" 
                                        onclick="openSinglePayModal(
                                            '{{ $order->id }}',
                                            '{{ $order->order_number }}',
                                            '{{ date('d M, Y', strtotime($order->order_date)) }}',
                                            '{{ $order->due_amount }}',
                                            '{{ $order->total_amount }}',
                                            '{{ $order->paid_amount }}'
                                        )"
                                        class="px-3 py-1.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-[11px] shadow-sm transition-all inline-flex items-center gap-1 cursor-pointer">
                                    <i class="fa-solid fa-dollar-sign text-[10px]"></i>
                                    <span>Pay Invoice</span>
                                </button>

                                <!-- View Invoice Link -->
                                <a href="{{ route('factory.orders.invoice', $order->id) }}" 
                                   title="View & Print Full Invoice"
                                   class="p-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-cyan-300 hover:text-white border border-slate-700 transition-colors inline-flex items-center text-xs">
                                    <i class="fa-solid fa-file-invoice"></i>
                                </a>

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <i class="fa-solid fa-circle-check text-4xl text-emerald-400"></i>
                                <span class="text-white font-bold text-base">This customer has 0 due!</span>
                                <span class="text-xs text-slate-400">All invoices for {{ $customer->company_name }} are 100% paid.</span>
                                <a href="{{ route('factory.customers.due.index') }}" class="mt-2 text-xs text-cyan-400 hover:underline">
                                    Back to Due Customers
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>

<!-- SINGLE INVOICE PAYMENT POPUP MODAL -->
<div id="singleInvoicePayModal" class="fixed inset-0 z-50 bg-black/75 backdrop-blur-md hidden flex items-center justify-center p-4">
    <div class="glass-panel rounded-3xl p-6 border border-slate-700/80 max-w-md w-full relative shadow-2xl bg-[#0a1228]/95">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-700/80 mb-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-file-invoice-dollar text-rose-400"></i>
                <span>Pay Single Invoice (<span id="modalOrderNumber"></span>)</span>
            </h3>
            <button type="button" onclick="closeSinglePayModal()" class="text-slate-400 hover:text-white p-1 text-base">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="singleInvoicePayForm" method="POST" class="space-y-3.5">
            @csrf

            <!-- Invoice Overview Summary -->
            <div class="bg-[#070d1e] p-3 rounded-2xl border border-slate-800 text-xs grid grid-cols-3 gap-2 text-center">
                <div>
                    <span class="text-slate-400 text-[10px] block">Order Date</span>
                    <span id="modalOrderDate" class="text-white font-mono font-bold mt-0.5 block"></span>
                </div>
                <div>
                    <span class="text-slate-400 text-[10px] block">Total Invoiced</span>
                    <span id="modalOrderTotal" class="text-white font-mono font-bold mt-0.5 block"></span>
                </div>
                <div>
                    <span class="text-rose-400 text-[10px] font-bold block">Current Due</span>
                    <span id="modalOrderDue" class="text-rose-400 font-mono font-black mt-0.5 block"></span>
                </div>
            </div>

            <!-- Payment Amount -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1 flex items-center justify-between">
                    <span>Payment Amount (৳) *</span>
                    <button type="button" onclick="fillSingleFullDue()" class="text-[10px] text-cyan-400 hover:underline">
                        Pay Full Due
                    </button>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-bold text-rose-400">৳</span>
                    <input type="number" step="0.01" min="0.01" name="amount" id="modalSinglePayAmount" required
                           class="w-full bg-[#070d1e] text-base font-mono font-black text-rose-300 rounded-xl pl-8 pr-3.5 py-2.5 border border-slate-700 focus:border-rose-400 focus:outline-none">
                </div>
            </div>

            <!-- Payment Date -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Payment Date *</label>
                <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                       style="color-scheme: dark;"
                       class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3.5 py-2 border border-slate-700 focus:border-rose-400 focus:outline-none cursor-pointer">
            </div>

            <!-- Payment Method -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Payment Method</label>
                <select name="payment_method" class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3.5 py-2 border border-slate-700 focus:border-rose-400 focus:outline-none">
                    <option value="Cash">Cash (নগদ)</option>
                    <option value="Bank Transfer">Bank Transfer / Cheque</option>
                    <option value="bKash / Nagad">Mobile Banking (bKash/Nagad)</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Notes / Transaction Reference</label>
                <input type="text" name="notes" placeholder="e.g. Received via Bank Transfer"
                       class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3.5 py-2 border border-slate-700 focus:border-rose-400 focus:outline-none">
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-700/80">
                <button type="button" onclick="closeSinglePayModal()" 
                        class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-5 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-check"></i>
                    <span>Confirm Payment</span>
                </button>
            </div>
        </form>

    </div>
</div>

@push('scripts')
<script>
    // Data list of due invoices for live client-side FIFO preview calculation
    const dueInvoicesData = [
        @foreach($dueOrders as $ord)
        {
            id: {{ $ord->id }},
            number: '{{ $ord->order_number }}',
            date: '{{ date('d M, Y', strtotime($ord->order_date)) }}',
            due: {{ floatval($ord->due_amount) }}
        },
        @endforeach
    ];

    // FIFO Live Preview Calculation
    function updateFifoPreview(val) {
        const amount = parseFloat(val);
        const previewBox = document.getElementById('fifoPreviewBox');
        const listContainer = document.getElementById('previewItemsList');
        const totalAllocatedEl = document.getElementById('previewTotalAllocated');

        if (isNaN(amount) || amount <= 0) {
            previewBox.classList.add('hidden');
            return;
        }

        previewBox.classList.remove('hidden');
        listContainer.innerHTML = '';

        let remaining = amount;
        let totalAllocated = 0;

        dueInvoicesData.forEach((inv, index) => {
            if (remaining <= 0.001) return;

            const allocated = Math.min(remaining, inv.due);
            const remainingDueAfter = inv.due - allocated;
            totalAllocated += allocated;
            remaining -= allocated;

            const isFullyPaid = remainingDueAfter <= 0.001;
            const badgeClass = isFullyPaid 
                ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' 
                : 'bg-amber-500/20 text-amber-300 border-amber-500/30';
            const badgeText = isFullyPaid ? 'Fully Cleared (বকেয়া ০)' : `Remaining Due: ৳ ${remainingDueAfter.toFixed(2)}`;

            const row = document.createElement('div');
            row.className = 'flex items-center justify-between p-2 rounded-xl bg-[#09122c] border border-slate-800';
            row.innerHTML = `
                <div class="flex items-center gap-2">
                    <span class="font-mono text-cyan-300 font-bold">${inv.number}</span>
                    <span class="text-slate-400 text-[10px]">(${inv.date})</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="font-mono font-bold text-emerald-400">+ ৳ ${allocated.toFixed(2)}</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border ${badgeClass}">${badgeText}</span>
                </div>
            `;
            listContainer.appendChild(row);
        });

        totalAllocatedEl.innerText = `Total Allocated: ৳ ${totalAllocated.toFixed(2)}`;
    }

    function setFullDueAmount(fullDue) {
        const input = document.getElementById('fifoPaymentAmountInput');
        if (!input) return;
        const val = parseFloat(fullDue);
        input.value = val.toFixed(2);
        updateFifoPreview(val);
        input.focus();
        input.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    // Single Invoice Modal
    let activeModalMaxDue = 0;

    function openSinglePayModal(orderId, orderNum, dateStr, dueAmt, totalAmt, paidAmt) {
        activeModalMaxDue = parseFloat(dueAmt);
        document.getElementById('modalOrderNumber').innerText = orderNum;
        document.getElementById('modalOrderDate').innerText = dateStr;
        document.getElementById('modalOrderTotal').innerText = '৳ ' + parseFloat(totalAmt).toFixed(2);
        document.getElementById('modalOrderDue').innerText = '৳ ' + activeModalMaxDue.toFixed(2);

        const amountInput = document.getElementById('modalSinglePayAmount');
        amountInput.value = activeModalMaxDue.toFixed(2);
        amountInput.max = activeModalMaxDue;

        document.getElementById('singleInvoicePayForm').action = `/factory/customers/{{ $customer->id }}/invoice-payment/${orderId}`;
        document.getElementById('singleInvoicePayModal').classList.remove('hidden');
    }

    function closeSinglePayModal() {
        document.getElementById('singleInvoicePayModal').classList.add('hidden');
    }

    function fillSingleFullDue() {
        document.getElementById('modalSinglePayAmount').value = activeModalMaxDue.toFixed(2);
    }

    // Keyboard ESC to close modal
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeSinglePayModal();
        }
    });
</script>
@endpush
@endsection

