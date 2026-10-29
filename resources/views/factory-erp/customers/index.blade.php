@extends('layouts.app')

@section('title', 'Customers Management | Factory ERP')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-user-group text-cyan-400"></i>
                Customer Directory & Ledger
            </h2>
            <p class="text-xs text-slate-400">Company name, phone, address, and comprehensive order & payment histories</p>
        </div>
    </div>

    @if(session('success'))
    <div class="p-3 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs flex items-center gap-2">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left: Add New Customer -->
        <div class="lg:col-span-4 glass-panel rounded-3xl p-5 border border-slate-700/60 h-fit">
            <h3 class="text-sm font-bold text-white mb-3 flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-cyan-400"></i>
                Add New Customer
            </h3>

            <form action="{{ route('factory.customers.store') }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Company / Showroom Name *</label>
                    <input type="text" name="company_name" placeholder="e.g. Rahim Traders & Showroom" required
                           class="w-full bg-[#0a1228] text-xs text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Phone Number *</label>
                    <input type="text" name="phone" placeholder="e.g. +880 1823-112233" required
                           class="w-full bg-[#0a1228] text-xs text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Contact Person</label>
                    <input type="text" name="contact_person" placeholder="e.g. Md. Rahim Ali"
                           class="w-full bg-[#0a1228] text-xs text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Full Address</label>
                    <textarea name="address" rows="2.5" placeholder="Shop #, Market, Area, City"
                              class="w-full bg-[#0a1228] text-xs text-white rounded-xl px-3.5 py-2 border border-slate-700 focus:border-cyan-400 focus:outline-none"></textarea>
                </div>

                <button type="submit" 
                        class="w-full py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-bold text-xs shadow-lg shadow-cyan-500/25 hover:from-cyan-400 hover:to-blue-500 transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-check"></i>
                    <span>Save Customer</span>
                </button>
            </form>
        </div>

        <!-- Right: Customers Table with Ledger View Links -->
        <div class="lg:col-span-8 glass-panel rounded-3xl p-5 border border-slate-700/60">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-bold text-white">Registered Clients</h3>
                <span class="text-xs text-cyan-400 font-mono">{{ $customers->count() }} Customers</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 border-b border-slate-700/80 font-semibold">
                            <th class="pb-2.5">#</th>
                            <th class="pb-2.5">Company Name</th>
                            <th class="pb-2.5">Phone & Person</th>
                            <th class="pb-2.5 text-right">Total Ordered</th>
                            <th class="pb-2.5 text-right">Total Due</th>
                            <th class="pb-2.5 text-center">History</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @forelse($customers as $idx => $c)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-3 text-slate-400 font-mono">{{ $idx + 1 }}</td>
                            <td class="py-3">
                                <span class="font-bold text-white block">{{ $c->company_name }}</span>
                                <span class="text-[10px] text-slate-400 truncate max-w-xs block">{{ $c->address }}</span>
                            </td>
                            <td class="py-3">
                                <span class="text-cyan-300 font-mono font-medium block">{{ $c->phone }}</span>
                                <span class="text-[10px] text-slate-400 block">{{ $c->contact_person ?? 'Owner' }}</span>
                            </td>
                            <td class="py-3 text-right font-mono font-bold text-white">
                                ৳ {{ number_format($c->total_ordered, 2) }}
                            </td>
                            <td class="py-3 text-right font-mono font-bold {{ $c->total_due > 0 ? 'text-rose-400' : 'text-emerald-400' }}">
                                ৳ {{ number_format($c->total_due, 2) }}
                            </td>
                            <td class="py-3 text-center">
                                <a href="{{ route('factory.customers.details', $c->id) }}" 
                                   class="px-2.5 py-1 rounded-lg bg-blue-600/30 hover:bg-blue-600/50 text-cyan-300 border border-blue-500/30 text-[11px] font-semibold transition-all inline-flex items-center gap-1">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                    <span>Full History</span>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-500">No customers registered yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
