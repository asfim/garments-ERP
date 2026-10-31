@extends('layouts.app')

@section('title', 'Customers Due Payments | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('factory.customers.index') }}" class="text-xs text-cyan-400 hover:underline flex items-center gap-1">
                    <i class="fa-solid fa-arrow-left"></i> Back to All Customers
                </a>
            </div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-hand-holding-dollar text-rose-400"></i>
                Customer Due Payment Registry
            </h2>
            <p class="text-xs text-slate-400">Clients with outstanding due balances and pending invoice collections (যেসব কাস্টমারের বকেয়া রয়েছে)</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('factory.customers.index') }}" 
               class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 text-xs font-semibold transition-all flex items-center gap-2">
                <i class="fa-solid fa-users"></i>
                <span>All Customers Directory</span>
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="p-3 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs flex items-center gap-2">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="p-3 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs flex items-center gap-2">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="glass-panel rounded-2xl p-4 border border-rose-500/40 bg-gradient-to-br from-rose-950/30 to-[#0a1228] flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-rose-300 uppercase tracking-wider block">Total Outstanding Market Due</span>
                <span class="text-2xl font-mono font-black text-rose-400 mt-1 block">৳ {{ number_format($totalMarketDue, 2) }}</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-400 flex items-center justify-center text-lg">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
        </div>

        <div class="glass-panel rounded-2xl p-4 border border-amber-500/40 bg-gradient-to-br from-amber-950/20 to-[#0a1228] flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-amber-300 uppercase tracking-wider block">Clients with Pending Due</span>
                <span class="text-2xl font-mono font-black text-amber-400 mt-1 block">{{ $totalDueCustomers }}</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-400 flex items-center justify-center text-lg">
                <i class="fa-solid fa-user-clock"></i>
            </div>
        </div>

        <div class="glass-panel rounded-2xl p-4 border border-cyan-500/40 bg-gradient-to-br from-cyan-950/20 to-[#0a1228] flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-cyan-300 uppercase tracking-wider block">Total Unpaid Invoices</span>
                <span class="text-2xl font-mono font-black text-cyan-400 mt-1 block">{{ $totalDueInvoices }}</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-cyan-500/15 border border-cyan-500/30 text-cyan-400 flex items-center justify-center text-lg">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
        </div>
    </div>

    <!-- Due Customers Table Card -->
    <div class="glass-panel rounded-3xl p-5 border border-slate-700/60 w-full space-y-4">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-700/70">
            <div>
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-users text-rose-400"></i>
                    <span>Clients Having Outstanding Due</span>
                </h3>
                <span id="dueCustomerCountDisplay" class="text-[11px] text-rose-300 font-mono">
                    Showing <span id="visibleDueCount">{{ min(10, $customers->count()) }}</span> of <span id="totalDueCount">{{ $customers->count() }}</span> Due Clients
                </span>
            </div>

            <!-- Live Search Box -->
            <div class="relative w-full sm:w-80">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="dueCustomerSearchInput" placeholder="Live search by Client, Phone, Address, Due..." 
                       class="w-full bg-[#070d1e] text-xs text-white placeholder-slate-500 rounded-xl pl-8 pr-7 py-2 border border-slate-700 focus:border-rose-400 focus:outline-none transition-all">
                <button type="button" id="clearDueCustomerSearchBtn" onclick="clearDueCustomerSearch()" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white hidden text-xs">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs min-w-[780px]">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-700/80 font-bold uppercase tracking-wider text-[11px]">
                        <th class="py-3 px-3.5">SL</th>
                        <th class="py-3 px-3.5">Customer / Company</th>
                        <th class="py-3 px-3.5">Phone & Person</th>
                        <th class="py-3 px-3.5">Address</th>
                        <th class="py-3 px-3.5 text-center">Due Invoices</th>
                        <th class="py-3 px-3.5 text-right">Total Ordered</th>
                        <th class="py-3 px-3.5 text-right">Total Paid</th>
                        <th class="py-3 px-3.5 text-right">Total Due Balance</th>
                        <th class="py-3 px-3.5 text-right min-w-[150px]">Payment Actions</th>
                    </tr>
                </thead>
                <tbody id="dueCustomersTableBody" class="divide-y divide-slate-800/80">
                    @forelse($customers as $idx => $c)
                    @php
                        $dueOrdersCount = $c->orders->count();
                        $searchKeywords = strtolower(implode(' ', array_filter([
                            $c->company_name,
                            $c->phone,
                            $c->contact_person,
                            $c->address,
                            $c->total_due,
                            number_format($c->total_due, 2)
                        ])));
                    @endphp
                    <tr class="due-customer-row hover:bg-slate-800/40 transition-colors"
                        data-search="{{ $searchKeywords }}">
                        
                        <!-- SL -->
                        <td class="py-3.5 px-3.5 font-mono text-slate-400 whitespace-nowrap">
                            #{{ $loop->iteration }}
                        </td>

                        <!-- Company -->
                        <td class="py-3.5 px-3.5 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <span class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center font-bold text-xs border border-rose-500/20">
                                    <i class="fa-solid fa-building"></i>
                                </span>
                                <div>
                                    <span class="font-bold text-white block">{{ $c->company_name }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $c->contact_person ?? 'Owner' }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Phone -->
                        <td class="py-3.5 px-3.5 whitespace-nowrap">
                            <a href="tel:{{ $c->phone }}" class="inline-flex items-center gap-1.5 text-cyan-300 hover:text-cyan-200 font-mono text-xs">
                                <i class="fa-solid fa-phone text-[10px] text-slate-400"></i>
                                <span>{{ $c->phone }}</span>
                            </a>
                        </td>

                        <!-- Address -->
                        <td class="py-3.5 px-3.5 text-slate-300 max-w-[200px]">
                            <div class="flex items-start gap-1.5">
                                <i class="fa-solid fa-location-dot text-slate-400 text-[11px] mt-0.5"></i>
                                <span class="truncate" title="{{ $c->address ?? 'N/A' }}">
                                    {{ $c->address ?? 'N/A' }}
                                </span>
                            </div>
                        </td>

                        <!-- Due Invoices Count -->
                        <td class="py-3.5 px-3.5 text-center whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 font-mono">
                                {{ $dueOrdersCount }} {{ Str::plural('Invoice', $dueOrdersCount) }}
                            </span>
                        </td>

                        <!-- Total Ordered -->
                        <td class="py-3.5 px-3.5 text-right font-mono font-bold text-slate-300 whitespace-nowrap">
                            ৳ {{ number_format($c->total_ordered, 2) }}
                        </td>

                        <!-- Total Paid -->
                        <td class="py-3.5 px-3.5 text-right font-mono font-bold text-emerald-400 whitespace-nowrap">
                            ৳ {{ number_format($c->total_paid, 2) }}
                        </td>

                        <!-- Total Due Balance -->
                        <td class="py-3.5 px-3.5 text-right font-mono font-black text-rose-400 whitespace-nowrap text-sm">
                            ৳ {{ number_format($c->total_due, 2) }}
                        </td>

                        <!-- Action: Enter Customer to View Due Invoices & Pay -->
                        <td class="py-3.5 px-3.5 text-right whitespace-nowrap">
                            <a href="{{ route('factory.customers.due.invoices', $c->id) }}"
                               class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-rose-500 to-amber-600 hover:from-rose-400 hover:to-amber-500 text-white font-bold text-xs shadow-lg shadow-rose-500/25 transition-all inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-hand-holding-dollar"></i>
                                <span>View Invoices & Pay</span>
                                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <i class="fa-solid fa-circle-check text-4xl text-emerald-400"></i>
                                <span class="text-white font-bold text-sm">No Pending Due Payments!</span>
                                <span class="text-xs text-slate-400">All customer accounts and invoices are completely settled.</span>
                                <a href="{{ route('factory.customers.index') }}" class="mt-2 text-xs text-cyan-400 hover:underline">
                                    Return to Customers Directory
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse

                    <!-- No Search Results Row -->
                    <tr id="noDueCustomerSearchResultRow" class="hidden">
                        <td colspan="9" class="py-8 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center gap-1.5">
                                <i class="fa-solid fa-magnifying-glass text-2xl text-slate-600"></i>
                                <span class="text-xs">No due clients match your search query.</span>
                                <button type="button" onclick="clearDueCustomerSearch()" class="text-rose-400 text-xs underline mt-1">Clear search</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Load More Button -->
        <div id="loadMoreDueCustomersContainer" class="mt-4 pt-3 border-t border-slate-800 text-center {{ $customers->count() > 10 ? '' : 'hidden' }}">
            <button type="button" onclick="loadMoreDueCustomers()"
                    class="px-5 py-2 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-rose-300 hover:text-white border border-slate-700 hover:border-rose-500/40 text-xs font-semibold transition-all inline-flex items-center gap-2 shadow-lg">
                <i class="fa-solid fa-angles-down text-rose-400"></i>
                <span>Load More Due Clients (+10)</span>
            </button>
        </div>

    </div>

