@extends('layouts.app')

@section('title', 'Fund Transfers | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-money-bill-transfer text-cyan-400"></i>
                Internal Fund Transfers
            </h2>
            <p class="text-xs text-slate-400">Move funds between Cash in Hand, Bank Accounts, and Mobile Wallets without affecting profit & loss</p>
        </div>

        <div class="flex items-center gap-2.5">
            <button type="button" onclick="openTransferModal()" 
                    class="px-4 py-2 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-bold text-xs shadow-lg shadow-cyan-500/25 transition-all flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-plus-circle"></i>
                <span>+ New Fund Transfer</span>
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
        <div class="glass-panel p-4 rounded-2xl border border-cyan-500/30 bg-gradient-to-br from-cyan-950/20 to-slate-900/40 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-cyan-400 block">Total Filtered Transfers</span>
                <h3 class="text-2xl font-black text-white font-mono mt-1">৳ {{ number_format($totalTransferred, 2) }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center">
                <i class="fa-solid fa-arrows-rotate text-lg"></i>
            </div>
        </div>

        <div class="glass-panel p-4 rounded-2xl border border-slate-800 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Total Transfer Vouchers</span>
                <h3 class="text-2xl font-black text-white font-mono mt-1">{{ $transfers->total() }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-800 text-slate-400 flex items-center justify-center">
                <i class="fa-solid fa-file-invoice text-lg"></i>
            </div>
        </div>

        <div class="glass-panel p-4 rounded-2xl border border-amber-500/30 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-400 block">Next Transfer Sequence</span>
                <h3 class="text-xl font-black text-amber-300 font-mono mt-1">{{ $nextVoucherNo }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center">
                <i class="fa-solid fa-barcode text-lg"></i>
            </div>
        </div>
    </div>

    <!-- FILTER BAR -->
    <div class="glass-panel p-4 rounded-2xl border border-slate-800/80">
        <form method="GET" action="{{ route('factory.accounts.transfers.index') }}" class="flex flex-wrap items-center gap-3 text-xs">
            <span class="text-slate-400 font-medium flex items-center gap-1.5">
                <i class="fa-regular fa-calendar text-cyan-400"></i> Date Filter:
            </span>
            <input type="date" name="from_date" value="{{ request('from_date') }}" 
                   class="bg-slate-900/80 border border-slate-700/80 rounded-xl px-2.5 py-1.5 text-slate-200 focus:outline-none focus:border-cyan-500 text-xs">
            <span class="text-slate-500">to</span>
            <input type="date" name="to_date" value="{{ request('to_date') }}" 
                   class="bg-slate-900/80 border border-slate-700/80 rounded-xl px-2.5 py-1.5 text-slate-200 focus:outline-none focus:border-cyan-500 text-xs">
            <button type="submit" class="px-3 py-1.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold transition-all">
                Filter
            </button>
            @if(request()->anyFilled(['from_date', 'to_date']))
            <a href="{{ route('factory.accounts.transfers.index') }}" class="text-slate-400 hover:text-rose-400 text-xs underline ml-1">Reset</a>
            @endif
        </form>
    </div>

    <!-- TRANSFERS TABLE -->
    <div class="glass-panel rounded-2xl border border-slate-800/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/60 text-slate-400 uppercase text-[10px] font-bold border-b border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4">Voucher No</th>
                        <th class="py-3.5 px-4">Date</th>
                        <th class="py-3.5 px-4">From Account (Sending)</th>
                        <th class="py-3.5 px-4">To Account (Receiving)</th>
                        <th class="py-3.5 px-4">Notes / Narration</th>
                        <th class="py-3.5 px-4">Method & Ref</th>
                        <th class="py-3.5 px-4 text-right">Amount (৳)</th>
                        <th class="py-3.5 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($transfers as $t)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="py-3.5 px-4 font-mono font-bold text-amber-400">
                            {{ $t->voucher_no }}
                        </td>
                        <td class="py-3.5 px-4 font-mono text-slate-400">
                            {{ $t->voucher_date->format('d M, Y') }}
                        </td>
                        <td class="py-3.5 px-4 font-mono text-rose-300">
                            <span class="font-bold block">{{ $t->creditAccount?->name ?? 'N/A' }}</span>
                            <span class="text-[10px] text-slate-500">Credit / Sent</span>
                        </td>
                        <td class="py-3.5 px-4 font-mono text-emerald-300">
                            <span class="font-bold block">{{ $t->debitAccount?->name ?? 'N/A' }}</span>
                            <span class="text-[10px] text-slate-500">Debit / Received</span>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="text-slate-300">{{ $t->notes ?: 'Internal fund transfer' }}</span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-300 text-[11px]">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-800 text-slate-300 border border-slate-700">
                                {{ $t->payment_method_label }}
                            </span>
                            @if($t->reference_no)
                            <span class="text-[10px] text-slate-400 font-mono block mt-0.5">Ref: {{ $t->reference_no }}</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-right font-mono font-black text-sm text-cyan-400">
                            ৳ {{ number_format($t->amount, 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <form action="{{ route('factory.accounts.vouchers.destroy', $t->id) }}" method="POST" 
                                  onsubmit="return confirm('Delete transfer voucher {{ $t->voucher_no }}? Both account balances will be restored.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg bg-slate-800 hover:bg-rose-600/30 text-slate-400 hover:text-rose-400 text-xs transition-all" title="Delete Transfer">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-500">
                            <i class="fa-solid fa-money-bill-transfer text-3xl mb-2 opacity-50 block"></i>
                            No fund transfer records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transfers->hasPages())
        <div class="p-4 border-t border-slate-800 bg-slate-950/40">
            {{ $transfers->links() }}
        </div>
        @endif
    </div>

</div>

<!-- MODAL: ADD NEW TRANSFER -->
<div id="transferModal" onclick="if(event.target === this) closeTransferModal()" class="fixed inset-0 z-[9999] bg-slate-950/85 hidden flex items-center justify-center p-4 overflow-y-auto transform-gpu">
    <div onclick="event.stopPropagation()" class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-lg shadow-2xl p-6 relative">
        <button type="button" onclick="closeTransferModal()" class="absolute top-5 right-5 text-slate-400 hover:text-white text-lg cursor-pointer">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="flex items-center gap-3 border-b border-slate-800 pb-4 mb-5">
            <div class="w-10 h-10 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center">
                <i class="fa-solid fa-money-bill-transfer text-lg"></i>
            </div>
            <div>
                <h3 class="text-base font-extrabold text-white">Transfer Funds</h3>
                <p class="text-xs text-slate-400">Transfer money from Cash to Bank, Bank to Cash, or between accounts</p>
            </div>
        </div>

        <form action="{{ route('factory.accounts.transfers.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf

            <!-- Date -->
            <div>
                <label class="block text-slate-300 font-bold mb-1">Transfer Date <span class="text-rose-400">*</span></label>
                <input type="date" name="voucher_date" value="{{ date('Y-m-d') }}" required
                       class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500">
            </div>

            <!-- From Account -->
            <div>
                <label class="block text-slate-300 font-bold mb-1">From Account <span class="text-rose-400">*</span></label>
                <select name="from_account_id" required
                        class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500">
                    <option value="">Select Sending Account</option>
                    @foreach($liquidAccounts as $acc)
                    <option value="{{ $acc->id }}">
                        {{ $acc->name }} [Bal: ৳ {{ number_format($acc->current_balance, 2) }}]
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- To Account -->
            <div>
                <label class="block text-slate-300 font-bold mb-1">To Account <span class="text-rose-400">*</span></label>
                <select name="to_account_id" required
                        class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500">
                    <option value="">Select Receiving Account</option>
                    @foreach($liquidAccounts as $acc)
                    <option value="{{ $acc->id }}">
                        {{ $acc->name }} [Bal: ৳ {{ number_format($acc->current_balance, 2) }}]
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Amount -->
            <div>
                <label class="block text-slate-300 font-bold mb-1">Transfer Amount (৳) <span class="text-rose-400">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-slate-400 font-bold">৳</span>
                    <input type="number" step="0.01" min="0.01" name="amount" placeholder="0.00" required
                           class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl pl-8 pr-3 py-2.5 text-slate-200 font-mono font-bold text-sm focus:outline-none focus:border-cyan-500">
                </div>
            </div>

            <!-- Method & Ref -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Transfer Channel</label>
                    <select name="payment_method" required
                            class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500">
                        <option value="bank_transfer">Bank Deposit/Transfer</option>
                        <option value="cash">Cash Withdrawal/Deposit</option>
                        <option value="cheque">Cheque Deposit</option>
                        <option value="bkash">bKash</option>
                        <option value="nagad">Nagad</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Reference No / Slip #</label>
                    <input type="text" name="reference_no" placeholder="Deposit slip or TrxID"
                           class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500">
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-slate-300 font-bold mb-1">Notes / Narration</label>
                <input type="text" name="notes" placeholder="e.g. Deposited cash in hand to DBBL current account"
                       class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500">
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <button type="button" onclick="closeTransferModal()" 
                        class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold transition-all">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-bold shadow-lg shadow-cyan-500/25 transition-all">
                    <i class="fa-solid fa-check mr-1.5"></i> Transfer Now
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openTransferModal() {
    const m = document.getElementById('transferModal');
    if (m) m.classList.remove('hidden');
}
function closeTransferModal() {
    const m = document.getElementById('transferModal');
    if (m) m.classList.add('hidden');
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeTransferModal();
    }
});
</script>
@endsection

