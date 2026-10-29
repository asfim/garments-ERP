@extends('layouts.app')

@section('title', 'Unit Management | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-scale-balanced text-cyan-400"></i>
                Unit Management
            </h2>
            <p class="text-xs text-slate-400">Define dynamic measurement units (Pcs, Dozen, Box, Kg, etc.) used across Products & Production</p>
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
        
        <!-- Left: Add New Unit Form -->
        <div class="lg:col-span-4 glass-panel rounded-3xl p-5 border border-slate-700/60 h-fit">
            <h3 class="text-sm font-bold text-white mb-3 flex items-center gap-2">
                <i class="fa-solid fa-plus-circle text-cyan-400"></i>
                Add New Unit
            </h3>

            <form action="{{ route('factory.units.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Unit Full Name *</label>
                    <input type="text" name="name" placeholder="e.g. Pieces, Dozen, Kilogram" required
                           class="w-full bg-[#0a1228] text-xs text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Short Code / Symbol</label>
                    <input type="text" name="short_code" placeholder="e.g. Pcs, Dzn, Kg"
                           class="w-full bg-[#0a1228] text-xs text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                </div>

                <button type="submit" 
                        class="w-full py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-bold text-xs shadow-lg shadow-cyan-500/25 hover:from-cyan-400 hover:to-blue-500 transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-check"></i>
                    <span>Create Unit</span>
                </button>
            </form>
        </div>

        <!-- Right: Unit List Table -->
        <div class="lg:col-span-8 glass-panel rounded-3xl p-5 border border-slate-700/60">
            <h3 class="text-sm font-bold text-white mb-3 flex items-center justify-between">
                <span>Active Units List</span>
                <span class="text-xs text-cyan-400 font-mono">{{ $units->count() }} Units</span>
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 border-b border-slate-700/80 font-semibold">
                            <th class="pb-2.5">#</th>
                            <th class="pb-2.5">Unit Name</th>
                            <th class="pb-2.5">Short Code</th>
                            <th class="pb-2.5">Total Products</th>
                            <th class="pb-2.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @forelse($units as $idx => $unit)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-3 text-slate-400 font-mono">{{ $idx + 1 }}</td>
                            <td class="py-3 font-bold text-white">{{ $unit->name }}</td>
                            <td class="py-3">
                                <span class="px-2 py-0.5 rounded-lg bg-blue-500/10 text-cyan-300 font-mono text-[11px] border border-blue-500/20">
                                    {{ $unit->short_code ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="py-3 text-slate-300">{{ $unit->products_count }} products</td>
                            <td class="py-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" 
                                            onclick="openEditUnitModal({{ json_encode($unit) }})"
                                            class="px-2 py-1 rounded-lg bg-cyan-600/20 hover:bg-cyan-600/40 text-cyan-300 border border-cyan-500/30 text-[11px] font-semibold transition-all inline-flex items-center gap-1">
                                        <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                        <span>Edit</span>
                                    </button>
                                    <form action="{{ route('factory.units.destroy', $unit->id) }}" method="POST" onsubmit="return confirm('Delete this unit \'{{ $unit->name }}\'?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1 rounded-lg bg-rose-600/20 hover:bg-rose-600/40 text-rose-300 border border-rose-500/30 text-[11px] font-semibold transition-all inline-flex items-center gap-1">
                                            <i class="fa-solid fa-trash-can text-[10px]"></i>
                                            <span>Delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-500">No units added yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

<!-- EDIT UNIT MODAL (GLASS POPUP) -->
<div id="editUnitModal" class="fixed inset-0 z-50 bg-black/75 backdrop-blur-md hidden flex items-center justify-center p-4">
    <div class="glass-panel rounded-3xl p-6 border border-slate-700/80 max-w-md w-full relative shadow-2xl bg-[#0a1228]/95">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-700/80 mb-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-scale-balanced text-cyan-400"></i>
                Edit Unit
            </h3>
            <button type="button" onclick="closeEditUnitModal()" class="text-slate-400 hover:text-white p-1 text-base">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="editUnitForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Unit Full Name *</label>
                <input type="text" name="name" id="edit_unit_name" required
                       class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Short Code / Symbol</label>
                <input type="text" name="short_code" id="edit_unit_short_code"
                       class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-700/80">
                <button type="button" onclick="closeEditUnitModal()" 
                        class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-5 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-bold text-xs shadow-lg shadow-cyan-500/25 hover:from-cyan-400 hover:to-blue-500 transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-check"></i>
                    <span>Update Unit</span>
                </button>
            </div>
        </form>

    </div>
</div>

<script>
    function openEditUnitModal(unit) {
        document.getElementById('edit_unit_name').value = unit.name;
        document.getElementById('edit_unit_short_code').value = unit.short_code || '';
        document.getElementById('editUnitForm').action = `/factory/units/${unit.id}`;
        document.getElementById('editUnitModal').classList.remove('hidden');
    }

    function closeEditUnitModal() {
        document.getElementById('editUnitModal').classList.add('hidden');
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeEditUnitModal();
    });
</script>
@endsection