</div>

@push('scripts')
<script>
    let allDueRows = [];
    let dueVisibleLimit = 10;
    const batchSize = 10;

    document.addEventListener('DOMContentLoaded', () => {
        allDueRows = Array.from(document.querySelectorAll('.due-customer-row'));
        applyDueVisibility();

        const searchInput = document.getElementById('dueCustomerSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase().trim();
                document.getElementById('clearDueCustomerSearchBtn').classList.toggle('hidden', query === '');
                filterDueCustomers(query);
            });
        }
    });

    function applyDueVisibility() {
        allDueRows.forEach((row, index) => {
            if (index < dueVisibleLimit) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        const visibleCount = Math.min(dueVisibleLimit, allDueRows.length);
        const visibleEl = document.getElementById('visibleDueCount');
        if (visibleEl) visibleEl.innerText = visibleCount;

        const loadMoreContainer = document.getElementById('loadMoreDueCustomersContainer');
        if (loadMoreContainer) {
            loadMoreContainer.classList.toggle('hidden', visibleCount >= allDueRows.length);
        }
    }

    function loadMoreDueCustomers() {
        dueVisibleLimit += batchSize;
        applyDueVisibility();
    }

    function filterDueCustomers(query) {
        if (!query) {
            document.getElementById('noDueCustomerSearchResultRow').classList.add('hidden');
            applyDueVisibility();
            return;
        }

        let matched = 0;
        allDueRows.forEach(row => {
            const text = (row.getAttribute('data-search') || '').toLowerCase();
            if (text.includes(query)) {
                row.style.display = '';
                matched++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('noDueCustomerSearchResultRow').classList.toggle('hidden', matched > 0);
        document.getElementById('loadMoreDueCustomersContainer').classList.add('hidden');
        document.getElementById('visibleDueCount').innerText = matched;
    }

    function clearDueCustomerSearch() {
        const searchInput = document.getElementById('dueCustomerSearchInput');
        if (searchInput) {
            searchInput.value = '';
            document.getElementById('clearDueCustomerSearchBtn').classList.add('hidden');
            document.getElementById('noDueCustomerSearchResultRow').classList.add('hidden');
            applyDueVisibility();
            searchInput.focus();
        }
    }
</script>
@endpush
@endsection

