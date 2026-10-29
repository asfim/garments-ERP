@extends('layouts.app')

@section('title', 'Process Delivery for ' . $order->order_number . ' | TBG ERP')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    <!-- Header Breadcrumb -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('factory.deliveries.index') }}" class="text-xs text-cyan-400 hover:underline flex items-center gap-1">
                    <i class="fa-solid fa-arrow-left"></i> Back to Delivery Center
                </a>
            </div>
            <h2 class="text-2xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-truck-fast text-emerald-400"></i>
                Shipment & Delivery Dispatch
            </h2>
            <p class="text-xs text-slate-400">
                Order: <strong class="text-cyan-300 font-mono">{{ $order->order_number }}</strong> | 
                Client: <strong class="text-white">{{ $order->customer->company_name }}</strong>
            </p>
        </div>
    </div>

    @if(session('error'))
    <div class="p-3.5 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs flex items-center gap-2.5">
        <i class="fa-solid fa-triangle-exclamation text-base"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <form action="{{ route('factory.deliveries.store') }}" method="POST" id="deliveryForm">
        @csrf
        <input type="hidden" name="order_id" value="{{ $order->id }}">

        <div class="glass-panel rounded-3xl p-6 border border-slate-700/60 space-y-6">
            
            <!-- Delivery Info -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pb-4 border-b border-slate-700/80">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Challan / Delivery #</label>
                    <input type="text" value="{{ $deliveryNumber }}" readonly
                           class="w-full bg-[#070d1e] text-xs font-mono font-bold text-cyan-300 rounded-xl px-3.5 py-2.5 border border-slate-700/80 cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Client Address</label>
                    <input type="text" value="{{ $order->customer->address }}" readonly
                           class="w-full bg-[#070d1e] text-xs text-slate-300 rounded-xl px-3.5 py-2.5 border border-slate-700/80 cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Delivery Date *</label>
                    <input type="date" name="delivery_date" value="{{ date('Y-m-d') }}" required
                           class="w-full bg-[#0a1228] text-xs text-white rounded-xl px-3.5 py-2.5 border border-slate-700 focus:border-cyan-400 focus:outline-none">
                </div>
            </div>

            <!-- Items Delivery Table with Live Stock Check & Warning Alerts -->
            <div>
                <h3 class="text-sm font-bold text-white mb-2 flex items-center justify-between">
                    <span>Specify Dispatch Quantities for this Delivery</span>
                    <span class="text-[11px] text-amber-300 font-normal">
                        * Quantities are validated against available factory warehouse stock
                    </span>
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-slate-400 border-b border-slate-700/80 font-semibold uppercase">
                                <th class="pb-2.5" style="width: 30%;">Product</th>
                                <th class="pb-2.5 text-center" style="width: 10%;">Unit</th>
                                <th class="pb-2.5 text-center" style="width: 12%;">Order Qty</th>
                                <th class="pb-2.5 text-center" style="width: 12%;">Already Sent</th>
                                <th class="pb-2.5 text-center" style="width: 12%;">Remaining Due</th>
                                <th class="pb-2.5 text-center" style="width: 12%;">Available Stock</th>
                                <th class="pb-2.5 text-center" style="width: 12%;">Ship Now Qty *</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80">
                            @foreach($order->items as $idx => $item)
                            @php
                                $stockAvailable = $item->product->stock ? $item->product->stock->current_stock : 0;
                            @endphp
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="py-3">
                                    <span class="font-bold text-white block">{{ $item->product->name }}</span>
                                    <span class="text-[10px] text-slate-400">Rate: ৳ {{ number_format($item->sell_price, 2) }}</span>
                                    <input type="hidden" name="items[{{ $idx }}][order_item_id]" value="{{ $item->id }}">
                                </td>
                                <td class="py-3 text-center">
                                    <span class="px-2 py-0.5 rounded bg-blue-500/10 text-cyan-300 font-mono text-[11px]">
                                        {{ $item->product->unit->short_code ?? 'Pcs' }}
                                    </span>
                                </td>
                                <td class="py-3 text-center font-mono font-bold text-white">
                                    {{ number_format($item->qty) }}
                                </td>
                                <td class="py-3 text-center font-mono text-slate-400">
                                    {{ number_format($item->delivered_qty) }}
                                </td>
                                <td class="py-3 text-center font-mono font-bold text-amber-300" id="due-text-{{ $idx }}">
                                    {{ number_format($item->due_qty) }}
                                </td>
                                <td class="py-3 text-center">
                                    <span class="px-2 py-0.5 rounded-full font-mono font-bold text-xs {{ $stockAvailable > 0 ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-400 border border-rose-500/30' }}">
                                        {{ number_format($stockAvailable) }}
                                    </span>
                                </td>
                                <td class="py-3 text-center">
                                    @if($item->due_qty > 0)
                                        <input type="number" step="1" min="0" 
                                               max="{{ min($item->due_qty, $stockAvailable) }}" 
                                               name="items[{{ $idx }}][deliver_qty]" 
                                               value="{{ min($item->due_qty, $stockAvailable) }}"
                                               data-due="{{ $item->due_qty }}"
                                               data-stock="{{ $stockAvailable }}"
                                               oninput="validateShipQty(this, {{ $idx }})"
                                               class="w-24 bg-[#0a1228] text-center font-mono font-bold text-emerald-400 rounded-xl px-2 py-2 border border-slate-700 focus:border-cyan-400 focus:outline-none text-xs">
                                        <span id="warn-{{ $idx }}" class="text-[10px] text-rose-400 block mt-1 hidden font-semibold">Exceeds!</span>
                                    @else
                                        <span class="text-xs text-emerald-400 font-bold">Done (0 Due)</span>
                                        <input type="hidden" name="items[{{ $idx }}][deliver_qty]" value="0">
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Delivery / Driver / Vehicle Notes</label>
                <textarea name="notes" rows="2" placeholder="Vehicle No: Dhaka-Metro 12-3456, Driver Phone: 01700-000000"
                          class="w-full bg-[#0a1228] text-xs text-white rounded-xl px-3.5 py-2 border border-slate-700 focus:border-cyan-400 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-700/80">
                <p class="text-xs text-slate-400">
                    Submitting this will deduct delivered items from <strong class="text-white">Warehouse Stock</strong> and subtract quantities from the order due balance.
                </p>

                <button type="submit" 
                        class="px-6 py-3 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold text-xs shadow-xl shadow-emerald-500/25 hover:from-emerald-400 hover:to-teal-500 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-truck-ramp-box"></i>
                    <span>Confirm & Dispatch Delivery</span>
                </button>
            </div>

        </div>
    </form>

</div>

<script>
    function validateShipQty(input, idx) {
        const val = parseFloat(input.value) || 0;
        const due = parseFloat(input.getAttribute('data-due')) || 0;
        const stock = parseFloat(input.getAttribute('data-stock')) || 0;
        const warn = document.getElementById('warn-' + idx);

        if (val > due) {
            warn.textContent = 'Exceeds Order Due (' + due + ')!';
            warn.classList.remove('hidden');
            input.classList.add('border-rose-500', 'text-rose-400');
        } else if (val > stock) {
            warn.textContent = 'Exceeds Stock (' + stock + ')!';
            warn.classList.remove('hidden');
            input.classList.add('border-rose-500', 'text-rose-400');
        } else {
            warn.classList.add('hidden');
            input.classList.remove('border-rose-500', 'text-rose-400');
            input.classList.add('text-emerald-400');
        }
    }
</script>
@endsection

