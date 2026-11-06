@extends('layouts.app')

@section('title', 'Supplier Sub-Ledger | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-truck text-orange-400"></i>
                Supplier Sub-Ledger
            </h2>
            <p class="text-xs text-slate-400">Detailed double-entry purchase bills, raw material receipts, and payments by supplier</p>
        </div>

        <div class="flex items-center gap-2.5">
            <button onclick="window.print()" 
                    class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold text-xs shadow-lg transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-print"></i>
                <span>Print Ledger</span>
            </button>
            <a href="{{ route('factory.accounts.dashboard') }}" 
               class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Dashboard</span>
            </a>
        </div>
    </div>

    <!-- LEDGER NAVIGATION TABS -->
    <div class="glass-panel p-2 rounded-2xl border border-slate-800/80 flex flex-wrap gap-2">
        <a href="{{ route('factory.accounts.ledger.index') }}" 
           class="px-4 py-2 rounded-xl font-bold text-xs transition-all flex items-center gap-2 text-slate-400 hover:text-white hover:bg-slate-800/60">
            <i class="fa-solid fa-receipt text-sm"></i>
            <span>1. General Ledger</span>
        </a>
        <a href="{{ route('factory.accounts.ledgers.customer') }}" 
           class="px-4 py-2 rounded-xl font-bold text-xs transition-all flex items-center gap-2 text-slate-400 hover:text-white hover:bg-slate-800/60">
            <i class="fa-solid fa-user text-sm"></i>
            <span>2. Customer Ledger</span>
        </a>
        <a href="{{ route('factory.accounts.ledgers.supplier') }}" 
           class="px-4 py-2 rounded-xl font-bold text-xs transition-all flex items-center gap-2 bg-gradient-to-r from-orange-600 to-amber-600 text-white shadow-lg shadow-orange-500/25">
            <i class="fa-solid fa-truck text-sm"></i>
            <span>3. Supplier Ledger</span>
        </a>
        <a href="{{ route('factory.accounts.ledgers.employee') }}" 
           class="px-4 py-2 rounded-xl font-bold text-xs transition-all flex items-center gap-2 text-slate-400 hover:text-white hover:bg-slate-800/60">
            <i class="fa-solid fa-user-tie text-sm"></i>
            <span>4. Employee Advance Ledger</span>
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="glass-panel p-4 rounded-2xl border border-slate-800/80">
        <form method="GET" action="{{ route('factory.accounts.ledgers.supplier') }}" class="flex flex-wrap items-center gap-3 text-xs">
            <div>
                <label class="block text-slate-400 text-[10px] uppercase font-bold mb-1">Select Supplier:</label>
                <select name="supplier_id" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-slate-200 text-xs min-w-[200px]">
                    @foreach($suppliers as $s)
                    <option value="{{ $s->id }}" {{ $selectedSupplier?->id == $s->id ? 'selected' : '' }}>
                        {{ $s->name }} ({{ $s->phone }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-slate-400 text-[10px] uppercase font-bold mb-1">From Date:</label>
                <input type="date" name="from_date" value="{{ $fromDate }}" 
                       class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-slate-200 text-xs">
            </div>

            <div>
                <label class="block text-slate-400 text-[10px] uppercase font-bold mb-1">To Date:</label>
                <input type="date" name="to_date" value="{{ $toDate }}" 
                       class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-slate-200 text-xs">
            </div>

            <div class="pt-4">
                <button type="submit" class="px-4 py-2 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-bold transition-all">
                    Load Ledger
                </button>
            </div>
        </form>
    </div>

    @if($ledgerData && $selectedSupplier)
    <!-- Supplier Info Card & Closing Balance -->
    <div class="glass-panel p-5 rounded-2xl border border-orange-500/30 bg-orange-950/10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-truck text-orange-400"></i>
                {{ $selectedSupplier->name }}
            </h3>
            <div class="text-xs text-slate-400 mt-1 space-x-3">
                <span>Phone: <strong class="text-slate-200">{{ $selectedSupplier->phone ?: 'N/A' }}</strong></span>
                <span>Address: <strong class="text-slate-200">{{ $selectedSupplier->address ?: 'N/A' }}</strong></span>
            </div>
        </div>

        <div class="text-right font-mono">
            <span class="text-slate-400 uppercase text-[10px] block">Period Closing Payable Balance</span>
            <h3 class="text-2xl font-black {{ $ledgerData['closing_balance'] > 0 ? 'text-rose-400' : 'text-emerald-400' }}">
                ৳ {{ number_format($ledgerData['closing_balance'], 2) }}
            </h3>
            <span class="text-[10px] text-slate-500 font-sans block">
                {{ $ledgerData['closing_balance'] > 0 ? 'Accounts Payable Due' : 'Paid in Full' }}
            </span>
        </div>
    </div>

    <!-- Ledger Table -->
    <div class="glass-panel rounded-2xl border border-slate-800/80 overflow-hidden">
        <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-950/60 text-slate-400 uppercase text-[10px] font-bold border-b border-slate-800">
                <tr>
                    <th class="py-3 px-4">Date</th>
                    <th class="py-3 px-4">Voucher No</th>
                    <th class="py-3 px-4">Description / Particulars</th>
                    <th class="py-3 px-4">Ref / Mode</th>
                    <th class="py-3 px-4 text-right">Debit (Payment ৳)</th>
                    <th class="py-3 px-4 text-right">Credit (Bill ৳)</th>
                    <th class="py-3 px-4 text-right">Running Payable (৳)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60 font-mono">
                @forelse($ledgerData['rows'] as $r)
                <tr class="supplier-entry-row hover:bg-slate-800/30 {{ $loop->index >= 20 ? 'hidden' : '' }}" data-index="{{ $loop->index }}">
                    <td class="py-2.5 px-4 text-slate-300">
                        {{ date('d M, Y', strtotime($r['date'])) }}
                    </td>
                    <td class="py-2.5 px-4 font-bold text-orange-400">
                        {{ $r['voucher_no'] }}
                    </td>
                    <td class="py-2.5 px-4 font-sans text-white">
                        {{ $r['description'] }}
                    </td>
                    <td class="py-2.5 px-4 font-sans text-slate-400 text-[11px]">
                        {{ ucfirst($r['payment_method'] ?? 'N/A') }} {{ $r['reference_no'] ? "({$r['reference_no']})" : '' }}
                    </td>
                    <td class="py-2.5 px-4 text-right font-black {{ $r['debit'] > 0 ? 'text-emerald-400' : 'text-slate-600' }}">
                        {{ $r['debit'] > 0 ? '৳ ' . number_format($r['debit'], 2) : '-' }}
                    </td>
                    <td class="py-2.5 px-4 text-right font-black {{ $r['credit'] > 0 ? 'text-rose-400' : 'text-slate-600' }}">
                        {{ $r['credit'] > 0 ? '৳ ' . number_format($r['credit'], 2) : '-' }}
                    </td>
                    <td class="py-2.5 px-4 text-right font-black {{ $r['running_balance'] > 0 ? 'text-rose-400' : 'text-emerald-400' }}">
                        ৳ {{ number_format($r['running_balance'], 2) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-8 text-center text-slate-500 font-sans">
                        No transactions recorded for this supplier in selected date range.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- SUPPLIER LEDGER LOAD MORE CONTAINER -->
        @if(!empty($ledgerData['rows']) && count($ledgerData['rows']) > 0)
        <div id="supplierLedgerLoadMoreContainer" class="p-4 border-t border-slate-800 bg-slate-950/40 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="text-xs text-slate-400 font-mono">
                Showing <span id="supplierLedgerShowingCount" class="text-orange-400 font-bold">{{ min(20, count($ledgerData['rows'])) }}</span> of <span id="supplierLedgerTotalCount" class="text-white font-bold">{{ count($ledgerData['rows']) }}</span> entries
            </div>

            @if(count($ledgerData['rows']) > 20)
            <button type="button" 
                    id="supplierLedgerLoadMoreBtn"
                    onclick="loadMoreSupplierLedgerRows()"
                    class="px-5 py-2 rounded-xl bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white text-xs font-bold shadow-lg shadow-orange-950/50 flex items-center gap-2 transition-all cursor-pointer">
                <i class="fa-solid fa-circle-down"></i>
                <span>Load More Entries</span>
            </button>
            @else
            <div class="text-xs text-slate-500 font-semibold italic flex items-center gap-1.5">
                <i class="fa-solid fa-check text-emerald-400"></i> All entries loaded
            </div>
            @endif
        </div>
        @endif
    </div>
    @endif

</div>

@if($ledgerData && !empty($ledgerData['rows']))
<script>
let supplierLedgerPageSize = 20;
let currentSupplierVisible = Math.min(20, {{ count($ledgerData['rows']) }});
const totalSupplierEntries = {{ count($ledgerData['rows']) }};

function loadMoreSupplierLedgerRows() {
    const hiddenRows = document.querySelectorAll('.supplier-entry-row.hidden');
    let toShow = Math.min(supplierLedgerPageSize, hiddenRows.length);
    for (let i = 0; i < toShow; i++) {
        hiddenRows[i].classList.remove('hidden');
    }
    currentSupplierVisible += toShow;
    const countEl = document.getElementById('supplierLedgerShowingCount');
    if (countEl) countEl.textContent = currentSupplierVisible;

    if (currentSupplierVisible >= totalSupplierEntries || hiddenRows.length <= toShow) {
        const btn = document.getElementById('supplierLedgerLoadMoreBtn');
        if (btn) btn.remove();
        const container = document.getElementById('supplierLedgerLoadMoreContainer');
        if (container) {
            const doneDiv = document.createElement('div');
            doneDiv.className = 'text-xs text-slate-500 font-semibold italic flex items-center gap-1.5';
            doneDiv.innerHTML = '<i class="fa-solid fa-check text-emerald-400"></i> All entries loaded';
            container.appendChild(doneDiv);
        }
    }
}
</script>
@endif
@endsection

