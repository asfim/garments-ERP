@extends('layouts.app')

@section('title', 'Create Journal Voucher | Factory ERP')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto" x-data="journalForm()">

    <!-- Top Header -->
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-file-signature text-blue-400"></i>
                New Double-Entry Journal Voucher (নতুন জাবেদা ভাউচার)
            </h2>
            <p class="text-xs text-slate-400">Record compound multi-line double-entry accounting transactions. Total Debit must equal Total Credit.</p>
        </div>

        <a href="{{ route('factory.accounts.journals.index') }}" 
           class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition-all flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Journals</span>
        </a>
    </div>

    <!-- Error Alerts -->
    @if(session('error'))
    <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs font-semibold flex items-center gap-2">
        <i class="fa-solid fa-circle-exclamation text-base"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs">
        <div class="font-bold mb-1">Please fix the following validation errors:</div>
        <ul class="list-disc list-inside space-y-0.5 text-slate-300">
            @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('factory.accounts.journals.store') }}" @submit="handleSubmit($event)">
        @csrf

        <!-- Voucher Header Details -->
        <div class="glass-panel p-5 rounded-2xl border border-slate-800/80 mb-6 space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-blue-400 border-b border-slate-800 pb-2">
                1. Voucher Header Information
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Voucher No</label>
                    <input type="text" value="{{ $nextVoucherNo }}" readonly 
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-blue-400 font-mono font-bold text-xs">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Voucher Date <span class="text-rose-400">*</span></label>
                    <input type="date" name="voucher_date" value="{{ old('voucher_date', date('Y-m-d')) }}" required 
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-slate-200 text-xs focus:border-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Reference No / Bill #</label>
                    <input type="text" name="reference_no" value="{{ old('reference_no') }}" placeholder="e.g. INV-9042, REF-100" 
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-slate-200 text-xs focus:border-blue-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Customer (Optional Tag)</label>
                    <select name="customer_id" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-slate-200 text-xs">
                        <option value="">-- None / General --</option>
                        @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Supplier (Optional Tag)</label>
                    <select name="supplier_id" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-slate-200 text-xs">
                        <option value="">-- None / General --</option>
                        @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->company_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Employee (Optional Tag)</label>
                    <select name="employee_id" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-slate-200 text-xs">
                        <option value="">-- None / General --</option>
                        @foreach($employees as $e)
                        <option value="{{ $e->id }}" {{ old('employee_id') == $e->id ? 'selected' : '' }}>{{ $e->first_name }} {{ $e->last_name }} ({{ $e->employee_id }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="pt-2">
                <label class="block text-xs font-medium text-slate-300 mb-1">Description / Narration <span class="text-rose-400">*</span></label>
                <textarea name="notes" rows="2" required placeholder="Detailed explanation of the journal transaction..."
                          class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-slate-200 text-xs focus:border-blue-500 focus:outline-none">{{ old('notes') }}</textarea>
            </div>
        </div>

        <!-- Multi-line Journal Entries -->
        <div class="glass-panel p-5 rounded-2xl border border-slate-800/80 mb-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-blue-400">
                    2. Journal Entry Lines (Debit & Credit)
                </h3>
                <button type="button" @click="addLine()" 
                        class="px-3 py-1.5 rounded-lg bg-blue-600/20 hover:bg-blue-600/30 text-blue-300 border border-blue-500/30 text-xs font-bold transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-plus"></i> Add Line
                </button>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950/60 text-slate-400 uppercase text-[10px] font-bold border-b border-slate-800">
                        <tr>
                            <th class="py-2.5 px-3 w-5/12">Account</th>
                            <th class="py-2.5 px-3 w-2/12">Type</th>
                            <th class="py-2.5 px-3 w-2/12 text-right">Amount (৳)</th>
                            <th class="py-2.5 px-3 w-2/12">Narration</th>
                            <th class="py-2.5 px-3 w-1/12 text-center">Remove</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        <template x-for="(line, index) in lines" :key="index">
                            <tr class="hover:bg-slate-800/20">
                                <td class="py-2.5 px-3">
                                    <select :name="'lines[' + index + '][account_id]'" x-model="line.account_id" required 
                                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-slate-200 text-xs">
                                        <option value="">-- Select Account --</option>
                                        @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">[{{ $acc->account_code }}] {{ $acc->name }} ({{ ucfirst($acc->type) }})</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="py-2.5 px-3">
                                    <select :name="'lines[' + index + '][type]'" x-model="line.type" @change="calculateTotals()" required 
                                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs font-bold font-mono"
                                            :class="line.type === 'debit' ? 'text-cyan-400' : 'text-amber-400'">
                                        <option value="debit">DEBIT (Dr)</option>
                                        <option value="credit">CREDIT (Cr)</option>
                                    </select>
                                </td>
                                <td class="py-2.5 px-3">
                                    <input type="number" step="0.01" min="0.01" :name="'lines[' + index + '][amount]'" 
                                           x-model="line.amount" @input="calculateTotals()" required placeholder="0.00"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-right font-mono font-bold text-xs"
                                           :class="line.type === 'debit' ? 'text-cyan-400' : 'text-amber-400'">
                                </td>
                                <td class="py-2.5 px-3">
                                    <input type="text" :name="'lines[' + index + '][narration]'" x-model="line.narration" placeholder="Line note (opt)" 
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-slate-200 text-xs">
                                </td>
                                <td class="py-2.5 px-3 text-center">
                                    <button type="button" @click="removeLine(index)" :disabled="lines.length <= 2"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-rose-400 disabled:opacity-30 disabled:cursor-not-allowed transition-all">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Double-Entry Balance Bar -->
            <div class="mt-4 p-4 rounded-xl border font-mono"
                 :class="isBalanced ? 'bg-emerald-950/20 border-emerald-500/40' : 'bg-rose-950/20 border-rose-500/40'">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-6 text-xs">
                        <div>
                            <span class="text-slate-400 uppercase text-[10px] block">Total Debit (Dr)</span>
                            <span class="text-cyan-400 font-black text-lg">৳ <span x-text="totalDebit.toFixed(2)"></span></span>
                        </div>
                        <div class="text-slate-600 font-sans text-xl">|</div>
                        <div>
                            <span class="text-slate-400 uppercase text-[10px] block">Total Credit (Cr)</span>
                            <span class="text-amber-400 font-black text-lg">৳ <span x-text="totalCredit.toFixed(2)"></span></span>
                        </div>
                        <div class="text-slate-600 font-sans text-xl">|</div>
                        <div>
                            <span class="text-slate-400 uppercase text-[10px] block">Difference (Dr - Cr)</span>
                            <span class="font-black text-lg" :class="isBalanced ? 'text-emerald-400' : 'text-rose-400'">
                                ৳ <span x-text="difference.toFixed(2)"></span>
                            </span>
                        </div>
                    </div>

                    <div>
                        <span x-show="isBalanced" class="px-3 py-1.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-xs font-bold flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-check"></i> Balanced Entry
                        </span>
                        <span x-show="!isBalanced" class="px-3 py-1.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/40 text-xs font-bold flex items-center gap-1.5">
                            <i class="fa-solid fa-triangle-exclamation"></i> Unbalanced
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('factory.accounts.journals.index') }}" 
               class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition-all">
                Cancel
            </a>
            <button type="submit" :disabled="!isBalanced || totalDebit <= 0" 
                    class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-xs shadow-lg shadow-blue-500/25 disabled:opacity-40 disabled:cursor-not-allowed transition-all flex items-center gap-2">
                <i class="fa-solid fa-check"></i>
                <span>Post Journal Voucher</span>
            </button>
        </div>

    </form>
