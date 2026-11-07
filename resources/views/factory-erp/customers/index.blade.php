@extends('layouts.app')

@section('title', 'Customers Management | Factory ERP')

@section('content')
<style>
    .clients-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .clients-scrollbar::-webkit-scrollbar-track {
        background: rgba(15, 23, 42, 0.6);
        border-radius: 8px;
    }
    .clients-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(56, 189, 248, 0.25);
        border-radius: 8px;
    }
    .clients-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(56, 189, 248, 0.5);
    }
</style>

<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-user-group text-cyan-400"></i>
                Customer Directory & Ledger
            </h2>
            <p class="text-xs text-slate-400">Company name, phone, address, and comprehensive order & payment histories</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Add New Customer Button (Opens Popup Modal) -->
            <button type="button" onclick="openAddCustomerModal()" 
                    class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold text-xs shadow-lg shadow-cyan-500/25 transition-all flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-user-plus text-sm"></i>
                <span>+ Add New Customer</span>
            </button>

            <!-- Due Payment Button -->
            <a href="{{ route('factory.customers.due.index') }}" 
               class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-rose-500 via-pink-600 to-amber-600 hover:from-rose-400 hover:to-amber-500 text-white font-bold text-xs shadow-lg shadow-rose-500/25 transition-all flex items-center gap-2 border border-rose-400/30">
                <i class="fa-solid fa-hand-holding-dollar text-sm"></i>
                <span>Due Payment</span>
                <span class="ml-1 px-2 py-0.5 rounded-full text-[10px] bg-black/40 font-mono font-extrabold text-white">
                    {{ $dueCustomersCount ?? 0 }}
                </span>
            </a>
        </div>
    </div>

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
    @if($errors->any())
    <div class="p-3.5 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs space-y-1">
        <div class="font-bold flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span>Please correct the errors:</span>
        </div>
        <ul class="list-disc list-inside text-[11px] space-y-0.5 pl-2">
            @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Full-Width Registered Clients Table Panel -->
    <div class="glass-panel rounded-3xl p-6 border border-slate-700/60 shadow-xl">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                <div class="flex items-center gap-3 flex-wrap">
                    <div>
                        <h3 class="text-sm font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-users text-cyan-400"></i>
                            Registered Clients
                        </h3>
                        <span id="customerCountDisplay" class="text-[11px] text-cyan-300 font-mono">
                            Showing <span class="visibleCountDisplay font-bold">{{ min(10, $customers->count()) }}</span> of <span class="totalCountDisplay font-bold">{{ $customers->count() }}</span> Customers
                        </span>
                    </div>

                    <!-- Quick Due Payment Link -->
                    <a href="{{ route('factory.customers.due.index') }}" 
                       class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-rose-500/20 to-amber-500/20 hover:from-rose-500/30 hover:to-amber-500/30 text-rose-300 hover:text-white border border-rose-500/40 text-xs font-bold transition-all inline-flex items-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-hand-holding-dollar text-rose-400"></i>
                        <span>Due Payment ({{ $dueCustomersCount ?? 0 }})</span>
                        <i class="fa-solid fa-chevron-right text-[9px]"></i>
                    </a>
                </div>

                <!-- Live Search Box (Supports Name, Mobile e.g. 018..., Person, Address) -->
                <div class="relative w-full sm:w-72">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="customerSearchInput" placeholder="Search by Name, Mobile (e.g. 018...)" 
                           class="w-full bg-[#070d1e] text-xs text-white placeholder-slate-500 rounded-xl pl-8 pr-7 py-2 border border-slate-700 focus:border-cyan-400 focus:outline-none transition-all">
                    <button type="button" id="clearSearchBtn" onclick="clearCustomerSearch()" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white hidden text-xs">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

            <!-- Scrollable Table Container -->
            <div class="overflow-x-auto overflow-y-auto max-h-[500px] rounded-2xl border border-slate-800/80 bg-slate-950/20 clients-scrollbar">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="sticky top-0 z-10 bg-[#0c1429] backdrop-blur-md shadow-sm">
                        <tr class="text-slate-400 border-b border-slate-700/80 font-semibold uppercase tracking-wider text-[11px]">
                            <th class="py-3 px-3">#</th>
                            <th class="py-3 px-3">Company Name</th>
                            <th class="py-3 px-3">Phone & Person</th>
                            <th class="py-3 px-3 text-right">Total Ordered</th>
                            <th class="py-3 px-3 text-right">Total Due</th>
                            <th class="py-3 px-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody id="customersTableBody" class="divide-y divide-slate-800/80">
                        @forelse($customers as $idx => $c)
                        @php
                            $rawDigits = preg_replace('/[^0-9]/', '', $c->phone);
                            $localBd = preg_replace('/^880?/', '0', $rawDigits);
                            $noZero = ltrim($localBd, '0');
                            $searchKeywords = strtolower(implode(' ', array_filter([
                                $c->company_name,
                                $c->phone,
                                $rawDigits,
                                $localBd,
                                $noZero,
                                $c->contact_person,
                                $c->address
                            ])));
                        @endphp
                        <tr class="customer-row hover:bg-slate-800/40 transition-colors {{ $idx >= 10 ? 'hidden' : '' }}"
                            data-search="{{ $searchKeywords }}"
                            data-phone-digits="{{ $rawDigits }}"
                            data-phone-local="{{ $localBd }}"
                            data-phone-raw="{{ $c->phone }}">
                            <td class="py-3 px-3 text-slate-400 font-mono">{{ $idx + 1 }}</td>
                            <td class="py-3 px-3">
                                <span class="font-bold text-white block">{{ $c->company_name }}</span>
                                <span class="text-[10px] text-slate-400 truncate max-w-xs block">{{ $c->address }}</span>
                            </td>
                            <td class="py-3 px-3">
                                <span class="text-cyan-300 font-mono font-medium block">{{ $c->phone }}</span>
                                <span class="text-[10px] text-slate-400 block">{{ $c->contact_person ?? 'Owner' }}</span>
                            </td>
                            <td class="py-3 px-3 text-right font-mono font-bold text-white">
                                ৳ {{ number_format($c->total_ordered, 2) }}
                            </td>
                            <td class="py-3 px-3 text-right font-mono font-bold {{ $c->total_due > 0 ? 'text-rose-400' : 'text-emerald-400' }}">
                                ৳ {{ number_format($c->total_due, 2) }}
                            </td>
                            <td class="py-3 px-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('factory.customers.details', $c->id) }}" 
                                       title="View Full Ledger History"
                                       class="w-7 h-7 rounded-lg bg-blue-600/20 hover:bg-blue-600/40 text-cyan-300 border border-blue-500/30 transition-all inline-flex items-center justify-center">
                                        <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                                    </a>

                                    <button type="button" 
                                            onclick="openEditCustomerModal({{ json_encode($c) }})"
                                            title="Edit Customer"
                                            class="w-7 h-7 rounded-lg bg-cyan-600/20 hover:bg-cyan-600/40 text-cyan-300 border border-cyan-500/30 transition-all inline-flex items-center justify-center">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>

                                    <form action="{{ route('factory.customers.destroy', $c->id) }}" method="POST" 
                                          onsubmit="return confirm('Delete customer \'{{ $c->company_name }}\'?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                title="Delete Customer"
                                                class="w-7 h-7 rounded-lg bg-rose-600/20 hover:bg-rose-600/40 text-rose-300 border border-rose-500/30 transition-all inline-flex items-center justify-center">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-users text-3xl mb-2 opacity-50 block"></i>
                                No customers registered yet. Click 
                                <button type="button" onclick="openAddCustomerModal()" class="text-cyan-400 font-bold hover:underline cursor-pointer">"Add New Customer"</button> 
                                to register your first client.
                            </td>
                        </tr>
                        @endforelse

                        <!-- No Search Match Row -->
                        <tr id="noSearchResultRow" class="hidden">
                            <td colspan="6" class="py-8 text-center text-slate-500">
                                <i class="fa-solid fa-user-slash text-2xl text-slate-600 block mb-2"></i>
                                No matching clients found for "<span id="searchQueryDisplay" class="text-cyan-400 font-semibold"></span>"
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Load More Controls & Status Footer -->
            <div id="loadMoreContainer" class="mt-4 pt-3 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-xs text-slate-400 font-mono">
                    Showing <span class="visibleCountDisplay text-cyan-400 font-bold">{{ min(10, $customers->count()) }}</span> of <span class="totalCountDisplay text-white font-bold">{{ $customers->count() }}</span> Customers
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" 
                            id="loadMoreBtn" 
                            onclick="loadMoreCustomers()"
                            class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white text-xs font-bold shadow-lg shadow-cyan-950/50 flex items-center gap-2 transition-all cursor-pointer {{ $customers->count() <= 10 ? 'hidden' : '' }}">
                        <i class="fa-solid fa-circle-down"></i>
                        <span>Load More Customers (+10)</span>
                    </button>
                    <div id="allLoadedMessage" class="text-xs text-slate-500 font-semibold italic flex items-center gap-1.5 {{ $customers->count() <= 10 ? '' : 'hidden' }}">
                        <i class="fa-solid fa-check text-emerald-400"></i> All clients loaded
                    </div>
                </div>
            </div>
        </div>

