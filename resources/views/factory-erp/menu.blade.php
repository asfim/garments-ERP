<div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-widest text-blue-400/80">
    Factory Modules
</div>

<!-- 1. Product list -->
@php $isProd = request()->routeIs('factory.products.*'); @endphp
<a href="{{ route('factory.products.index') }}" 
   class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all {{ $isProd ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold shadow-lg shadow-blue-500/25 border border-cyan-400/40' : 'text-slate-300 hover:text-white hover:bg-slate-800/50 font-medium border border-transparent group' }}">
    <span class="w-6 h-6 rounded-lg {{ $isProd ? 'bg-white/20 text-white' : 'bg-blue-500/10 text-blue-400 group-hover:bg-blue-500/20' }} flex items-center justify-center">
        <i class="fa-solid fa-boxes-stacked text-[11px]"></i>
    </span>
    <span>Product List</span>
</a>

<!-- 2. Unit Management -->
@php $isUnit = request()->routeIs('factory.units.*'); @endphp
<a href="{{ route('factory.units.index') }}" 
   class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all {{ $isUnit ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold shadow-lg shadow-blue-500/25 border border-cyan-400/40' : 'text-slate-300 hover:text-white hover:bg-slate-800/50 font-medium border border-transparent group' }}">
    <span class="w-6 h-6 rounded-lg {{ $isUnit ? 'bg-white/20 text-white' : 'bg-purple-500/10 text-purple-400 group-hover:bg-purple-500/20' }} flex items-center justify-center">
        <i class="fa-solid fa-scale-balanced text-[11px]"></i>
    </span>
    <span>Units</span>
</a>

<!-- 3. Customer -->
@php $isCust = request()->routeIs('factory.customers.*'); @endphp
<a href="{{ route('factory.customers.index') }}" 
   class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all {{ $isCust ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold shadow-lg shadow-blue-500/25 border border-cyan-400/40' : 'text-slate-300 hover:text-white hover:bg-slate-800/50 font-medium border border-transparent group' }}">
    <span class="w-6 h-6 rounded-lg {{ $isCust ? 'bg-white/20 text-white' : 'bg-cyan-500/10 text-cyan-400 group-hover:bg-cyan-500/20' }} flex items-center justify-center">
        <i class="fa-solid fa-user-group text-[11px]"></i>
    </span>
    <span>Customer</span>
</a>

<!-- 4. Order & Invoicing -->
@php $isOrd = request()->routeIs('factory.orders.*'); @endphp
<a href="{{ route('factory.orders.index') }}" 
   class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all {{ $isOrd ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold shadow-lg shadow-blue-500/25 border border-cyan-400/40' : 'text-slate-300 hover:text-white hover:bg-slate-800/50 font-medium border border-transparent group' }}">
    <span class="w-6 h-6 rounded-lg {{ $isOrd ? 'bg-white/20 text-white' : 'bg-amber-500/10 text-amber-400 group-hover:bg-amber-500/20' }} flex items-center justify-center">
        <i class="fa-solid fa-cart-shopping text-[11px]"></i>
    </span>
    <span>Order</span>
</a>

<!-- 5. Production -->
@php $isProdRun = request()->routeIs('factory.productions.*'); @endphp
<a href="{{ route('factory.productions.index') }}" 
   class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all {{ $isProdRun ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold shadow-lg shadow-blue-500/25 border border-cyan-400/40' : 'text-slate-300 hover:text-white hover:bg-slate-800/50 font-medium border border-transparent group' }}">
    <span class="w-6 h-6 rounded-lg {{ $isProdRun ? 'bg-white/20 text-white' : 'bg-purple-500/10 text-purple-400 group-hover:bg-purple-500/20' }} flex items-center justify-center">
        <i class="fa-solid fa-industry text-[11px]"></i>
    </span>
    <span>Production</span>
</a>

<!-- 6. Delivery -->
@php $isDeliv = request()->routeIs('factory.deliveries.*'); @endphp
<a href="{{ route('factory.deliveries.index') }}" 
   class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all {{ $isDeliv ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold shadow-lg shadow-blue-500/25 border border-cyan-400/40' : 'text-slate-300 hover:text-white hover:bg-slate-800/50 font-medium border border-transparent group' }}">
    <span class="w-6 h-6 rounded-lg {{ $isDeliv ? 'bg-white/20 text-white' : 'bg-emerald-500/10 text-emerald-400 group-hover:bg-emerald-500/20' }} flex items-center justify-center">
        <i class="fa-solid fa-truck-fast text-[11px]"></i>
    </span>
    <span>Delivery</span>
</a>

<!-- 7. Stock -->
@php $isStock = request()->routeIs('factory.stocks.*'); @endphp
<a href="{{ route('factory.stocks.index') }}" 
   class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all {{ $isStock ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold shadow-lg shadow-blue-500/25 border border-cyan-400/40' : 'text-slate-300 hover:text-white hover:bg-slate-800/50 font-medium border border-transparent group' }}">
    <span class="w-6 h-6 rounded-lg {{ $isStock ? 'bg-white/20 text-white' : 'bg-teal-500/10 text-teal-400 group-hover:bg-teal-500/20' }} flex items-center justify-center">
        <i class="fa-solid fa-warehouse text-[11px]"></i>
    </span>
    <span>Stock</span>
</a>

<!-- 8. Settings -->
@php $isSet = request()->routeIs('factory.settings.*'); @endphp
<a href="{{ route('factory.settings.index') }}" 
   class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all {{ $isSet ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold shadow-lg shadow-blue-500/25 border border-cyan-400/40' : 'text-slate-300 hover:text-white hover:bg-slate-800/50 font-medium border border-transparent group' }}">
    <span class="w-6 h-6 rounded-lg {{ $isSet ? 'bg-white/20 text-white' : 'bg-rose-500/10 text-rose-400 group-hover:bg-rose-500/20' }} flex items-center justify-center">
        <i class="fa-solid fa-gears text-[11px]"></i>
    </span>
    <span>Settings</span>
</a>
