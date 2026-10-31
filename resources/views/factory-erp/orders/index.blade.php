@extends('layouts.app')

@section('title', 'Orders Management | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Controls -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-cart-shopping text-cyan-400"></i>
                Orders & Sales Invoices
            </h2>
            <p class="text-xs text-slate-400">Manage client orders, invoice amounts, paid/due tracking, and delivery progress</p>
        </div>

        <a href="{{ route('factory.orders.create') }}" 
           class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-bold text-xs shadow-lg shadow-cyan-500/25 hover:from-cyan-400 hover:to-blue-500 transition-all flex items-center gap-2">
            <i class="fa-solid fa-cart-plus text-sm"></i>
            <span>+ Create New Order</span>
        </a>
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

    <div class="glass-panel rounded-3xl p-5 border border-slate-700/60">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <div>
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-cyan-400"></i>
                    All Recorded Orders
                </h3>
                <span id="orderCountDisplay" class="text-[11px] text-cyan-300 font-mono">
                    Showing <span id="visibleCount">{{ min(15, $orders->count()) }}</span> of <span id="totalCount">{{ $orders->count() }}</span> Orders
                </span>
            </div>

            <!-- Live Search Box (Order #, Customer, Phone) -->
            <div class="relative w-full sm:w-80">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="orderSearchInput" placeholder="Search by Order #, Customer, Phone..." 
                       class="w-full bg-[#070d1e] text-xs text-white placeholder-slate-500 rounded-xl pl-8 pr-7 py-2 border border-slate-700 focus:border-cyan-400 focus:outline-none transition-all">
                <button type="button" id="clearOrderSearchBtn" onclick="clearOrderSearch()" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white hidden text-xs">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-700/80 font-semibold">
                        <th class="pb-2.5">Order / Inv #</th>
                        <th class="pb-2.5">Customer Name</th>
                        <th class="pb-2.5">Date</th>
                        <th class="pb-2.5">Ordered Items</th>
                        <th class="pb-2.5 text-right">Total Price</th>
                        <th class="pb-2.5 text-right">Paid</th>
                        <th class="pb-2.5 text-right">Due</th>
                        <th class="pb-2.5 text-center">Delivery Status</th>
                        <th class="pb-2.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="ordersTableBody" class="divide-y divide-slate-800/80">
                    @forelse($orders as $ord)
                    @php
                        $cPhone = $ord->customer->phone ?? '';
                        $rawDigits = preg_replace('/[^0-9]/', '', $cPhone);
                        $localBd = preg_replace('/^880?/', '0', $rawDigits);
                        $noZero = ltrim($localBd, '0');
                        $searchKeywords = strtolower(implode(' ', array_filter([
                            $ord->order_number,
                            $ord->customer->company_name ?? '',
                            $ord->customer->contact_person ?? '',
                            $cPhone,
                            $rawDigits,
                            $localBd,
                            $noZero,
                            date('d M Y', strtotime($ord->order_date)),
                            $ord->delivery_status
                        ])));
                    @endphp
                    <tr class="order-row hover:bg-slate-800/40 transition-colors"
                        data-search="{{ $searchKeywords }}"
                        data-order-num="{{ strtolower($ord->order_number) }}"
                        data-customer="{{ strtolower($ord->customer->company_name ?? '') }}"
                        data-phone-digits="{{ $rawDigits }}"
                        data-phone-local="{{ $localBd }}"
                        data-phone-raw="{{ $cPhone }}">
                        <td class="py-3 font-mono font-bold text-cyan-300">
                            {{ $ord->order_number }}
                        </td>
                        <td class="py-3">
                            <a href="{{ route('factory.customers.details', $ord->customer_id) }}" class="font-bold text-white hover:text-cyan-400 block transition-colors">
                                {{ $ord->customer->company_name ?? 'N/A' }}
                            </a>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $ord->customer->phone ?? '' }}</span>
                        </td>
                        <td class="py-3 text-slate-300">
                            {{ date('d M, Y', strtotime($ord->order_date)) }}
                        </td>
                        <td class="py-3">
                            <span class="text-slate-300">{{ $ord->items->count() }} Product types</span>
                        </td>
                        <td class="py-3 text-right font-mono font-bold text-white">
                            ৳ {{ number_format($ord->total_amount, 2) }}
                        </td>
                        <td class="py-3 text-right font-mono font-bold text-emerald-400">
                            ৳ {{ number_format($ord->paid_amount, 2) }}
                        </td>
                        <td class="py-3 text-right font-mono font-bold {{ $ord->due_amount > 0 ? 'text-rose-400' : 'text-slate-400' }}">
                            ৳ {{ number_format($ord->due_amount, 2) }}
                        </td>
                        <td class="py-3 text-center">
                            @if($ord->delivery_status === 'delivered')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Delivered</span>
                            @elseif($ord->delivery_status === 'partial')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">Partial</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">Pending</span>
                            @endif
                        </td>
                        <td class="py-3 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('factory.orders.invoice', $ord->id) }}" title="View Invoice"
                                   class="px-2 py-1 rounded-lg bg-blue-600/20 hover:bg-blue-600/40 text-cyan-300 border border-blue-500/30 text-[11px] font-semibold transition-all inline-flex items-center gap-1">
                                    <i class="fa-solid fa-file-invoice text-[10px]"></i>
                                    <span>Invoice</span>
                                </a>

                                @if($ord->delivery_status !== 'delivered')
                                <a href="{{ route('factory.deliveries.create', $ord->id) }}" title="Process Delivery"
                                   class="px-2 py-1 rounded-lg bg-emerald-600/20 hover:bg-emerald-600/40 text-emerald-300 border border-emerald-500/30 text-[11px] font-semibold transition-all inline-flex items-center gap-1">
                                    <i class="fa-solid fa-truck-fast text-[10px]"></i>
                                    <span>Deliver</span>
                                </a>
                                @endif

                                <button type="button" 
                                        onclick="openEditOrderModal({{ json_encode($ord) }})"
                                        class="px-2 py-1 rounded-lg bg-cyan-600/20 hover:bg-cyan-600/40 text-cyan-300 border border-cyan-500/30 text-[11px] font-semibold transition-all inline-flex items-center gap-1">
                                    <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                    <span>Edit</span>
                                </button>

                                <form action="{{ route('factory.orders.destroy', $ord->id) }}" method="POST" 
                                      onsubmit="return confirm('Delete order \'{{ $ord->order_number }}\'? This will remove the invoice and line items.');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="px-2 py-1 rounded-lg bg-rose-600/20 hover:bg-rose-600/40 text-rose-300 border border-rose-500/30 text-[11px] font-semibold transition-all inline-flex items-center gap-1">
                                        <i class="fa-solid fa-trash-can text-[10px]"></i>
                                        <span>Delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-8 text-center text-slate-500">No orders created yet. Click "+ Create New Order" to start!</td>
                    </tr>
                    @endforelse

                    <!-- No Search Results Row -->
                    <tr id="noOrderSearchResultRow" class="hidden">
                        <td colspan="9" class="py-8 text-center text-slate-500">
                            <i class="fa-solid fa-receipt text-2xl text-slate-600 block mb-2"></i>
                            No matching orders found for "<span id="searchOrderQueryDisplay" class="text-cyan-400 font-semibold"></span>"
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Load More Button (+15) -->
        <div id="loadMoreContainer" class="mt-4 pt-3 border-t border-slate-800/80 text-center {{ $orders->count() <= 15 ? 'hidden' : '' }}">
            <button type="button" id="loadMoreBtn" onclick="loadMoreOrders()"
                    class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-cyan-300 hover:text-white border border-slate-700 hover:border-cyan-500/40 text-xs font-semibold transition-all inline-flex items-center gap-2 shadow-lg">
                <i class="fa-solid fa-arrow-down-long text-[11px]"></i>
                <span>Load More Orders (+15)</span>
            </button>
        </div>
    </div>

