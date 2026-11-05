@extends('layouts.app')

@section('title', 'Income & Revenue Management | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-arrow-trend-up text-emerald-400"></i>
                Income & Revenue Management (আয় ব্যবস্থাপনা)
            </h2>
            <p class="text-xs text-slate-400">Record factory sales, sample charges, scrap sales, and other cash/bank inflows</p>
        </div>

        <div class="flex items-center gap-2.5">
            <button type="button" onclick="openIncomeModal()" 
                    class="px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs shadow-lg shadow-emerald-500/25 transition-all flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-plus-circle"></i>
                <span>+ New Income Voucher (নতুন আয়)</span>
            </button>
            <a href="{{ route('factory.accounts.dashboard') }}" 
               class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Dashboard</span>
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="p-3.5 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs flex items-center gap-2">
        <i class="fa-solid fa-circle-check text-sm"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="p-3.5 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs flex items-center gap-2">
        <i class="fa-solid fa-triangle-exclamation text-sm"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- STAT SUMMARY BANNER -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="glass-panel p-4 rounded-2xl border border-emerald-500/30 bg-gradient-to-br from-emerald-950/20 to-slate-900/40 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-400 block">Total Filtered Income (মোট সংগৃহীত আয়)</span>
                <h3 class="text-2xl font-black text-white font-mono mt-1">৳ {{ number_format($totalFilteredIncome, 2) }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center">
                <i class="fa-solid fa-sack-dollar text-lg"></i>
            </div>
        </div>

        <div class="glass-panel p-4 rounded-2xl border border-slate-800 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Total Income Vouchers</span>
                <h3 class="text-2xl font-black text-white font-mono mt-1">{{ $vouchers->total() }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-800 text-slate-400 flex items-center justify-center">
                <i class="fa-solid fa-file-invoice text-lg"></i>
            </div>
        </div>

        <div class="glass-panel p-4 rounded-2xl border border-cyan-500/30 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-cyan-400 block">Next Voucher Sequence</span>
                <h3 class="text-xl font-black text-cyan-300 font-mono mt-1">{{ $nextVoucherNo }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center">
                <i class="fa-solid fa-barcode text-lg"></i>
            </div>
        </div>
    </div>

    <!-- FILTER & SEARCH BAR -->
    <div class="glass-panel p-4 rounded-2xl border border-slate-800/80">
        <form method="GET" action="{{ route('factory.accounts.income.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 text-xs">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-slate-400 text-[10px] uppercase font-bold mb-1">Search Keyword</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Voucher #, party name, ref, notes..." 
                       class="w-full bg-slate-900/80 border border-slate-700/80 rounded-xl px-3 py-2 text-slate-200 focus:outline-none focus:border-emerald-500 text-xs">
            </div>

            <!-- From Date -->
            <div>
                <label class="block text-slate-400 text-[10px] uppercase font-bold mb-1">From Date</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" 
                       class="w-full bg-slate-900/80 border border-slate-700/80 rounded-xl px-3 py-2 text-slate-200 focus:outline-none focus:border-emerald-500 text-xs">
            </div>

            <!-- To Date -->
            <div>
                <label class="block text-slate-400 text-[10px] uppercase font-bold mb-1">To Date</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" 
                       class="w-full bg-slate-900/80 border border-slate-700/80 rounded-xl px-3 py-2 text-slate-200 focus:outline-none focus:border-emerald-500 text-xs">
            </div>

            <!-- Income Head -->
            <div>
                <label class="block text-slate-400 text-[10px] uppercase font-bold mb-1">Income Head (খাত)</label>
                <select name="income_account_id" class="w-full bg-slate-900/80 border border-slate-700/80 rounded-xl px-3 py-2 text-slate-200 focus:outline-none focus:border-emerald-500 text-xs">
                    <option value="">All Income Heads</option>
                    @foreach($incomeAccounts as $acc)
                    <option value="{{ $acc->id }}" {{ request('income_account_id') == $acc->id ? 'selected' : '' }}>
                        {{ $acc->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold transition-all flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-filter text-xs"></i>
                    <span>Filter</span>
                </button>
                @if(request()->anyFilled(['search', 'from_date', 'to_date', 'income_account_id', 'payment_method']))
                <a href="{{ route('factory.accounts.income.index') }}" class="px-2.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold transition-all text-center" title="Reset Filters">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- INCOME VOUCHERS TABLE -->
    <div class="glass-panel rounded-2xl border border-slate-800/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/60 text-slate-400 uppercase text-[10px] font-bold border-b border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4">Voucher No</th>
                        <th class="py-3.5 px-4">Date</th>
                        <th class="py-3.5 px-4">Income Head</th>
                        <th class="py-3.5 px-4">Deposit To (A/C)</th>
                        <th class="py-3.5 px-4">Received From / Party</th>
                        <th class="py-3.5 px-4 text-right">Amount (৳)</th>
                        <th class="py-3.5 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($vouchers as $v)
                    @php
                        $isDelivery = ($v->source_module === 'App\Models\Delivery' || str_contains($v->reference_no ?? '', 'DEL-'));
                        $incomeHeadName = $isDelivery ? 'Product Sales Revenue' : ($v->creditAccount?->name ?? 'Product Sales Revenue');
                        $partyName = $v->party_name ?: ($v->customer?->company_name ?: 'General Customer');
                        $voucherJson = json_encode([
                            'voucher_no' => $v->voucher_no,
                            'voucher_date' => $v->voucher_date->format('d M, Y'),
                            'income_head' => $incomeHeadName,
                            'income_code' => $v->creditAccount?->code ?? '4001',
                            'deposit_to' => $v->debitAccount?->name ?? 'Cash/Bank',
                            'deposit_code' => $v->debitAccount?->code ?? '1050',
                            'party_name' => $partyName,
                            'amount' => number_format($v->amount, 2),
                            'payment_method' => $v->payment_method_label,
                            'reference_no' => $v->reference_no ?: 'N/A',
                            'notes' => $v->notes ?: 'N/A',
                            'is_delivery' => $isDelivery,
                            'print_url' => route('factory.accounts.income.print', $v->id),
                            'attachment_url' => $v->attachment ? asset('storage/' . $v->attachment) : null,
                            'created_by' => $v->createdBy?->name ?? 'System Admin',
                        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                    @endphp
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="py-3.5 px-4 font-mono font-bold text-amber-400">
                            {{ $v->voucher_no }}
                            @if($isDelivery)
                            <span class="block mt-0.5 px-1.5 py-0.2 rounded text-[9px] font-sans font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30 w-fit">
                                <i class="fa-solid fa-truck text-[8px]"></i> Delivery Sales
                            </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 font-mono text-slate-400">
                            {{ $v->voucher_date->format('d M, Y') }}
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="text-emerald-300 font-semibold block">
                                {{ $incomeHeadName }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 font-mono text-slate-300">
                            <span class="text-cyan-400 font-bold block">{{ $v->debitAccount?->name ?? 'Cash/Bank' }}</span>
                            @if($v->debitAccount?->account_number)
                            <span class="text-[10px] text-slate-500">A/C: {{ $v->debitAccount->account_number }}</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="text-white font-semibold block">
                                {{ $partyName }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right font-mono font-bold text-sm text-emerald-400">
                            {{ number_format($v->amount, 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <button type="button" 
                                        onclick="showVoucherDetails(this)"
                                        data-voucher="{{ $voucherJson }}"
                                        class="px-2 py-1.5 rounded-lg bg-blue-600/20 hover:bg-blue-600/30 text-blue-300 border border-blue-500/40 text-xs font-semibold flex items-center gap-1 transition-all cursor-pointer" title="View Full Details">
                                    <i class="fa-solid fa-circle-info"></i>
                                    <span>Details</span>
                                </button>

                                <a href="{{ route('factory.accounts.income.print', $v->id) }}" target="_blank" 
                                   class="p-1.5 rounded-lg bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 border border-emerald-500/40 text-xs" title="Print Money Receipt">
                                    <i class="fa-solid fa-print"></i>
                                </a>

                                @if($v->attachment)
                                <a href="{{ asset('storage/' . $v->attachment) }}" target="_blank" 
                                   class="p-1.5 rounded-lg bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-300 border border-cyan-500/40 text-xs" title="View Attachment">
                                    <i class="fa-solid fa-paperclip"></i>
                                </a>
                                @endif

                                <form action="{{ route('factory.accounts.vouchers.destroy', $v->id) }}" method="POST" 
                                      onsubmit="return confirm('Delete income voucher {{ $v->voucher_no }}? The deposit account balance will be reversed.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg bg-slate-800 hover:bg-rose-600/30 text-slate-400 hover:text-rose-400 text-xs transition-all" title="Delete Voucher">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-500">
                            <i class="fa-solid fa-arrow-trend-up text-3xl mb-2 opacity-50 block"></i>
                            No income records found matching your filters.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vouchers->hasPages())
        <div class="p-4 border-t border-slate-800 bg-slate-950/40">
            {{ $vouchers->links() }}
        </div>
        @endif
    </div>

</div>

<!-- MODAL: ADD NEW INCOME VOUCHER -->
<div id="incomeModal" onclick="if(event.target === this) closeIncomeModal()" class="fixed inset-0 z-[9999] bg-slate-950/85 hidden flex items-center justify-center p-4 overflow-y-auto transform-gpu">
    <div onclick="event.stopPropagation()" class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl p-6 relative">
        <button type="button" onclick="closeIncomeModal()" class="absolute top-5 right-5 text-slate-400 hover:text-white text-lg cursor-pointer">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="flex items-center gap-3 border-b border-slate-800 pb-4 mb-5">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center">
                <i class="fa-solid fa-sack-dollar text-lg"></i>
            </div>
            <div>
                <h3 class="text-base font-extrabold text-white">Create New Income Voucher (নতুন আয় যুক্ত করুন)</h3>
                <p class="text-xs text-slate-400">Record incoming money and automatically debit bank/cash and credit income head</p>
            </div>
        </div>

        <form action="{{ route('factory.accounts.income.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Voucher Date -->
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Voucher Date <span class="text-rose-400">*</span></label>
                    <input type="date" name="voucher_date" value="{{ date('Y-m-d') }}" required
                           class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-emerald-500">
                </div>

                <!-- Income Category / Head (Dynamic: Turkey Bangla Products from Image 2 & General Heads) -->
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Income Head (আয়ের খাত) <span class="text-rose-400">*</span></label>
                    <select name="income_account_id" id="incomeAccountSelect" onchange="handleIncomeHeadChange(this)" required
                            class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-emerald-500">
                        <option value="">Select Income Account / Product</option>

                        @if(isset($products) && $products->count() > 0)
                        <optgroup label="🛍️ পণ্য বিক্রয় আয় (Turkey Bangla Consumer & Food - ৭টি পণ্য)">
                            @foreach($products as $prod)
                            <option value="13"
                                    data-is-product="true"
                                    data-product-name="{{ $prod->name }}"
                                    data-rate="{{ $prod->sell_price }}">
                                {{ $prod->name }} — ৳ {{ number_format($prod->sell_price, 2) }} / {{ $prod->unit->short_code ?? 'Pcs' }}
                            </option>
                            @endforeach
                        </optgroup>
                        @endif

                        <optgroup label="💼 সাধারণ আয়ের খাত (General Income Heads)">
                            @foreach($incomeAccounts as $acc)
                            <option value="{{ $acc->id }}" data-is-product="false" data-account-name="{{ $acc->name }}">
                                {{ $acc->name }}
                            </option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>

                <!-- Deposit To Account (User rule: strictly Cash in Hand) -->
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Deposit To (জমা হবে যে অ্যাকাউন্টে) <span class="text-rose-400">*</span></label>
                    <select name="deposit_account_id" required
                            class="w-full bg-slate-950/80 border border-emerald-500/50 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-emerald-500">
                        @foreach($liquidAccounts as $acc)
                        <option value="{{ $acc->id }}" {{ $loop->first ? 'selected' : '' }}>
                            💵 {{ $acc->name }} [Bal: ৳ {{ number_format($acc->current_balance, 2) }}]
                        </option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-emerald-400 mt-1 font-semibold flex items-center gap-1">
                        <i class="fa-solid fa-money-bill-wave text-[9px]"></i> শুধুমাত্র ক্যাশ ইন হ্যান্ড একাউন্ট (Cash Only)
                    </p>
                </div>

                <!-- Amount -->
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Income Amount (পরিমাণ ৳) <span class="text-rose-400">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-slate-400 font-bold">৳</span>
                        <input type="number" step="0.01" min="0.01" name="amount" id="incomeAmountInput" placeholder="0.00" required
                               class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl pl-8 pr-3 py-2.5 text-slate-200 font-mono font-bold text-sm focus:outline-none focus:border-emerald-500">
                    </div>
                </div>

                <!-- Customer (Optional) -->
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Customer (গ্রাহক - যদি থাকে)</label>
                    <select name="customer_id" id="incomeCustomerSelect" onchange="autoFillPayer()"
                            class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-emerald-500">
                        <option value="">Direct / General (No Customer)</option>
                        @foreach($customers as $c)
                        <option value="{{ $c->id }}" data-name="{{ $c->company_name }}">{{ $c->company_name }} ({{ $c->phone }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Received From (Party Name) -->
                <div class="sm:col-span-2">
                    <label class="block text-slate-300 font-bold mb-1">Received From (প্রদানকারী ব্যক্তির নাম/কোম্পানি)</label>
                    <input type="text" name="party_name" id="incomePartyName" placeholder="e.g. Md. Hasan / Turkish Cafe & Resto"
                           class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-emerald-500">
                </div>

                <!-- Method & Ref comment kore rakha holo (Hidden default to cash) -->
                <input type="hidden" name="payment_method" value="cash">
                <!--
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Payment Method (পরিশোধ মাধ্যম) <span class="text-rose-400">*</span></label>
                    <select name="payment_method">
                        <option value="cash" selected>Cash (নগদ টাকা)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Reference No / Cheque No / TrxID</label>
                    <input type="text" name="reference_no" placeholder="e.g. CHQ-981245 or TRX-87234">
                </div>
                -->
            </div>

            <!-- Notes & Attachment -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Receipt Slip / Attachment (রসিদের ছবি বা ফাইল)</label>
                    <input type="file" name="attachment" accept="image/*,.pdf"
                           class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2 text-slate-400 text-xs focus:outline-none file:mr-3 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-xs file:bg-emerald-600 file:text-white">
                </div>
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Notes / Description (বিবরণ)</label>
                    <input type="text" name="notes" id="incomeNotesInput" placeholder="e.g. Jumbo straw sales / Delivery collection"
                           class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-emerald-500">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <button type="button" onclick="closeIncomeModal()" 
                        class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold transition-all">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold shadow-lg shadow-emerald-500/25 transition-all">
                    <i class="fa-solid fa-check mr-1.5"></i> Save & Generate Voucher
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: VOUCHER DETAILS -->
<div id="voucherDetailsModal" onclick="if(event.target === this) closeVoucherDetailsModal()" class="fixed inset-0 z-[9999] bg-slate-950/85 hidden flex items-center justify-center p-4 overflow-y-auto transform-gpu">
    <div onclick="event.stopPropagation()" class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-xl max-h-[90vh] overflow-y-auto shadow-2xl p-6 relative">
        <button type="button" onclick="closeVoucherDetailsModal()" class="absolute top-5 right-5 text-slate-400 hover:text-white text-lg cursor-pointer">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="flex items-center gap-3 border-b border-slate-800 pb-4 mb-5">
            <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center">
                <i class="fa-solid fa-file-invoice text-lg"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 id="detVoucherNo" class="text-base font-extrabold text-white font-mono"></h3>
                    <span id="detDeliveryBadge" class="hidden px-2 py-0.5 rounded text-[10px] font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                        <i class="fa-solid fa-truck text-[9px] mr-1"></i> Delivery Sales
                    </span>
                </div>
                <p id="detVoucherDate" class="text-xs text-slate-400 mt-0.5 font-mono"></p>
            </div>
        </div>

        <div class="space-y-4 text-xs">
            <!-- Summary Grid -->
            <div class="grid grid-cols-2 gap-3 p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800/80">
                <div>
                    <span class="text-slate-400 block text-[11px]">Income Head (Credit A/C):</span>
                    <span id="detIncomeHead" class="font-bold text-emerald-400 text-sm block mt-0.5"></span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Deposit To / Receivable (Debit):</span>
                    <span id="detDepositTo" class="font-bold text-cyan-400 text-sm block mt-0.5"></span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Received From / Customer:</span>
                    <span id="detPartyName" class="font-semibold text-white block mt-0.5"></span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Total Amount:</span>
                    <span class="font-mono font-black text-emerald-400 text-base block mt-0.5">৳ <span id="detAmount"></span></span>
                </div>
            </div>

            <!-- Meta details -->
            <div class="grid grid-cols-2 gap-3 p-3.5 rounded-2xl bg-slate-950/40 border border-slate-800/60">
                <div>
                    <span class="text-slate-400 block text-[11px]">Payment Method:</span>
                    <span id="detPaymentMethod" class="font-medium text-slate-200 mt-0.5 block"></span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Ref / Challan No:</span>
                    <span id="detRefNo" class="font-mono text-slate-200 mt-0.5 block"></span>
                </div>
                <div class="col-span-2">
                    <span class="text-slate-400 block text-[11px]">Full Narration / Notes:</span>
                    <p id="detNotes" class="text-slate-300 mt-1 bg-slate-900/90 p-2.5 rounded-xl border border-slate-800 text-[11px] leading-relaxed break-words"></p>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Recorded By:</span>
                    <span id="detCreatedBy" class="text-slate-300 mt-0.5 block"></span>
                </div>
                <div id="detAttachmentContainer" class="hidden">
                    <span class="text-slate-400 block text-[11px]">Attachment:</span>
                    <a id="detAttachmentLink" href="#" target="_blank" class="text-cyan-400 hover:underline flex items-center gap-1 mt-0.5">
                        <i class="fa-solid fa-paperclip"></i> View File
                    </a>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="flex items-center justify-between pt-3 border-t border-slate-800">
                <button type="button" onclick="closeVoucherDetailsModal()" 
                        class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold transition-all">
                    Close
                </button>
                <a id="detPrintBtn" href="#" target="_blank" 
                   class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold flex items-center gap-1.5 shadow-lg shadow-emerald-600/30 transition-all">
                    <i class="fa-solid fa-print"></i>
                    <span>Print Money Receipt</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function openIncomeModal() {
    const m = document.getElementById('incomeModal');
    if (m) m.classList.remove('hidden');
}
function closeIncomeModal() {
    const m = document.getElementById('incomeModal');
    if (m) m.classList.add('hidden');
}
function autoFillPayer() {
    const sel = document.getElementById('incomeCustomerSelect');
    if (!sel) return;
    const opt = sel.options[sel.selectedIndex];
    const name = opt ? opt.getAttribute('data-name') : '';
    if (name) {
        document.getElementById('incomePartyName').value = name;
    }
}
function handleIncomeHeadChange(select) {
    if (!select) return;
    const opt = select.options[select.selectedIndex];
    if (!opt) return;

    const isProduct = opt.getAttribute('data-is-product') === 'true';
    const prodName = opt.getAttribute('data-product-name');
    const rate = opt.getAttribute('data-rate');

    const amountInput = document.getElementById('incomeAmountInput');
    const notesInput = document.getElementById('incomeNotesInput');

    if (isProduct && prodName) {
        if (notesInput && (!notesInput.value || notesInput.value.startsWith('Product Sales:') || notesInput.value.startsWith('Sales of '))) {
            notesInput.value = 'Sales of ' + prodName + ' (Rate: ৳ ' + rate + ')';
        }
        if (amountInput && (!amountInput.value || parseFloat(amountInput.value) <= 0)) {
            amountInput.value = rate;
        }
    }
}
function showVoucherDetails(arg) {
    let data = arg;
    if (arg instanceof HTMLElement) {
        try {
            data = JSON.parse(arg.getAttribute('data-voucher'));
        } catch(e) {
            console.error('Failed to parse voucher data:', e);
            return;
        }
    }

    if (!data) return;

    document.getElementById('detVoucherNo').textContent = data.voucher_no || '';
    document.getElementById('detVoucherDate').textContent = 'Voucher Date: ' + (data.voucher_date || '');
    document.getElementById('detIncomeHead').textContent = data.income_head || '';
    document.getElementById('detDepositTo').textContent = data.deposit_to || '';
    document.getElementById('detPartyName').textContent = data.party_name || '';
    document.getElementById('detAmount').textContent = data.amount || '0.00';
    document.getElementById('detPaymentMethod').textContent = data.payment_method || '';
    document.getElementById('detRefNo').textContent = data.reference_no || 'N/A';
    document.getElementById('detNotes').textContent = data.notes || 'N/A';
    document.getElementById('detCreatedBy').textContent = data.created_by || 'System';
    document.getElementById('detPrintBtn').href = data.print_url || '#';

    const badge = document.getElementById('detDeliveryBadge');
    if (badge) {
        if (data.is_delivery) {
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }

    const attContainer = document.getElementById('detAttachmentContainer');
    const attLink = document.getElementById('detAttachmentLink');
    if (attContainer && attLink) {
        if (data.attachment_url) {
            attContainer.classList.remove('hidden');
            attLink.href = data.attachment_url;
        } else {
            attContainer.classList.add('hidden');
        }
    }

    const m = document.getElementById('voucherDetailsModal');
    if (m) m.classList.remove('hidden');
}

function closeVoucherDetailsModal() {
    const m = document.getElementById('voucherDetailsModal');
    if (m) m.classList.add('hidden');
}

// Close modals on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeIncomeModal();
        closeVoucherDetailsModal();
    }
});
</script>
@endsection

