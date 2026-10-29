@extends('layouts.app')

@section('title', 'Production Management | Factory ERP')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-industry text-purple-400"></i>
                Production Floor
            </h2>
            <p class="text-xs text-slate-400">Record finished production: auto-fetches target order quantities & auto-deposits into Stock</p>
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

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left: Record Finished Production Form -->
        <div class="lg:col-span-4 glass-panel rounded-3xl p-5 border border-slate-700/60 h-fit">
            <h3 class="text-sm font-bold text-white mb-3 flex items-center gap-2">
                <i class="fa-solid fa-plus-circle text-purple-400"></i>
                Record Daily Production
            </h3>

            <form action="{{ route('factory.productions.store') }}" method="POST" class="space-y-3.5">
                @csrf
                
                <!-- Product Dropdown -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Select Product *</label>
                    <select name="product_id" id="prodSelect" required onchange="onProductionProductChange(this)"
                            class="w-full bg-[#0a1228] text-xs text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                        <option value="">-- Choose Product to Produce --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" 
                                    data-unit="{{ $p->unit->name ?? 'Pcs' }}" 
                                    data-target="{{ $pendingOrderQty[$p->id] ?? 0 }}">
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Auto Unit Dropdown -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Measurement Unit</label>
                    <input type="text" id="unitDisplay" readonly value="Pcs"
                           class="w-full bg-[#070d1e] text-xs font-mono font-bold text-cyan-300 rounded-xl px-3.5 py-2.5 border border-slate-700/80 cursor-not-allowed">
                </div>

                <!-- Auto Order Target Qty (Editable) -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-300">Target Production Qty</label>
                        <span class="text-[10px] text-amber-300 font-mono">(From pending orders)</span>
                    </div>
                    <input type="number" step="1" min="0" name="target_qty" id="targetQty" value="0" required
                           class="w-full bg-[#0a1228] text-xs text-white font-mono rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                </div>

                <!-- Actual Produced Qty -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Actual Produced Qty (Adds to Stock) *</label>
                    <input type="number" step="1" min="1" name="produced_qty" id="producedQty" value="0" required
                           class="w-full bg-[#0a1228] text-sm font-bold font-mono text-emerald-400 rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Production Date *</label>
                    <input type="date" name="production_date" value="{{ date('Y-m-d') }}" required
                           class="w-full bg-[#0a1228] text-xs text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Shift / Floor Notes</label>
                    <textarea name="notes" rows="2" placeholder="Line #2, Morning shift finished batch"
                              class="w-full bg-[#0a1228] text-xs text-white rounded-xl px-3 py-2 border border-slate-700 focus:border-cyan-400 focus:outline-none"></textarea>
                </div>

                <button type="submit" 
                        class="w-full py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold text-xs shadow-lg shadow-purple-500/25 hover:from-purple-500 hover:to-indigo-500 transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-boxes-packing"></i>
                    <span>Submit & Add to Stock</span>
                </button>
            </form>
        </div>

        <!-- Right: Recent Production Records -->
        <div class="lg:col-span-8 glass-panel rounded-3xl p-5 border border-slate-700/60">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-bold text-white">Factory Production Logs</h3>
                <span class="text-xs text-cyan-400 font-mono">{{ $productions->count() }} Batches</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 border-b border-slate-700/80 font-semibold">
                            <th class="pb-2.5">Date</th>
                            <th class="pb-2.5">Product Name</th>
                            <th class="pb-2.5">Unit</th>
                            <th class="pb-2.5 text-center">Target Qty</th>
                            <th class="pb-2.5 text-center">Produced Qty</th>
                            <th class="pb-2.5 text-center">Status</th>
                            <th class="pb-2.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @forelse($productions as $prod)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-3 text-slate-300 font-medium">
                                {{ date('d M, Y', strtotime($prod->production_date)) }}
                            </td>
                            <td class="py-3 font-bold text-white">
                                {{ $prod->product->name }}
                            </td>
                            <td class="py-3">
                                <span class="px-2 py-0.5 rounded bg-blue-500/10 text-cyan-300 font-mono text-[11px]">
                                    {{ $prod->unit->short_code ?? 'Pcs' }}
                                </span>
                            </td>
                            <td class="py-3 text-center font-mono text-slate-400">
                                {{ number_format($prod->target_qty) }}
                            </td>
                            <td class="py-3 text-center font-mono font-bold text-emerald-400 text-sm">
                                + {{ number_format($prod->produced_qty) }}
                            </td>
                            <td class="py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    In Stock
                                </span>
                            </td>
                            <td class="py-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" 
                                            onclick="openEditProductionModal({{ json_encode($prod) }})"
                                            class="px-2 py-1 rounded-lg bg-cyan-600/20 hover:bg-cyan-600/40 text-cyan-300 border border-cyan-500/30 text-[11px] font-semibold transition-all inline-flex items-center gap-1">
                                        <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                        <span>Edit</span>
                                    </button>

                                    <form action="{{ route('factory.productions.destroy', $prod->id) }}" method="POST" 
                                          onsubmit="return confirm('Delete this production run? It will reverse {{ number_format($prod->produced_qty) }} units from current stock.');" class="inline">
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
                            <td colspan="7" class="py-8 text-center text-slate-500">No production runs recorded yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

