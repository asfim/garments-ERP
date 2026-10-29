<div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-widest text-blue-400/80">
    Factory Modules
</div>

<!-- 1. Product list -->
<a href="{{ route('factory.products.index') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800/50 transition-all group">
    <div class="flex items-center gap-3">
        <span class="w-6 h-6 rounded-lg bg-blue-500/10 text-blue-400 flex items-center justify-center group-hover:bg-blue-500/20">
            <i class="fa-solid fa-boxes-stacked text-[11px]"></i>
        </span>
        <span>Product List</span>
    </div>
    <i class="fa-solid fa-chevron-right text-[10px] text-slate-500"></i>
</a>

<!-- 2. Unit Management -->
<a href="{{ route('factory.units.index') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800/50 transition-all group">
    <div class="flex items-center gap-3">
        <span class="w-6 h-6 rounded-lg bg-purple-500/10 text-purple-400 flex items-center justify-center group-hover:bg-purple-500/20">
            <i class="fa-solid fa-scale-balanced text-[11px]"></i>
        </span>
        <span>Units</span>
    </div>
    <span class="text-[10px] px-1.5 py-0.2 rounded bg-purple-500/20 text-purple-300 font-mono">Dynamic</span>
</a>

<!-- 3. Customer -->
<a href="{{ route('factory.customers.index') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800/50 transition-all group">
    <div class="flex items-center gap-3">
        <span class="w-6 h-6 rounded-lg bg-cyan-500/10 text-cyan-400 flex items-center justify-center group-hover:bg-cyan-500/20">
            <i class="fa-solid fa-user-group text-[11px]"></i>
        </span>
        <span>Customer</span>
    </div>
    <i class="fa-solid fa-chevron-right text-[10px] text-slate-500"></i>
</a>

<!-- 4. Order & Invoicing -->
<a href="{{ route('factory.orders.index') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800/50 transition-all group">
    <div class="flex items-center gap-3">
        <span class="w-6 h-6 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center group-hover:bg-amber-500/20">
            <i class="fa-solid fa-cart-shopping text-[11px]"></i>
        </span>
        <span>Order</span>
    </div>
    <span class="text-[10px] px-1.5 py-0.2 bg-amber-500/20 text-amber-300 rounded font-mono font-bold">+ New</span>
</a>

<!-- 5. Production -->
<a href="{{ route('factory.productions.index') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800/50 transition-all group">
    <div class="flex items-center gap-3">
        <span class="w-6 h-6 rounded-lg bg-purple-500/10 text-purple-400 flex items-center justify-center group-hover:bg-purple-500/20">
            <i class="fa-solid fa-industry text-[11px]"></i>
        </span>
        <span>Production</span>
    </div>
    <i class="fa-solid fa-chevron-right text-[10px] text-slate-500"></i>
</a>

<!-- 6. Delivery -->
<a href="{{ route('factory.deliveries.index') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800/50 transition-all group">
    <div class="flex items-center gap-3">
        <span class="w-6 h-6 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center group-hover:bg-emerald-500/20">
            <i class="fa-solid fa-truck-fast text-[11px]"></i>
        </span>
        <span>Delivery</span>
    </div>
    <i class="fa-solid fa-chevron-right text-[10px] text-slate-500"></i>
</a>

<!-- 7. Stock -->
<a href="{{ route('factory.stocks.index') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800/50 transition-all group">
    <div class="flex items-center gap-3">
        <span class="w-6 h-6 rounded-lg bg-teal-500/10 text-teal-400 flex items-center justify-center group-hover:bg-teal-500/20">
            <i class="fa-solid fa-warehouse text-[11px]"></i>
        </span>
        <span>Stock</span>
    </div>
    <i class="fa-solid fa-chevron-right text-[10px] text-slate-500"></i>
</a>

<!-- 8. Settings (For Company Name, Logo, Address, Phone binding) -->
<a href="{{ route('factory.settings.index') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800/50 transition-all group">
    <div class="flex items-center gap-3">
        <span class="w-6 h-6 rounded-lg bg-rose-500/10 text-rose-400 flex items-center justify-center group-hover:bg-rose-500/20">
            <i class="fa-solid fa-gears text-[11px]"></i>
        </span>
        <span>Settings</span>
    </div>
    <span class="text-[10px] px-1.5 py-0.2 rounded bg-rose-500/20 text-rose-300 font-mono">Company</span>
</a>
