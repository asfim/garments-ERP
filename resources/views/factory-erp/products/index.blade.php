@extends('layouts.app')

@section('title', 'Product List | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Controls: Header + PDF Download Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked text-cyan-400"></i>
                Product List
            </h2>
            <p class="text-xs text-slate-400">Manage products, dynamic units, costs, prices with instant Edit & Delete</p>
        </div>

        <div class="flex items-center gap-2.5">
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
    <div class="p-3 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs flex items-center gap-2">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left: Add New Product Form -->
        <div class="lg:col-span-4 glass-panel rounded-3xl p-5 border border-slate-700/60 h-fit">
            <h3 class="text-sm font-bold text-white mb-3 flex items-center gap-2">
                <i class="fa-solid fa-circle-plus text-cyan-400"></i>
                Add New Product
            </h3>

            <form action="{{ route('factory.products.store') }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Product Name *</label>
                    <input type="text" name="name" placeholder="e.g. Cotton Polo T-Shirt" required
                           class="w-full bg-[#0a1228] text-xs text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                </div>

                <!-- Dynamic Unit Dropdown -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-300">Select Unit *</label>
                        <a href="{{ route('factory.units.index') }}" class="text-[10px] text-cyan-400 hover:underline">+ Add Unit</a>
                    </div>
                    <select name="unit_id" required
                            class="w-full bg-[#0a1228] text-xs text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                        <option value="">-- Choose Unit --</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->short_code }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Production Price (৳) *</label>
                        <input type="number" step="0.01" name="production_price" placeholder="0.00" required
                               class="w-full bg-[#0a1228] text-xs text-white rounded-xl px-3 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Sell Price (৳) *</label>
                        <input type="number" step="0.01" name="sell_price" placeholder="0.00" required
                               class="w-full bg-[#0a1228] text-xs text-white rounded-xl px-3 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none font-bold text-cyan-300">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Description / Spec</label>
                    <textarea name="description" rows="2" placeholder="Fabric composition, color, etc."
                              class="w-full bg-[#0a1228] text-xs text-white rounded-xl px-3.5 py-2 border border-slate-700 focus:border-cyan-400 focus:outline-none"></textarea>
                </div>

                <button type="submit" 
                        class="w-full py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-bold text-xs shadow-lg shadow-cyan-500/25 hover:from-cyan-400 hover:to-blue-500 transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-check"></i>
                    <span>Save Product</span>
                </button>
            </form>
        </div>

        <!-- Right: Product List Table with Edit & Delete -->
        <div class="lg:col-span-8 glass-panel rounded-3xl p-5 border border-slate-700/60">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-bold text-white">Registered Products</h3>
                <span class="text-xs text-cyan-400 font-mono">{{ $products->count() }} Items</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 border-b border-slate-700/80 font-semibold">
                            <th class="pb-2.5">#</th>
                            <th class="pb-2.5">Product Name</th>
                            <th class="pb-2.5">Unit</th>
                            <th class="pb-2.5 text-right">Production Cost</th>
                            <th class="pb-2.5 text-right">Selling Price</th>
                            <th class="pb-2.5 text-right">Stock</th>
                            <th class="pb-2.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @forelse($products as $idx => $prod)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-3 text-slate-400 font-mono">{{ $idx + 1 }}</td>
                            <td class="py-3">
                                <span class="font-bold text-white block">{{ $prod->name }}</span>
                                @if($prod->description)
                                    <span class="text-[10px] text-slate-400 truncate max-w-xs block">{{ $prod->description }}</span>
                                @endif
                            </td>
                            <td class="py-3">
                                <span class="px-2 py-0.5 rounded-lg bg-blue-500/10 text-cyan-300 font-medium text-[11px] border border-blue-500/20">
                                    {{ $prod->unit->name ?? 'Pcs' }}
                                </span>
                            </td>
                            <td class="py-3 text-right text-slate-300 font-mono">
                                ৳ {{ number_format($prod->production_price, 2) }}
                            </td>
                            <td class="py-3 text-right font-mono font-bold text-cyan-300 text-sm">
                                ৳ {{ number_format($prod->sell_price, 2) }}
                            </td>
                            <td class="py-3 text-right">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ ($prod->current_stock > 50) ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30' }}">
                                    {{ number_format($prod->current_stock) }} {{ $prod->unit->short_code ?? '' }}
                                </span>
                            </td>
                            <td class="py-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Edit Button (Opens Modal) -->
                                    <button type="button" 
                                            onclick="openEditProductModal({{ json_encode($prod) }})"
                                            class="px-2 py-1 rounded-lg bg-cyan-600/20 hover:bg-cyan-600/40 text-cyan-300 border border-cyan-500/30 text-[11px] font-semibold transition-all inline-flex items-center gap-1">
                                        <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                        <span>Edit</span>
                                    </button>

                                    <!-- Delete Button Form -->
                                    <form action="{{ route('factory.products.destroy', $prod->id) }}" method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete \'{{ $prod->name }}\'? This will remove associated stock and records.');" 
                                          class="inline">
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
                            <td colspan="7" class="py-6 text-center text-slate-500">No products created yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

<!-- EDIT PRODUCT MODAL (GLASS POPUP) -->
<div id="editProductModal" class="fixed inset-0 z-50 bg-black/75 backdrop-blur-md hidden flex items-center justify-center p-4">
    <div class="glass-panel rounded-3xl p-6 border border-slate-700/80 max-w-lg w-full relative shadow-2xl bg-[#0a1228]/95">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-700/80 mb-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-cyan-400"></i>
                Edit Product Details
            </h3>
            <button type="button" onclick="closeEditProductModal()" class="text-slate-400 hover:text-white p-1 text-base">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="editProductForm" method="POST" class="space-y-3.5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Product Name *</label>
                <input type="text" name="name" id="edit_name" required
                       class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Measurement Unit *</label>
                <select name="unit_id" id="edit_unit_id" required
                        class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->short_code }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Production Price (৳) *</label>
                    <input type="number" step="0.01" name="production_price" id="edit_production_price" required
                           class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Selling Price (৳) *</label>
                    <input type="number" step="0.01" name="sell_price" id="edit_sell_price" required
                           class="w-full bg-[#070d1e] text-xs text-cyan-300 font-bold rounded-xl px-3 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Description</label>
                <textarea name="description" id="edit_description" rows="2"
                          class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3.5 py-2 border border-slate-700 focus:border-cyan-400 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-700/80">
                <button type="button" onclick="closeEditProductModal()" 
                        class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-5 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-bold text-xs shadow-lg shadow-cyan-500/25 hover:from-cyan-400 hover:to-blue-500 transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-check"></i>
                    <span>Update Product</span>
                </button>
            </div>
        </form>

    </div>
</div>

<script>
    function openEditProductModal(product) {
        document.getElementById('edit_name').value = product.name;
        document.getElementById('edit_unit_id').value = product.unit_id;
        document.getElementById('edit_production_price').value = parseFloat(product.production_price).toFixed(2);
        document.getElementById('edit_sell_price').value = parseFloat(product.sell_price).toFixed(2);
        document.getElementById('edit_description').value = product.description || '';

        const form = document.getElementById('editProductForm');
        form.action = `/factory/products/${product.id}`;

        document.getElementById('editProductModal').classList.remove('hidden');
    }

    function closeEditProductModal() {
        document.getElementById('editProductModal').classList.add('hidden');
    }

    // Close modal on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeEditProductModal();
        }
    });
</script>
@endsection