</div>

<script>
function journalForm() {
    return {
        lines: [
            { account_id: '', type: 'debit', amount: '', narration: '' },
            { account_id: '', type: 'credit', amount: '', narration: '' }
        ],
        totalDebit: 0.0,
        totalCredit: 0.0,
        difference: 0.0,
        isBalanced: true,

        addLine() {
            this.lines.push({ account_id: '', type: 'credit', amount: '', narration: '' });
            this.calculateTotals();
        },

        removeLine(index) {
            if (this.lines.length > 2) {
                this.lines.splice(index, 1);
                this.calculateTotals();
            }
        },

        calculateTotals() {
            let dr = 0.0;
            let cr = 0.0;
            this.lines.forEach(l => {
                let amt = parseFloat(l.amount) || 0;
                if (l.type === 'debit') {
                    dr += amt;
                } else {
                    cr += amt;
                }
            });
            this.totalDebit = Math.round(dr * 100) / 100;
            this.totalCredit = Math.round(cr * 100) / 100;
            this.difference = Math.round(Math.abs(this.totalDebit - this.totalCredit) * 100) / 100;
            this.isBalanced = this.difference < 0.01 && this.totalDebit > 0;
        },

        handleSubmit(e) {
            this.calculateTotals();
            if (!this.isBalanced || this.totalDebit <= 0) {
                e.preventDefault();
                alert('Cannot post unbalanced journal! Total Debit must equal Total Credit.');
            }
        }
    }
}
</script>
@endsection

