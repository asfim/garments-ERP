<!-- LEFT SIDEBAR (DYNAMICALLY CONTROLLED BY BUTTONS) -->
<aside class="w-64 min-w-[256px] glass-panel border-r border-slate-800/80 flex flex-col justify-between py-4 px-3 select-none">
    
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
            <a href="/" class="flex items-center justify-between px-3 py-2.5 rounded-xl bg-gradient-to-r from-blue-600/90 to-cyan-600/90 text-white font-semibold text-xs shadow-lg shadow-blue-500/25 border border-cyan-400/30">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-house text-sm"></i>
                    <span>Dashboard</span>
                </div>
                <i class="fa-solid fa-chevron-right text-[10px] text-cyan-200"></i>
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

    <!-- Bottom Glowing 3D Cube Card -->
    <div class="relative overflow-hidden rounded-2xl p-4 bg-gradient-to-br from-indigo-950/80 via-[#0a122e] to-purple-950/80 border border-blue-500/30 shadow-xl shadow-blue-500/10 text-center">
        <div class="absolute -top-10 -right-10 w-24 h-24 bg-cyan-500/20 rounded-full blur-xl"></div>
        <div class="absolute -bottom-10 -left-10 w-24 h-24 bg-purple-500/20 rounded-full blur-xl"></div>
        
        <div class="relative mx-auto mb-2 w-14 h-14 flex items-center justify-center floating-cube">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-cyan-500 via-indigo-500 to-pink-500 p-[2px] shadow-lg shadow-cyan-500/30">
                <div class="w-full h-full bg-[#0d1630] rounded-[10px] flex items-center justify-center">
                    <i class="fa-solid fa-cubes-stacked text-xl text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-purple-400"></i>
                </div>
            </div>
        </div>

        <h4 class="text-xs font-bold text-white tracking-wide">Smarter Business</h4>
        <p class="text-[10px] text-slate-300 mt-0.5">Bigger Future</p>
        <div class="mt-2 inline-block px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/40 text-[10px] font-mono text-cyan-300 font-bold">
            TBG ERP V6
        </div>
    </div>

</aside>

