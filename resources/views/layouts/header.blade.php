<!-- TOP HEADER -->
<header class="glass-panel sticky top-0 z-50 border-b border-slate-700/50 px-4 lg:px-6 py-2.5">
    <div class="flex items-center justify-between gap-4">
        
        <!-- Left: Logo & Company -->
        <div class="flex items-center gap-3 min-w-[280px]">
            <div class="relative flex items-center justify-center w-11 h-11 rounded-xl bg-gradient-to-tr from-cyan-600 via-blue-600 to-indigo-600 p-[1.5px] shadow-lg shadow-blue-500/20">
                <div class="w-full h-full bg-[#0a1128] rounded-[10px] flex items-center justify-center">
                    <span class="text-sm font-black tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-400">TBG</span>
                </div>
            </div>
            <div>
                <h1 class="text-base font-extrabold tracking-wide text-white flex items-center gap-2">
                    Turkey Bangla Garments Ltd
                </h1>
                <p class="text-[11px] font-medium text-slate-400 flex items-center gap-1.5">
                    <span class="text-cyan-400">Business ERP Solution</span>
                    <span class="text-slate-600">•</span>
                    <span class="px-1.5 py-0.2 rounded bg-blue-500/10 text-blue-400 font-mono text-[10px] border border-blue-500/20">V6.0</span>
                </p>
            </div>
        </div>

        <!-- ERP MODE SWITCHER BUTTONS (KEY REQUIREMENT) -->
        <div class="flex items-center">
            <div class="flex items-center p-1 rounded-2xl bg-[#09112a] border border-slate-700/60 shadow-inner">
                <!-- Package ERP Button -->
                <button id="btnPackageERP" onclick="switchErpMode('package')" 
                        class="mode-btn flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white transition-all">
                    <i class="fa-solid fa-box-open text-cyan-400"></i>
                    <span>Package ERP</span>
                </button>

                <!-- Factory ERP Button (Default Active) -->
                <button id="btnFactoryERP" onclick="switchErpMode('factory')" 
                        class="mode-btn active flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white transition-all">
                    <i class="fa-solid fa-industry text-blue-400"></i>
                    <span>Factory ERP</span>
                </button>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="hidden md:flex items-center flex-1 max-w-md mx-2">
            <div class="relative w-full">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" placeholder="Search anything... (Product, Invoice, Customer, etc.)"
                       class="w-full bg-[#0a1228]/80 text-xs text-slate-200 placeholder-slate-500 rounded-xl pl-9 pr-14 py-2 border border-slate-700/60 focus:border-cyan-400 focus:outline-none focus:ring-1 focus:ring-cyan-400/40 transition-all">
                <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[10px] bg-slate-800/80 text-slate-400 border border-slate-700 px-1.5 py-0.5 rounded font-mono">Ctrl + K</span>
            </div>
        </div>

        <!-- Right Controls: Date/Time, Theme, User -->
        <div class="flex items-center gap-3">
            <!-- Date Time -->
            <div class="hidden xl:flex flex-col text-right pr-2 border-r border-slate-800">
                <span class="text-xs font-semibold text-slate-200" id="liveDateTime"></span>
                <span class="text-[10px] text-slate-400" id="liveDay"></span>
            </div>

            <!-- Day/Night Toggle -->
            <div class="hidden sm:flex items-center bg-[#0a1228] border border-slate-700/60 rounded-xl p-0.5 text-[11px]">
                <button class="px-2 py-1 text-slate-400 hover:text-white flex items-center gap-1"><i class="fa-regular fa-sun text-[10px]"></i> Day</button>
                <button class="px-2 py-1 rounded-lg bg-blue-600/30 text-cyan-300 font-semibold border border-blue-500/30 flex items-center gap-1"><i class="fa-solid fa-moon text-[10px]"></i> Night</button>
            </div>

            <!-- User Profile -->
            <div class="flex items-center gap-2.5 pl-1">
                <div class="relative">
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&h=100&fit=crop&crop=face" 
                         alt="User Avatar" class="w-9 h-9 rounded-xl object-cover ring-2 ring-cyan-500/40 shadow-md">
                    <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-500 rounded-full ring-2 ring-[#0a1128]"></span>
                </div>
                <div class="hidden lg:block text-left">
                    <div class="text-xs font-bold text-white flex items-center gap-1">
                        Kazi Jahidur Rahman
                    </div>
                    <div class="text-[10px] font-medium text-cyan-400 tracking-wider uppercase">Admin</div>
                </div>
            </div>

        </div>

    </div>
</header>