</div>

<!-- 1. ADD NEW CUSTOMER MODAL (GLASS POPUP) -->
<div id="addCustomerModal" onclick="if(event.target === this) closeAddCustomerModal()" class="fixed inset-0 z-50 bg-black/75 backdrop-blur-md hidden flex items-center justify-center p-4">
    <div onclick="event.stopPropagation()" class="glass-panel rounded-3xl p-6 border border-slate-700/80 max-w-lg w-full relative shadow-2xl bg-[#0a1228]/95">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-700/80 mb-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-cyan-400"></i>
                Add New Customer
            </h3>
            <button type="button" onclick="closeAddCustomerModal()" class="text-slate-400 hover:text-white p-1 text-base cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('factory.customers.store') }}" method="POST" class="space-y-3.5">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Company / Showroom Name *</label>
                <input type="text" name="company_name" placeholder="e.g. Rahim Traders & Showroom" required
                       class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Contact Person</label>
                    <input type="text" name="contact_person" placeholder="e.g. Md. Rahim Ali"
                           class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Phone Number *</label>
                    <input type="text" name="phone" placeholder="e.g. +880 1823-112233" required
                           class="w-full bg-[#070d1e] text-xs text-cyan-300 font-mono rounded-xl px-3 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Factory / Office Address</label>
                <textarea name="address" rows="2.5" placeholder="Shop #, Market, Area, City"
                          class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3.5 py-2 border border-slate-700 focus:border-cyan-400 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-700/80">
                <button type="button" onclick="closeAddCustomerModal()" 
                        class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition-colors cursor-pointer">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-5 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-bold text-xs shadow-lg shadow-cyan-500/25 hover:from-cyan-400 hover:to-blue-500 transition-all flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-check"></i>
                    <span>Save Customer</span>
                </button>
            </div>
        </form>

    </div>
