@extends('layouts.app')

@section('title', 'Expense Management | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-money-bill-wave text-rose-400"></i>
                Expense Management (ব্যয় ও খরচ ব্যবস্থাপনা)
            </h2>
            <p class="text-xs text-slate-400">Track raw materials purchase, rent, utilities, maintenance, wages, and operating expenses</p>
        </div>

        <div class="flex items-center gap-2.5">
            <button type="button" onclick="openExpenseModal()" 
                    class="px-4 py-2 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 text-white font-bold text-xs shadow-lg shadow-rose-500/25 transition-all flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-plus-circle"></i>
                <span>+ New Expense Voucher (নতুন খরচ)</span>
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
                <span class="text-[11px] font-bold uppercase tracking-wider text-rose-400 block">Total Filtered Expense (মোট খরচ)</span>
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
                <label class="block text-slate-400 text-[10px] uppercase font-bold mb-1">Expense Head (খাত)</label>
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
                        <th class="py-3.5 px-4">Expense Head (খরচের খাত)</th>
                        <th class="py-3.5 px-4">Paid From (A/C)</th>
                        <th class="py-3.5 px-4">Paid To / Payee</th>
                        <th class="py-3.5 px-4">Method & Ref</th>
                        <th class="py-3.5 px-4 text-right">Amount (৳)</th>
                        <th class="py-3.5 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
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
                        <td class="py-3.5 px-4 font-mono text-slate-300">
                            <span class="text-cyan-400 font-bold block">{{ $v->creditAccount?->name ?? 'Cash/Bank' }}</span>
                            @if($v->creditAccount?->account_number)
                            <span class="text-[10px] text-slate-500">A/C: {{ $v->creditAccount->account_number }}</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="text-white font-medium block">
                                {{ $v->party_name ?: ($v->supplier?->name ?: ($v->employee?->name ?: 'General Payee')) }}
                            </span>
                            @if($v->supplier)
                            <span class="text-[10px] text-indigo-400 font-bold block">Supplier: {{ $v->supplier->name }}</span>
                            @elseif($v->employee)
                            <span class="text-[10px] text-teal-400 font-bold block">Employee: {{ $v->employee->name }} ({{ $v->employee->employee_code }})</span>
                            @endif
                            @if($v->notes)
                            <span class="text-[11px] text-slate-400 truncate max-w-xs block mt-0.5">{{ $v->notes }}</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-slate-300 text-[11px]">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-800 text-slate-300 border border-slate-700">
                                {{ $v->payment_method_label }}
                            </span>
                            @if($v->reference_no)
                            <span class="text-[10px] text-slate-400 font-mono block mt-0.5">Ref: {{ $v->reference_no }}</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-right font-mono font-black text-sm text-rose-400">
                            - ৳ {{ number_format($v->amount, 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
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
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-500">
                            <i class="fa-solid fa-receipt text-3xl mb-2 opacity-50 block"></i>
                            No expense records found matching your filters.
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
                <h3 class="text-base font-extrabold text-white">Create Debit Voucher / Expense (নতুন খরচ যুক্ত করুন)</h3>
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
                    <label class="block text-slate-300 font-bold mb-1">Expense Head (খরচের খাত) <span class="text-rose-400">*</span></label>
                    <select name="expense_account_id" required
                            class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-rose-500">
                        <option value="">Select Expense Account</option>
                        @foreach($expenseAccounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->category }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Paid From Account -->
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Paid From (পরিশোধের অ্যাকাউন্ট) <span class="text-rose-400">*</span></label>
                    <select name="paid_from_account_id" required
                            class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-rose-500">
                        <option value="">Select Cash / Bank Account</option>
                        @foreach($liquidAccounts as $acc)
                        <option value="{{ $acc->id }}">
                            {{ $acc->name }} [Bal: ৳ {{ number_format($acc->current_balance, 2) }}]
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Amount -->
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Expense Amount (পরিমাণ ৳) <span class="text-rose-400">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-slate-400 font-bold">৳</span>
                        <input type="number" step="0.01" min="0.01" name="amount" placeholder="0.00" required
                               class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl pl-8 pr-3 py-2.5 text-slate-200 font-mono font-bold text-sm focus:outline-none focus:border-rose-500">
                    </div>
                </div>

                <!-- Supplier (Optional) -->
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Supplier (সাপ্লায়ার - যদি প্রযোজ্য হয়)</label>
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
                    <label class="block text-slate-300 font-bold mb-1">Employee (কর্মচারী - যদি প্রযোজ্য হয়)</label>
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
                    <label class="block text-slate-300 font-bold mb-1">Paid To / Payee Name (গ্রহীতার নাম/প্রতিষ্ঠান)</label>
                    <input type="text" name="party_name" id="expensePartyName" placeholder="e.g. DESCO / Titas Gas / Khan Fabrics"
                           class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-rose-500">
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Payment Method (পরিশোধ মাধ্যম) <span class="text-rose-400">*</span></label>
                    <select name="payment_method" required
                            class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-rose-500">
                        <option value="cash">Cash (নগদ টাকা)</option>
                        <option value="bank">Bank Transfer (ব্যাংক ট্রান্সফার)</option>
                        <option value="cheque">Cheque (চেক)</option>
                        <option value="bkash">bKash (বিকাশ)</option>
                        <option value="nagad">Nagad (নগদ)</option>
                        <option value="rocket">Rocket (রকেট)</option>
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
                <label class="block text-slate-300 font-bold mb-1">Expense Purpose / Description (খরচের কারণ ও বিবরণ)</label>
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

<script>
function openExpenseModal() {
    const m = document.getElementById('expenseModal');
    if (m) m.classList.remove('hidden');
}
function closeExpenseModal() {
    const m = document.getElementById('expenseModal');
    if (m) m.classList.add('hidden');
}
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

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeExpenseModal();
    }
});
</script>
@endsection