</div>

<!-- EDIT ORDER MODAL (GLASS POPUP) -->
<div id="editOrderModal" class="fixed inset-0 z-50 bg-black/75 backdrop-blur-md hidden flex items-center justify-center p-4">
    <div class="glass-panel rounded-3xl p-6 border border-slate-700/80 max-w-md w-full relative shadow-2xl bg-[#0a1228]/95">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-700/80 mb-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-receipt text-cyan-400"></i>
                Edit Order #<span id="modalOrderNumber"></span>
            </h3>
            <button type="button" onclick="closeEditOrderModal()" class="text-slate-400 hover:text-white p-1 text-base">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="editOrderForm" method="POST" class="space-y-3.5">
            @csrf
            @method('PUT')

            <div class="bg-[#070d1e] p-3 rounded-xl border border-slate-800 flex justify-between items-center text-xs">
                <span class="text-slate-400">Total Order Amount:</span>
                <span id="modalTotalAmount" class="text-white font-mono font-bold text-sm"></span>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Paid Amount (৳) *</label>
                <input type="number" step="0.01" min="0" name="paid_amount" id="edit_paid_amount" required
                       class="w-full bg-[#070d1e] text-xs font-bold text-emerald-400 rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Delivery Status *</label>
                <select name="delivery_status" id="edit_delivery_status" required
                        class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                    <option value="pending">Pending</option>
                    <option value="partial">Partial</option>
                    <option value="delivered">Delivered</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Order Notes</label>
                <textarea name="notes" id="edit_order_notes" rows="2"
                          class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3.5 py-2 border border-slate-700 focus:border-cyan-400 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-700/80">
                <button type="button" onclick="closeEditOrderModal()" 
                        class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-5 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-bold text-xs shadow-lg shadow-cyan-500/25 hover:from-cyan-400 hover:to-blue-500 transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-check"></i>
                    <span>Update Order</span>
                </button>
            </div>
        </form>

    </div>
