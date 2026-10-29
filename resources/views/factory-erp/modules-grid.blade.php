<!-- Factory ERP Modules Card (Connected to Real Routes) -->
<div class="glass-panel rounded-3xl p-5 border border-slate-700/60">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-industry text-blue-400"></i>
                Factory ERP Modules
            </h3>
            <p class="text-[11px] text-slate-400">Integrated production, order, dynamic delivery & live inventory workflow</p>
        </div>
        <span class="text-[10px] text-blue-400 font-mono px-2 py-0.5 rounded-full bg-blue-500/10 border border-blue-500/20">8 Active Modules</span>
    </div>

    <!-- Factory ERP Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        
        <!-- 1. Product List -->
        <a href="{{ route('factory.products.index') }}" class="glass-card rounded-2xl p-3.5 flex flex-col items-center text-center group border border-blue-500/20 hover:border-blue-400">
            <div class="w-11 h-11 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform shadow-lg shadow-blue-500/20">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <span class="text-xs font-bold text-white group-hover:text-cyan-300">Product List</span>
            <span class="text-[10px] text-slate-400 mt-0.5">Sell Price & PDF</span>
        </a>

        <!-- 2. Units -->
        <a href="{{ route('factory.units.index') }}" class="glass-card rounded-2xl p-3.5 flex flex-col items-center text-center group border border-purple-500/20 hover:border-purple-400">
            <div class="w-11 h-11 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform shadow-lg shadow-purple-500/20">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <span class="text-xs font-bold text-white group-hover:text-purple-300">Units</span>
            <span class="text-[10px] text-slate-400 mt-0.5">Dynamic Units</span>
        </a>

        <!-- 3. Customer -->
        <a href="{{ route('factory.customers.index') }}" class="glass-card rounded-2xl p-3.5 flex flex-col items-center text-center group border border-cyan-500/20 hover:border-cyan-400">
            <div class="w-11 h-11 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform shadow-lg shadow-cyan-500/20">
                <i class="fa-solid fa-user-group"></i>
            </div>
            <span class="text-xs font-bold text-white group-hover:text-cyan-300">Customer</span>
            <span class="text-[10px] text-slate-400 mt-0.5">Full Order History</span>
        </a>

        <!-- 4. Order & Invoicing -->
        <a href="{{ route('factory.orders.index') }}" class="glass-card rounded-2xl p-3.5 flex flex-col items-center text-center group border border-amber-500/20 hover:border-amber-400">
            <div class="w-11 h-11 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform shadow-lg shadow-amber-500/20">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
            <span class="text-xs font-bold text-white group-hover:text-amber-300">Orders & Invoices</span>
            <span class="text-[10px] text-slate-400 mt-0.5">Multi-Product Live</span>
        </a>

        <!-- 5. Production -->
        <a href="{{ route('factory.productions.index') }}" class="glass-card rounded-2xl p-3.5 flex flex-col items-center text-center group border border-indigo-500/20 hover:border-indigo-400">
            <div class="w-11 h-11 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform shadow-lg shadow-indigo-500/20">
                <i class="fa-solid fa-industry"></i>
            </div>
            <span class="text-xs font-bold text-white group-hover:text-indigo-300">Production</span>
            <span class="text-[10px] text-slate-400 mt-0.5">Target & Stock In</span>
        </a>

        <!-- 6. Delivery -->
        <a href="{{ route('factory.deliveries.index') }}" class="glass-card rounded-2xl p-3.5 flex flex-col items-center text-center group border border-emerald-500/20 hover:border-emerald-400">
            <div class="w-11 h-11 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform shadow-lg shadow-emerald-500/20">
                <i class="fa-solid fa-truck-fast"></i>
            </div>
            <span class="text-xs font-bold text-white group-hover:text-emerald-300">Delivery Dispatch</span>
            <span class="text-[10px] text-slate-400 mt-0.5">Stock Check & Due</span>
        </a>

        <!-- 7. Stock -->
        <a href="{{ route('factory.stocks.index') }}" class="glass-card rounded-2xl p-3.5 flex flex-col items-center text-center group border border-teal-500/20 hover:border-teal-400">
            <div class="w-11 h-11 rounded-xl bg-teal-500/20 text-teal-400 flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform shadow-lg shadow-teal-500/20">
                <i class="fa-solid fa-warehouse"></i>
            </div>
            <span class="text-xs font-bold text-white group-hover:text-teal-300">Stock & Inventory</span>
            <span class="text-[10px] text-slate-400 mt-0.5">Auto Logs & Manual</span>
        </a>

        <!-- 8. Settings -->
        <a href="{{ route('factory.settings.index') }}" class="glass-card rounded-2xl p-3.5 flex flex-col items-center text-center group border border-rose-500/20 hover:border-rose-400">
            <div class="w-11 h-11 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform shadow-lg shadow-rose-500/20">
                <i class="fa-solid fa-gears"></i>
            </div>
            <span class="text-xs font-bold text-white group-hover:text-rose-300">Company Settings</span>
            <span class="text-[10px] text-slate-400 mt-0.5">PDF Header Binding</span>
        </a>

    </div>
</div>