</div>

<!-- 2. EDIT CUSTOMER MODAL (GLASS POPUP) -->
<div id="editCustomerModal" onclick="if(event.target === this) closeEditCustomerModal()" class="fixed inset-0 z-50 bg-black/75 backdrop-blur-md hidden flex items-center justify-center p-4">
    <div onclick="event.stopPropagation()" class="glass-panel rounded-3xl p-6 border border-slate-700/80 max-w-lg w-full relative shadow-2xl bg-[#0a1228]/95">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-700/80 mb-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-user-pen text-cyan-400"></i>
                Edit Customer Details
            </h3>
            <button type="button" onclick="closeEditCustomerModal()" class="text-slate-400 hover:text-white p-1 text-base cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="editCustomerForm" method="POST" class="space-y-3.5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Company / Client Name *</label>
                <input type="text" name="company_name" id="edit_company_name" required
                       class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Contact Person</label>
                    <input type="text" name="contact_person" id="edit_contact_person"
                           class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Phone Number *</label>
                    <input type="text" name="phone" id="edit_phone" required
                           class="w-full bg-[#070d1e] text-xs text-cyan-300 font-mono rounded-xl px-3 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Factory / Office Address</label>
                <textarea name="address" id="edit_address" rows="2"
                          class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3.5 py-2 border border-slate-700 focus:border-cyan-400 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-700/80">
                <button type="button" onclick="closeEditCustomerModal()" 
                        class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-5 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-bold text-xs shadow-lg shadow-cyan-500/25 hover:from-cyan-400 hover:to-blue-500 transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-check"></i>
                    <span>Update Customer</span>
                </button>
            </div>
        </form>

    </div>
</div>

