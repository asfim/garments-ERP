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

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Add New Income Head Button (Manual Popup) -->
            <button type="button" onclick="openIncomeHeadModal()" 
                    class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-cyan-300 font-bold text-xs border border-cyan-500/30 transition-all flex items-center gap-1.5 cursor-pointer shadow-md">
                <i class="fa-solid fa-folder-plus text-cyan-400"></i>
                <span>+ Add Income Head</span>
            </button>

            <!-- Create New Income Voucher Button -->
            <button type="button" onclick="openIncomeModal()" 
                    class="px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs shadow-lg shadow-emerald-500/25 transition-all flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-plus-circle"></i>
                <span>+ New Income Voucher</span>
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

                <!-- Income Category / Head (Independent Income Accounts - No Products) -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-slate-300 font-bold">Income Head (আয়ের খাত) <span class="text-rose-400">*</span></label>
                        <button type="button" onclick="openIncomeHeadModal()" class="text-[11px] text-cyan-400 hover:text-cyan-300 font-bold flex items-center gap-1 hover:underline cursor-pointer">
                            <i class="fa-solid fa-plus text-[10px]"></i> + Add Head
                        </button>
                    </div>
                    <select name="income_account_id" id="incomeAccountSelect" required
                            class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-emerald-500">
                        <option value="">Select Income Account</option>
                        @foreach($incomeAccounts as $acc)
                        <option value="{{ $acc->id }}">
                            {{ $acc->name }} ({{ $acc->category }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Deposit To Account (User rule: strictly Cash in Hand) -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-slate-300 font-bold">Deposit To (জমা হবে যে অ্যাকাউন্টে) <span class="text-rose-400">*</span></label>
                        <span class="text-[10px] px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 font-bold uppercase tracking-wider">
                            <i class="fa-solid fa-lock text-[9px] mr-1"></i> Cash Only
                        </span>
                    </div>
                    <select name="deposit_account_id" required
                            class="w-full bg-slate-950/80 border border-emerald-500/50 rounded-xl px-3.5 py-2.5 text-emerald-300 font-semibold focus:outline-none focus:border-emerald-500">
                        @foreach($liquidAccounts as $acc)
                        <option value="{{ $acc->id }}" selected>
                            💵 {{ $acc->name }} [Bal: ৳ {{ number_format($acc->current_balance, 2) }}]
                        </option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-emerald-400 mt-1 font-semibold flex items-center gap-1">
                        <i class="fa-solid fa-check-circle text-[9px]"></i> সমস্ত আয় শুধুমাত্র প্রধান ক্যাশ ইন হ্যান্ডে (ক্যাশ অনলি) জমা হবে।
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

<!-- MODAL: ADD NEW INCOME HEAD (ম্যানুয়ালি নতুন আয়ের খাত যুক্ত করুন) -->
<div id="incomeHeadModal" onclick="if(event.target === this) closeIncomeHeadModal()" class="fixed inset-0 z-[10000] bg-slate-950/85 backdrop-blur-sm hidden flex items-center justify-center p-4 overflow-y-auto transform-gpu">
    <div onclick="event.stopPropagation()" class="bg-slate-900 border border-slate-700/80 rounded-3xl w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl p-6 relative my-8">
        <button type="button" onclick="closeIncomeHeadModal()" class="absolute top-5 right-5 text-slate-400 hover:text-white text-lg cursor-pointer">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="flex items-center gap-3 border-b border-slate-800 pb-4 mb-5">
            <div class="w-10 h-10 rounded-xl bg-cyan-500/20 text-cyan-400 border border-cyan-500/30 flex items-center justify-center" id="incomeHeadModalIconWrap">
                <i class="fa-solid fa-folder-plus text-lg" id="incomeHeadModalIcon"></i>
            </div>
            <div>
                <h3 class="text-base font-extrabold text-white" id="incomeHeadModalTitle">Add New Income Head (নতুন আয়ের খাত)</h3>
                <p class="text-xs text-slate-400" id="incomeHeadModalSubtitle">ম্যানুয়ালি নিজস্ব আয়ের খাত তৈরি ও পরিচালনা করুন (এটি পণ্যের তালিকা থেকে স্বাধীন)</p>
            </div>
        </div>

        <form id="incomeHeadForm" onsubmit="submitIncomeHeadForm(event)" action="{{ route('factory.accounts.income-heads.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <input type="hidden" name="head_id" id="edit_head_id" value="">

            <!-- Income Head Name -->
            <div>
                <label class="block text-slate-300 font-bold mb-1.5">Income Head Name (আয়ের খাতের নাম) <span class="text-rose-400">*</span></label>
                <input type="text" name="name" id="new_head_name" placeholder="e.g. ক্যাশ ডিসকাউন্ট আয় / স্পেশাল সার্ভিস ফি / কার্গো চার্জ" required
                       class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-400">
            </div>

            <!-- Category -->
            <div>
                <label class="block text-slate-300 font-bold mb-1.5">Category (আয়ের শ্রেণি/ক্যাটাগরি) <span class="text-rose-400">*</span></label>
                <select name="category" id="new_head_category" required
                        class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-400">
                    <option value="Operating Income">Operating Income (পরিচালন আয়)</option>
                    <option value="Service Income">Service Income (সেবা আয়)</option>
                    <option value="Other Income" selected>Other Income (অন্যান্য বিবিধ আয়)</option>
                </select>
            </div>

            <!-- Account Code (Optional / Auto-generated) -->
            <div>
                <label class="block text-slate-300 font-bold mb-1.5">
                    Account Code (অ্যাকাউন্ট কোড) 
                    <span class="text-slate-400 text-[10px] font-normal">(ফাঁকা রাখলে স্বয়ংক্রিয় তৈরি হবে)</span>
                </label>
                <input type="text" name="account_code" id="new_head_code" placeholder="Auto e.g. 4005"
                       class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-slate-200 font-mono focus:outline-none focus:border-cyan-400">
            </div>

            <!-- Description -->
            <div>
                <label class="block text-slate-300 font-bold mb-1.5">Description (বিবরণ / নোট)</label>
                <textarea name="description" id="new_head_description" rows="2" placeholder="খাত সম্পর্কিত সংক্ষিপ্ত তথ্য..."
                          class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3.5 py-2 text-slate-200 focus:outline-none focus:border-cyan-400"></textarea>
            </div>

            <div id="incomeHeadModalAlert" class="hidden p-2.5 rounded-xl text-xs font-semibold"></div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                <button type="button" id="cancelEditIncomeHeadBtn" onclick="resetIncomeHeadForm()"
                        class="hidden px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-300 font-bold border border-amber-500/30 transition-all cursor-pointer">
                    <i class="fa-solid fa-arrow-rotate-left mr-1"></i> Cancel Edit (বাতিল)
                </button>
                <button type="button" onclick="closeIncomeHeadModal()" 
                        class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold transition-all cursor-pointer">
                    Close
                </button>
                <button type="submit" id="saveIncomeHeadBtn"
                        class="px-5 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold shadow-lg shadow-cyan-500/25 transition-all flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-check" id="saveIncomeHeadIcon"></i>
                    <span id="saveIncomeHeadText">Save Income Head</span>
                </button>
            </div>
        </form>

        <!-- Currently Registered Income Heads List -->
        <div class="mt-6 pt-5 border-t border-slate-800">
            <h4 class="text-xs font-bold text-slate-300 mb-2.5 flex items-center justify-between">
                <span>বর্তমান আয়ের খাতসমূহ (Active Income Heads):</span>
                <span class="text-cyan-400 font-mono text-[11px]" id="incomeHeadsCountBadge">{{ $incomeAccounts->count() }} Heads</span>
            </h4>
            <div class="max-h-52 overflow-y-auto space-y-2 pr-1 text-[11px]" id="existingIncomeHeadsList">
                @foreach($incomeAccounts as $acc)
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-950/60 border border-slate-800/80 text-slate-300 hover:border-slate-700 transition-all" id="income-head-row-{{ $acc->id }}">
                    <div class="flex items-center gap-2 min-w-0 pr-2">
                        <span class="font-mono text-cyan-400 font-bold text-[10px] bg-cyan-500/10 px-1.5 py-0.5 rounded border border-cyan-500/20" id="head-code-label-{{ $acc->id }}">{{ $acc->account_code }}</span>
                        <div class="truncate">
                            <span class="font-semibold text-white block truncate" id="head-name-label-{{ $acc->id }}">{{ $acc->name }}</span>
                            <span class="text-[9px] text-slate-400" id="head-cat-label-{{ $acc->id }}">{{ $acc->category }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <!-- Edit Button -->
                        <button type="button" 
                                data-id="{{ $acc->id }}"
                                data-name="{{ $acc->name }}"
                                data-category="{{ $acc->category }}"
                                data-code="{{ $acc->account_code }}"
                                data-description="{{ $acc->description ?? '' }}"
                                onclick="editIncomeHeadFromBtn(this)"
                                title="Edit this income head"
                                class="px-2.5 py-1 rounded-lg bg-sky-500/15 hover:bg-sky-500/30 text-sky-400 border border-sky-500/30 text-[10px] font-bold flex items-center gap-1 transition-all cursor-pointer">
                            <i class="fa-solid fa-pen-to-square text-[9px]"></i>
                            <span>Edit</span>
                        </button>
                        <!-- Delete Button -->
                        @if(!$acc->is_system)
                        <button type="button" 
                                data-id="{{ $acc->id }}"
                                data-name="{{ $acc->name }}"
                                onclick="deleteIncomeHeadFromBtn(this)"
                                title="Delete this income head"
                                class="px-2.5 py-1 rounded-lg bg-rose-500/15 hover:bg-rose-500/30 text-rose-400 border border-rose-500/30 text-[10px] font-bold flex items-center gap-1 transition-all cursor-pointer">
                            <i class="fa-solid fa-trash-can text-[9px]"></i>
                            <span>Delete</span>
                        </button>
                        @else
                        <span class="text-[9px] text-slate-500 italic px-1.5 py-0.5 rounded bg-slate-800/60 border border-slate-700/50">System</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
// 1. Income Voucher Modal Handlers
function openIncomeModal() {
    const m = document.getElementById('incomeModal');
    if (m) m.classList.remove('hidden');
}
function closeIncomeModal() {
    const m = document.getElementById('incomeModal');
    if (m) m.classList.add('hidden');
}

// 2. Income Head Modal Handlers (Manual Add)
function openIncomeHeadModal() {
    const m = document.getElementById('incomeHeadModal');
    if (m) m.classList.remove('hidden');
}
function closeIncomeHeadModal() {
    const m = document.getElementById('incomeHeadModal');
    if (m) m.classList.add('hidden');
    resetIncomeHeadForm();
}

function editIncomeHeadFromBtn(btn) {
    if (!btn) return;
    const data = {
        id: btn.getAttribute('data-id'),
        name: btn.getAttribute('data-name'),
        category: btn.getAttribute('data-category'),
        account_code: btn.getAttribute('data-code'),
        description: btn.getAttribute('data-description') || ''
    };
    editIncomeHead(data);
}

function deleteIncomeHeadFromBtn(btn) {
    if (!btn) return;
    const id = btn.getAttribute('data-id');
    const name = btn.getAttribute('data-name');
    deleteIncomeHead(id, name);
}

function editIncomeHead(data) {
    document.getElementById('edit_head_id').value = data.id;
    document.getElementById('new_head_name').value = data.name;
    document.getElementById('new_head_category').value = data.category;
    document.getElementById('new_head_code').value = data.account_code || '';
    document.getElementById('new_head_description').value = data.description || '';

    // Update modal UI for edit mode
    document.getElementById('incomeHeadModalTitle').textContent = 'Edit Income Head (খাত সম্পাদনা)';
    document.getElementById('incomeHeadModalSubtitle').textContent = 'আয়ের খাতের নাম, ক্যাটাগরি ও বিবরণ পরিবর্তন করুন';
    document.getElementById('saveIncomeHeadText').textContent = 'Update Income Head';
    document.getElementById('saveIncomeHeadIcon').className = 'fa-solid fa-arrows-rotate';
    document.getElementById('cancelEditIncomeHeadBtn').classList.remove('hidden');

    const alertBox = document.getElementById('incomeHeadModalAlert');
    alertBox.className = 'hidden';

    // Highlight row
    document.querySelectorAll('#existingIncomeHeadsList > div').forEach(el => el.classList.remove('ring-2', 'ring-cyan-400'));
    const row = document.getElementById('income-head-row-' + data.id);
    if (row) row.classList.add('ring-2', 'ring-cyan-400');

    // Smooth scroll modal to top
    const container = document.getElementById('incomeHeadModal').firstElementChild;
    if (container) container.scrollTo({ top: 0, behavior: 'smooth' });
}

function resetIncomeHeadForm() {
    document.getElementById('incomeHeadForm').reset();
    document.getElementById('edit_head_id').value = '';
    document.getElementById('incomeHeadModalTitle').textContent = 'Add New Income Head (নতুন আয়ের খাত)';
    document.getElementById('incomeHeadModalSubtitle').textContent = 'ম্যানুয়ালি নিজস্ব আয়ের খাত তৈরি ও পরিচালনা করুন (এটি পণ্যের তালিকা থেকে স্বাধীন)';
    document.getElementById('saveIncomeHeadText').textContent = 'Save Income Head';
    document.getElementById('saveIncomeHeadIcon').className = 'fa-solid fa-check';
    document.getElementById('cancelEditIncomeHeadBtn').classList.add('hidden');

    const alertBox = document.getElementById('incomeHeadModalAlert');
    alertBox.className = 'hidden';

    document.querySelectorAll('#existingIncomeHeadsList > div').forEach(el => el.classList.remove('ring-2', 'ring-cyan-400'));
}

function submitIncomeHeadForm(e) {
    e.preventDefault();
    const form = document.getElementById('incomeHeadForm');
    const formData = new FormData(form);
    const saveBtn = document.getElementById('saveIncomeHeadBtn');
    const alertBox = document.getElementById('incomeHeadModalAlert');
    const editId = document.getElementById('edit_head_id').value;

    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';
    alertBox.className = 'hidden';

    let url = form.action;
    if (editId) {
        url = '{{ url("/factory/accounts/income-heads") }}/' + editId;
        formData.append('_method', 'PUT');
    }

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(async res => {
        const data = await res.json();
        saveBtn.disabled = false;
        saveBtn.innerHTML = editId ? '<i class="fa-solid fa-arrows-rotate"></i> <span>Update Income Head</span>' : '<i class="fa-solid fa-check"></i> <span>Save Income Head</span>';

        if (res.ok && data.success && data.account) {
            const acc = data.account;

            if (editId) {
                // 1. Update Existing Row in List
                const nameLabel = document.getElementById('head-name-label-' + acc.id);
                const catLabel = document.getElementById('head-cat-label-' + acc.id);
                const codeLabel = document.getElementById('head-code-label-' + acc.id);
                if (nameLabel) nameLabel.textContent = acc.name;
                if (catLabel) catLabel.textContent = acc.category;
                if (codeLabel) codeLabel.textContent = acc.account_code || 'N/A';

                // 2. Update Income Voucher dropdown
                const voucherSelect = document.getElementById('incomeAccountSelect');
                if (voucherSelect) {
                    const opt = voucherSelect.querySelector('option[value="' + acc.id + '"]');
                    if (opt) opt.textContent = acc.name + ' (' + acc.category + ')';
                }

                // 3. Update filter dropdown
                const filterSelect = document.querySelector('select[name="income_account_id"]');
                if (filterSelect) {
                    const fOpt = filterSelect.querySelector('option[value="' + acc.id + '"]');
                    if (fOpt) fOpt.textContent = acc.name;
                }

                alertBox.textContent = 'আয়ের খাত "' + acc.name + '" সফলভাবে আপডেট করা হয়েছে!';
                alertBox.className = 'p-2.5 rounded-xl text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 block';
                resetIncomeHeadForm();
            } else {
                // 1. Append to Income Voucher dropdown & select it
                const voucherSelect = document.getElementById('incomeAccountSelect');
                if (voucherSelect) {
                    const opt = document.createElement('option');
                    opt.value = acc.id;
                    opt.textContent = acc.name + ' (' + acc.category + ')';
                    opt.selected = true;
                    voucherSelect.appendChild(opt);
                    voucherSelect.value = acc.id;
                }

                // 2. Append to filter dropdown
                const filterSelect = document.querySelector('select[name="income_account_id"]');
                if (filterSelect) {
                    const fOpt = document.createElement('option');
                    fOpt.value = acc.id;
                    fOpt.textContent = acc.name;
                    filterSelect.appendChild(fOpt);
                }

                // 3. Append to existing heads list
                const list = document.getElementById('existingIncomeHeadsList');
                if (list) {
                    const item = document.createElement('div');
                    item.id = 'income-head-row-' + acc.id;
                    item.className = 'flex items-center justify-between p-2.5 rounded-xl bg-slate-950/60 border border-slate-800/80 text-slate-300 hover:border-slate-700 transition-all';
                    
                    item.innerHTML = `
                        <div class="flex items-center gap-2 min-w-0 pr-2">
                            <span class="font-mono text-cyan-400 font-bold text-[10px] bg-cyan-500/10 px-1.5 py-0.5 rounded border border-cyan-500/20" id="head-code-label-${acc.id}">${acc.account_code || 'N/A'}</span>
                            <div class="truncate">
                                <span class="font-semibold text-white block truncate" id="head-name-label-${acc.id}">${acc.name}</span>
                                <span class="text-[9px] text-slate-400" id="head-cat-label-${acc.id}">${acc.category}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <button type="button" 
                                    data-id="${acc.id}"
                                    data-name="${acc.name}"
                                    data-category="${acc.category}"
                                    data-code="${acc.account_code || ''}"
                                    data-description="${acc.description || ''}"
                                    onclick="editIncomeHeadFromBtn(this)"
                                    title="Edit this income head"
                                    class="px-2.5 py-1 rounded-lg bg-sky-500/15 hover:bg-sky-500/30 text-sky-400 border border-sky-500/30 text-[10px] font-bold flex items-center gap-1 transition-all cursor-pointer">
                                <i class="fa-solid fa-pen-to-square text-[9px]"></i>
                                <span>Edit</span>
                            </button>
                            <button type="button" 
                                    data-id="${acc.id}"
                                    data-name="${acc.name}"
                                    onclick="deleteIncomeHeadFromBtn(this)"
                                    title="Delete this income head"
                                    class="px-2.5 py-1 rounded-lg bg-rose-500/15 hover:bg-rose-500/30 text-rose-400 border border-rose-500/30 text-[10px] font-bold flex items-center gap-1 transition-all cursor-pointer">
                                <i class="fa-solid fa-trash-can text-[9px]"></i>
                                <span>Delete</span>
                            </button>
                        </div>
                    `;
                    list.insertBefore(item, list.firstChild);
                }

                // Update count badge
                const countBadge = document.getElementById('incomeHeadsCountBadge');
                if (countBadge) {
                    const current = parseInt(countBadge.textContent) || 0;
                    countBadge.textContent = (current + 1) + ' Heads';
                }

                form.reset();
                alertBox.textContent = 'নতুন আয়ের খাত "' + acc.name + '" তৈরি ও নির্বাচিত হয়েছে!';
                alertBox.className = 'p-2.5 rounded-xl text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 block';
            }
        } else {
            alertBox.textContent = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Error saving income head.');
            alertBox.className = 'p-2.5 rounded-xl text-xs font-semibold bg-rose-500/20 text-rose-300 border border-rose-500/40 block';
        }
    })
    .catch(err => {
        saveBtn.disabled = false;
        saveBtn.innerHTML = editId ? '<i class="fa-solid fa-arrows-rotate"></i> <span>Update Income Head</span>' : '<i class="fa-solid fa-check"></i> <span>Save Income Head</span>';
        alertBox.textContent = 'Failed to save income head. Please check your network or input values.';
        alertBox.className = 'p-2.5 rounded-xl text-xs font-semibold bg-rose-500/20 text-rose-300 border border-rose-500/40 block';
    });
}

function deleteIncomeHead(id, name) {
    if (!confirm('আপনি কি নিশ্চিত যে আয়ের খাত "' + name + '" মুছে ফেলতে চান?')) {
        return;
    }

    const alertBox = document.getElementById('incomeHeadModalAlert');
    alertBox.className = 'hidden';

    const csrf = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '{{ csrf_token() }}';
    const formData = new FormData();
    formData.append('_token', csrf);
    formData.append('_method', 'DELETE');

    fetch('{{ url("/factory/accounts/income-heads") }}/' + id, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(async res => {
        const data = await res.json();
        if (res.ok && data.success) {
            // Remove from list
            const row = document.getElementById('income-head-row-' + id);
            if (row) {
                row.style.opacity = '0';
                row.style.transform = 'scale(0.95)';
                setTimeout(() => row.remove(), 250);
            }

            // Remove from Income Voucher dropdown
            const voucherSelect = document.getElementById('incomeAccountSelect');
            if (voucherSelect) {
                const opt = voucherSelect.querySelector('option[value="' + id + '"]');
                if (opt) opt.remove();
            }

            // Remove from filter dropdown
            const filterSelect = document.querySelector('select[name="income_account_id"]');
            if (filterSelect) {
                const fOpt = filterSelect.querySelector('option[value="' + id + '"]');
                if (fOpt) fOpt.remove();
            }

            // Decrement badge count
            const countBadge = document.getElementById('incomeHeadsCountBadge');
            if (countBadge) {
                const current = parseInt(countBadge.textContent) || 0;
                countBadge.textContent = Math.max(0, current - 1) + ' Heads';
            }

            alertBox.textContent = data.message || 'আয়ের খাতটি সফলভাবে মুছে ফেলা হয়েছে!';
            alertBox.className = 'p-2.5 rounded-xl text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 block';
        } else {
            alertBox.textContent = data.message || 'খাতটি মুছে ফেলা সম্ভব হয়নি।';
            alertBox.className = 'p-2.5 rounded-xl text-xs font-semibold bg-rose-500/20 text-rose-300 border border-rose-500/40 block';
        }
    })
    .catch(err => {
        alertBox.textContent = 'Server error occurred while deleting income head.';
        alertBox.className = 'p-2.5 rounded-xl text-xs font-semibold bg-rose-500/20 text-rose-300 border border-rose-500/40 block';
    });
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
        closeIncomeHeadModal();
        closeIncomeModal();
        closeVoucherDetailsModal();
    }
});
</script>
@endsection

