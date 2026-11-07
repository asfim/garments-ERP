@extends('layouts.app')

@section('title', 'Journal Vouchers | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-file-signature text-blue-400"></i>
                Journal Vouchers
            </h2>
            <p class="text-xs text-slate-400">Multi-line double-entry journal vouchers with audit trail and reversal tracking</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('factory.accounts.journals.create') }}" 
               class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-xs shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus-circle"></i>
                <span>+ New Journal Entry</span>
            </a>
            <a href="{{ route('factory.accounts.dashboard') }}" 
               class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Dashboard</span>
            </a>
        </div>
    </div>

    <!-- Filters & Stats Bar -->
    <div class="glass-panel p-4 rounded-2xl border border-slate-800/80 flex flex-wrap items-center justify-between gap-4">
        <form method="GET" action="{{ route('factory.accounts.journals.index') }}" class="flex flex-wrap items-center gap-3 text-xs">
            <input type="text" name="voucher_no" value="{{ request('voucher_no') }}" placeholder="Voucher #..." 
                   class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-slate-200 text-xs w-36">

            <select name="status" class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-slate-200 text-xs">
                <option value="">All Status</option>
                <option value="posted" {{ request('status') === 'posted' ? 'selected' : '' }}>Posted</option>
                <option value="reversed" {{ request('status') === 'reversed' ? 'selected' : '' }}>Reversed</option>
            </select>

            <input type="date" name="from_date" value="{{ request('from_date') }}" 
                   class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-slate-200 text-xs">
            <span class="text-slate-500">to</span>
            <input type="date" name="to_date" value="{{ request('to_date') }}" 
                   class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-slate-200 text-xs">

            <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold transition-all">
                Filter
            </button>
            @if(request()->anyFilled(['voucher_no', 'status', 'from_date', 'to_date']))
            <a href="{{ route('factory.accounts.journals.index') }}" class="text-xs text-slate-400 hover:text-white underline">Clear</a>
            @endif
        </form>

        <div class="text-xs font-mono text-slate-400 flex items-center gap-4">
            <div>Total Journals: <strong class="text-white">{{ $journals->total() }}</strong></div>
            <div>Period Volume: <strong class="text-emerald-400">৳ {{ number_format($totalAmount, 2) }}</strong></div>
        </div>
    </div>

    <!-- Journals Table -->
    <div class="glass-panel rounded-2xl border border-slate-800/80 overflow-hidden">
        <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-950/60 text-slate-400 uppercase text-[10px] font-bold border-b border-slate-800">
                <tr>
                    <th class="py-3 px-4">Voucher No</th>
                    <th class="py-3 px-4">Date</th>
                    <th class="py-3 px-4">Particulars & Multi-lines</th>
                    <th class="py-3 px-4">Party / Reference</th>
                    <th class="py-3 px-4 text-right">Amount (৳)</th>
                    <th class="py-3 px-4 text-center">Status</th>
                    <th class="py-3 px-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($journals as $j)
                <tr class="hover:bg-slate-800/40 transition-colors {{ $j->is_reversed ? 'opacity-60 bg-rose-950/10' : '' }}">
                    <td class="py-3 px-4 font-mono font-bold text-blue-400">
                        {{ $j->voucher_no }}
                        @if($j->source_module)
                            <span class="block text-[9px] text-slate-500 font-sans uppercase">System Auto-Post</span>
                        @endif
                    </td>
                    <td class="py-3 px-4 font-mono text-slate-300">
                        {{ $j->voucher_date->format('d M, Y') }}
                    </td>
                    <td class="py-3 px-4 max-w-md">
                        <div class="font-medium text-white mb-1">{{ $j->notes ?: 'Journal Entry' }}</div>
                        <div class="space-y-1 bg-slate-950/40 p-2 rounded-lg border border-slate-800/60 font-mono text-[11px]">
                            @foreach($j->lines as $line)
                            <div class="flex items-center justify-between text-xs">
                                <span class="{{ $line->type === 'debit' ? 'text-cyan-400' : 'text-amber-400 pl-4' }}">
                                    <strong>{{ strtoupper($line->type) }}:</strong> [{{ $line->account?->account_code }}] {{ $line->account?->name }}
                                </span>
                                <span class="font-black {{ $line->type === 'debit' ? 'text-cyan-400' : 'text-amber-400' }}">
                                    ৳ {{ number_format($line->amount, 2) }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </td>
                    <td class="py-3 px-4 text-slate-300">
                        @if($j->customer)
                            <span class="text-emerald-400 block font-medium"><i class="fa-solid fa-user text-[10px]"></i> {{ $j->customer->company_name }}</span>
                        @elseif($j->supplier)
                            <span class="text-orange-400 block font-medium"><i class="fa-solid fa-truck text-[10px]"></i> {{ $j->supplier->company_name }}</span>
                        @elseif($j->employee)
                            <span class="text-purple-400 block font-medium"><i class="fa-solid fa-user-tie text-[10px]"></i> {{ $j->employee->full_name }}</span>
                        @else
                            <span class="text-slate-500 text-[11px]">Internal Entry</span>
                        @endif
                        @if($j->reference_no)
                            <span class="text-[10px] text-slate-400 font-mono block">Ref: {{ $j->reference_no }}</span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-right font-mono font-black text-sm text-white">
                        ৳ {{ number_format($j->amount, 2) }}
                    </td>
                    <td class="py-3 px-4 text-center">
                        @if($j->is_reversed)
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                Reversed
                            </span>
                            @if($j->reversedBy)
                            <span class="block text-[9px] font-mono text-slate-500 mt-0.5">by {{ $j->reversedBy->voucher_no }}</span>
                            @endif
                        @else
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                Posted
                            </span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-right">
                        @if(!$j->is_reversed && $j->status === 'posted')
                        <form method="POST" action="{{ route('factory.accounts.vouchers.reverse', $j->id) }}" onsubmit="return confirm('Reverse this journal voucher? An opposite contra-voucher will be generated and posted automatically.');" class="inline">
                            @csrf
                            <input type="hidden" name="reason" value="User reversed journal from dashboard">
                            <button type="submit" title="Reverse Voucher" 
                                    class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/30 text-xs font-bold transition-all">
                                <i class="fa-solid fa-rotate-left"></i> Reverse
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-8 text-center text-slate-500">
                        No journal vouchers found matching criteria.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div>
        {{ $journals->links() }}
    </div>

</div>
@endsection

