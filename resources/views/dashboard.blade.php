<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TBG - Turkey Bangla Garments Ltd | ERP Solution V6.0</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Noto+Sans+Bengali:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', '"Noto Sans Bengali"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            dark: '#070c1e',
                            card: '#0d1630',
                            cardBorder: 'rgba(255, 255, 255, 0.08)',
                            neonBlue: '#00f2fe',
                            neonPurple: '#9d4edd',
                            neonGreen: '#10b981',
                            neonAmber: '#f59e0b',
                            neonRose: '#f43f5e'
                        }
                    }
                }
            }
        }
    </script>

    <style>
        * {
            box-sizing: border-box;
            scrollbar-width: thin;
            scrollbar-color: rgba(59, 130, 246, 0.3) rgba(10, 17, 40, 0.5);
        }
        *::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        *::-webkit-scrollbar-track {
            background: rgba(10, 17, 40, 0.5);
        }
        *::-webkit-scrollbar-thumb {
            background: rgba(59, 130, 246, 0.35);
            border-radius: 9999px;
        }
        *::-webkit-scrollbar-thumb:hover {
            background: rgba(59, 130, 246, 0.6);
        }

        body {
            background-color: #060a19;
            background-image: 
                radial-gradient(at 0% 0%, rgba(14, 165, 233, 0.12) 0px, transparent 50%),
                radial-gradient(at 50% 0%, rgba(99, 102, 241, 0.1) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(217, 70, 239, 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(16, 185, 129, 0.07) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(59, 130, 246, 0.08) 0px, transparent 50%);
            background-attachment: fixed;
            color: #e2e8f0;
            overflow-x: hidden;
        }

        .glass-panel {
            background: rgba(13, 22, 48, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.07);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.35);
        }

        .glass-panel-glow {
            background: rgba(13, 22, 48, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(56, 189, 248, 0.2);
            box-shadow: 0 0 25px -5px rgba(14, 165, 233, 0.2), 0 8px 32px 0 rgba(0, 0, 0, 0.35);
        }

        .glass-card {
            background: rgba(16, 27, 58, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.06);
            transition: all 0.25s ease;
        }
        .glass-card:hover {
            border-color: rgba(56, 189, 248, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4);
        }

        /* Mode Switcher Glow Buttons */
        .mode-btn {
            position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .mode-btn.active {
            background: linear-gradient(135deg, #0ea5e9, #3b82f6);
            color: #ffffff;
            box-shadow: 0 0 20px rgba(14, 165, 233, 0.55), inset 0 1px 0 rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.3);
            font-weight: 700;
        }
        .mode-btn.active::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 20%;
            right: 20%;
            height: 3px;
            background: #38bdf8;
            border-radius: 9999px;
            box-shadow: 0 0 8px #38bdf8;
        }

        /* 3D Isometric Glow Cube animation */
        @keyframes float-cube {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-6px) rotate(2deg); }
        }
        .floating-cube {
            animation: float-cube 4s ease-in-out infinite;
        }

        /* Pulsing Dot */
        @keyframes pulse-glow {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.15); }
        }
        .pulse-online {
            animation: pulse-glow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Ultra-Realistic 3D Glowing Glassmorphism KPI Cards (Exact replica of Reference Image) */
        .kpi-exact-card {
            position: relative;
            border-radius: 18px;
            padding: 16px 18px;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .kpi-exact-card:hover {
            transform: translateY(-4px);
        }

        /* Glass top highlight sheen */
        .kpi-exact-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 48%;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.22) 0%, rgba(255, 255, 255, 0.02) 100%);
            border-radius: 17px 17px 60px 60px;
            pointer-events: none;
            z-index: 1;
        }

        /* 1. BLUE CARD - Total Sales */
        .kpi-exact-blue {
            background: linear-gradient(135deg, #0264d8 0%, #0382f7 40%, #014ca5 100%);
            border: 1.5px solid rgba(125, 211, 252, 0.65);
            box-shadow: 0 10px 25px -4px rgba(2, 132, 247, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.7), inset 0 -2px 6px rgba(0, 0, 0, 0.3);
        }
        .kpi-exact-blue:hover {
            box-shadow: 0 14px 32px 0 rgba(2, 132, 247, 0.7), inset 0 1px 2px rgba(255, 255, 255, 0.8);
        }
        .kpi-orb-blue {
            background: radial-gradient(circle at 35% 30%, #5eead4 0%, #0ea5e9 45%, #0369a1 90%);
            border: 2px solid rgba(255, 255, 255, 0.7);
            box-shadow: 0 0 18px rgba(14, 165, 233, 0.8), inset 0 2px 4px rgba(255, 255, 255, 0.75), inset 0 -2px 4px rgba(0, 0, 0, 0.4);
        }

        /* 2. GREEN CARD - Total Purchase */
        .kpi-exact-green {
            background: linear-gradient(135deg, #059669 0%, #10b981 40%, #047857 100%);
            border: 1.5px solid rgba(110, 231, 183, 0.65);
            box-shadow: 0 10px 25px -4px rgba(16, 185, 129, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.7), inset 0 -2px 6px rgba(0, 0, 0, 0.3);
        }
        .kpi-exact-green:hover {
            box-shadow: 0 14px 32px 0 rgba(16, 185, 129, 0.7), inset 0 1px 2px rgba(255, 255, 255, 0.8);
        }
        .kpi-orb-green {
            background: radial-gradient(circle at 35% 30%, #a7f3d0 0%, #34d399 45%, #059669 90%);
            border: 2px solid rgba(255, 255, 255, 0.7);
            box-shadow: 0 0 18px rgba(52, 211, 153, 0.8), inset 0 2px 4px rgba(255, 255, 255, 0.75), inset 0 -2px 4px rgba(0, 0, 0, 0.4);
        }

        /* 3. PURPLE CARD - Total Due */
        .kpi-exact-purple {
            background: linear-gradient(135deg, #7c3aed 0%, #9333ea 40%, #6b21a8 100%);
            border: 1.5px solid rgba(216, 180, 254, 0.65);
            box-shadow: 0 10px 25px -4px rgba(147, 51, 234, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.7), inset 0 -2px 6px rgba(0, 0, 0, 0.3);
        }
        .kpi-exact-purple:hover {
            box-shadow: 0 14px 32px 0 rgba(147, 51, 234, 0.7), inset 0 1px 2px rgba(255, 255, 255, 0.8);
        }
        .kpi-orb-purple {
            background: radial-gradient(circle at 35% 30%, #f3e8ff 0%, #c084fc 45%, #7e22ce 90%);
            border: 2px solid rgba(255, 255, 255, 0.7);
            box-shadow: 0 0 18px rgba(192, 132, 252, 0.8), inset 0 2px 4px rgba(255, 255, 255, 0.75), inset 0 -2px 4px rgba(0, 0, 0, 0.4);
        }

        /* 4. AMBER / ORANGE CARD - Total Stock Value */
        .kpi-exact-amber {
            background: linear-gradient(135deg, #d97706 0%, #f59e0b 40%, #b45309 100%);
            border: 1.5px solid rgba(253, 230, 138, 0.7);
            box-shadow: 0 10px 25px -4px rgba(245, 158, 11, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.75), inset 0 -2px 6px rgba(0, 0, 0, 0.3);
        }
        .kpi-exact-amber:hover {
            box-shadow: 0 14px 32px 0 rgba(245, 158, 11, 0.7), inset 0 1px 2px rgba(255, 255, 255, 0.85);
        }
        .kpi-orb-amber {
            background: radial-gradient(circle at 35% 30%, #fef3c7 0%, #fbbf24 45%, #d97706 90%);
            border: 2px solid rgba(255, 255, 255, 0.7);
            box-shadow: 0 0 18px rgba(251, 191, 36, 0.8), inset 0 2px 4px rgba(255, 255, 255, 0.75), inset 0 -2px 4px rgba(0, 0, 0, 0.4);
        }
    </style>
</head>
<body class="min-h-screen text-slate-200 antialiased flex flex-col justify-between">

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

            <!-- Right Controls: Date/Time, Lang, Theme, User -->
            <div class="flex items-center gap-3">
                <!-- Date Time -->
                <div class="hidden xl:flex flex-col text-right pr-2 border-r border-slate-800">
                    <span class="text-xs font-semibold text-slate-200" id="liveDateTime">20 Apr 2026 | 11:24 AM</span>
                    <span class="text-[10px] text-slate-400" id="liveDay">Monday</span>
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

    <!-- MAIN WRAPPER (SIDEBAR + CONTENT) -->
    <div class="flex flex-1 overflow-hidden relative">

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
                    <a href="#" class="flex items-center justify-between px-3 py-2.5 rounded-xl bg-gradient-to-r from-blue-600/90 to-cyan-600/90 text-white font-semibold text-xs shadow-lg shadow-blue-500/25 border border-cyan-400/30">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-house text-sm"></i>
                            <span>Dashboard</span>
                        </div>
                        <i class="fa-solid fa-chevron-right text-[10px] text-cyan-200"></i>
                    </a>

                    <!-- PACKAGE ERP MENUS (Visible when Package ERP is active) -->
                    <div id="packageMenuContainer" class="space-y-1 hidden pt-1">
                        <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-widest text-cyan-400/80">
                            Package Modules
                        </div>

                        <!-- Product Name Menu -->
                        <a href="#product-name" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800/50 hover:border-slate-700/60 border border-transparent transition-all group">
                            <div class="flex items-center gap-3">
                                <span class="w-7 h-7 rounded-lg bg-cyan-500/10 text-cyan-400 flex items-center justify-center group-hover:bg-cyan-500/20 border border-cyan-500/20">
                                    <i class="fa-solid fa-tags text-xs"></i>
                                </span>
                                <span class="font-semibold text-white">Product Name</span>
                            </div>
                            <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">New</span>
                        </a>

                        <!-- Unit Name Menu -->
                        <a href="#unit-name" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800/50 hover:border-slate-700/60 border border-transparent transition-all group">
                            <div class="flex items-center gap-3">
                                <span class="w-7 h-7 rounded-lg bg-purple-500/10 text-purple-400 flex items-center justify-center group-hover:bg-purple-500/20 border border-purple-500/20">
                                    <i class="fa-solid fa-scale-balanced text-xs"></i>
                                </span>
                                <span class="font-semibold text-white">Unit Name</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-500"></i>
                        </a>

                        <div class="pt-3 border-t border-slate-800/80">
                            <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                                Package Config
                            </div>
                            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-slate-400 hover:text-slate-200 hover:bg-slate-800/30">
                                <i class="fa-solid fa-box-archive text-xs text-amber-400"></i>
                                <span>Packaging Sizes</span>
                            </a>
                            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-slate-400 hover:text-slate-200 hover:bg-slate-800/30">
                                <i class="fa-solid fa-barcode text-xs text-emerald-400"></i>
                                <span>Barcode Generator</span>
                            </a>
                        </div>
                    </div>

                    <!-- FACTORY ERP MENUS (Visible when Factory ERP is active) -->
                    <div id="factoryMenuContainer" class="space-y-0.5 pt-1">
                        <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-widest text-blue-400/80">
                            Factory Modules
                        </div>

                        <!-- 1. Product list -->
                        <a href="#product-list" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800/50 transition-all group">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-lg bg-blue-500/10 text-blue-400 flex items-center justify-center group-hover:bg-blue-500/20">
                                    <i class="fa-solid fa-boxes-stacked text-[11px]"></i>
                                </span>
                                <span>Product List</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-500"></i>
                        </a>

                        <!-- 2. Customer -->
                        <a href="#customer" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800/50 transition-all group">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-lg bg-cyan-500/10 text-cyan-400 flex items-center justify-center group-hover:bg-cyan-500/20">
                                    <i class="fa-solid fa-user-group text-[11px]"></i>
                                </span>
                                <span>Customer</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-500"></i>
                        </a>

                        <!-- 3. Price Package -->
                        <a href="#price-package" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800/50 transition-all group">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center group-hover:bg-emerald-500/20">
                                    <i class="fa-solid fa-tags text-[11px]"></i>
                                </span>
                                <span>Price Package</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-500"></i>
                        </a>

                        <!-- 4. Order -->
                        <a href="#order" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800/50 transition-all group">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center group-hover:bg-amber-500/20">
                                    <i class="fa-solid fa-cart-shopping text-[11px]"></i>
                                </span>
                                <span>Order</span>
                            </div>
                            <span class="text-[10px] px-1.5 py-0.2 bg-rose-500/20 text-rose-300 rounded font-mono font-bold">14</span>
                        </a>

                        <!-- 5. Production -->
                        <a href="#production" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800/50 transition-all group">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-lg bg-purple-500/10 text-purple-400 flex items-center justify-center group-hover:bg-purple-500/20">
                                    <i class="fa-solid fa-industry text-[11px]"></i>
                                </span>
                                <span>Production</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-500"></i>
                        </a>

                        <!-- 6. Delivery -->
                        <a href="#delivery" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800/50 transition-all group">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center group-hover:bg-indigo-500/20">
                                    <i class="fa-solid fa-truck-fast text-[11px]"></i>
                                </span>
                                <span>Delivery</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-500"></i>
                        </a>

                        <!-- 7. Stock -->
                        <a href="#stock" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800/50 transition-all group">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-lg bg-teal-500/10 text-teal-400 flex items-center justify-center group-hover:bg-teal-500/20">
                                    <i class="fa-solid fa-warehouse text-[11px]"></i>
                                </span>
                                <span>Stock</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-500"></i>
                        </a>

                        <!-- 8. Investor -->
                        <a href="#investor" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800/50 transition-all group">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-lg bg-pink-500/10 text-pink-400 flex items-center justify-center group-hover:bg-pink-500/20">
                                    <i class="fa-solid fa-hand-holding-dollar text-[11px]"></i>
                                </span>
                                <span>Investor</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-500"></i>
                        </a>

                        <!-- 9. Reports -->
                        <a href="#reports" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800/50 transition-all group">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-lg bg-rose-500/10 text-rose-400 flex items-center justify-center group-hover:bg-rose-500/20">
                                    <i class="fa-solid fa-chart-pie text-[11px]"></i>
                                </span>
                                <span>Reports</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-500"></i>
                        </a>

                    </div>

                </nav>

            </div>

            <!-- Bottom Glowing 3D Cube Card (As in Reference Image) -->
            <div class="relative overflow-hidden rounded-2xl p-4 bg-gradient-to-br from-indigo-950/80 via-[#0a122e] to-purple-950/80 border border-blue-500/30 shadow-xl shadow-blue-500/10 text-center">
                <div class="absolute -top-10 -right-10 w-24 h-24 bg-cyan-500/20 rounded-full blur-xl"></div>
                <div class="absolute -bottom-10 -left-10 w-24 h-24 bg-purple-500/20 rounded-full blur-xl"></div>
                
                <!-- 3D Cube graphic -->
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

        <!-- MAIN DASHBOARD CONTENT AREA -->
        <main class="flex-1 overflow-y-auto p-4 lg:p-6 space-y-5">

            <!-- TOP BANNER & 4 KPI METRIC CARDS -->
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-5">
                
                <!-- Welcome Banner (Factory Showcase) -->
                <div class="xl:col-span-12 relative overflow-hidden rounded-3xl glass-panel border border-slate-700/60 shadow-2xl" style="min-height: 220px;">
                    
                    <!-- === PANORAMIC FUTURISTIC CITY/FACTORY BACKGROUND === -->
                    <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1800&auto=format&fit=crop&q=85" 
                         alt="City Skyline" class="absolute inset-0 w-full h-full object-cover object-center opacity-50">
                    
                    <!-- Gradient overlays for depth -->
                    <div class="absolute inset-0 bg-gradient-to-r from-[#060b1d]/95 via-[#070e24]/80 to-[#0a1535]/50 z-[1]"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-[#060b1d]/90 via-transparent to-transparent z-[2]"></div>

                    <!-- Neon glow accents -->
                    <div class="absolute top-0 left-1/4 w-72 h-40 bg-cyan-500/10 rounded-full blur-3xl z-[3] pointer-events-none"></div>
                    <div class="absolute bottom-0 right-1/4 w-72 h-40 bg-indigo-500/10 rounded-full blur-3xl z-[3] pointer-events-none"></div>
                    
                    <!-- === MAIN CONTENT LAYER === -->
                    <div class="relative z-10 p-6">
                        
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <!-- LEFT: Welcome Text -->
                            <div>
                                <div class="flex items-center gap-2 mb-1.5">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-cyan-500/20 text-cyan-400 border border-cyan-500/30 flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-ping"></span>
                                        Live Factory Monitoring
                                    </span>
                                    <span class="text-[11px] text-slate-400/80">Turkey Bangla Garments Head Office</span>
                                </div>
                                <p class="text-sm text-slate-400 font-medium">Welcome Back,</p>
                                <h2 class="text-2xl lg:text-3xl font-extrabold text-white tracking-tight mt-0.5">
                                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-blue-400 to-indigo-400">Kazi Jahidur Rahman</span>
                                </h2>
                                <p class="text-xs text-slate-400 mt-1.5 max-w-md leading-relaxed">
                                    Here's what's happening with your business today.
                                </p>
                            </div>

                            <!-- RIGHT: TBG Logo Circle + Calligraphy Motto (exactly like reference image) -->
                            <div class="hidden lg:flex items-center gap-5 pr-4">
                                
                                <!-- TBG 3D Glowing Logo Circle -->
                                <div class="relative flex-shrink-0">
                                    <!-- Outer glow ring -->
                                    <div class="absolute -inset-3 rounded-full bg-gradient-to-tr from-cyan-500/25 via-blue-500/20 to-indigo-500/25 blur-lg animate-pulse"></div>
                                    
                                    <div class="relative w-20 h-20 rounded-full bg-gradient-to-tr from-cyan-600 via-blue-600 to-indigo-700 p-[2.5px] shadow-xl shadow-cyan-500/30 ring-2 ring-cyan-400/20 ring-offset-2 ring-offset-[#0a1535]">
                                        <div class="w-full h-full rounded-full bg-[#0a1128] flex items-center justify-center">
                                            <span class="text-lg font-black tracking-widest text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-blue-300 to-indigo-400">TBG</span>
                                        </div>
                                    </div>

                                    <!-- Decorative orbit dots -->
                                    <div class="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-cyan-400 shadow-lg shadow-cyan-400/60"></div>
                                    <div class="absolute -bottom-1 -left-1 w-1.5 h-1.5 rounded-full bg-indigo-400 shadow-lg shadow-indigo-400/60"></div>
                                </div>

                                <!-- Calligraphy Motto Text (italic handwritten style) -->
                                <div class="text-right">
                                    <p class="text-lg font-bold italic text-transparent bg-clip-text bg-gradient-to-r from-cyan-300 via-sky-200 to-blue-300 leading-tight" style="font-family: 'Georgia', 'Times New Roman', serif;">
                                        Quality Fashion
                                    </p>
                                    <p class="text-base italic text-transparent bg-clip-text bg-gradient-to-r from-blue-300 via-indigo-200 to-purple-300 leading-tight" style="font-family: 'Georgia', 'Times New Roman', serif;">
                                        For a Better Tomorrow
                                    </p>
                                    <div class="mt-2 flex items-center justify-end gap-1.5">
                                        <span class="w-8 h-[1px] bg-gradient-to-r from-transparent to-cyan-400/60"></span>
                                        <span class="text-[9px] font-semibold uppercase tracking-[0.25em] text-cyan-400/60">Turkey Bangla Garments</span>
                                        <span class="w-8 h-[1px] bg-gradient-to-l from-transparent to-cyan-400/60"></span>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- 4 GLOWING KPI CARDS (EXACT REPLICA OF REFERENCE IMAGE) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6 relative z-10">
                        
                        <!-- 1. Total Sales (Today) - Vibrant Deep Blue Card -->
                        <div class="kpi-exact-card kpi-exact-blue">
                            <!-- Left: 3D Glossy Sphere Icon -->
                            <div class="relative z-10 flex-shrink-0">
                                <div class="w-14 h-14 rounded-full kpi-orb-blue flex items-center justify-center text-white text-xl shadow-lg">
                                    <i class="fa-solid fa-cart-shopping drop-shadow-md"></i>
                                </div>
                            </div>
                            <!-- Right: Value & Details -->
                            <div class="relative z-10 text-right flex-1 pl-3">
                                <p class="text-[11px] font-semibold text-cyan-100 tracking-wide">Total Sales (Today)</p>
                                <h3 class="text-2xl font-black text-white tracking-tight leading-tight my-0.5">৳ 2,45,680</h3>
                                <div class="flex items-center justify-end gap-1.5 text-[11px] font-bold text-emerald-300">
                                    <span>↑ 12%</span>
                                    <!-- Mini 3-bar chart icon -->
                                    <span class="inline-flex items-end gap-0.5 h-3 ml-1">
                                        <span class="w-1 h-1.5 bg-emerald-300 rounded-sm"></span>
                                        <span class="w-1 h-2.5 bg-emerald-300 rounded-sm"></span>
                                        <span class="w-1 h-3.5 bg-emerald-300 rounded-sm"></span>
                                    </span>
                                </div>
                            </div>
                            <!-- Corner decorative notch -->
                            <span class="absolute bottom-1.5 left-2 text-[9px] text-white/30 font-mono select-none">◣</span>
                        </div>

                        <!-- 2. Total Purchase (Today) - Vibrant Emerald Green Card -->
                        <div class="kpi-exact-card kpi-exact-green">
                            <!-- Left: 3D Glossy Sphere Icon -->
                            <div class="relative z-10 flex-shrink-0">
                                <div class="w-14 h-14 rounded-full kpi-orb-green flex items-center justify-center text-white text-xl shadow-lg">
                                    <i class="fa-solid fa-cart-plus drop-shadow-md"></i>
                                </div>
                            </div>
                            <!-- Right: Value & Details -->
                            <div class="relative z-10 text-right flex-1 pl-3">
                                <p class="text-[11px] font-semibold text-emerald-100 tracking-wide">Total Purchase (Today)</p>
                                <h3 class="text-2xl font-black text-white tracking-tight leading-tight my-0.5">৳ 1,60,430</h3>
                                <div class="flex items-center justify-end gap-1.5 text-[11px] font-bold text-emerald-200">
                                    <span>↑ 8%</span>
                                    <span class="inline-flex items-end gap-0.5 h-3 ml-1">
                                        <span class="w-1 h-1.5 bg-emerald-200 rounded-sm"></span>
                                        <span class="w-1 h-2 bg-emerald-200 rounded-sm"></span>
                                        <span class="w-1 h-3 bg-emerald-200 rounded-sm"></span>
                                    </span>
                                </div>
                            </div>
                            <span class="absolute bottom-1.5 left-2 text-[9px] text-white/30 font-mono select-none">◣</span>
                        </div>

                        <!-- 3. Total Due (Customer) - Vibrant Royal Purple Card -->
                        <div class="kpi-exact-card kpi-exact-purple">
                            <!-- Left: 3D Glossy Sphere Icon -->
                            <div class="relative z-10 flex-shrink-0">
                                <div class="w-14 h-14 rounded-full kpi-orb-purple flex items-center justify-center text-white text-xl shadow-lg">
                                    <i class="fa-solid fa-user drop-shadow-md"></i>
                                </div>
                            </div>
                            <!-- Right: Value & Details -->
                            <div class="relative z-10 text-right flex-1 pl-3">
                                <p class="text-[11px] font-semibold text-purple-100 tracking-wide">Total Due (Customer)</p>
                                <h3 class="text-2xl font-black text-white tracking-tight leading-tight my-0.5">৳ 95,240</h3>
                                <div class="flex items-center justify-end gap-1.5 text-[11px] font-bold text-rose-300">
                                    <span>↓ 5%</span>
                                    <span class="inline-flex items-end gap-0.5 h-3 ml-1">
                                        <span class="w-1 h-3 bg-purple-200 rounded-sm"></span>
                                        <span class="w-1 h-2 bg-purple-200 rounded-sm"></span>
                                        <span class="w-1 h-1 bg-purple-200 rounded-sm"></span>
                                    </span>
                                </div>
                            </div>
                            <span class="absolute bottom-1.5 left-2 text-[9px] text-white/30 font-mono select-none">◣</span>
                        </div>

                        <!-- 4. Total Stock Value - Vibrant Warm Amber / Orange Card -->
                        <div class="kpi-exact-card kpi-exact-amber">
                            <!-- Left: 3D Glossy Sphere Icon -->
                            <div class="relative z-10 flex-shrink-0">
                                <div class="w-14 h-14 rounded-full kpi-orb-amber flex items-center justify-center text-white text-xl shadow-lg">
                                    <i class="fa-solid fa-box-open drop-shadow-md"></i>
                                </div>
                            </div>
                            <!-- Right: Value & Details -->
                            <div class="relative z-10 text-right flex-1 pl-3">
                                <p class="text-[11px] font-semibold text-amber-100 tracking-wide">Total Stock Value</p>
                                <h3 class="text-2xl font-black text-white tracking-tight leading-tight my-0.5">৳ 12,560</h3>
                                <div class="flex items-center justify-end gap-1.5 text-[11px] font-bold text-amber-200">
                                    <span>↑ 3%</span>
                                    <span class="inline-flex items-end gap-0.5 h-3 ml-1">
                                        <span class="w-1 h-1 bg-amber-200 rounded-sm"></span>
                                        <span class="w-1 h-2 bg-amber-200 rounded-sm"></span>
                                        <span class="w-1 h-3 bg-amber-200 rounded-sm"></span>
                                    </span>
                                </div>
                            </div>
                            <span class="absolute bottom-1.5 left-2 text-[9px] text-white/30 font-mono select-none">◣</span>
                        </div>

                    </div> <!-- /KPI grid -->
                    </div> <!-- /p-6 content wrapper -->
                </div> <!-- /banner -->

            </div>

            <!-- ROW 2: CHARTS & QUICK ACTIONS -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                
                <!-- Sales Overview Area Chart (5 Cols) -->
                <div class="lg:col-span-5 glass-panel rounded-3xl p-5 border border-slate-700/60 flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                                <i class="fa-solid fa-chart-line text-cyan-400"></i>
                                Sales Overview
                            </h3>
                            <p class="text-[11px] text-slate-400">Daily garments shipment & sales analytics</p>
                        </div>
                        <select class="bg-[#0b142d] text-xs text-slate-300 border border-slate-700 rounded-lg px-2.5 py-1 focus:outline-none focus:border-cyan-400">
                            <option>Last 7 Days</option>
                            <option>Last 30 Days</option>
                            <option>This Year</option>
                        </select>
                    </div>
                    <div class="relative h-56 w-full">
                        <canvas id="salesOverviewChart"></canvas>
                    </div>
                </div>

                <!-- Sales by Category Donut Chart (4 Cols) -->
                <div class="lg:col-span-4 glass-panel rounded-3xl p-5 border border-slate-700/60 flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                                <i class="fa-solid fa-chart-pie text-purple-400"></i>
                                Sales by Category
                            </h3>
                            <p class="text-[11px] text-slate-400">Distribution among garments segments</p>
                        </div>
                        <span class="text-[10px] font-semibold text-cyan-400 bg-cyan-500/10 px-2 py-0.5 rounded-full border border-cyan-500/20">Today</span>
                    </div>

                    <div class="flex items-center justify-center relative my-auto">
                        <div class="w-44 h-44 relative">
                            <canvas id="categoryDonutChart"></canvas>
                            <!-- Center Label -->
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none text-center">
                                <span class="text-[9px] uppercase font-bold text-slate-400">Total</span>
                                <span class="text-xs font-black text-cyan-300">৳ 2,45,680</span>
                                <span class="text-[9px] text-slate-400">(Today)</span>
                            </div>
                        </div>

                        <!-- Legend Details -->
                        <div class="ml-4 space-y-1.5 text-[11px]">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-cyan-400"></span>
                                <span class="text-slate-300">Men's Innerwear</span>
                                <span class="font-bold text-white ml-auto">32%</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-purple-400"></span>
                                <span class="text-slate-300">Women's Innerwear</span>
                                <span class="font-bold text-white ml-auto">24%</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                                <span class="text-slate-300">Socks</span>
                                <span class="font-bold text-white ml-auto">15%</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                                <span class="text-slate-300">T-Shirt</span>
                                <span class="font-bold text-white ml-auto">12%</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-pink-400"></span>
                                <span class="text-slate-300">Others</span>
                                <span class="font-bold text-white ml-auto">17%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stock Status & Alerts (3 Cols) -->
                <div class="lg:col-span-3 glass-panel rounded-3xl p-5 border border-slate-700/60 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                                <i class="fa-solid fa-boxes-stacked text-amber-400"></i>
                                Stock Status
                            </h3>
                            <button class="text-[10px] text-cyan-400 hover:underline">Manage</button>
                        </div>

                        <!-- Stock Hero Box -->
                        <div class="p-3.5 rounded-2xl bg-gradient-to-r from-blue-900/30 to-indigo-900/20 border border-blue-500/20 flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-400/30 flex items-center justify-center text-amber-300 text-lg">
                                <i class="fa-solid fa-box"></i>
                            </div>
                            <div>
                                <span class="text-lg font-black text-white">12,560</span>
                                <p class="text-[10px] text-slate-400">Total Stock Items in Warehouse</p>
                            </div>
                        </div>

                        <!-- Progress Bars -->
                        <div class="space-y-3">
                            <!-- In Stock -->
                            <div>
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="flex items-center gap-1.5 text-slate-300 font-medium">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span> In Stock
                                    </span>
                                    <span class="font-bold text-white">10,245</span>
                                </div>
                                <div class="w-full bg-slate-800 rounded-full h-2 overflow-hidden">
                                    <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-2 rounded-full" style="width: 82%"></div>
                                </div>
                            </div>

                            <!-- Low Stock -->
                            <div>
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="flex items-center gap-1.5 text-slate-300 font-medium">
                                        <span class="w-2 h-2 rounded-full bg-amber-400"></span> Low Stock
                                    </span>
                                    <span class="font-bold text-amber-300">1,850</span>
                                </div>
                                <div class="w-full bg-slate-800 rounded-full h-2 overflow-hidden">
                                    <div class="bg-gradient-to-r from-amber-500 to-yellow-400 h-2 rounded-full" style="width: 15%"></div>
                                </div>
                            </div>

                            <!-- Out of Stock -->
                            <div>
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="flex items-center gap-1.5 text-slate-300 font-medium">
                                        <span class="w-2 h-2 rounded-full bg-rose-400"></span> Out of Stock
                                    </span>
                                    <span class="font-bold text-rose-400">465</span>
                                </div>
                                <div class="w-full bg-slate-800 rounded-full h-2 overflow-hidden">
                                    <div class="bg-gradient-to-r from-rose-500 to-red-400 h-2 rounded-full" style="width: 3%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-800/80 flex items-center justify-between text-[11px] text-slate-400">
                        <span>Auto-reorder threshold: Active</span>
                        <i class="fa-solid fa-check text-emerald-400"></i>
                    </div>
                </div>

            </div>

            <!-- ROW 3: MAIN MODULES (10 GLOWING BUTTONS) & RIGHT SIDEBAR (QUICK ACTIONS + RECENT ACTIVITIES) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                
                <!-- Left: Main Modules Grid (8 Cols) -->
                <div class="lg:col-span-8 space-y-5">
                    
                    <!-- Main Modules Card -->
                    <div class="glass-panel rounded-3xl p-5 border border-slate-700/60">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                                    <i class="fa-solid fa-grid-2 text-cyan-400"></i>
                                    Main ERP Modules
                                </h3>
                                <p class="text-[11px] text-slate-400">Quick access to essential departments</p>
                            </div>
                            <span class="text-[10px] text-cyan-400 font-mono">10 Modules Online</span>
                        </div>

                        <!-- 2x5 Buttons Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                            
                            <!-- 1. Sales & Billing -->
                            <a href="#" class="glass-card rounded-2xl p-3 flex flex-col items-center text-center group border border-blue-500/20 hover:border-blue-400">
                                <div class="w-11 h-11 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform shadow-lg shadow-blue-500/20">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                </div>
                                <span class="text-xs font-bold text-white group-hover:text-cyan-300">Sales & Billing</span>
                                <i class="fa-solid fa-arrow-right text-[10px] text-slate-500 mt-1 group-hover:text-cyan-400 group-hover:translate-x-1 transition-all"></i>
                            </a>

                            <!-- 2. Purchase -->
                            <a href="#" class="glass-card rounded-2xl p-3 flex flex-col items-center text-center group border border-emerald-500/20 hover:border-emerald-400">
                                <div class="w-11 h-11 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform shadow-lg shadow-emerald-500/20">
                                    <i class="fa-solid fa-truck"></i>
                                </div>
                                <span class="text-xs font-bold text-white group-hover:text-emerald-300">Purchase</span>
                                <i class="fa-solid fa-arrow-right text-[10px] text-slate-500 mt-1 group-hover:text-emerald-400 group-hover:translate-x-1 transition-all"></i>
                            </a>

                            <!-- 3. Inventory -->
                            <a href="#" class="glass-card rounded-2xl p-3 flex flex-col items-center text-center group border border-amber-500/20 hover:border-amber-400">
                                <div class="w-11 h-11 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform shadow-lg shadow-amber-500/20">
                                    <i class="fa-solid fa-boxes-packing"></i>
                                </div>
                                <span class="text-xs font-bold text-white group-hover:text-amber-300">Inventory</span>
                                <i class="fa-solid fa-arrow-right text-[10px] text-slate-500 mt-1 group-hover:text-amber-400 group-hover:translate-x-1 transition-all"></i>
                            </a>

                            <!-- 4. Production -->
                            <a href="#" class="glass-card rounded-2xl p-3 flex flex-col items-center text-center group border border-purple-500/20 hover:border-purple-400">
                                <div class="w-11 h-11 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform shadow-lg shadow-purple-500/20">
                                    <i class="fa-solid fa-gears"></i>
                                </div>
                                <span class="text-xs font-bold text-white group-hover:text-purple-300">Production</span>
                                <i class="fa-solid fa-arrow-right text-[10px] text-slate-500 mt-1 group-hover:text-purple-400 group-hover:translate-x-1 transition-all"></i>
                            </a>

                            <!-- 5. Raw Material -->
                            <a href="#" class="glass-card rounded-2xl p-3 flex flex-col items-center text-center group border border-teal-500/20 hover:border-teal-400">
                                <div class="w-11 h-11 rounded-xl bg-teal-500/20 text-teal-400 flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform shadow-lg shadow-teal-500/20">
                                    <i class="fa-solid fa-layer-group"></i>
                                </div>
                                <span class="text-xs font-bold text-white group-hover:text-teal-300">Raw Material</span>
                                <i class="fa-solid fa-arrow-right text-[10px] text-slate-500 mt-1 group-hover:text-teal-400 group-hover:translate-x-1 transition-all"></i>
                            </a>

                            <!-- 6. Customer & CRM -->
                            <a href="#" class="glass-card rounded-2xl p-3 flex flex-col items-center text-center group border border-sky-500/20 hover:border-sky-400">
                                <div class="w-11 h-11 rounded-xl bg-sky-500/20 text-sky-400 flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform shadow-lg shadow-sky-500/20">
                                    <i class="fa-solid fa-user-tie"></i>
                                </div>
                                <span class="text-xs font-bold text-white group-hover:text-sky-300">Customer & CRM</span>
                                <i class="fa-solid fa-arrow-right text-[10px] text-slate-500 mt-1 group-hover:text-sky-400 group-hover:translate-x-1 transition-all"></i>
                            </a>

                            <!-- 7. Supplier -->
                            <a href="#" class="glass-card rounded-2xl p-3 flex flex-col items-center text-center group border border-cyan-500/20 hover:border-cyan-400">
                                <div class="w-11 h-11 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform shadow-lg shadow-cyan-500/20">
                                    <i class="fa-solid fa-handshake"></i>
                                </div>
                                <span class="text-xs font-bold text-white group-hover:text-cyan-300">Supplier</span>
                                <i class="fa-solid fa-arrow-right text-[10px] text-slate-500 mt-1 group-hover:text-cyan-400 group-hover:translate-x-1 transition-all"></i>
                            </a>

                            <!-- 8. HR & Payroll -->
                            <a href="#" class="glass-card rounded-2xl p-3 flex flex-col items-center text-center group border border-orange-500/20 hover:border-orange-400">
                                <div class="w-11 h-11 rounded-xl bg-orange-500/20 text-orange-400 flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform shadow-lg shadow-orange-500/20">
                                    <i class="fa-solid fa-users"></i>
                                </div>
                                <span class="text-xs font-bold text-white group-hover:text-orange-300">HR & Payroll</span>
                                <i class="fa-solid fa-arrow-right text-[10px] text-slate-500 mt-1 group-hover:text-orange-400 group-hover:translate-x-1 transition-all"></i>
                            </a>

                            <!-- 9. Delivery & Courier -->
                            <a href="#" class="glass-card rounded-2xl p-3 flex flex-col items-center text-center group border border-indigo-500/20 hover:border-indigo-400">
                                <div class="w-11 h-11 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform shadow-lg shadow-indigo-500/20">
                                    <i class="fa-solid fa-truck-fast"></i>
                                </div>
                                <span class="text-xs font-bold text-white group-hover:text-indigo-300">Delivery & Courier</span>
                                <i class="fa-solid fa-arrow-right text-[10px] text-slate-500 mt-1 group-hover:text-indigo-400 group-hover:translate-x-1 transition-all"></i>
                            </a>

                            <!-- 10. Marketing & Dealer -->
                            <a href="#" class="glass-card rounded-2xl p-3 flex flex-col items-center text-center group border border-rose-500/20 hover:border-rose-400">
                                <div class="w-11 h-11 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform shadow-lg shadow-rose-500/20">
                                    <i class="fa-solid fa-bullhorn"></i>
                                </div>
                                <span class="text-xs font-bold text-white group-hover:text-rose-300">Marketing & Dealer</span>
                                <i class="fa-solid fa-arrow-right text-[10px] text-slate-500 mt-1 group-hover:text-rose-400 group-hover:translate-x-1 transition-all"></i>
                            </a>

                        </div>
                    </div>

                    <!-- RECENT ORDERS TABLE -->
                    <div class="glass-panel rounded-3xl p-5 border border-slate-700/60">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                                    <i class="fa-solid fa-receipt text-cyan-400"></i>
                                    Recent Orders
                                </h3>
                                <p class="text-[11px] text-slate-400">Live order status from showrooms & dealers</p>
                            </div>
                            <a href="#" class="text-xs text-cyan-400 hover:underline font-semibold">View All</a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="text-slate-400 border-b border-slate-700/80 font-semibold">
                                        <th class="pb-2.5">Order ID</th>
                                        <th class="pb-2.5">Customer</th>
                                        <th class="pb-2.5">Amount</th>
                                        <th class="pb-2.5">Status</th>
                                        <th class="pb-2.5 text-right">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/80">
                                    <tr class="hover:bg-slate-800/40 transition-colors">
                                        <td class="py-2.5 font-mono text-cyan-300 font-medium">SO-2026-0012</td>
                                        <td class="py-2.5 text-white font-medium">Rahim Traders</td>
                                        <td class="py-2.5 font-bold text-white">৳ 18,430</td>
                                        <td class="py-2.5">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Paid</span>
                                        </td>
                                        <td class="py-2.5 text-right text-slate-400"><span class="live-date-today-time" data-offset-hours="-1"></span></td>
                                    </tr>
                                    <tr class="hover:bg-slate-800/40 transition-colors">
                                        <td class="py-2.5 font-mono text-cyan-300 font-medium">SO-2026-0011</td>
                                        <td class="py-2.5 text-white font-medium">Fashion House</td>
                                        <td class="py-2.5 font-bold text-white">৳ 25,750</td>
                                        <td class="py-2.5">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">Due</span>
                                        </td>
                                        <td class="py-2.5 text-right text-slate-400"><span class="live-date-today-time" data-offset-hours="-2"></span></td>
                                    </tr>
                                    <tr class="hover:bg-slate-800/40 transition-colors">
                                        <td class="py-2.5 font-mono text-cyan-300 font-medium">SO-2026-0010</td>
                                        <td class="py-2.5 text-white font-medium">New Life Fashion</td>
                                        <td class="py-2.5 font-bold text-white">৳ 12,300</td>
                                        <td class="py-2.5">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Paid</span>
                                        </td>
                                        <td class="py-2.5 text-right text-slate-400"><span class="live-date-today-time" data-offset-hours="-28"></span></td>
                                    </tr>
                                    <tr class="hover:bg-slate-800/40 transition-colors">
                                        <td class="py-2.5 font-mono text-cyan-300 font-medium">SO-2026-0009</td>
                                        <td class="py-2.5 text-white font-medium">City Mart</td>
                                        <td class="py-2.5 font-bold text-white">৳ 8,950</td>
                                        <td class="py-2.5">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/20 text-blue-400 border border-blue-500/30">Processing</span>
                                        </td>
                                        <td class="py-2.5 text-right text-slate-400"><span class="live-date-today-time" data-offset-hours="-30"></span></td>
                                    </tr>
                                    <tr class="hover:bg-slate-800/40 transition-colors">
                                        <td class="py-2.5 font-mono text-cyan-300 font-medium">SO-2026-0008</td>
                                        <td class="py-2.5 text-white font-medium">Al Amin Store</td>
                                        <td class="py-2.5 font-bold text-white">৳ 15,620</td>
                                        <td class="py-2.5">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Paid</span>
                                        </td>
                                        <td class="py-2.5 text-right text-slate-400"><span class="live-date-today-time" data-offset-hours="-33"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <!-- Right: Quick Actions & Recent Activities Feed (4 Cols) -->
                <div class="lg:col-span-4 space-y-5">
                    
                    <!-- Quick Actions Grid (3x3) -->
                    <div class="glass-panel rounded-3xl p-5 border border-slate-700/60">
                        <h3 class="text-sm font-bold text-white mb-3 flex items-center gap-2">
                            <i class="fa-solid fa-bolt-lightning text-amber-400"></i>
                            Quick Actions
                        </h3>

                        <div class="grid grid-cols-3 gap-2.5">
                            <!-- New Sale -->
                            <button class="p-2.5 rounded-xl bg-blue-600/20 hover:bg-blue-600/30 border border-blue-500/30 flex flex-col items-center text-center transition-all group">
                                <i class="fa-solid fa-cart-plus text-base text-cyan-400 mb-1 group-hover:scale-110 transition-transform"></i>
                                <span class="text-[11px] font-semibold text-slate-200">New Sale</span>
                            </button>
                            <!-- New Purchase -->
                            <button class="p-2.5 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/30 border border-emerald-500/30 flex flex-col items-center text-center transition-all group">
                                <i class="fa-solid fa-bag-shopping text-base text-emerald-400 mb-1 group-hover:scale-110 transition-transform"></i>
                                <span class="text-[11px] font-semibold text-slate-200">New Purchase</span>
                            </button>
                            <!-- Add Product -->
                            <button class="p-2.5 rounded-xl bg-purple-600/20 hover:bg-purple-600/30 border border-purple-500/30 flex flex-col items-center text-center transition-all group">
                                <i class="fa-solid fa-shirt text-base text-purple-400 mb-1 group-hover:scale-110 transition-transform"></i>
                                <span class="text-[11px] font-semibold text-slate-200">Add Product</span>
                            </button>
                            <!-- Create Invoice -->
                            <button class="p-2.5 rounded-xl bg-amber-600/20 hover:bg-amber-600/30 border border-amber-500/30 flex flex-col items-center text-center transition-all group">
                                <i class="fa-solid fa-file-invoice text-base text-amber-400 mb-1 group-hover:scale-110 transition-transform"></i>
                                <span class="text-[11px] font-semibold text-slate-200">Create Invoice</span>
                            </button>
                            <!-- Customer -->
                            <button class="p-2.5 rounded-xl bg-sky-600/20 hover:bg-sky-600/30 border border-sky-500/30 flex flex-col items-center text-center transition-all group">
                                <i class="fa-solid fa-users text-base text-sky-400 mb-1 group-hover:scale-110 transition-transform"></i>
                                <span class="text-[11px] font-semibold text-slate-200">Customer</span>
                            </button>
                            <!-- Supplier -->
                            <button class="p-2.5 rounded-xl bg-pink-600/20 hover:bg-pink-600/30 border border-pink-500/30 flex flex-col items-center text-center transition-all group">
                                <i class="fa-solid fa-truck-moving text-base text-pink-400 mb-1 group-hover:scale-110 transition-transform"></i>
                                <span class="text-[11px] font-semibold text-slate-200">Supplier</span>
                            </button>
                            <!-- Payment -->
                            <button class="p-2.5 rounded-xl bg-teal-600/20 hover:bg-teal-600/30 border border-teal-500/30 flex flex-col items-center text-center transition-all group">
                                <i class="fa-solid fa-credit-card text-base text-teal-400 mb-1 group-hover:scale-110 transition-transform"></i>
                                <span class="text-[11px] font-semibold text-slate-200">Payment</span>
                            </button>
                            <!-- Stock Adjust -->
                            <button class="p-2.5 rounded-xl bg-cyan-600/20 hover:bg-cyan-600/30 border border-cyan-500/30 flex flex-col items-center text-center transition-all group">
                                <i class="fa-solid fa-sliders text-base text-cyan-400 mb-1 group-hover:scale-110 transition-transform"></i>
                                <span class="text-[11px] font-semibold text-slate-200">Stock Adjust</span>
                            </button>
                            <!-- Reports -->
                            <button class="p-2.5 rounded-xl bg-indigo-600/20 hover:bg-indigo-600/30 border border-indigo-500/30 flex flex-col items-center text-center transition-all group">
                                <i class="fa-solid fa-chart-column text-base text-indigo-400 mb-1 group-hover:scale-110 transition-transform"></i>
                                <span class="text-[11px] font-semibold text-slate-200">Reports</span>
                            </button>
                        </div>
                    </div>

                    <!-- Recent Activities List -->
                    <div class="glass-panel rounded-3xl p-5 border border-slate-700/60">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                                <i class="fa-solid fa-clock-rotate-left text-cyan-400"></i>
                                Recent Activities
                            </h3>
                            <a href="#" class="text-[11px] text-cyan-400 hover:underline">View All</a>
                        </div>

                        <div class="space-y-3">
                            
                            <!-- Act 1 -->
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-500/20 text-cyan-400 flex items-center justify-center text-xs shrink-0 mt-0.5">
                                    <i class="fa-solid fa-file-invoice-dollar"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-white font-medium truncate">New Sale Invoice <span class="text-cyan-300 font-mono">#S-2026-0012</span></p>
                                    <p class="text-[11px] text-slate-400">৳ 18,430 • <span class="text-slate-500">2 min ago</span></p>
                                </div>
                            </div>

                            <!-- Act 2 -->
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs shrink-0 mt-0.5">
                                    <i class="fa-solid fa-boxes-packing"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-white font-medium truncate">Purchase Received <span class="text-emerald-300 font-mono">#P-2026-0045</span></p>
                                    <p class="text-[11px] text-slate-400">৳ 56,200 • <span class="text-slate-500">12 min ago</span></p>
                                </div>
                            </div>

                            <!-- Act 3 -->
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-pink-500/20 text-pink-400 flex items-center justify-center text-xs shrink-0 mt-0.5">
                                    <i class="fa-solid fa-mobile-screen"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-white font-medium truncate">Payment Received (bKash)</p>
                                    <p class="text-[11px] text-slate-400">৳ 12,500 • <span class="text-slate-500">25 min ago</span></p>
                                </div>
                            </div>

                            <!-- Act 4 -->
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-sky-500/20 text-sky-400 flex items-center justify-center text-xs shrink-0 mt-0.5">
                                    <i class="fa-solid fa-user-plus"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-white font-medium truncate">New Customer Registered</p>
                                    <p class="text-[11px] text-slate-400">Rahim Traders • <span class="text-slate-500">32 min ago</span></p>
                                </div>
                            </div>

                            <!-- Act 5 -->
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center text-xs shrink-0 mt-0.5">
                                    <i class="fa-solid fa-arrows-rotate"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-white font-medium truncate">Stock Adjustment</p>
                                    <p class="text-[11px] text-slate-400">T-Shirt (Blue) • <span class="text-slate-500">45 min ago</span></p>
                                </div>
                            </div>

                            <!-- Act 6 -->
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-rose-500/20 text-rose-400 flex items-center justify-center text-xs shrink-0 mt-0.5">
                                    <i class="fa-solid fa-bell"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-white font-medium truncate">Due Payment Reminder</p>
                                    <p class="text-[11px] text-slate-400">Customer: M/S Fashion House • <span class="text-slate-500">1 hr ago</span></p>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

            <!-- ROW 4: TOP SELLING PRODUCTS, DUE SUMMARY & WEATHER WIDGET -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                
                <!-- Top Selling Products (4 Cols) -->
                <div class="md:col-span-4 glass-panel rounded-3xl p-5 border border-slate-700/60">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-crown text-amber-400"></i>
                            Top Selling Products (Today)
                        </h3>
                        <a href="#" class="text-[11px] text-cyan-400 hover:underline">View All</a>
                    </div>

                    <div class="space-y-3">
                        <!-- Item 1 -->
                        <div class="flex items-center justify-between p-2 rounded-xl bg-slate-800/30 border border-slate-800">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-lg bg-blue-500/10 text-cyan-400 flex items-center justify-center font-bold text-xs border border-blue-500/20">
                                    👕
                                </div>
                                <div>
                                    <h4 class="text-xs font-semibold text-white">Men's Cotton Inner</h4>
                                    <p class="text-[10px] text-slate-400">SKU: MCI-001</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-bold text-white">1,250 pcs</span>
                                <p class="text-[10px] text-emerald-400 font-semibold">↑ 15%</p>
                            </div>
                        </div>

                        <!-- Item 2 -->
                        <div class="flex items-center justify-between p-2 rounded-xl bg-slate-800/30 border border-slate-800">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-lg bg-pink-500/10 text-pink-400 flex items-center justify-center font-bold text-xs border border-pink-500/20">
                                    👚
                                </div>
                                <div>
                                    <h4 class="text-xs font-semibold text-white">Women's Bra</h4>
                                    <p class="text-[10px] text-slate-400">SKU: WB-002</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-bold text-white">980 pcs</span>
                                <p class="text-[10px] text-emerald-400 font-semibold">↑ 12%</p>
                            </div>
                        </div>

                        <!-- Item 3 -->
                        <div class="flex items-center justify-between p-2 rounded-xl bg-slate-800/30 border border-slate-800">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center font-bold text-xs border border-amber-500/20">
                                    👶
                                </div>
                                <div>
                                    <h4 class="text-xs font-semibold text-white">Kids Innerwear</h4>
                                    <p class="text-[10px] text-slate-400">SKU: KI-003</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-bold text-white">760 pcs</span>
                                <p class="text-[10px] text-emerald-400 font-semibold">↑ 10%</p>
                            </div>
                        </div>

                        <!-- Item 4 -->
                        <div class="flex items-center justify-between p-2 rounded-xl bg-slate-800/30 border border-slate-800">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-lg bg-purple-500/10 text-purple-400 flex items-center justify-center font-bold text-xs border border-purple-500/20">
                                    🧦
                                </div>
                                <div>
                                    <h4 class="text-xs font-semibold text-white">Socks (Pack)</h4>
                                    <p class="text-[10px] text-slate-400">SKU: SK-004</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-bold text-white">620 pcs</span>
                                <p class="text-[10px] text-emerald-400 font-semibold">↑ 8%</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Due Summary (4 Cols) -->
                <div class="md:col-span-4 glass-panel rounded-3xl p-5 border border-slate-700/60 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                                <i class="fa-solid fa-scale-balanced text-cyan-400"></i>
                                Due Summary
                            </h3>
                            <a href="#" class="text-[11px] text-cyan-400 hover:underline">View All</a>
                        </div>

                        <div class="grid grid-cols-3 gap-2.5 mb-4">
                            <!-- Customer Due -->
                            <div class="p-2.5 rounded-xl bg-purple-950/40 border border-purple-500/30 text-center">
                                <p class="text-[10px] text-purple-300 font-medium">Customer Due</p>
                                <span class="text-xs font-black text-white">৳ 95,240</span>
                                <p class="text-[9px] text-slate-400 mt-0.5">42 customers</p>
                            </div>

                            <!-- Supplier Due -->
                            <div class="p-2.5 rounded-xl bg-rose-950/40 border border-rose-500/30 text-center">
                                <p class="text-[10px] text-rose-300 font-medium">Supplier Due</p>
                                <span class="text-xs font-black text-white">৳ 68,750</span>
                                <p class="text-[9px] text-slate-400 mt-0.5">12 suppliers</p>
                            </div>

                            <!-- Advance -->
                            <div class="p-2.5 rounded-xl bg-indigo-950/40 border border-indigo-500/30 text-center">
                                <p class="text-[10px] text-indigo-300 font-medium">Advance</p>
                                <span class="text-xs font-black text-white">৳ 24,500</span>
                                <p class="text-[9px] text-slate-400 mt-0.5">8 customers</p>
                            </div>
                        </div>

                        <!-- Due Collection Progress -->
                        <div>
                            <div class="flex justify-between text-xs mb-1.5 font-medium">
                                <span class="text-slate-300">Due Collection Progress</span>
                                <span class="font-bold text-cyan-300">68%</span>
                            </div>
                            <div class="w-full bg-slate-800 rounded-full h-3 overflow-hidden p-0.5 border border-slate-700">
                                <div class="bg-gradient-to-r from-cyan-400 to-emerald-400 h-2 rounded-full shadow-lg shadow-cyan-400/50" style="width: 68%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-800/80 flex items-center justify-between text-[11px] text-slate-400">
                        <span>Recovery target this month</span>
                        <span class="text-emerald-400 font-bold">On Track</span>
                    </div>
                </div>

                <!-- Weather & Time Widget (4 Cols) -->
                <div class="md:col-span-4 glass-panel rounded-3xl p-5 border border-slate-700/60 flex flex-col justify-between relative overflow-hidden">
                    <!-- City Skyline Silhouette Background -->
                    <div class="absolute bottom-0 inset-x-0 h-20 bg-gradient-to-t from-cyan-950/40 to-transparent pointer-events-none opacity-40"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-amber-500/20 border border-amber-400/40 flex items-center justify-center text-amber-300 text-2xl shadow-lg shadow-amber-500/20">
                                    <i class="fa-solid fa-cloud-sun"></i>
                                </div>
                                <div>
                                    <h4 class="text-lg font-black text-white">28°C</h4>
                                    <p class="text-xs text-slate-300 font-semibold">Dhaka, Bangladesh</p>
                                    <p class="text-[10px] text-slate-400">Partly Cloudy</p>
                                </div>
                            </div>

                            <div class="text-right">
                                <span class="text-sm font-bold text-cyan-300 font-mono" id="weatherTime"></span>
                                <p class="text-[11px] text-slate-300" id="weatherDate"></p>
                                <p class="text-[10px] text-slate-400" id="weatherDay"></p>
                            </div>
                        </div>

                        <!-- Quote of the Day -->
                        <div class="mt-4 p-3 rounded-2xl bg-[#09132c]/80 border border-slate-800/80">
                            <p class="text-[11px] italic text-slate-300 leading-relaxed text-center">
                                "Success is not the key to happiness. Happiness is the key to success."
                            </p>
                        </div>
                    </div>

                    <div class="relative z-10 pt-2 flex items-center justify-between text-[10px] text-slate-400">
                        <span>Humidity: 64%</span>
                        <span>Air Quality: Good (42 AQI)</span>
                    </div>
                </div>

            </div>

        </main>

    </div>

    <!-- BOTTOM FOOTER STATUS BAR -->
    <footer class="glass-panel border-t border-slate-800 px-4 py-2 text-xs flex flex-wrap items-center justify-between gap-3 text-slate-400 select-none">
        
        <!-- Left Info -->
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-1.5 font-bold text-white">
                <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                <span>Turkey Bangla Garments Ltd</span>
            </div>
            <span class="text-slate-600">|</span>
            <span class="font-mono text-cyan-400 text-[11px]">ERP V6.0</span>
        </div>

        <!-- Center Status -->
        <div class="flex items-center gap-4 text-[11px]">
            <div class="flex items-center gap-1.5 text-emerald-400 font-semibold">
                <span class="w-2 h-2 rounded-full bg-emerald-400 pulse-online"></span>
                <span>System Online</span>
            </div>
            <span class="text-slate-600">•</span>
            <span>Last Sync: <span class="text-slate-200 font-mono" id="lastSyncTime"></span></span>
            <span class="text-slate-600">•</span>
            <span>Branch: <span class="text-cyan-300 font-medium">TNG-001 (Tongi Head Branch)</span></span>
        </div>

        <!-- Right Quick Actions -->
        <div class="flex items-center gap-3 text-[11px]">
            <button class="hover:text-cyan-400 flex items-center gap-1 transition-colors">
                <i class="fa-solid fa-database"></i> Backup
            </button>
            <button class="hover:text-cyan-400 flex items-center gap-1 transition-colors">
                <i class="fa-solid fa-arrows-rotate"></i> Sync
            </button>
            <button class="hover:text-cyan-400 flex items-center gap-1 transition-colors">
                <i class="fa-solid fa-circle-question"></i> Help
            </button>
            <button class="text-rose-400 hover:text-rose-300 flex items-center gap-1 transition-colors ml-2 font-medium">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
            </button>
        </div>

    </footer>

    <!-- INTERACTION LOGIC (MODE SWITCHER + CHARTS + LIVE CLOCK) -->
    <script>
        // ========== DATE/TIME HELPER FUNCTIONS ==========
        const MONTHS_SHORT = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        const DAYS_FULL = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

        function formatDate(d) {
            return d.getDate() + ' ' + MONTHS_SHORT[d.getMonth()] + ' ' + d.getFullYear();
        }
        function formatTime12(d) {
            let h = d.getHours(), m = d.getMinutes();
            const ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12 || 12;
            return h + ':' + String(m).padStart(2, '0') + ' ' + ampm;
        }
        function formatDateTime(d) {
            return formatDate(d) + ' | ' + formatTime12(d);
        }
        function formatShortDateTime(d) {
            return d.getDate() + ' ' + MONTHS_SHORT[d.getMonth()] + ' ' + formatTime12(d);
        }
        function getLast7DayLabels() {
            const labels = [];
            for (let i = 6; i >= 0; i--) {
                const d = new Date();
                d.setDate(d.getDate() - i);
                labels.push(d.getDate() + ' ' + MONTHS_SHORT[d.getMonth()]);
            }
            return labels;
        }

        // ========== LIVE CLOCK (updates every second) ==========
        function updateLiveClock() {
            const now = new Date();

            // Header date/time
            const headerDT = document.getElementById('liveDateTime');
            const headerDay = document.getElementById('liveDay');
            if (headerDT) headerDT.textContent = formatDateTime(now);
            if (headerDay) headerDay.textContent = DAYS_FULL[now.getDay()];

            // Weather widget date/time
            const wTime = document.getElementById('weatherTime');
            const wDate = document.getElementById('weatherDate');
            const wDay = document.getElementById('weatherDay');
            if (wTime) wTime.textContent = formatTime12(now);
            if (wDate) wDate.textContent = formatDate(now);
            if (wDay) wDay.textContent = DAYS_FULL[now.getDay()];

            // Footer last sync
            const lastSync = document.getElementById('lastSyncTime');
            if (lastSync) lastSync.textContent = formatDate(now) + ' ' + formatTime12(now);
        }

        // ========== POPULATE ORDER TABLE DATES ==========
        function populateOrderDates() {
            document.querySelectorAll('.live-date-today-time').forEach(el => {
                const offsetHours = parseInt(el.dataset.offsetHours || '0');
                const d = new Date();
                d.setHours(d.getHours() + offsetHours);
                el.textContent = formatShortDateTime(d);
            });
        }

        // ========== ERP MODE SWITCHER ==========
        function switchErpMode(mode) {
            const btnPackage = document.getElementById('btnPackageERP');
            const btnFactory = document.getElementById('btnFactoryERP');
            const packageMenu = document.getElementById('packageMenuContainer');
            const factoryMenu = document.getElementById('factoryMenuContainer');
            const modeLabel = document.getElementById('currentModeLabel');
            const modeIcon = document.getElementById('currentModeIcon');

            if (mode === 'package') {
                btnPackage.classList.add('active');
                btnFactory.classList.remove('active');
                packageMenu.classList.remove('hidden');
                factoryMenu.classList.add('hidden');
                modeLabel.innerText = 'Package ERP Mode';
                modeLabel.className = 'text-[11px] font-bold text-cyan-300 uppercase tracking-wider';
                modeIcon.className = 'fa-solid fa-box-open text-xs text-cyan-400';
                localStorage.setItem('tbg_erp_mode', 'package');
            } else {
                btnFactory.classList.add('active');
                btnPackage.classList.remove('active');
                factoryMenu.classList.remove('hidden');
                packageMenu.classList.add('hidden');
                modeLabel.innerText = 'Factory ERP Mode';
                modeLabel.className = 'text-[11px] font-bold text-blue-300 uppercase tracking-wider';
                modeIcon.className = 'fa-solid fa-industry text-xs text-blue-400';
                localStorage.setItem('tbg_erp_mode', 'factory');
            }
        }

        // ========== INIT ON DOM READY ==========
        document.addEventListener('DOMContentLoaded', () => {
            // Restore ERP mode
            const savedMode = localStorage.getItem('tbg_erp_mode') || 'factory';
            switchErpMode(savedMode);

            // Start live clock
            updateLiveClock();
            setInterval(updateLiveClock, 1000);

            // Set order dates
            populateOrderDates();

            // Initialize Charts
            initSalesOverviewChart();
            initCategoryDonutChart();
        });

        // ========== 1. Sales Overview Area Chart ==========
        function initSalesOverviewChart() {
            const ctx = document.getElementById('salesOverviewChart').getContext('2d');
            
            const gradient = ctx.createLinearGradient(0, 0, 0, 220);
            gradient.addColorStop(0, 'rgba(14, 165, 233, 0.45)');
            gradient.addColorStop(1, 'rgba(14, 165, 233, 0.01)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: getLast7DayLabels(),
                    datasets: [{
                        label: 'Sales (৳)',
                        data: [120000, 190000, 165000, 230000, 210000, 280000, 245680],
                        borderColor: '#00f2fe',
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#00f2fe',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.4,
                        fill: true,
                        backgroundColor: gradient
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(10, 18, 42, 0.9)',
                            titleColor: '#00f2fe',
                            bodyColor: '#ffffff',
                            borderColor: 'rgba(56, 189, 248, 0.3)',
                            borderWidth: 1,
                            padding: 10,
                            callbacks: {
                                label: function(context) {
                                    return ' ৳ ' + context.parsed.y.toLocaleString('en-IN');
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: 'rgba(255, 255, 255, 0.05)' },
                            ticks: { color: '#94a3b8', font: { size: 10 } }
                        },
                        y: {
                            grid: { color: 'rgba(255, 255, 255, 0.05)' },
                            ticks: {
                                color: '#94a3b8',
                                font: { size: 10 },
                                callback: function(value) {
                                    return '৳ ' + (value / 1000) + 'k';
                                }
                            }
                        }
                    }
                }
            });
        }

        // ========== 2. Category Donut Chart ==========
        function initCategoryDonutChart() {
            const ctx = document.getElementById('categoryDonutChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ["Men's Innerwear", "Women's Innerwear", "Socks", "T-Shirt", "Others"],
                    datasets: [{
                        data: [32, 24, 15, 12, 17],
                        backgroundColor: [
                            '#38bdf8',
                            '#c084fc',
                            '#3b82f6',
                            '#34d399',
                            '#f472b6'
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(10, 18, 42, 0.9)',
                            borderColor: 'rgba(255, 255, 255, 0.1)',
                            borderWidth: 1,
                            callbacks: {
                                label: function(context) {
                                    return ' ' + context.label + ': ' + context.parsed + '%';
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>

