<!-- TOP HEADER -->
<header class="glass-panel sticky top-0 z-50 border-b border-slate-700/50 px-4 lg:px-6 py-2.5">
    <div class="flex items-center justify-between gap-4">
        
        <!-- Left: Logo & Company -->
        <div class="flex items-center gap-3 min-w-[280px]">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                @if(!empty($globalCompany->logo) && file_exists(public_path('storage/' . $globalCompany->logo)))
                    <div class="relative flex items-center justify-center h-11 sm:h-12 w-auto min-w-[48px] max-w-[200px] rounded-xl bg-white p-1 shadow-lg shadow-blue-500/20 flex-shrink-0 overflow-hidden border border-cyan-400/50">
                        <img src="{{ asset('storage/' . $globalCompany->logo) }}" alt="Logo" class="h-full w-auto max-w-full object-contain">
                    </div>
                @else
                    <div class="relative flex items-center justify-center w-11 h-11 rounded-xl bg-gradient-to-tr from-cyan-600 via-blue-600 to-indigo-600 p-[1.5px] shadow-lg shadow-blue-500/20 flex-shrink-0 overflow-hidden">
                        <div class="w-full h-full bg-[#0a1128] rounded-[10px] flex items-center justify-center">
                            <span class="text-sm font-black tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-400">
                                {{ strtoupper(substr($globalCompany->company_name ?? 'TBG', 0, 3)) }}
                            </span>
                        </div>
                    </div>
                @endif
                <div>
                    <h1 class="text-base font-extrabold tracking-wide text-white flex items-center gap-2 group-hover:text-cyan-300 transition-colors">
                        {{ $globalCompany->company_name ?? 'Turkey Bangla Garments Ltd' }}
                    </h1>
                    <p class="text-[11px] font-medium text-slate-400 flex items-center gap-1.5">
                        <span class="text-cyan-400">Business ERP Solution</span>
                        <span class="text-slate-600">•</span>
                        <span class="px-1.5 py-0.2 rounded bg-blue-500/10 text-blue-400 font-mono text-[10px] border border-blue-500/20">V6.0</span>
                    </p>
                </div>
            </a>
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

        <!-- Right Controls: User Profile -->
        <div class="flex items-center gap-3">
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