<!-- EDIT PRODUCTION MODAL (GLASS POPUP) -->
<div id="editProductionModal" class="fixed inset-0 z-50 bg-black/75 backdrop-blur-md hidden flex items-center justify-center p-4">
    <div class="glass-panel rounded-3xl p-6 border border-slate-700/80 max-w-md w-full relative shadow-2xl bg-[#0a1228]/95">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-700/80 mb-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-boxes-packing text-cyan-400"></i>
                Edit Production Batch
            </h3>
            <button type="button" onclick="closeEditProductionModal()" class="text-slate-400 hover:text-white p-1 text-base">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="editProductionForm" method="POST" class="space-y-3.5">
            @csrf
            @method('PUT')

            <div class="bg-[#070d1e] p-3 rounded-xl border border-slate-800 text-xs">
                <span class="text-slate-400">Product:</span>
                <span id="modalProdName" class="text-white font-bold block mt-0.5"></span>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Target Qty</label>
                    <input type="number" step="1" min="0" name="target_qty" id="edit_target_qty" required
                           class="w-full bg-[#070d1e] text-xs font-mono text-white rounded-xl px-3 py-2 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Produced Qty *</label>
                    <input type="number" step="1" min="1" name="produced_qty" id="edit_produced_qty" required
                           class="w-full bg-[#070d1e] text-xs font-mono font-bold text-emerald-400 rounded-xl px-3 py-2 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Production Date *</label>
                <input type="date" name="production_date" id="edit_production_date" required
                       class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3.5 py-2 border border-slate-700 focus:border-cyan-400 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Shift / Floor Notes</label>
                <textarea name="notes" id="edit_prod_notes" rows="2"
                          class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3.5 py-2 border border-slate-700 focus:border-cyan-400 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-700/80">
                <button type="button" onclick="closeEditProductionModal()" 
                        class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-5 py-2 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold text-xs shadow-lg shadow-purple-500/25 hover:from-purple-500 hover:to-indigo-500 transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-check"></i>
                    <span>Update Batch</span>
                </button>
            </div>
        </form>

    </div>
</div>

<script>
    function onProductionProductChange(sel) {
        const opt = sel.options[sel.selectedIndex];
        const unit = opt.getAttribute('data-unit') || 'Pcs';
        const target = opt.getAttribute('data-target') || 0;

        document.getElementById('unitDisplay').value = unit;
        document.getElementById('targetQty').value = target;
        document.getElementById('producedQty').value = target > 0 ? target : 100;
    }

    function openEditProductionModal(prod) {
        document.getElementById('modalProdName').innerText = prod.product.name + ' (' + (prod.unit ? prod.unit.short_code : '') + ')';
        document.getElementById('edit_target_qty').value = prod.target_qty;
        document.getElementById('edit_produced_qty').value = prod.produced_qty;
        document.getElementById('edit_production_date').value = prod.production_date.split('T')[0];
        document.getElementById('edit_prod_notes').value = prod.notes || '';
        document.getElementById('editProductionForm').action = `/factory/productions/${prod.id}`;
        document.getElementById('editProductionModal').classList.remove('hidden');
    }

    function closeEditProductionModal() {
        document.getElementById('editProductionModal').classList.add('hidden');
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeEditProductionModal();
    });
</script>
@endsection