</div>

<script>
    // ========== LIVE SEARCH & LOAD MORE (15 PER BATCH) ==========
    let orderCurrentLimit = 15;
    const ORDER_PAGE_SIZE = 15;

    function updateOrderVisibility() {
        const searchInput = document.getElementById('orderSearchInput');
        const searchVal = searchInput ? searchInput.value.trim().toLowerCase() : '';
        const cleanDigits = searchVal.replace(/[^0-9]/g, '');
        const rows = document.querySelectorAll('.order-row');
        const noResultRow = document.getElementById('noOrderSearchResultRow');
        const loadMoreContainer = document.getElementById('loadMoreContainer');
        const clearBtn = document.getElementById('clearOrderSearchBtn');
        const visibleCountEl = document.getElementById('visibleCount');

        if (clearBtn) {
            clearBtn.classList.toggle('hidden', searchVal === '');
        }

        if (searchVal !== '') {
            // Live Search Mode (Order #, Customer Name, Mobile e.g. 018..., Status, Date)
            let matchCount = 0;
            rows.forEach(row => {
                const data = (row.getAttribute('data-search') || '').toLowerCase();
                const orderNum = (row.getAttribute('data-order-num') || '').toLowerCase();
                const customer = (row.getAttribute('data-customer') || '').toLowerCase();
                const phoneDigits = row.getAttribute('data-phone-digits') || '';
                const phoneLocal = row.getAttribute('data-phone-local') || '';
                const phoneRaw = (row.getAttribute('data-phone-raw') || '').toLowerCase();

                let isMatch = false;

                // 1. Order Number, Customer Name, Full string match
                if (data.includes(searchVal) || orderNum.includes(searchVal) || customer.includes(searchVal) || phoneRaw.includes(searchVal)) {
                    isMatch = true;
                }

                // 2. Mobile digits match (e.g. typing 018..., 18..., 017...)
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
                const querySpan = document.getElementById('searchOrderQueryDisplay');
                if (querySpan) querySpan.innerText = searchVal;
            }

            // Hide load more button while actively searching
            if (loadMoreContainer) loadMoreContainer.classList.add('hidden');
            if (visibleCountEl) visibleCountEl.innerText = matchCount;

        } else {
            // Normal Pagination Mode (15 items per batch)
            if (noResultRow) noResultRow.classList.add('hidden');

            let visibleCount = 0;
            rows.forEach((row, index) => {
                if (index < orderCurrentLimit) {
                    row.classList.remove('hidden');
                    visibleCount++;
                } else {
                    row.classList.add('hidden');
                }
            });

            if (visibleCountEl) visibleCountEl.innerText = visibleCount;

            if (loadMoreContainer) {
                loadMoreContainer.classList.toggle('hidden', orderCurrentLimit >= rows.length);
            }
        }
    }

    function loadMoreOrders() {
        orderCurrentLimit += ORDER_PAGE_SIZE;
        updateOrderVisibility();
    }

    function clearOrderSearch() {
        const input = document.getElementById('orderSearchInput');
        if (input) {
            input.value = '';
            input.focus();
            updateOrderVisibility();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('orderSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', updateOrderVisibility);
        }
        updateOrderVisibility();
    });

    // ========== EDIT ORDER MODAL ==========
    function openEditOrderModal(order) {
        document.getElementById('modalOrderNumber').innerText = order.order_number;
        document.getElementById('modalTotalAmount').innerText = '৳ ' + parseFloat(order.total_amount).toFixed(2);
        document.getElementById('edit_paid_amount').value = parseFloat(order.paid_amount).toFixed(2);
        document.getElementById('edit_delivery_status').value = order.delivery_status;
        document.getElementById('edit_order_notes').value = order.notes || '';
        document.getElementById('editOrderForm').action = `/factory/orders/${order.id}`;
        document.getElementById('editOrderModal').classList.remove('hidden');
    }

    function closeEditOrderModal() {
        document.getElementById('editOrderModal').classList.add('hidden');
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeEditOrderModal();
    });
</script>
@endsection