<script>
    // ========== LIVE SEARCH & LOAD MORE (10 AT A TIME) ==========
    let currentLimit = 10;
    const PAGE_SIZE = 10;

    function updateCustomerVisibility() {
        const searchInput = document.getElementById('customerSearchInput');
        const searchVal = searchInput ? searchInput.value.trim().toLowerCase() : '';
        const rows = document.querySelectorAll('.customer-row');
        const noResultRow = document.getElementById('noSearchResultRow');
        const loadMoreContainer = document.getElementById('loadMoreContainer');
        const loadMoreBtn = document.getElementById('loadMoreBtn');
        const allLoadedMessage = document.getElementById('allLoadedMessage');
        const clearBtn = document.getElementById('clearSearchBtn');
        const visibleCountEls = document.querySelectorAll('.visibleCountDisplay');

        if (clearBtn) {
            clearBtn.classList.toggle('hidden', searchVal === '');
        }

        if (searchVal !== '') {
            // Live Search Mode (matches Company Name, Mobile e.g. 018..., Contact Person, Address)
            const cleanDigits = searchVal.replace(/[^0-9]/g, '');
            let matchCount = 0;

            rows.forEach(row => {
                const data = (row.getAttribute('data-search') || '').toLowerCase();
                const phoneDigits = row.getAttribute('data-phone-digits') || '';
                const phoneLocal = row.getAttribute('data-phone-local') || '';
                const phoneRaw = (row.getAttribute('data-phone-raw') || '').toLowerCase();

                let isMatch = false;

                // 1. Text match (company name, contact person, address, full raw phone)
                if (data.includes(searchVal) || phoneRaw.includes(searchVal)) {
                    isMatch = true;
                }

                // 2. Mobile number digit match (e.g. typing 018..., 18..., 017..., +880...)
                if (!isMatch && cleanDigits.length >= 2) {
                    if (phoneDigits.includes(cleanDigits) || phoneLocal.includes(cleanDigits)) {
                        isMatch = true;
                    }
                }

                if (isMatch) {
                    row.classList.remove('hidden');
                    matchCount++;
                } else {
                    row.classList.add('hidden');
                }
            });

            if (noResultRow) {
                noResultRow.classList.toggle('hidden', matchCount > 0);
                const querySpan = document.getElementById('searchQueryDisplay');
                if (querySpan) querySpan.innerText = searchVal;
            }

            // Hide load more button & loaded notice while searching
            if (loadMoreBtn) loadMoreBtn.classList.add('hidden');
            if (allLoadedMessage) allLoadedMessage.classList.add('hidden');
            visibleCountEls.forEach(el => el.innerText = matchCount);

        } else {
            // Normal Pagination Mode (10 items per load)
            if (noResultRow) noResultRow.classList.add('hidden');
            
            let visibleCount = 0;
            rows.forEach((row, index) => {
                if (index < currentLimit) {
                    row.classList.remove('hidden');
                    visibleCount++;
                } else {
                    row.classList.add('hidden');
                }
            });

            visibleCountEls.forEach(el => el.innerText = visibleCount);

            const hasMore = currentLimit < rows.length;
            if (loadMoreBtn) {
                loadMoreBtn.classList.toggle('hidden', !hasMore);
            }
            if (allLoadedMessage) {
                allLoadedMessage.classList.toggle('hidden', hasMore || rows.length === 0);
            }
        }
    }

    function loadMoreCustomers() {
        const tableContainer = document.querySelector('.clients-scrollbar');
        currentLimit += PAGE_SIZE;
        updateCustomerVisibility();

        if (tableContainer) {
            setTimeout(() => {
                tableContainer.scrollBy({
                    top: 140,
                    behavior: 'smooth'
                });
            }, 60);
        }
    }

    function clearCustomerSearch() {
        const input = document.getElementById('customerSearchInput');
        if (input) {
            input.value = '';
            input.focus();
            updateCustomerVisibility();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('customerSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', updateCustomerVisibility);
        }
        updateCustomerVisibility();
    });

    // ========== ADD & EDIT CUSTOMER MODALS ==========
    function openAddCustomerModal() {
        const modal = document.getElementById('addCustomerModal');
        if (modal) modal.classList.remove('hidden');
    }

    function closeAddCustomerModal() {
        const modal = document.getElementById('addCustomerModal');
        if (modal) modal.classList.add('hidden');
    }

    function openEditCustomerModal(customer) {
        document.getElementById('edit_company_name').value = customer.company_name;
        document.getElementById('edit_contact_person').value = customer.contact_person || '';
        document.getElementById('edit_phone').value = customer.phone;
        document.getElementById('edit_address').value = customer.address || '';
        document.getElementById('editCustomerForm').action = `/factory/customers/${customer.id}`;
        document.getElementById('editCustomerModal').classList.remove('hidden');
    }

    function closeEditCustomerModal() {
        document.getElementById('editCustomerModal').classList.add('hidden');
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeAddCustomerModal();
            closeEditCustomerModal();
        }
    });
</script>
@endsection

