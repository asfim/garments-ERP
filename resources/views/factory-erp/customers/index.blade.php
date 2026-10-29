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
    @if(session('error'))
    <div class="p-3 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs flex items-center gap-2">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span>{{ session('error') }}</span>
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
                            <th class="pb-2.5 text-right">Action</th>
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
                            <td class="py-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('factory.customers.details', $c->id) }}" 
                                       title="View Full Ledger History"
                                       class="px-2 py-1 rounded-lg bg-blue-600/20 hover:bg-blue-600/40 text-cyan-300 border border-blue-500/30 text-[11px] font-semibold transition-all inline-flex items-center gap-1">
                                        <i class="fa-solid fa-clock-rotate-left text-[10px]"></i>
                                        <span>History</span>
                                    </a>

                                    <button type="button" 
                                            onclick="openEditCustomerModal({{ json_encode($c) }})"
                                            class="px-2 py-1 rounded-lg bg-cyan-600/20 hover:bg-cyan-600/40 text-cyan-300 border border-cyan-500/30 text-[11px] font-semibold transition-all inline-flex items-center gap-1">
                                        <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                        <span>Edit</span>
                                    </button>

                                    <form action="{{ route('factory.customers.destroy', $c->id) }}" method="POST" 
                                          onsubmit="return confirm('Delete customer \'{{ $c->company_name }}\'?');" class="inline">
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
                            <td colspan="6" class="py-6 text-center text-slate-500">No customers registered yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

<!-- EDIT CUSTOMER MODAL (GLASS POPUP) -->
<div id="editCustomerModal" class="fixed inset-0 z-50 bg-black/75 backdrop-blur-md hidden flex items-center justify-center p-4">
    <div class="glass-panel rounded-3xl p-6 border border-slate-700/80 max-w-lg w-full relative shadow-2xl bg-[#0a1228]/95">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-700/80 mb-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-user-pen text-cyan-400"></i>
                Edit Customer Details
            </h3>
            <button type="button" onclick="closeEditCustomerModal()" class="text-slate-400 hover:text-white p-1 text-base">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="editCustomerForm" method="POST" class="space-y-3.5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Company / Client Name *</label>
                <input type="text" name="company_name" id="edit_company_name" required
                       class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Contact Person</label>
                    <input type="text" name="contact_person" id="edit_contact_person"
                           class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Phone Number *</label>
                    <input type="text" name="phone" id="edit_phone" required
                           class="w-full bg-[#070d1e] text-xs text-cyan-300 font-mono rounded-xl px-3 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Factory / Office Address</label>
                <textarea name="address" id="edit_address" rows="2"
                          class="w-full bg-[#070d1e] text-xs text-white rounded-xl px-3.5 py-2 border border-slate-700 focus:border-cyan-400 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-700/80">
                <button type="button" onclick="closeEditCustomerModal()" 
                        class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-5 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-bold text-xs shadow-lg shadow-cyan-500/25 hover:from-cyan-400 hover:to-blue-500 transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-check"></i>
                    <span>Update Customer</span>
                </button>
            </div>
        </form>

    </div>
</div>

<script>
    function openEditCustomerModal(customer) {
        document.getElementById('edit_company_name').value = customer.company_name;
        document.getElementById('edit_contact_person').value = customer.contact_person || '';
        document.getElementById('edit_phone').value = customer.phone;
        document.getElementById('edit_address').value = customer.address || '';
        document.getElementById('editCustomerForm').action = `/factory/customers/${customer.id}`;
        document.getElementById('editCustomerModal').classList.remove('hidden');
    }

    function closeEditCustomerModal() {
        document.getElementById('editCustomerModal').classList.add('hidden');
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeEditCustomerModal();
    });
</script>
@endsection

