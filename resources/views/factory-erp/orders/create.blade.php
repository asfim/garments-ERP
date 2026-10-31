@extends('layouts.app')

@section('title', 'Create Order & Invoice | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-cart-shopping text-cyan-400"></i>
                New Customer Order & Invoice
            </h2>
            <p class="text-xs text-slate-400">Select customer, choose products live, specify quantities & selling prices</p>
        </div>

        <a href="{{ route('factory.orders.index') }}" class="text-xs text-slate-400 hover:text-white flex items-center gap-1">
            <i class="fa-solid fa-arrow-left"></i> All Orders
        </a>
    </div>

    @if(session('error'))
    <div class="p-3 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs flex items-center gap-2">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <form action="{{ route('factory.orders.store') }}" method="POST" id="orderForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- Left: Order Info & Product Items Grid (8 Cols) -->
            <div class="lg:col-span-8 space-y-5">
                
                <!-- Order Top Details -->
                <div class="glass-panel rounded-3xl p-5 border border-slate-700/60">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Invoice / Order #</label>
                            <input type="text" value="{{ $orderNumber }}" readonly
                                   class="w-full bg-[#070d1e] text-xs font-mono font-bold text-cyan-300 rounded-xl px-3.5 py-2.5 border border-slate-700/80 cursor-not-allowed">
                        </div>

                        <!-- Customer Searchable Dropdown -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-semibold text-slate-300">Customer Name *</label>
                                <a href="{{ route('factory.customers.index') }}" class="text-[10px] text-cyan-400 hover:underline">+ New Client</a>
                            </div>
                            <select name="customer_id" required id="customerSelect"
                                    class="w-full bg-[#0a1228] text-xs text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                                <option value="">-- Search / Select Customer --</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}">{{ $c->company_name }} ({{ $c->phone }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Order Date *</label>
                            <input type="date" name="order_date" value="{{ date('Y-m-d') }}" required
                                   style="color-scheme: dark;"
                                   class="w-full bg-[#0a1228] text-xs text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- Multiple Dynamic Product Items Selector -->
                <div class="glass-panel rounded-3xl p-5 border border-slate-700/60">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-boxes-stacked text-cyan-400"></i>
                            Select Ordered Products (Multiple Lines)
                        </h3>
                        <button type="button" onclick="addProductRow()" 
                                class="px-3 py-1.5 rounded-xl bg-blue-600/30 hover:bg-blue-600/50 text-cyan-300 border border-blue-500/40 text-xs font-bold transition-all flex items-center gap-1.5">
                            <i class="fa-solid fa-plus"></i>
                            <span>Add Product Line</span>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs" id="itemsTable">
                            <thead>
                                <tr class="text-slate-400 border-b border-slate-700/80 font-semibold">
                                    <th class="pb-2.5" style="width: 38%;">Product Name (Search & Dropdown)</th>
                                    <th class="pb-2.5" style="width: 14%;">Unit</th>
                                    <th class="pb-2.5" style="width: 16%;">Quantity</th>
                                    <th class="pb-2.5" style="width: 16%;">Sell Price (৳)</th>
                                    <th class="pb-2.5 text-right" style="width: 16%;">Total Price (৳)</th>
                                    <th class="pb-2.5 text-center" style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody" class="divide-y divide-slate-800/80">
                                <!-- Dynamic Rows Inserted Here -->
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Right: Order Summary, Advance / Paid & Submit (4 Cols) -->
            <div class="lg:col-span-4 space-y-5">
                
                <div class="glass-panel rounded-3xl p-5 border border-slate-700/60 sticky top-20">
                    <h3 class="text-sm font-bold text-white mb-4 pb-2 border-b border-slate-700/80">
                        Order Financial Summary
                    </h3>

                    <div class="space-y-3.5">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-400">Total Items Amount</span>
                            <span class="font-bold text-white font-mono text-base" id="summaryTotal">৳ 0.00</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Paid / Advance Amount (৳)</label>
                            <input type="number" step="0.01" name="paid_amount" id="paidInput" value="0" min="0" oninput="calculateTotals()"
                                   class="w-full bg-[#0a1228] text-sm font-bold text-emerald-400 rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                        </div>

                        <div class="p-3 rounded-2xl bg-slate-900/60 border border-slate-700 flex justify-between items-center text-xs">
                            <span class="text-slate-300 font-semibold">Remaining Due:</span>
                            <span class="font-bold text-rose-400 font-mono text-base" id="summaryDue">৳ 0.00</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Notes / Special Instructions</label>
                            <textarea name="notes" rows="2" placeholder="Delivery deadline, packaging notes..."
                                      class="w-full bg-[#0a1228] text-xs text-white rounded-xl px-3 py-2 border border-slate-700 focus:border-cyan-400 focus:outline-none"></textarea>
                        </div>

                        <button type="submit" 
                                class="w-full py-3 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-extrabold text-sm shadow-xl shadow-emerald-500/25 hover:from-emerald-400 hover:to-teal-500 transition-all flex items-center justify-center gap-2 mt-2">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                            <span>Create Order & Generate Invoice</span>
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </form>

</div>

<!-- Products JSON for dynamic row calculations -->
<script>
    const availableProducts = @json($products);
    let rowCount = 0;

    function addProductRow() {
        rowCount++;
        const tbody = document.getElementById('itemsBody');
        const tr = document.createElement('tr');
        tr.id = 'row-' + rowCount;
        tr.className = 'hover:bg-slate-800/40 transition-colors';

        let options = '<option value="">-- Choose Product --</option>';
        availableProducts.forEach(p => {
            options += `<option value="${p.id}" data-price="${p.sell_price}" data-unit="${p.unit ? p.unit.name : 'Pcs'}">${p.name}</option>`;
        });

        tr.innerHTML = `
            <td class="py-2.5">
                <select name="items[${rowCount}][product_id]" required onchange="onProductChange(${rowCount}, this)"
                        class="w-full bg-[#0a1228] text-xs text-white rounded-xl px-2.5 py-2 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                    ${options}
                </select>
            </td>
            <td class="py-2.5">
                <span id="unit-text-${rowCount}" class="px-2 py-1 rounded bg-slate-800 text-cyan-300 font-mono text-[11px] block text-center">Pcs</span>
            </td>
            <td class="py-2.5">
                <input type="number" step="1" min="1" name="items[${rowCount}][qty]" id="qty-${rowCount}" value="1" required oninput="calculateRow(${rowCount})"
                       class="w-full bg-[#0a1228] text-xs text-white font-mono rounded-xl px-2.5 py-2 border border-slate-700 focus:border-cyan-400 focus:outline-none text-center">
            </td>
            <td class="py-2.5">
                <input type="number" step="0.01" min="0" name="items[${rowCount}][sell_price]" id="price-${rowCount}" value="0.00" required oninput="calculateRow(${rowCount})"
                       class="w-full bg-[#0a1228] text-xs text-cyan-300 font-bold font-mono rounded-xl px-2.5 py-2 border border-slate-700 focus:border-cyan-400 focus:outline-none text-right">
            </td>
            <td class="py-2.5 text-right font-mono font-bold text-white" id="total-${rowCount}">
                ৳ 0.00
            </td>
            <td class="py-2.5 text-center">
                <button type="button" onclick="removeRow(${rowCount})" class="text-rose-400 hover:text-rose-300 p-1">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </td>
        `;

        tbody.appendChild(tr);
    }

    function onProductChange(rowId, selectEl) {
        const selectedOpt = selectEl.options[selectEl.selectedIndex];
        const price = selectedOpt.getAttribute('data-price') || 0;
        const unit = selectedOpt.getAttribute('data-unit') || 'Pcs';

        document.getElementById(`price-${rowId}`).value = parseFloat(price).toFixed(2);
        document.getElementById(`unit-text-${rowId}`).textContent = unit;
        calculateRow(rowId);
    }

    function calculateRow(rowId) {
        const qty = parseFloat(document.getElementById(`qty-${rowId}`).value) || 0;
        const price = parseFloat(document.getElementById(`price-${rowId}`).value) || 0;
        const total = qty * price;
        document.getElementById(`total-${rowId}`).textContent = '৳ ' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        calculateTotals();
    }

    function removeRow(rowId) {
        const tr = document.getElementById('row-' + rowId);
        if (tr) {
            tr.remove();
            calculateTotals();
        }
    }

    function calculateTotals() {
        let grandTotal = 0;
        const rows = document.querySelectorAll('#itemsBody tr');
        rows.forEach(tr => {
            const id = tr.id.replace('row-', '');
            const qty = parseFloat(document.getElementById(`qty-${id}`).value) || 0;
            const price = parseFloat(document.getElementById(`price-${id}`).value) || 0;
            grandTotal += (qty * price);
        });

        const paid = parseFloat(document.getElementById('paidInput').value) || 0;
        const due = Math.max(0, grandTotal - paid);

        document.getElementById('summaryTotal').textContent = '৳ ' + grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('summaryDue').textContent = '৳ ' + due.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    // Auto load first 2 rows on open
    document.addEventListener('DOMContentLoaded', () => {
        addProductRow();
    });
</script>
@endsection

