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
                                <form action="{{ route('factory.units.destroy', $unit->id) }}" method="POST" onsubmit="return confirm('Delete this unit?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-400 hover:text-rose-300 p-1.5 transition-colors">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
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
@endsection

