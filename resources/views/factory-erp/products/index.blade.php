@extends('layouts.app')

@section('title', 'Product List | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Controls: Header + Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked text-cyan-400"></i>
                Product List
            </h2>
            <p class="text-xs text-slate-400">Manage products, dynamic units, costs, prices with instant Edit & Delete</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Separate Add Product Button (Opens Popup) -->
            <button type="button" onclick="openAddProductModal()" 
                    class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold text-xs shadow-lg shadow-cyan-500/25 transition-all flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-circle-plus text-sm"></i>
                <span>+ Add New Product</span>
            </button>

            <!-- PDF Download Button (Bound to Company Settings) -->
            <a href="{{ route('factory.products.pdf') }}" 
               class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-rose-500 to-pink-600 text-white font-bold text-xs shadow-lg shadow-rose-500/25 hover:from-rose-400 hover:to-pink-500 transition-all flex items-center gap-2">
                <i class="fa-solid fa-file-pdf text-sm"></i>
                <span>Download Price PDF</span>
            </a>

            <!-- Quick Add Unit link -->
            <a href="{{ route('factory.units.index') }}" 
               class="px-3.5 py-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-scale-balanced text-cyan-400"></i>
                <span>Manage Units</span>
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="p-3.5 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs flex items-center gap-2">
        <i class="fa-solid fa-circle-check text-sm"></i>
        <span>{{ session('success') }}</span>
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

    <!-- Full-Width Registered Products Table Panel -->
    <div class="glass-panel rounded-3xl p-6 border border-slate-700/60 shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 pb-3 border-b border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-cyan-500/10 text-cyan-400 flex items-center justify-center font-bold">
                    <i class="fa-solid fa-list-check text-sm"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Registered Products</h3>
                    <p class="text-[11px] text-slate-400">Active products inventory and rate sheet</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- Live Search in Table -->
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-500 text-xs"></i>
                    <input type="text" id="productSearchInput" onkeyup="filterProductsTable()" placeholder="Search product..." 
                           class="bg-slate-950/80 text-xs text-white rounded-xl pl-8 pr-3 py-1.5 border border-slate-700 focus:border-cyan-400 focus:outline-none w-48 sm:w-64">
                </div>
                <span class="text-xs text-cyan-400 font-mono font-bold bg-cyan-500/10 px-3 py-1.5 rounded-xl border border-cyan-500/20">
                    Total: {{ $products->count() }}
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs" id="productsTable">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-700/80 font-semibold uppercase tracking-wider text-[11px]">
                        <th class="py-3 px-3">#</th>
                        <th class="py-3 px-3">Product Name & Specifications</th>
                        <th class="py-3 px-3 text-center">Unit</th>
                        <th class="py-3 px-3 text-right">Production Cost</th>
                        <th class="py-3 px-3 text-right">Selling Price</th>
                        <th class="py-3 px-3 text-center">Current Stock</th>
                        <th class="py-3 px-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80" id="productsTableBody">
                    @forelse($products as $idx => $prod)
                    <tr class="hover:bg-slate-800/40 transition-colors product-row {{ $idx >= 15 ? 'hidden' : '' }}" data-index="{{ $idx }}">
                        <td class="py-3.5 px-3 text-slate-400 font-mono">{{ $idx + 1 }}</td>
                        <td class="py-3.5 px-3">
                            <span class="font-bold text-white text-sm block product-name">{{ $prod->name }}</span>
                            @if($prod->description)
                                <span class="text-[11px] text-slate-400 block mt-0.5 product-desc">{{ $prod->description }}</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-3 text-center">
                            <span class="px-2.5 py-1 rounded-lg bg-blue-500/10 text-cyan-300 font-semibold text-xs border border-blue-500/20">
                                {{ $prod->unit->name ?? 'Pcs' }} ({{ $prod->unit->short_code ?? '' }})
                            </span>
                        </td>
                        <td class="py-3.5 px-3 text-right text-slate-300 font-mono">
                            ৳ {{ number_format($prod->production_price, 2) }}
                        </td>
                        <td class="py-3.5 px-3 text-right font-mono font-bold text-cyan-300 text-sm">
                            ৳ {{ number_format($prod->sell_price, 2) }}
                        </td>
                        <td class="py-3.5 px-3 text-center">
                            @php
                                $stockQty = $prod->stock ? $prod->stock->current_stock : ($prod->current_stock ?? 0);
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-bold font-mono inline-block {{ ($stockQty > 50) ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : ($stockQty > 0 ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30') }}">
                                {{ number_format($stockQty) }} {{ $prod->unit->short_code ?? '' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-3 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <!-- Edit Button (Opens Modal) -->
                                <button type="button" 
                                        onclick="openEditProductModal({{ json_encode($prod) }})"
                                        class="px-2.5 py-1.5 rounded-lg bg-cyan-600/20 hover:bg-cyan-600/40 text-cyan-300 border border-cyan-500/30 text-xs font-semibold transition-all inline-flex items-center gap-1.5 cursor-pointer">
                                    <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                    <span>Edit</span>
                                </button>

                                <!-- Delete Button Form -->
                                <form action="{{ route('factory.products.destroy', $prod->id) }}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete \'{{ $prod->name }}\'? This will remove associated stock and records.');" 
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="px-2.5 py-1.5 rounded-lg bg-rose-600/20 hover:bg-rose-600/40 text-rose-300 border border-rose-500/30 text-xs font-semibold transition-all inline-flex items-center gap-1.5 cursor-pointer">
                                        <i class="fa-solid fa-trash-can text-[11px]"></i>
                                        <span>Delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-500">
                            <i class="fa-solid fa-box-open text-3xl mb-2 opacity-50 block"></i>
                            No products created yet. Click "Add New Product" to register your first product.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PRODUCT LOAD MORE CONTAINER -->
        @if($products->count() > 0)
        <div id="productLoadMoreContainer" class="p-4 border-t border-slate-800 bg-slate-950/40 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="text-xs text-slate-400 font-mono">
                Showing <span id="productShowingCount" class="text-cyan-400 font-bold">{{ min(15, $products->count()) }}</span> of <span id="productTotalCount" class="text-white font-bold">{{ $products->count() }}</span> products
            </div>

            @if($products->count() > 15)
            <button type="button" 
                    id="productLoadMoreBtn"
                    onclick="loadMoreProducts()"
                    class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white text-xs font-bold shadow-lg shadow-cyan-950/50 flex items-center gap-2 transition-all cursor-pointer">
                <i class="fa-solid fa-circle-down"></i>
                <span id="productLoadText">Load More Products</span>
            </button>
            @else
            <div id="productAllLoaded" class="text-xs text-slate-500 font-semibold italic flex items-center gap-1.5">
                <i class="fa-solid fa-check text-emerald-400"></i> All products loaded
            </div>
            @endif
        </div>
        @endif
    </div>

</div>

<!-- 1. ADD NEW PRODUCT MODAL (POPUP) -->
<div id="addProductModal" onclick="if(event.target === this) closeAddProductModal()" class="fixed inset-0 z-[9999] bg-slate-950/85 backdrop-blur-sm hidden flex items-center justify-center p-4 overflow-y-auto transform-gpu">
    <div onclick="event.stopPropagation()" class="bg-slate-900 border border-slate-700/80 rounded-3xl w-full max-w-lg shadow-2xl p-6 relative my-8">
        <button type="button" onclick="closeAddProductModal()" class="absolute top-5 right-5 text-slate-400 hover:text-white text-lg cursor-pointer">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="flex items-center gap-3 border-b border-slate-800 pb-4 mb-5">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-cyan-600/30 to-blue-600/30 border border-cyan-500/30 text-cyan-400 flex items-center justify-center">
                <i class="fa-solid fa-circle-plus text-lg"></i>
            </div>
            <div>
                <h3 class="text-base font-extrabold text-white">Add New Product</h3>
                <p class="text-xs text-slate-400">Register new item with unit, production cost, and selling price</p>
            </div>
        </div>

        <form action="{{ route('factory.products.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf

            <!-- Product Name -->
            <div>
                <label class="block text-slate-300 font-bold mb-1.5">Product Name <span class="text-rose-400">*</span></label>
                <input type="text" name="name" placeholder="e.g. Jumbo straw With your branding" required
                       class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-400">
            </div>

            <!-- Unit -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-slate-300 font-bold">Measurement Unit <span class="text-rose-400">*</span></label>
                    <a href="{{ route('factory.units.index') }}" target="_blank" class="text-[11px] text-cyan-400 hover:underline flex items-center gap-1 font-semibold">
                        <i class="fa-solid fa-plus text-[10px]"></i> Add / Manage Units
                    </a>
                </div>
                <select name="unit_id" required
                        class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-400">
                    <option value="">-- Choose Unit --</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->short_code }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Production Cost & Selling Price -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 font-bold mb-1.5">Production Cost (৳) <span class="text-rose-400">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-slate-400 font-bold">৳</span>
                        <input type="number" step="0.01" min="0" name="production_price" placeholder="0.00" required
                               class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl pl-8 pr-3 py-2.5 text-slate-200 font-mono focus:outline-none focus:border-cyan-400">
                    </div>
                </div>
                <div>
                    <label class="block text-slate-300 font-bold mb-1.5">Selling Price (৳) <span class="text-rose-400">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-cyan-400 font-bold">৳</span>
                        <input type="number" step="0.01" min="0" name="sell_price" placeholder="0.00" required
                               class="w-full bg-slate-950/80 border border-cyan-500/50 rounded-xl pl-8 pr-3 py-2.5 text-cyan-300 font-mono font-bold text-sm focus:outline-none focus:border-cyan-400">
                    </div>
                </div>
            </div>

            <!-- Description / Specifications -->
            <div>
                <label class="block text-slate-300 font-bold mb-1.5">Description / Specifications</label>
                <textarea name="description" rows="3" placeholder="e.g. Food-grade PLA, 8mm diameter, customized wrapper"
                          class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3.5 py-2 text-slate-200 focus:outline-none focus:border-cyan-400"></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <button type="button" onclick="closeAddProductModal()" 
                        class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold transition-all cursor-pointer">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold shadow-lg shadow-cyan-500/25 transition-all flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-check"></i>
                    <span>Save Product</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 2. EDIT PRODUCT MODAL (POPUP) -->
<div id="editProductModal" onclick="if(event.target === this) closeEditProductModal()" class="fixed inset-0 z-[9999] bg-slate-950/85 backdrop-blur-sm hidden flex items-center justify-center p-4 overflow-y-auto transform-gpu">
    <div onclick="event.stopPropagation()" class="bg-slate-900 border border-slate-700/80 rounded-3xl w-full max-w-lg shadow-2xl p-6 relative my-8">
        
        <button type="button" onclick="closeEditProductModal()" class="absolute top-5 right-5 text-slate-400 hover:text-white text-lg cursor-pointer">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="flex items-center gap-3 border-b border-slate-800 pb-4 mb-5">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-cyan-600/30 to-blue-600/30 border border-cyan-500/30 text-cyan-400 flex items-center justify-center">
                <i class="fa-solid fa-pen-to-square text-lg"></i>
            </div>
            <div>
                <h3 class="text-base font-extrabold text-white">Edit Product Details</h3>
                <p class="text-xs text-slate-400">Update product name, unit, cost and selling rates</p>
            </div>
        </div>

        <form id="editProductForm" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-slate-300 font-bold mb-1.5">Product Name <span class="text-rose-400">*</span></label>
                <input type="text" name="name" id="edit_name" required
                       class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-400">
            </div>

            <div>
                <label class="block text-slate-300 font-bold mb-1.5">Measurement Unit <span class="text-rose-400">*</span></label>
                <select name="unit_id" id="edit_unit_id" required
                        class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-400">
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->short_code }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 font-bold mb-1.5">Production Cost (৳) <span class="text-rose-400">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-slate-400 font-bold">৳</span>
                        <input type="number" step="0.01" min="0" name="production_price" id="edit_production_price" required
                               class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl pl-8 pr-3 py-2.5 text-slate-200 font-mono focus:outline-none focus:border-cyan-400">
                    </div>
                </div>
                <div>
                    <label class="block text-slate-300 font-bold mb-1.5">Selling Price (৳) <span class="text-rose-400">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-cyan-400 font-bold">৳</span>
                        <input type="number" step="0.01" min="0" name="sell_price" id="edit_sell_price" required
                               class="w-full bg-slate-950/80 border border-cyan-500/50 rounded-xl pl-8 pr-3 py-2.5 text-cyan-300 font-mono font-bold text-sm focus:outline-none focus:border-cyan-400">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-slate-300 font-bold mb-1.5">Description / Specifications</label>
                <textarea name="description" id="edit_description" rows="3"
                          class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3.5 py-2 text-slate-200 focus:outline-none focus:border-cyan-400"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <button type="button" onclick="closeEditProductModal()" 
                        class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold transition-all cursor-pointer">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold shadow-lg shadow-cyan-500/25 transition-all flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-check"></i>
                    <span>Update Product</span>
                </button>
            </div>
        </form>

    </div>
</div>

<script>
    // 1. Add Product Modal Handlers
    function openAddProductModal() {
        const m = document.getElementById('addProductModal');
        if (m) m.classList.remove('hidden');
    }

    function closeAddProductModal() {
        const m = document.getElementById('addProductModal');
        if (m) m.classList.add('hidden');
    }

    // 2. Edit Product Modal Handlers
    function openEditProductModal(product) {
        document.getElementById('edit_name').value = product.name || '';
        document.getElementById('edit_unit_id').value = product.unit_id || '';
        document.getElementById('edit_production_price').value = parseFloat(product.production_price || 0).toFixed(2);
        document.getElementById('edit_sell_price').value = parseFloat(product.sell_price || 0).toFixed(2);
        document.getElementById('edit_description').value = product.description || '';

        const form = document.getElementById('editProductForm');
        form.action = `/factory/products/${product.id}`;

        const m = document.getElementById('editProductModal');
        if (m) m.classList.remove('hidden');
    }

    function closeEditProductModal() {
        const m = document.getElementById('editProductModal');
        if (m) m.classList.add('hidden');
    }

    // 3. Live Search Filter in Table (with Load More support)
    let productPageSize = 15;
    let currentProductVisible = Math.min(productPageSize, {{ $products->count() }});
    const totalProducts = {{ $products->count() }};

    function filterProductsTable() {
        const input = document.getElementById('productSearchInput');
        const filter = input.value.toLowerCase().trim();
        const rows = document.querySelectorAll('.product-row');
        const container = document.getElementById('productLoadMoreContainer');

        if (filter.length > 0) {
            // When actively searching, hide the load more button and search across all products
            if (container) container.classList.add('hidden');

            rows.forEach(row => {
                const name = row.querySelector('.product-name')?.textContent?.toLowerCase() || '';
                const desc = row.querySelector('.product-desc')?.textContent?.toLowerCase() || '';
                if (name.includes(filter) || desc.includes(filter)) {
                    row.classList.remove('hidden');
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        } else {
            // When search is cleared, restore progressive load more visibility
            if (container) container.classList.remove('hidden');

            rows.forEach((row, idx) => {
                row.style.display = '';
                if (idx < currentProductVisible) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });
        }
    }

    // 4. Progressive Load More Products
    function loadMoreProducts() {
        const hiddenRows = document.querySelectorAll('.product-row.hidden');
        let toShow = Math.min(productPageSize, hiddenRows.length);
        for (let i = 0; i < toShow; i++) {
            hiddenRows[i].classList.remove('hidden');
        }
        currentProductVisible += toShow;

        const countEl = document.getElementById('productShowingCount');
        if (countEl) countEl.textContent = currentProductVisible;

        if (currentProductVisible >= totalProducts || hiddenRows.length <= toShow) {
            const btn = document.getElementById('productLoadMoreBtn');
            if (btn) btn.remove();
            const container = document.getElementById('productLoadMoreContainer');
            if (container && !document.getElementById('productAllLoaded')) {
                const doneDiv = document.createElement('div');
                doneDiv.id = 'productAllLoaded';
                doneDiv.className = 'text-xs text-slate-500 font-semibold italic flex items-center gap-1.5';
                doneDiv.innerHTML = '<i class="fa-solid fa-check text-emerald-400"></i> All products loaded';
                container.appendChild(doneDiv);
            }
        }
    }

    // 5. Close modals on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeAddProductModal();
            closeEditProductModal();
        }
    });
</script>
@endsection
