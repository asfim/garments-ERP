@extends('layouts.app')

@section('title', 'Accounting System Mappings | Factory ERP')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    <!-- Top Header -->
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-sliders text-amber-400"></i>
                Accounting System Mappings (স্বয়ংক্রিয় হিসাব ম্যাপিং)
            </h2>
            <p class="text-xs text-slate-400">Configure which Chart of Accounts ledger accounts are automatically debited or credited by ERP operations (Production, Sales, Payroll, Advances).</p>
        </div>

        <a href="{{ route('factory.accounts.dashboard') }}" 
           class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition-all flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Dashboard</span>
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs font-semibold flex items-center gap-2">
        <i class="fa-solid fa-circle-check text-base"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <form method="POST" action="{{ route('factory.accounts.mappings.update') }}">
        @csrf

        <div class="glass-panel p-5 rounded-2xl border border-slate-800/80 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-400">System Transaction Account Mapping Rules</span>
                <span class="text-[11px] text-slate-500 font-mono">{{ $mappings->count() }} Configured Rules</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950/60 text-slate-400 uppercase text-[10px] font-bold border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4 w-4/12">Transaction Event</th>
                            <th class="py-3 px-4 w-5/12">Mapped Account (হিসাব খাত)</th>
                            <th class="py-3 px-4 w-3/12">Current Account Code</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @foreach($mappings as $map)
                        <tr class="hover:bg-slate-800/20">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-white text-xs">{{ $map->title }}</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $map->description }}</div>
                                <span class="inline-block mt-1 px-1.5 py-0.5 rounded text-[9px] font-mono bg-slate-900 text-amber-400/80 border border-slate-800">
                                    {{ $map->mapping_key }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <select name="mappings[{{ $map->mapping_key }}]" required 
                                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-slate-200 text-xs font-medium focus:border-amber-500 focus:outline-none">
                                    @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}" {{ $map->account_id == $acc->id ? 'selected' : '' }}>
                                        [{{ $acc->account_code }}] {{ $acc->name }} ({{ ucfirst($acc->type) }})
                                    </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-amber-400">
                                #{{ $map->account?->account_code ?? 'N/A' }}
                                <span class="block text-[10px] text-slate-500 font-sans uppercase font-normal">
                                    {{ $map->account?->type ?? '' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 flex justify-end">
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-600 to-rose-600 hover:from-amber-500 hover:to-rose-500 text-white font-bold text-xs shadow-lg shadow-amber-500/25 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-save"></i>
                    <span>Save Account Mappings</span>
                </button>
            </div>
        </div>
    </form>

</div>
@endsection

