@extends('layouts.app')

@section('title', 'Expense Management | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-money-bill-wave text-rose-400"></i>
                Expense Management
            </h2>
            <p class="text-xs text-slate-400">Track raw materials purchase, rent, utilities, maintenance, wages, and operating expenses</p>
        </div>

        <div class="flex items-center gap-2.5">
            <button type="button" onclick="openExpenseHeadModal()" 
                    class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-rose-300 font-bold text-xs border border-rose-500/30 transition-all flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-folder-plus text-rose-400"></i>
                <span>+ Add Expense Head</span>
            </button>
            <button type="button" onclick="openExpenseModal()" 
                    class="px-4 py-2 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 text-white font-bold text-xs shadow-lg shadow-rose-500/25 transition-all flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-plus-circle"></i>
                <span>+ New Expense Voucher</span>
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
        <div class="glass-panel p-4 rounded-2xl border border-rose-500/30 bg-gradient-to-br from-rose-950/20 to-slate-900/40 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-rose-400 block">Total Filtered Expense</span>
                <h3 class="text-2xl font-black text-white font-mono mt-1">৳ {{ number_format($totalFilteredExpense, 2) }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center">
                <i class="fa-solid fa-receipt text-lg"></i>
            </div>
        </div>

        <div class="glass-panel p-4 rounded-2xl border border-slate-800 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Total Expense Vouchers</span>
                <h3 class="text-2xl font-black text-white font-mono mt-1">{{ $vouchers->total() }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-800 text-slate-400 flex items-center justify-center">
                <i class="fa-solid fa-file-invoice text-lg"></i>
            </div>
        </div>

        <div class="glass-panel p-4 rounded-2xl border border-amber-500/30 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-400 block">Next Voucher Sequence</span>
                <h3 class="text-xl font-black text-amber-300 font-mono mt-1">{{ $nextVoucherNo }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center">
                <i class="fa-solid fa-barcode text-lg"></i>
            </div>
        </div>
    </div>

    <!-- FILTER & SEARCH BAR -->
    <div class="glass-panel p-4 rounded-2xl border border-slate-800/80">
        <form method="GET" action="{{ route('factory.accounts.expense.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 text-xs">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-slate-400 text-[10px] uppercase font-bold mb-1">Search Keyword</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Voucher #, payee name, ref, notes..." 
                       class="w-full bg-slate-900/80 border border-slate-700/80 rounded-xl px-3 py-2 text-slate-200 focus:outline-none focus:border-rose-500 text-xs">
            </div>

            <!-- From Date -->
            <div>
                <label class="block text-slate-400 text-[10px] uppercase font-bold mb-1">From Date</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" 
                       class="w-full bg-slate-900/80 border border-slate-700/80 rounded-xl px-3 py-2 text-slate-200 focus:outline-none focus:border-rose-500 text-xs">
            </div>

            <!-- To Date -->
            <div>
                <label class="block text-slate-400 text-[10px] uppercase font-bold mb-1">To Date</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" 
                       class="w-full bg-slate-900/80 border border-slate-700/80 rounded-xl px-3 py-2 text-slate-200 focus:outline-none focus:border-rose-500 text-xs">
            </div>

            <!-- Expense Head -->
            <div>
                <label class="block text-slate-400 text-[10px] uppercase font-bold mb-1">Expense Head</label>
                <select name="expense_account_id" class="w-full bg-slate-900/80 border border-slate-700/80 rounded-xl px-3 py-2 text-slate-200 focus:outline-none focus:border-rose-500 text-xs">
                    <option value="">All Expense Heads</option>
                    @foreach($expenseAccounts as $acc)
                    <option value="{{ $acc->id }}" {{ request('expense_account_id') == $acc->id ? 'selected' : '' }}>
                        {{ $acc->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-3 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold transition-all flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-filter text-xs"></i>
                    <span>Filter</span>
                </button>
                @if(request()->anyFilled(['search', 'from_date', 'to_date', 'expense_account_id', 'payment_method']))
                <a href="{{ route('factory.accounts.expense.index') }}" class="px-2.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold transition-all text-center" title="Reset Filters">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- EXPENSE VOUCHERS TABLE -->
    <div class="glass-panel rounded-2xl border border-slate-800/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/60 text-slate-400 uppercase text-[10px] font-bold border-b border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4">Voucher No</th>
                        <th class="py-3.5 px-4">Date</th>
                        <th class="py-3.5 px-4">Expense Head</th>
                        <th class="py-3.5 px-4">Paid To / Payee</th>
                        <th class="py-3.5 px-4 text-right">Amount (৳)</th>
                        <th class="py-3.5 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60" id="expenseTableBody">
                    @forelse($vouchers as $v)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="py-3.5 px-4 font-mono font-bold text-amber-400">
                            {{ $v->voucher_no }}
                        </td>
                        <td class="py-3.5 px-4 font-mono text-slate-400">
                            {{ $v->voucher_date->format('d M, Y') }}
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="text-rose-300 font-semibold block">
                                {{ $v->debitAccount?->name ?? 'Expense' }}
                            </span>
                            @if($v->debitAccount?->category)
                            <span class="text-[10px] text-slate-500 uppercase">{{ $v->debitAccount->category }}</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="text-white font-bold block truncate max-w-[220px]">
                                {{ $v->party_name ?: ($v->supplier?->name ?: ($v->employee?->name ?: 'General Payee')) }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right font-mono font-black text-sm text-rose-400">
                            - ৳ {{ number_format($v->amount, 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <!-- Details Button (Opens Clean Modal) -->
                                <button type="button" 
                                        data-voucher-no="{{ $v->voucher_no }}"
                                        data-date="{{ $v->voucher_date->format('d M, Y') }}"
                                        data-head="{{ $v->debitAccount?->name ?? 'Expense' }}"
                                        data-category="{{ $v->debitAccount?->category ?? 'Operating Expense' }}"
                                        data-paid-from="{{ $v->creditAccount?->name ?? 'Cash in Hand' }}"
                                        data-payee="{{ $v->party_name ?: ($v->supplier?->name ?: ($v->employee?->name ?: 'General Payee')) }}"
                                        data-supplier="{{ $v->supplier?->name ?? '' }}"
                                        data-employee="{{ $v->employee ? $v->employee->name . ' (' . $v->employee->employee_code . ')' : '' }}"
                                        data-method="{{ $v->payment_method_label }}"
                                        data-ref="{{ $v->reference_no ?? '' }}"
                                        data-notes="{{ $v->notes ?? '' }}"
                                        data-amount="৳ {{ number_format($v->amount, 2) }}"
                                        data-attachment="{{ $v->attachment ? asset('storage/' . $v->attachment) : '' }}"
                                        data-print-url="{{ route('factory.accounts.expense.print', $v->id) }}"
                                        onclick="openExpenseDetails(this)"
                                        class="px-2.5 py-1 rounded-lg bg-blue-500/15 hover:bg-blue-500/30 text-blue-400 border border-blue-500/30 text-[11px] font-bold transition-all flex items-center gap-1 cursor-pointer" 
                                        title="View Full Details">
                                    <i class="fa-solid fa-eye text-[10px]"></i>
                                    <span>Details</span>
                                </button>

                                <a href="{{ route('factory.accounts.expense.print', $v->id) }}" target="_blank" 
                                   class="p-1.5 rounded-lg bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/40 text-xs" title="Print Debit Voucher">
                                    <i class="fa-solid fa-print"></i>
                                </a>

                                @if($v->attachment)
                                <a href="{{ asset('storage/' . $v->attachment) }}" target="_blank" 
                                   class="p-1.5 rounded-lg bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-300 border border-cyan-500/40 text-xs" title="View Attachment">
                                    <i class="fa-solid fa-paperclip"></i>
                                </a>
                                @endif

                                <form action="{{ route('factory.accounts.vouchers.destroy', $v->id) }}" method="POST" 
                                      onsubmit="return confirm('Delete expense voucher {{ $v->voucher_no }}? The source account balance will be refunded.');">
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
                    <tr id="expenseEmptyRow">
                        <td colspan="6" class="py-12 text-center text-slate-500">
                            <i class="fa-solid fa-receipt text-3xl mb-2 opacity-50 block"></i>
                            No expense records found matching your filters.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- LOAD MORE & PAGINATION INFO -->
        <div id="expenseLoadMoreContainer" class="p-4 border-t border-slate-800 bg-slate-950/40 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="text-xs text-slate-400 font-mono">
                Showing <span id="expenseCurrentCount" class="text-rose-400 font-bold">{{ $vouchers->count() }}</span> of <span id="expenseTotalCount" class="text-white font-bold">{{ $vouchers->total() }}</span> records
            </div>

            @if($vouchers->hasMorePages())
            <button type="button" 
                    id="expenseLoadMoreBtn"
                    data-next-page="{{ $vouchers->currentPage() + 1 }}"
                    data-total-pages="{{ $vouchers->lastPage() }}"
                    onclick="loadMoreExpense()"
                    class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 text-white text-xs font-bold shadow-lg shadow-rose-950/50 flex items-center gap-2 transition-all cursor-pointer">
                <i class="fa-solid fa-spinner fa-spin hidden" id="expenseLoadSpinner"></i>
                <i class="fa-solid fa-circle-down" id="expenseLoadIcon"></i>
                <span id="expenseLoadText">Load More Records</span>
            </button>
            @else
            <div id="expenseAllLoaded" class="text-xs text-slate-500 font-semibold italic flex items-center gap-1.5">
                <i class="fa-solid fa-check text-rose-400"></i> All records loaded
            </div>
            @endif
        </div>
    </div>

</div>

<!-- MODAL: ADD NEW EXPENSE VOUCHER -->
<div id="expenseModal" onclick="if(event.target === this) closeExpenseModal()" class="fixed inset-0 z-[9999] bg-slate-950/85 hidden flex items-center justify-center p-4 overflow-y-auto transform-gpu">
    <div onclick="event.stopPropagation()" class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl p-6 relative">
        <button type="button" onclick="closeExpenseModal()" class="absolute top-5 right-5 text-slate-400 hover:text-white text-lg cursor-pointer">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="flex items-center gap-3 border-b border-slate-800 pb-4 mb-5">
            <div class="w-10 h-10 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center">
                <i class="fa-solid fa-receipt text-lg"></i>
            </div>
            <div>
                <h3 class="text-base font-extrabold text-white">Create Debit Voucher / Expense</h3>
                <p class="text-xs text-slate-400">Record payments for supplies, factory operations, maintenance, or overhead</p>
            </div>
        </div>

        <form action="{{ route('factory.accounts.expense.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Voucher Date -->
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Voucher Date <span class="text-rose-400">*</span></label>
                    <input type="date" name="voucher_date" value="{{ date('Y-m-d') }}" required
                           class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-rose-500">
                </div>

                <!-- Expense Head / Category -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-slate-300 font-bold">Expense Head <span class="text-rose-400">*</span></label>
                        <button type="button" onclick="openExpenseHeadModal()" 
                                class="text-[10px] text-rose-400 hover:text-rose-300 font-bold flex items-center gap-1 cursor-pointer">
                            <i class="fa-solid fa-plus-circle"></i> + Add Head
                        </button>
                    </div>
                    <select name="expense_account_id" id="expenseAccountSelect" required
                            class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-rose-500">
                        <option value="">Select Expense Account</option>
                        @foreach($expenseAccounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->category }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Paid From Account (User rule: strictly Cash in Hand) -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-slate-300 font-bold">Paid From <span class="text-rose-400">*</span></label>
                        <span class="text-[10px] px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 font-bold uppercase tracking-wider">
                            <i class="fa-solid fa-lock text-[9px] mr-1"></i> Cash Only
                        </span>
                    </div>
                    <select name="paid_from_account_id" required
                            class="w-full bg-slate-950/80 border border-emerald-500/50 rounded-xl px-3.5 py-2.5 text-emerald-300 font-semibold focus:outline-none focus:border-emerald-500">
                        @foreach($liquidAccounts as $acc)
                        <option value="{{ $acc->id }}" selected>
                            💵 {{ $acc->name }} [Bal: ৳ {{ number_format($acc->current_balance, 2) }}]
                        </option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-emerald-400 mt-1 font-semibold flex items-center gap-1">
                        <i class="fa-solid fa-check-circle text-[9px]"></i> All expenses are paid strictly from Cash in Hand.
                    </p>
                </div>

                <!-- Amount -->
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Expense Amount (৳) <span class="text-rose-400">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-slate-400 font-bold">৳</span>
                        <input type="number" step="0.01" min="0.01" name="amount" placeholder="0.00" required
                               class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl pl-8 pr-3 py-2.5 text-slate-200 font-mono font-bold text-sm focus:outline-none focus:border-rose-500">
                    </div>
                </div>

                <!-- Supplier (Optional) -->
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Supplier (Optional)</label>
                    <select name="supplier_id" id="expenseSupplierSelect" onchange="autoFillPayee('supplier')"
                            class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-rose-500">
                        <option value="">None / Direct Expense</option>
                        @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" data-name="{{ $s->name }}">{{ $s->name }} ({{ $s->phone }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Employee (Optional) -->
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Employee (Optional)</label>
                    <select name="employee_id" id="expenseEmployeeSelect" onchange="autoFillPayee('employee')"
                            class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-rose-500">
                        <option value="">None / Non-employee</option>
                        @foreach($employees as $e)
                        <option value="{{ $e->id }}" data-name="{{ $e->name }} ({{ $e->employee_code }})">{{ $e->name }} ({{ $e->employee_code }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Paid To (Payee Name) -->
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Paid To / Payee Name</label>
                    <input type="text" name="party_name" id="expensePartyName" placeholder="e.g. DESCO / Titas Gas / Khan Fabrics"
                           class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-rose-500">
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Payment Method <span class="text-rose-400">*</span></label>
                    <select name="payment_method" required
                            class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-rose-500">
                        <option value="cash">Cash</option>
                        <option value="bank">Bank Transfer</option>
                        <option value="cheque">Cheque</option>
                        <option value="bkash">bKash</option>
                        <option value="nagad">Nagad</option>
                        <option value="rocket">Rocket</option>
                        <option value="other">Other Method</option>
                    </select>
                </div>
            </div>

            <!-- Reference & Bill Slip -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Reference / Bill / Cheque No</label>
                    <input type="text" name="reference_no" placeholder="e.g. BILL-9823 or CHQ-0012"
                           class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-rose-500">
                </div>
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Cheque Date (if applicable)</label>
                    <input type="date" name="cheque_date"
                           class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-rose-500">
                </div>
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Bill / Voucher Attachment</label>
                    <input type="file" name="attachment" accept="image/*,.pdf"
                           class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2 text-slate-400 text-xs focus:outline-none file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-xs file:bg-rose-600 file:text-white">
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-slate-300 font-bold mb-1">Expense Purpose / Description</label>
                <input type="text" name="notes" placeholder="e.g. October 2026 factory electricity bill payment"
                       class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-rose-500">
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <button type="button" onclick="closeExpenseModal()" 
                        class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold transition-all">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 text-white font-bold shadow-lg shadow-rose-500/25 transition-all">
                    <i class="fa-solid fa-check mr-1.5"></i> Save & Record Debit Voucher
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: EXPENSE VOUCHER DETAILS POPUP -->
<div id="expenseDetailsModal" onclick="if(event.target === this) closeExpenseDetailsModal()" class="fixed inset-0 z-[10000] bg-slate-950/85 backdrop-blur-sm hidden flex items-center justify-center p-4 overflow-y-auto transform-gpu">
    <div onclick="event.stopPropagation()" class="bg-slate-900 border border-slate-700/80 rounded-3xl w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl p-6 relative my-8">
        <button type="button" onclick="closeExpenseDetailsModal()" class="absolute top-5 right-5 text-slate-400 hover:text-white text-lg cursor-pointer">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="flex items-center gap-3 border-b border-slate-800 pb-4 mb-5">
            <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-blue-400 border border-blue-500/30 flex items-center justify-center">
                <i class="fa-solid fa-file-invoice-dollar text-lg"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-base font-extrabold text-white" id="detExpVoucherNo">EV-000000</h3>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">DEBIT VOUCHER</span>
                </div>
                <p class="text-xs text-slate-400">Expense Voucher Full Particulars & Ledger Info</p>
            </div>
        </div>

        <div class="space-y-4 text-xs">
            <!-- Amount & Date Banner -->
            <div class="p-3.5 rounded-2xl bg-slate-950/80 border border-slate-800 flex items-center justify-between">
                <div>
                    <span class="text-slate-400 block text-[11px]">Expense Amount:</span>
                    <span class="text-rose-400 font-mono font-black text-xl" id="detExpAmount">৳ 0.00</span>
                </div>
                <div class="text-right">
                    <span class="text-slate-400 block text-[11px]">Voucher Date:</span>
                    <span class="text-slate-200 font-semibold" id="detExpVoucherDate">--</span>
                </div>
            </div>

            <!-- Expense Head & Category -->
            <div class="grid grid-cols-2 gap-3 p-3 rounded-xl bg-slate-950/40 border border-slate-800/80">
                <div>
                    <span class="text-slate-400 block text-[11px]">Expense Head:</span>
                    <span class="text-slate-200 font-bold block mt-0.5" id="detExpHead">--</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Category:</span>
                    <span class="text-slate-300 font-semibold block mt-0.5" id="detExpCategory">--</span>
                </div>
            </div>

            <!-- Paid From Account -->
            <div class="p-3 rounded-xl bg-slate-950/40 border border-slate-800/80">
                <span class="text-slate-400 block text-[11px]">Paid From:</span>
                <span class="text-emerald-400 font-bold flex items-center gap-1.5 mt-0.5">
                    <i class="fa-solid fa-money-bill-wave text-xs"></i>
                    <span id="detExpPaidFrom">Cash in Hand</span>
                </span>
            </div>

            <!-- Payee / Paid To -->
            <div class="p-3 rounded-xl bg-slate-950/40 border border-slate-800/80">
                <span class="text-slate-400 block text-[11px]">Paid To / Payee:</span>
                <span class="text-white font-bold block mt-0.5 text-sm" id="detExpPayee">--</span>
                <div id="detExpLinkedEntityWrap" class="hidden mt-1.5 pt-1.5 border-t border-slate-800/60 text-[11px] text-slate-400">
                    <span id="detExpLinkedEntity"></span>
                </div>
            </div>

            <!-- Payment Method & Reference -->
            <div class="grid grid-cols-2 gap-3 p-3 rounded-xl bg-slate-950/40 border border-slate-800/80">
                <div>
                    <span class="text-slate-400 block text-[11px]">Payment Method:</span>
                    <span class="text-slate-200 font-semibold capitalize block mt-0.5" id="detExpMethod">Cash</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Reference / Bill / Cheque No:</span>
                    <span class="text-slate-200 font-mono font-semibold block mt-0.5" id="detExpRef">N/A</span>
                </div>
            </div>

            <!-- Notes / Narration -->
            <div class="p-3 rounded-xl bg-slate-950/40 border border-slate-800/80">
                <span class="text-slate-400 block text-[11px]">Description / Purpose:</span>
                <p class="text-slate-300 mt-1 leading-relaxed italic" id="detExpNotes">No notes recorded.</p>
            </div>

            <!-- Attachment -->
            <div id="detExpAttachmentWrap" class="hidden p-3 rounded-xl bg-slate-950/40 border border-slate-800/80">
                <span class="text-slate-400 block text-[11px] mb-1.5">Attached Bill / Voucher:</span>
                <a id="detExpAttachmentLink" href="#" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-500/15 hover:bg-rose-500/25 text-rose-300 border border-rose-500/30 text-xs font-semibold transition-all">
                    <i class="fa-solid fa-paperclip"></i>
                    <span>View Attachment</span>
                </a>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-800">
                <a id="detExpPrintBtn" href="#" target="_blank" 
                   class="px-4 py-2 rounded-xl bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/40 font-bold transition-all flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-print"></i>
                    <span>Print Debit Voucher</span>
                </a>
                <button type="button" onclick="closeExpenseDetailsModal()" 
                        class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold transition-all cursor-pointer">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: ADD / EDIT EXPENSE HEAD -->
<div id="expenseHeadModal" onclick="if(event.target === this) closeExpenseHeadModal()" class="fixed inset-0 z-[10000] bg-slate-950/85 backdrop-blur-sm hidden flex items-center justify-center p-4 overflow-y-auto transform-gpu">
    <div onclick="event.stopPropagation()" class="bg-slate-900 border border-slate-700/80 rounded-3xl w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl p-6 relative my-8">
        <button type="button" onclick="closeExpenseHeadModal()" class="absolute top-5 right-5 text-slate-400 hover:text-white text-lg cursor-pointer">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="flex items-center gap-3 border-b border-slate-800 pb-4 mb-5">
            <div class="w-10 h-10 rounded-xl bg-rose-500/20 text-rose-400 border border-rose-500/30 flex items-center justify-center" id="expenseHeadModalIconWrap">
                <i class="fa-solid fa-folder-plus text-lg" id="expenseHeadModalIcon"></i>
            </div>
            <div>
                <h3 class="text-base font-extrabold text-white" id="expenseHeadModalTitle">Add New Expense Head</h3>
                <p class="text-xs text-slate-400" id="expenseHeadModalSubtitle">Create and manage custom expense heads</p>
            </div>
        </div>

        <form id="expenseHeadForm" onsubmit="submitExpenseHeadForm(event)" action="{{ route('factory.accounts.expense-heads.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <input type="hidden" name="head_id" id="edit_expense_head_id" value="">

            <!-- Expense Head Name -->
            <div>
                <label class="block text-slate-300 font-bold mb-1.5">Expense Head Name <span class="text-rose-400">*</span></label>
                <input type="text" name="name" id="new_expense_head_name" placeholder="e.g. Factory Electricity Bill / Office Refreshment / Transport" required
                       class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-rose-500">
            </div>

            <!-- Category -->
            <div>
                <label class="block text-slate-300 font-bold mb-1.5">Category <span class="text-rose-400">*</span></label>
                <select name="category" id="new_expense_head_category" required
                        class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-rose-500">
                    <option value="Direct Expense">Direct Expense</option>
                    <option value="Factory Overhead">Factory Overhead</option>
                    <option value="Administrative Expense" selected>Administrative Expense</option>
                    <option value="Selling & Distribution">Selling & Distribution</option>
                    <option value="Financial Expense">Financial Expense</option>
                    <option value="Other Expense">Other Expense</option>
                </select>
            </div>

            <!-- Account Code (Optional / Auto-generated) -->
            <div>
                <label class="block text-slate-300 font-bold mb-1.5">
                    Account Code 
                    <span class="text-slate-400 text-[10px] font-normal">(Leave blank for auto-generation)</span>
                </label>
                <input type="text" name="account_code" id="new_expense_head_code" placeholder="Auto e.g. 5015"
                       class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-slate-200 font-mono focus:outline-none focus:border-rose-500">
            </div>

            <!-- Description -->
            <div>
                <label class="block text-slate-300 font-bold mb-1.5">Description / Notes</label>
                <textarea name="description" id="new_expense_head_description" rows="2" placeholder="Brief notes about this expense head..."
                          class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3.5 py-2 text-slate-200 focus:outline-none focus:border-rose-500"></textarea>
            </div>

            <div id="expenseHeadModalAlert" class="hidden p-2.5 rounded-xl text-xs font-semibold"></div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                <button type="button" id="cancelEditExpenseHeadBtn" onclick="resetExpenseHeadForm()"
                        class="hidden px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-300 font-bold border border-amber-500/30 transition-all cursor-pointer">
                    <i class="fa-solid fa-arrow-rotate-left mr-1"></i> Cancel Edit
                </button>
                <button type="button" onclick="closeExpenseHeadModal()" 
                        class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold transition-all cursor-pointer">
                    Close
                </button>
                <button type="submit" id="saveExpenseHeadBtn"
                        class="px-5 py-2 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 text-white font-bold shadow-lg shadow-rose-500/25 transition-all flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-check" id="saveExpenseHeadIcon"></i>
                    <span id="saveExpenseHeadText">Save Expense Head</span>
                </button>
            </div>
        </form>

        <!-- Currently Registered Expense Heads List -->
        <div class="mt-6 pt-5 border-t border-slate-800">
            <h4 class="text-xs font-bold text-slate-300 mb-2.5 flex items-center justify-between">
                <span>Active Expense Heads:</span>
                <span class="text-rose-400 font-mono text-[11px]" id="expenseHeadsCountBadge">{{ $expenseAccounts->count() }} Heads</span>
            </h4>
            <div class="max-h-52 overflow-y-auto space-y-2 pr-1 text-[11px]" id="existingExpenseHeadsList">
                @foreach($expenseAccounts as $acc)
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-950/60 border border-slate-800/80 text-slate-300 hover:border-slate-700 transition-all" id="expense-head-row-{{ $acc->id }}">
                    <div class="flex items-center gap-2 min-w-0 pr-2">
                        <span class="font-mono text-rose-400 font-bold text-[10px] bg-rose-500/10 px-1.5 py-0.5 rounded border border-rose-500/20" id="exp-head-code-label-{{ $acc->id }}">{{ $acc->account_code }}</span>
                        <div class="truncate">
                            <span class="font-semibold text-white block truncate" id="exp-head-name-label-{{ $acc->id }}">{{ $acc->name }}</span>
                            <span class="text-[9px] text-slate-400" id="exp-head-cat-label-{{ $acc->id }}">{{ $acc->category }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <!-- Edit Button (Safe data-* attributes) -->
                        <button type="button" 
                                data-id="{{ $acc->id }}"
                                data-name="{{ $acc->name }}"
                                data-category="{{ $acc->category }}"
                                data-code="{{ $acc->account_code }}"
                                data-description="{{ $acc->description ?? '' }}"
                                onclick="editExpenseHeadFromBtn(this)"
                                title="Edit this expense head"
                                class="px-2.5 py-1 rounded-lg bg-sky-500/15 hover:bg-sky-500/30 text-sky-400 border border-sky-500/30 text-[10px] font-bold flex items-center gap-1 transition-all cursor-pointer">
                            <i class="fa-solid fa-pen-to-square text-[9px]"></i>
                            <span>Edit</span>
                        </button>
                        <!-- Delete Button (Safe data-* attributes) -->
                        @if(!$acc->is_system)
                        <button type="button" 
                                data-id="{{ $acc->id }}"
                                data-name="{{ $acc->name }}"
                                onclick="deleteExpenseHeadFromBtn(this)"
                                title="Delete this expense head"
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
// 1. Expense Voucher Create Modal Handlers
function openExpenseModal() {
    const m = document.getElementById('expenseModal');
    if (m) m.classList.remove('hidden');
}
function closeExpenseModal() {
    const m = document.getElementById('expenseModal');
    if (m) m.classList.add('hidden');
}

// 2. Expense Details Modal Handlers
function openExpenseDetails(btn) {
    if (!btn) return;
    const vNo = btn.getAttribute('data-voucher-no') || 'N/A';
    const date = btn.getAttribute('data-date') || 'N/A';
    const head = btn.getAttribute('data-head') || 'N/A';
    const cat = btn.getAttribute('data-category') || 'N/A';
    const paidFrom = btn.getAttribute('data-paid-from') || 'Cash in Hand';
    const payee = btn.getAttribute('data-payee') || 'General Payee';
    const supplier = btn.getAttribute('data-supplier') || '';
    const employee = btn.getAttribute('data-employee') || '';
    const method = btn.getAttribute('data-method') || 'Cash';
    const ref = btn.getAttribute('data-ref') || 'N/A';
    const notes = btn.getAttribute('data-notes') || 'No notes recorded.';
    const amount = btn.getAttribute('data-amount') || '৳ 0.00';
    const attachment = btn.getAttribute('data-attachment') || '';
    const printUrl = btn.getAttribute('data-print-url') || '#';

    document.getElementById('detExpVoucherNo').textContent = vNo;
    document.getElementById('detExpVoucherDate').textContent = date;
    document.getElementById('detExpHead').textContent = head;
    document.getElementById('detExpCategory').textContent = cat;
    document.getElementById('detExpPaidFrom').textContent = paidFrom;
    document.getElementById('detExpPayee').textContent = payee;
    document.getElementById('detExpMethod').textContent = method;
    document.getElementById('detExpRef').textContent = ref;
    document.getElementById('detExpNotes').textContent = notes;
    document.getElementById('detExpAmount').textContent = amount;

    // Linked Supplier / Employee details
    const linkedWrap = document.getElementById('detExpLinkedEntityWrap');
    const linkedText = document.getElementById('detExpLinkedEntity');
    if (supplier) {
        linkedWrap.classList.remove('hidden');
        linkedText.innerHTML = '<i class="fa-solid fa-truck-field text-amber-400 mr-1"></i> Supplier: <strong class="text-slate-200">' + supplier + '</strong>';
    } else if (employee) {
        linkedWrap.classList.remove('hidden');
        linkedText.innerHTML = '<i class="fa-solid fa-user-tie text-blue-400 mr-1"></i> Employee / Worker: <strong class="text-slate-200">' + employee + '</strong>';
    } else {
        linkedWrap.classList.add('hidden');
        linkedText.innerHTML = '';
    }

    // Attachment
    const attWrap = document.getElementById('detExpAttachmentWrap');
    const attLink = document.getElementById('detExpAttachmentLink');
    if (attachment) {
        attWrap.classList.remove('hidden');
        attLink.href = attachment;
    } else {
        attWrap.classList.add('hidden');
        attLink.href = '#';
    }

    // Print button
    const printBtn = document.getElementById('detExpPrintBtn');
    if (printBtn) printBtn.href = printUrl;

    const modal = document.getElementById('expenseDetailsModal');
    if (modal) modal.classList.remove('hidden');
}

function closeExpenseDetailsModal() {
    const modal = document.getElementById('expenseDetailsModal');
    if (modal) modal.classList.add('hidden');
}

// 3. Expense Head Modal Handlers (Manual Add / Edit / Delete)
function openExpenseHeadModal() {
    const m = document.getElementById('expenseHeadModal');
    if (m) m.classList.remove('hidden');
}

function closeExpenseHeadModal() {
    const m = document.getElementById('expenseHeadModal');
    if (m) m.classList.add('hidden');
    resetExpenseHeadForm();
}

function editExpenseHeadFromBtn(btn) {
    if (!btn) return;
    const data = {
        id: btn.getAttribute('data-id'),
        name: btn.getAttribute('data-name'),
        category: btn.getAttribute('data-category'),
        account_code: btn.getAttribute('data-code'),
        description: btn.getAttribute('data-description') || ''
    };
    editExpenseHead(data);
}

function deleteExpenseHeadFromBtn(btn) {
    if (!btn) return;
    const id = btn.getAttribute('data-id');
    const name = btn.getAttribute('data-name');
    deleteExpenseHead(id, name);
}

function editExpenseHead(data) {
    document.getElementById('edit_expense_head_id').value = data.id;
    document.getElementById('new_expense_head_name').value = data.name;
    document.getElementById('new_expense_head_category').value = data.category;
    document.getElementById('new_expense_head_code').value = data.account_code || '';
    document.getElementById('new_expense_head_description').value = data.description || '';

    // Update modal UI for edit mode
    document.getElementById('expenseHeadModalTitle').textContent = 'Edit Expense Head';
    document.getElementById('expenseHeadModalSubtitle').textContent = 'Update expense head details';
    document.getElementById('saveExpenseHeadText').textContent = 'Update Expense Head';
    document.getElementById('saveExpenseHeadIcon').className = 'fa-solid fa-arrows-rotate';
    document.getElementById('cancelEditExpenseHeadBtn').classList.remove('hidden');

    const alertBox = document.getElementById('expenseHeadModalAlert');
    alertBox.className = 'hidden';

    // Highlight row
    document.querySelectorAll('#existingExpenseHeadsList > div').forEach(el => el.classList.remove('ring-2', 'ring-rose-500'));
    const row = document.getElementById('expense-head-row-' + data.id);
    if (row) row.classList.add('ring-2', 'ring-rose-500');

    // Smooth scroll modal to top
    const container = document.getElementById('expenseHeadModal').firstElementChild;
    if (container) container.scrollTo({ top: 0, behavior: 'smooth' });
}

function resetExpenseHeadForm() {
    document.getElementById('expenseHeadForm').reset();
    document.getElementById('edit_expense_head_id').value = '';
    document.getElementById('expenseHeadModalTitle').textContent = 'Add New Expense Head';
    document.getElementById('expenseHeadModalSubtitle').textContent = 'Create and manage custom expense heads';
    document.getElementById('saveExpenseHeadText').textContent = 'Save Expense Head';
    document.getElementById('saveExpenseHeadIcon').className = 'fa-solid fa-check';
    document.getElementById('cancelEditExpenseHeadBtn').classList.add('hidden');

    const alertBox = document.getElementById('expenseHeadModalAlert');
    alertBox.className = 'hidden';

    document.querySelectorAll('#existingExpenseHeadsList > div').forEach(el => el.classList.remove('ring-2', 'ring-rose-500'));
}

function submitExpenseHeadForm(e) {
    e.preventDefault();
    const form = document.getElementById('expenseHeadForm');
    const formData = new FormData(form);
    const saveBtn = document.getElementById('saveExpenseHeadBtn');
    const alertBox = document.getElementById('expenseHeadModalAlert');
    const editId = document.getElementById('edit_expense_head_id').value;

    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';
    alertBox.className = 'hidden';

    let url = form.action;
    if (editId) {
        url = '{{ url("/factory/accounts/expense-heads") }}/' + editId;
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
        saveBtn.innerHTML = editId ? '<i class="fa-solid fa-arrows-rotate"></i> <span>Update Expense Head</span>' : '<i class="fa-solid fa-check"></i> <span>Save Expense Head</span>';

        if (res.ok && data.success && data.account) {
            const acc = data.account;

            if (editId) {
                // 1. Update Existing Row in List
                const nameLabel = document.getElementById('exp-head-name-label-' + acc.id);
                const catLabel = document.getElementById('exp-head-cat-label-' + acc.id);
                const codeLabel = document.getElementById('exp-head-code-label-' + acc.id);
                if (nameLabel) nameLabel.textContent = acc.name;
                if (catLabel) catLabel.textContent = acc.category;
                if (codeLabel) codeLabel.textContent = acc.account_code || 'N/A';

                // 2. Update Expense Voucher dropdown
                const voucherSelect = document.getElementById('expenseAccountSelect');
                if (voucherSelect) {
                    const opt = voucherSelect.querySelector('option[value="' + acc.id + '"]');
                    if (opt) opt.textContent = acc.name + ' (' + acc.category + ')';
                }

                // 3. Update filter dropdown
                const filterSelect = document.querySelector('select[name="expense_account_id"]');
                if (filterSelect) {
                    const fOpt = filterSelect.querySelector('option[value="' + acc.id + '"]');
                    if (fOpt) fOpt.textContent = acc.name;
                }

                alertBox.textContent = 'Expense head "' + acc.name + '" updated successfully!';
                alertBox.className = 'p-2.5 rounded-xl text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 block';
                resetExpenseHeadForm();
            } else {
                // 1. Append to Expense Voucher dropdown & select it
                const voucherSelect = document.getElementById('expenseAccountSelect');
                if (voucherSelect) {
                    const opt = document.createElement('option');
                    opt.value = acc.id;
                    opt.textContent = acc.name + ' (' + acc.category + ')';
                    opt.selected = true;
                    voucherSelect.appendChild(opt);
                    voucherSelect.value = acc.id;
                }

                // 2. Append to filter dropdown
                const filterSelect = document.querySelector('select[name="expense_account_id"]');
                if (filterSelect) {
                    const fOpt = document.createElement('option');
                    fOpt.value = acc.id;
                    fOpt.textContent = acc.name;
                    filterSelect.appendChild(fOpt);
                }

                // 3. Append to existing heads list
                const list = document.getElementById('existingExpenseHeadsList');
                if (list) {
                    const item = document.createElement('div');
                    item.id = 'expense-head-row-' + acc.id;
                    item.className = 'flex items-center justify-between p-2.5 rounded-xl bg-slate-950/60 border border-slate-800/80 text-slate-300 hover:border-slate-700 transition-all';
                    
                    item.innerHTML = `
                        <div class="flex items-center gap-2 min-w-0 pr-2">
                            <span class="font-mono text-rose-400 font-bold text-[10px] bg-rose-500/10 px-1.5 py-0.5 rounded border border-rose-500/20" id="exp-head-code-label-${acc.id}">${acc.account_code || 'N/A'}</span>
                            <div class="truncate">
                                <span class="font-semibold text-white block truncate" id="exp-head-name-label-${acc.id}">${acc.name}</span>
                                <span class="text-[9px] text-slate-400" id="exp-head-cat-label-${acc.id}">${acc.category}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <button type="button" 
                                    data-id="${acc.id}"
                                    data-name="${acc.name}"
                                    data-category="${acc.category}"
                                    data-code="${acc.account_code || ''}"
                                    data-description="${acc.description || ''}"
                                    onclick="editExpenseHeadFromBtn(this)"
                                    title="Edit this expense head"
                                    class="px-2.5 py-1 rounded-lg bg-sky-500/15 hover:bg-sky-500/30 text-sky-400 border border-sky-500/30 text-[10px] font-bold flex items-center gap-1 transition-all cursor-pointer">
                                <i class="fa-solid fa-pen-to-square text-[9px]"></i>
                                <span>Edit</span>
                            </button>
                            <button type="button" 
                                    data-id="${acc.id}"
                                    data-name="${acc.name}"
                                    onclick="deleteExpenseHeadFromBtn(this)"
                                    title="Delete this expense head"
                                    class="px-2.5 py-1 rounded-lg bg-rose-500/15 hover:bg-rose-500/30 text-rose-400 border border-rose-500/30 text-[10px] font-bold flex items-center gap-1 transition-all cursor-pointer">
                                <i class="fa-solid fa-trash-can text-[9px]"></i>
                                <span>Delete</span>
                            </button>
                        </div>
                    `;
                    list.insertBefore(item, list.firstChild);
                }

                // Update count badge
                const countBadge = document.getElementById('expenseHeadsCountBadge');
                if (countBadge) {
                    const current = parseInt(countBadge.textContent) || 0;
                    countBadge.textContent = (current + 1) + ' Heads';
                }

                form.reset();
                alertBox.textContent = 'Expense head "' + acc.name + '" created and selected!';
                alertBox.className = 'p-2.5 rounded-xl text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 block';
            }
        } else {
            alertBox.textContent = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Error saving expense head.');
            alertBox.className = 'p-2.5 rounded-xl text-xs font-semibold bg-rose-500/20 text-rose-300 border border-rose-500/40 block';
        }
    })
    .catch(err => {
        saveBtn.disabled = false;
        saveBtn.innerHTML = editId ? '<i class="fa-solid fa-arrows-rotate"></i> <span>Update Expense Head</span>' : '<i class="fa-solid fa-check"></i> <span>Save Expense Head</span>';
        alertBox.textContent = 'Failed to save expense head. Please check your network or input values.';
        alertBox.className = 'p-2.5 rounded-xl text-xs font-semibold bg-rose-500/20 text-rose-300 border border-rose-500/40 block';
    });
}

function deleteExpenseHead(id, name) {
    if (!confirm('Are you sure you want to delete expense head "' + name + '"?')) {
        return;
    }

    const alertBox = document.getElementById('expenseHeadModalAlert');
    alertBox.className = 'hidden';

    const csrf = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '{{ csrf_token() }}';
    const formData = new FormData();
    formData.append('_token', csrf);
    formData.append('_method', 'DELETE');

    fetch('{{ url("/factory/accounts/expense-heads") }}/' + id, {
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
            const row = document.getElementById('expense-head-row-' + id);
            if (row) {
                row.style.opacity = '0';
                row.style.transform = 'scale(0.95)';
                setTimeout(() => row.remove(), 250);
            }

            // Remove from Expense Voucher dropdown
            const voucherSelect = document.getElementById('expenseAccountSelect');
            if (voucherSelect) {
                const opt = voucherSelect.querySelector('option[value="' + id + '"]');
                if (opt) opt.remove();
            }

            // Remove from filter dropdown
            const filterSelect = document.querySelector('select[name="expense_account_id"]');
            if (filterSelect) {
                const fOpt = filterSelect.querySelector('option[value="' + id + '"]');
                if (fOpt) fOpt.remove();
            }

            // Decrement badge count
            const countBadge = document.getElementById('expenseHeadsCountBadge');
            if (countBadge) {
                const current = parseInt(countBadge.textContent) || 0;
                countBadge.textContent = Math.max(0, current - 1) + ' Heads';
            }

            alertBox.textContent = data.message || 'Expense head deleted successfully!';
            alertBox.className = 'p-2.5 rounded-xl text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 block';
        } else {
            alertBox.textContent = data.message || 'Could not delete expense head.';
            alertBox.className = 'p-2.5 rounded-xl text-xs font-semibold bg-rose-500/20 text-rose-300 border border-rose-500/40 block';
        }
    })
    .catch(err => {
        alertBox.textContent = 'Server error occurred while deleting expense head.';
        alertBox.className = 'p-2.5 rounded-xl text-xs font-semibold bg-rose-500/20 text-rose-300 border border-rose-500/40 block';
    });
}

// 4. AJAX Load More Expense Records
let currentExpensePage = parseInt(document.getElementById('expenseLoadMoreBtn')?.getAttribute('data-next-page') || '2');
let totalExpensePages = parseInt(document.getElementById('expenseLoadMoreBtn')?.getAttribute('data-total-pages') || '1');
let isLoadingExpense = false;

function loadMoreExpense() {
    const btn = document.getElementById('expenseLoadMoreBtn');
    if (!btn || isLoadingExpense || currentExpensePage > totalExpensePages) return;
    isLoadingExpense = true;

    const spinner = document.getElementById('expenseLoadSpinner');
    const icon = document.getElementById('expenseLoadIcon');
    const text = document.getElementById('expenseLoadText');

    if (spinner) spinner.classList.remove('hidden');
    if (icon) icon.classList.add('hidden');
    if (text) text.textContent = 'Loading...';
    btn.disabled = true;

    const url = new URL(window.location.href);
    url.searchParams.set('page', currentExpensePage);

    fetch(url.toString(), {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Network error');
        return response.text();
    })
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newRows = doc.querySelectorAll('#expenseTableBody tr');
        const tbody = document.getElementById('expenseTableBody');

        if (tbody && newRows.length > 0) {
            newRows.forEach(row => {
                if (!row.id || row.id !== 'expenseEmptyRow') {
                    tbody.appendChild(row);
                }
            });

            const currentCountEl = document.getElementById('expenseCurrentCount');
            if (currentCountEl) {
                currentCountEl.textContent = tbody.querySelectorAll('tr:not(#expenseEmptyRow)').length;
            }
        }

        currentExpensePage++;
        if (currentExpensePage > totalExpensePages) {
            btn.remove();
            const container = document.getElementById('expenseLoadMoreContainer');
            if (container && !document.getElementById('expenseAllLoaded')) {
                const finishedDiv = document.createElement('div');
                finishedDiv.id = 'expenseAllLoaded';
                finishedDiv.className = 'text-xs text-slate-500 font-semibold italic flex items-center gap-1.5';
                finishedDiv.innerHTML = '<i class="fa-solid fa-check text-rose-400"></i> All records loaded';
                container.appendChild(finishedDiv);
            }
        } else {
            btn.setAttribute('data-next-page', currentExpensePage);
            btn.disabled = false;
            if (spinner) spinner.classList.add('hidden');
            if (icon) icon.classList.remove('hidden');
            if (text) text.textContent = 'Load More Records';
        }
    })
    .catch(err => {
        console.error('Failed to load expense records:', err);
        if (text) text.textContent = 'Failed to load. Click to retry';
        if (spinner) spinner.classList.add('hidden');
        if (icon) icon.classList.remove('hidden');
        btn.disabled = false;
    })
    .finally(() => {
        isLoadingExpense = false;
    });
}

// 4. Global helpers & Hotkeys
function autoFillPayee(source) {
    if (source === 'supplier') {
        const sel = document.getElementById('expenseSupplierSelect');
        if (!sel) return;
        const opt = sel.options[sel.selectedIndex];
        const name = opt ? opt.getAttribute('data-name') : '';
        if (name) {
            document.getElementById('expensePartyName').value = name;
        }
    } else if (source === 'employee') {
        const sel = document.getElementById('expenseEmployeeSelect');
        if (!sel) return;
        const opt = sel.options[sel.selectedIndex];
        const name = opt ? opt.getAttribute('data-name') : '';
        if (name) {
            document.getElementById('expensePartyName').value = name;
        }
    }
}

// Close modals on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeExpenseModal();
        closeExpenseDetailsModal();
        closeExpenseHeadModal();
    }
});
</script>
@endsection

