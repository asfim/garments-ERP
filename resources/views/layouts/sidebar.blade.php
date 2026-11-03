<!-- LEFT SIDEBAR (DYNAMICALLY CONTROLLED BY BUTTONS) -->
<aside class="w-64 min-w-[256px] glass-panel border-r border-slate-800/80 flex flex-col py-4 px-3 select-none overflow-y-auto">
    
    <div class="space-y-4">
        
        <!-- Current Mode Indicator Badge -->
        <div class="px-3 py-2 rounded-xl bg-gradient-to-r from-blue-900/40 via-indigo-900/30 to-purple-900/20 border border-blue-500/30 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                <span id="currentModeLabel" class="text-[11px] font-bold text-cyan-300 uppercase tracking-wider">
                    Factory ERP Mode
                </span>
            </div>
            <i id="currentModeIcon" class="fa-solid fa-industry text-xs text-blue-400"></i>
        </div>

        <!-- Navigation Menu Container -->
        <nav class="space-y-1">
            
            <!-- Dashboard (Always Present) -->
            @php
                $isDash = request()->routeIs('dashboard') || request()->is('/');
            @endphp
            <a href="/" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-xs {{ $isDash ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold shadow-lg shadow-blue-500/25 border border-cyan-400/40' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 font-medium border border-transparent' }}">
                <span class="w-6 h-6 rounded-lg {{ $isDash ? 'bg-white/20 text-white' : 'bg-blue-500/10 text-cyan-400' }} flex items-center justify-center">
                    <i class="fa-solid fa-house text-[11px]"></i>
                </span>
                <span>Dashboard</span>
            </a>

            <!-- PACKAGE ERP MENUS (Component) -->
            <div id="packageMenuContainer" class="space-y-1 hidden pt-1">
                @include('package-erp.menu')
            </div>

            <!-- FACTORY ERP MENUS (Component) -->
            <div id="factoryMenuContainer" class="space-y-0.5 pt-1">
                @include('factory-erp.menu')
            </div>

        </nav>

    </div>

</aside>

