@extends('layouts.app')

@section('title', 'TBG - Turkey Bangla Garments Ltd | ERP Dashboard V6.0')

@section('content')

    <!-- TOP BANNER & 4 KPI METRIC CARDS -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-5">
        
        <!-- Welcome Banner (Factory Showcase) -->
        <div class="xl:col-span-12 relative overflow-hidden rounded-3xl glass-panel border border-slate-700/60 shadow-2xl" style="min-height: 220px;">
            
            <!-- Panoramic Futuristic City/Factory Background -->
            <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1800&auto=format&fit=crop&q=85" 
                 alt="City Skyline" class="absolute inset-0 w-full h-full object-cover object-center opacity-50">
            
            <!-- Gradient overlays for depth -->
            <div class="absolute inset-0 bg-gradient-to-r from-[#060b1d]/95 via-[#070e24]/80 to-[#0a1535]/50 z-[1]"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#060b1d]/90 via-transparent to-transparent z-[2]"></div>

            <!-- Neon glow accents -->
            <div class="absolute top-0 left-1/4 w-72 h-40 bg-cyan-500/10 rounded-full blur-3xl z-[3] pointer-events-none"></div>
            <div class="absolute bottom-0 right-1/4 w-72 h-40 bg-indigo-500/10 rounded-full blur-3xl z-[3] pointer-events-none"></div>
            
            <!-- Main Content Layer -->
            <div class="relative z-10 p-6">
                
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <!-- Left: Welcome Text -->
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

                    <!-- Right: TBG Logo Circle + Calligraphy Motto -->
                    <div class="hidden lg:flex items-center gap-5 pr-4">
                        <div class="relative flex-shrink-0">
                            <div class="absolute -inset-3 rounded-full bg-gradient-to-tr from-cyan-500/25 via-blue-500/20 to-indigo-500/25 blur-lg animate-pulse"></div>
                            
                            <div class="relative w-20 h-20 rounded-full bg-gradient-to-tr from-cyan-600 via-blue-600 to-indigo-700 p-[2.5px] shadow-xl shadow-cyan-500/30 ring-2 ring-cyan-400/20 ring-offset-2 ring-offset-[#0a1535]">
                                <div class="w-full h-full rounded-full bg-[#0a1128] flex items-center justify-center">
                                    <span class="text-lg font-black tracking-widest text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-blue-300 to-indigo-400">TBG</span>
                                </div>
                            </div>

                            <div class="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-cyan-400 shadow-lg shadow-cyan-400/60"></div>
                            <div class="absolute -bottom-1 -left-1 w-1.5 h-1.5 rounded-full bg-indigo-400 shadow-lg shadow-indigo-400/60"></div>
                        </div>

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

                <!-- 4 GLOWING KPI CARDS (SEMI-TRANSPARENT 3D GLASS) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6 relative z-10">
                    
                    <!-- 1. Total Sales (Today) -->
                    <div class="kpi-exact-card kpi-exact-blue">
                        <div class="relative z-10 flex-shrink-0">
                            <div class="w-14 h-14 rounded-full kpi-orb-blue flex items-center justify-center text-white text-xl shadow-lg">
                                <i class="fa-solid fa-cart-shopping drop-shadow-md"></i>
                            </div>
                        </div>
                        <div class="relative z-10 text-right flex-1 pl-3">
                            <p class="text-[11px] font-semibold text-cyan-100 tracking-wide">Total Sales (Today)</p>
                            <h3 class="text-2xl font-black text-white tracking-tight leading-tight my-0.5">৳ 2,45,680</h3>
                            <div class="flex items-center justify-end gap-1.5 text-[11px] font-bold text-emerald-300">
                                <span>↑ 12%</span>
                                <span class="inline-flex items-end gap-0.5 h-3 ml-1">
                                    <span class="w-1 h-1.5 bg-emerald-300 rounded-sm"></span>
                                    <span class="w-1 h-2.5 bg-emerald-300 rounded-sm"></span>
                                    <span class="w-1 h-3.5 bg-emerald-300 rounded-sm"></span>
                                </span>
                            </div>
                        </div>
                        <span class="absolute bottom-1.5 left-2 text-[9px] text-white/30 font-mono select-none">◣</span>
                    </div>

                    <!-- 2. Total Purchase (Today) -->
                    <div class="kpi-exact-card kpi-exact-green">
                        <div class="relative z-10 flex-shrink-0">
                            <div class="w-14 h-14 rounded-full kpi-orb-green flex items-center justify-center text-white text-xl shadow-lg">
                                <i class="fa-solid fa-cart-plus drop-shadow-md"></i>
                            </div>
                        </div>
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

                    <!-- 3. Total Due (Customer) -->
                    <div class="kpi-exact-card kpi-exact-purple">
                        <div class="relative z-10 flex-shrink-0">
                            <div class="w-14 h-14 rounded-full kpi-orb-purple flex items-center justify-center text-white text-xl shadow-lg">
                                <i class="fa-solid fa-user drop-shadow-md"></i>
                            </div>
                        </div>
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

                    <!-- 4. Total Stock Value -->
                    <div class="kpi-exact-card kpi-exact-amber">
                        <div class="relative z-10 flex-shrink-0">
                            <div class="w-14 h-14 rounded-full kpi-orb-amber flex items-center justify-center text-white text-xl shadow-lg">
                                <i class="fa-solid fa-box-open drop-shadow-md"></i>
                            </div>
                        </div>
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

                </div>
            </div>
        </div>

    </div>

    <!-- ROW 2: CHARTS & STOCK STATUS -->
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
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none text-center">
                        <span class="text-[9px] uppercase font-bold text-slate-400">Total</span>
                        <span class="text-xs font-black text-cyan-300">৳ 2,45,680</span>
                        <span class="text-[9px] text-slate-400">(Today)</span>
                    </div>
                </div>

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

                <div class="p-3.5 rounded-2xl bg-gradient-to-r from-blue-900/30 to-indigo-900/20 border border-blue-500/20 flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-400/30 flex items-center justify-center text-amber-300 text-lg">
                        <i class="fa-solid fa-box"></i>
                    </div>
                    <div>
                        <span class="text-lg font-black text-white">12,560</span>
                        <p class="text-[10px] text-slate-400">Total Stock Items in Warehouse</p>
                    </div>
                </div>

                <div class="space-y-3">
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

    <!-- ROW 3: FACTORY ERP (LEFT) & PACKAGE ERP + RECENT ACTIVITIES (RIGHT) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        
        <!-- Left: Factory ERP Grid (8 Cols) - Modular Component -->
        <div class="lg:col-span-8 space-y-5">
            
            @include('factory-erp.modules-grid')

            <!-- Recent Orders Table -->
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

        <!-- Right: Package ERP Grid (4 Cols) - Modular Component -->
        <div class="lg:col-span-4 space-y-5">
            
            @include('package-erp.modules-grid')

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
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/20 text-cyan-400 flex items-center justify-center text-xs shrink-0 mt-0.5">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-white font-medium truncate">New Sale Invoice <span class="text-cyan-300 font-mono">#S-2026-0012</span></p>
                            <p class="text-[11px] text-slate-400">৳ 18,430 • <span class="text-slate-500">2 min ago</span></p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs shrink-0 mt-0.5">
                            <i class="fa-solid fa-boxes-packing"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-white font-medium truncate">Purchase Received <span class="text-emerald-300 font-mono">#P-2026-0045</span></p>
                            <p class="text-[11px] text-slate-400">৳ 56,200 • <span class="text-slate-500">12 min ago</span></p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-pink-500/20 text-pink-400 flex items-center justify-center text-xs shrink-0 mt-0.5">
                            <i class="fa-solid fa-mobile-screen"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-white font-medium truncate">Payment Received (bKash)</p>
                            <p class="text-[11px] text-slate-400">৳ 12,500 • <span class="text-slate-500">25 min ago</span></p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-sky-500/20 text-sky-400 flex items-center justify-center text-xs shrink-0 mt-0.5">
                            <i class="fa-solid fa-user-plus"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-white font-medium truncate">New Customer Registered</p>
                            <p class="text-[11px] text-slate-400">Rahim Traders • <span class="text-slate-500">32 min ago</span></p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center text-xs shrink-0 mt-0.5">
                            <i class="fa-solid fa-arrows-rotate"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-white font-medium truncate">Stock Adjustment</p>
                            <p class="text-[11px] text-slate-400">T-Shirt (Blue) • <span class="text-slate-500">45 min ago</span></p>
                        </div>
                    </div>

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
                    <div class="p-2.5 rounded-xl bg-purple-950/40 border border-purple-500/30 text-center">
                        <p class="text-[10px] text-purple-300 font-medium">Customer Due</p>
                        <span class="text-xs font-black text-white">৳ 95,240</span>
                        <p class="text-[9px] text-slate-400 mt-0.5">42 customers</p>
                    </div>

                    <div class="p-2.5 rounded-xl bg-rose-950/40 border border-rose-500/30 text-center">
                        <p class="text-[10px] text-rose-300 font-medium">Supplier Due</p>
                        <span class="text-xs font-black text-white">৳ 68,750</span>
                        <p class="text-[9px] text-slate-400 mt-0.5">12 suppliers</p>
                    </div>

                    <div class="p-2.5 rounded-xl bg-indigo-950/40 border border-indigo-500/30 text-center">
                        <p class="text-[10px] text-indigo-300 font-medium">Advance</p>
                        <span class="text-xs font-black text-white">৳ 24,500</span>
                        <p class="text-[9px] text-slate-400 mt-0.5">8 customers</p>
                    </div>
                </div>

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

@endsection
