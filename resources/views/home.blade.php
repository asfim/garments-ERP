<!DOCTYPE html>
<html lang="en" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $company->company_name ?? 'Turkey Bangla Garments Ltd' }} | Next-Gen Industrial ERP</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Noto+Sans+Bengali:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <!-- Tailwind CSS CDN -->
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
                            dark: '#050a1b',
                            card: '#0a122e',
                            neonBlue: '#00f2fe',
                            neonCyan: '#06b6d4',
                            neonPurple: '#a855f7',
                            neonGreen: '#10b981',
                        }
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        pulseGlow: {
                            '0%, 100%': { opacity: '0.4' },
                            '50%': { opacity: '0.8' },
                        }
                    },
                    animation: {
                        float: 'float 5s ease-in-out infinite',
                        pulseGlow: 'pulseGlow 4s ease-in-out infinite',
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #050a1b;
            background-image: 
                radial-gradient(at 0% 0%, rgba(6, 182, 212, 0.12) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(168, 85, 247, 0.12) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(16, 185, 129, 0.06) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(59, 130, 246, 0.10) 0px, transparent 50%);
            background-attachment: fixed;
        }

        .glass-header {
            background: rgba(7, 13, 33, 0.82);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .glass-card {
            background: linear-gradient(135deg, rgba(13, 22, 48, 0.75), rgba(7, 13, 33, 0.85));
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5);
        }

        .glass-card:hover {
            border-color: rgba(6, 182, 212, 0.4);
            transform: translateY(-3px);
            box-shadow: 0 25px 50px -12px rgba(6, 182, 212, 0.2);
        }

        /* Glowing Login Button */
        .btn-glow-cyan {
            background: linear-gradient(135deg, #06b6d4, #2563eb);
            box-shadow: 0 0 20px rgba(6, 182, 212, 0.45);
            transition: all 0.3s ease;
        }
        .btn-glow-cyan:hover {
            box-shadow: 0 0 35px rgba(6, 182, 212, 0.75);
            transform: translateY(-2px) scale(1.02);
        }

        .btn-glow-emerald {
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.4);
            transition: all 0.3s ease;
        }
        .btn-glow-emerald:hover {
            box-shadow: 0 0 35px rgba(16, 185, 129, 0.7);
            transform: translateY(-2px) scale(1.02);
        }
    </style>
</head>
<body class="text-slate-200 min-h-screen flex flex-col justify-between selection:bg-cyan-500 selection:text-black">

    <!-- Ambient Background Glow Spheres -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-40 left-1/4 w-96 h-96 bg-cyan-500/15 rounded-full blur-3xl animate-pulseGlow"></div>
        <div class="absolute top-1/3 -right-20 w-80 h-80 bg-purple-500/15 rounded-full blur-3xl animate-pulseGlow" style="animation-delay: 2s;"></div>
        <div class="absolute -bottom-20 left-1/3 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl animate-pulseGlow" style="animation-delay: 1s;"></div>
    </div>

    <!-- ========================================================= -->
    <!-- HEADER (LOGO + NAVIGATION + MODERN LOGIN BUTTON)          -->
    <!-- ========================================================= -->
    <header class="sticky top-0 z-50 glass-header transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
            
            <!-- Company Logo & Brand -->
            <a href="{{ route('home') }}" class="flex items-center gap-3.5 group">
                <div class="relative w-11 h-11 rounded-2xl bg-gradient-to-tr from-cyan-500 via-blue-600 to-indigo-600 p-[2px] shadow-lg shadow-cyan-500/25 group-hover:shadow-cyan-400/40 transition-all">
                    <div class="w-full h-full bg-[#070d22] rounded-[14px] flex items-center justify-center overflow-hidden">
                        @if(!empty($company->logo) && file_exists(public_path('storage/' . $company->logo)))
                            <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo" class="w-full h-full object-contain p-1">
                        @else
                            <i class="fa-solid fa-industry text-lg text-cyan-400 group-hover:scale-110 transition-transform"></i>
                        @endif
                    </div>
                </div>
                <div>
                    <h1 class="text-base sm:text-lg font-black text-white tracking-tight flex items-center gap-2">
                        <span>{{ $company->company_name ?? 'Turkey Bangla Garments Ltd' }}</span>
                        <span class="text-[9px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">
                            ERP V6.0
                        </span>
                    </h1>
                    <p class="text-[11px] text-slate-400 font-medium">Garments Manufacturing & Enterprise Suite</p>
                </div>
            </a>

            <!-- Navigation Links (Desktop) -->
            <nav class="hidden md:flex items-center gap-7 text-xs font-semibold text-slate-300">
                <a href="#features" class="hover:text-cyan-400 transition-colors">Core Features</a>
                <a href="#modules" class="hover:text-cyan-400 transition-colors">ERP Modules</a>
                <a href="#portal" class="hover:text-cyan-400 transition-colors">Employee Portal</a>
                <a href="#contact" class="hover:text-cyan-400 transition-colors">Factory Info</a>
            </nav>

            <!-- Right Action: User Status or Login Button -->
            <div class="flex items-center gap-3">
                @auth
                    <!-- If User is already logged in -->
                    <div class="flex items-center gap-2.5">
                        <div class="text-right hidden sm:block">
                            <span class="text-xs font-bold text-white block">{{ auth()->user()->name }}</span>
                            <span class="text-[10px] text-cyan-400 font-mono uppercase">{{ auth()->user()->roles->pluck('name')->first() ?? (auth()->user()->isEmployee() ? 'Employee' : 'Admin') }}</span>
                        </div>

                        @if(auth()->user()->isEmployee())
                            <a href="{{ route('employee.dashboard') }}" class="btn-glow-emerald px-4 py-2 rounded-xl text-xs font-bold text-white flex items-center gap-2">
                                <i class="fa-solid fa-id-badge"></i>
                                <span>My Portal</span>
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="btn-glow-cyan px-4 py-2 rounded-xl text-xs font-bold text-white flex items-center gap-2">
                                <i class="fa-solid fa-chart-line"></i>
                                <span>ERP Dashboard</span>
                            </a>
                        @endif

                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" title="Logout" class="p-2 rounded-xl bg-slate-800 hover:bg-rose-500/20 hover:text-rose-400 text-slate-400 border border-slate-700 transition-all text-xs">
                                <i class="fa-solid fa-right-from-bracket"></i>
                            </button>
                        </form>
                    </div>
                @else
                    <!-- Guest: High-Converting Sleek Login Button -->
                    <a href="{{ route('login') }}" 
                       class="btn-glow-cyan px-5 py-2.5 rounded-xl text-xs font-bold text-white flex items-center gap-2.5 group">
                        <span class="w-5 h-5 rounded-lg bg-white/20 flex items-center justify-center text-[10px] group-hover:rotate-12 transition-transform">
                            <i class="fa-solid fa-arrow-right-to-bracket"></i>
                        </span>
                        <span>Sign In / লগইন করুন</span>
                    </a>
                @endauth
            </div>

        </div>
    </header>

    <!-- ========================================================= -->
    <!-- HERO SECTION                                              -->
    <!-- ========================================================= -->
    <main class="flex-1">
        <section class="relative pt-12 pb-20 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-3xl mx-auto space-y-6">
                    
                    <!-- Notification Pill Badge -->
                    <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-cyan-500/10 border border-cyan-500/30 text-cyan-300 text-xs font-bold shadow-lg shadow-cyan-500/10">
                        <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
                        <span>Smart Industrial Automation & Biometric HR</span>
                        <i class="fa-solid fa-sparkles text-cyan-400 text-[10px]"></i>
                    </div>

                    <!-- Bold Gradient Headline -->
                    <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-white tracking-tight leading-tight">
                        Precision Factory Control <br class="hidden sm:inline">
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-blue-400 to-purple-400">
                            From Cutting to Delivery
                        </span>
                    </h1>

                    <!-- Bengali & English Subheading -->
                    <p class="text-sm sm:text-base text-slate-300 leading-relaxed font-medium">
                        গার্মেন্টস ও টেক্সটাইল ফ্যাক্টরির অর্ডার ম্যানেজমেন্ট, লাইভ প্রোডাকশন ট্র্যাকিং, বায়োমেট্রিক হাজিরা, স্বয়ংক্রিয় পে-রোল এবং নির্ভুল ফাইন্যান্সিয়াল হিসাবনিকাশের একটি আধুনিক এন্টারপ্রাইজ সিস্টেম।
                    </p>

                    <!-- Call To Action Buttons (Dual Portals) -->
                    <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                        
                        <!-- Admin / Management Portal Button -->
                        <a href="{{ route('login') }}" 
                           class="btn-glow-cyan w-full sm:w-auto px-7 py-3.5 rounded-2xl text-xs sm:text-sm font-extrabold text-white flex items-center justify-center gap-3">
                            <i class="fa-solid fa-industry text-base"></i>
                            <span>Management & Admin Portal</span>
                            <i class="fa-solid fa-chevron-right text-xs opacity-75"></i>
                        </a>

                        <!-- Employee Self-Service Button -->
                        <a href="{{ route('login') }}?portal=employee" 
                           class="btn-glow-emerald w-full sm:w-auto px-7 py-3.5 rounded-2xl text-xs sm:text-sm font-extrabold text-white flex items-center justify-center gap-3">
                            <i class="fa-solid fa-id-badge text-base"></i>
                            <span>Worker & Employee Login</span>
                            <i class="fa-solid fa-chevron-right text-xs opacity-75"></i>
                        </a>

                    </div>

                    <!-- Quick Credentials Hint / Demo Pills -->
                    <div class="pt-2 flex flex-wrap items-center justify-center gap-3 text-[11px] text-slate-400">
                        <span class="flex items-center gap-1.5 bg-[#0a1330] px-3 py-1 rounded-xl border border-slate-800">
                            <i class="fa-solid fa-shield-halved text-cyan-400"></i>
                            <strong>Admin:</strong> admin@turkeybangla.com / admin123
                        </span>
                        <span class="flex items-center gap-1.5 bg-[#0a1330] px-3 py-1 rounded-xl border border-slate-800">
                            <i class="fa-solid fa-user-check text-emerald-400"></i>
                            <strong>Employee:</strong> TBG-EMP-001 / password
                        </span>
                    </div>

                </div>

                <!-- Live Metrics Counter Strip -->
                <div class="mt-14 grid grid-cols-2 md:grid-cols-4 gap-4 max-w-5xl mx-auto">
                    
                    <div class="glass-card rounded-2xl p-5 text-center">
                        <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center mx-auto mb-3">
                            <i class="fa-solid fa-users text-lg"></i>
                        </div>
                        <div class="text-2xl font-black text-white font-mono">{{ $stats['totalEmployees'] ?? 0 }}</div>
                        <div class="text-[11px] text-slate-400 font-semibold mt-0.5">Active Workforce (সক্রিয় কর্মী)</div>
                    </div>

                    <div class="glass-card rounded-2xl p-5 text-center">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center mx-auto mb-3">
                            <i class="fa-solid fa-fingerprint text-lg"></i>
                        </div>
                        <div class="text-2xl font-black text-emerald-400 font-mono">{{ $stats['presentToday'] ?? 0 }}</div>
                        <div class="text-[11px] text-slate-400 font-semibold mt-0.5">Present Today (আজকের উপস্থিতি)</div>
                    </div>

                    <div class="glass-card rounded-2xl p-5 text-center">
                        <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center mx-auto mb-3">
                            <i class="fa-solid fa-shirt text-lg"></i>
                        </div>
                        <div class="text-2xl font-black text-purple-300 font-mono">{{ number_format($stats['totalProduction'] ?? 0) }}</div>
                        <div class="text-[11px] text-slate-400 font-semibold mt-0.5">Produced Qty (মোট উৎপাদন)</div>
                    </div>

                    <div class="glass-card rounded-2xl p-5 text-center">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center mx-auto mb-3">
                            <i class="fa-solid fa-cart-shopping text-lg"></i>
                        </div>
                        <div class="text-2xl font-black text-amber-300 font-mono">{{ $stats['totalOrders'] ?? 0 }}</div>
                        <div class="text-[11px] text-slate-400 font-semibold mt-0.5">Total Orders (গৃহীত কার্যাদেশ)</div>
                    </div>

                </div>

            </div>
        </section>

        <!-- ========================================================= -->
        <!-- CORE CAPABILITIES / MODULES SECTION                       -->
        <!-- ========================================================= -->
        <section id="modules" class="py-16 border-t border-slate-800/80 bg-[#040817]/60">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <h2 class="text-xs font-bold uppercase tracking-widest text-cyan-400 mb-2">Integrated Architecture</h2>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-white">Full-Spectrum Garments Operation Modules</h3>
                    <p class="text-xs text-slate-400 mt-2">ফ্যাক্টরি ম্যানেজমেন্টের প্রতিটি ধাপ স্বয়ংক্রিয় ও সেন্ট্রালি নিয়ন্ত্রণ করুন।</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <!-- 1. Orders & Invoicing -->
                    <div class="glass-card rounded-2xl p-6 transition-all">
                        <div class="w-12 h-12 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center mb-4">
                            <i class="fa-solid fa-file-invoice-dollar text-xl"></i>
                        </div>
                        <h4 class="text-base font-bold text-white mb-2">Order & Invoice Generation</h4>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            বায়ারের ওয়ার্ক অর্ডার তৈরি, স্টাইল ও সাইজ-ওয়াইজ আইটেম এন্ট্রি, অটোমেটিক ইনভয়েস, পেমেন্ট ও বাকি ট্র্যাকিং।
                        </p>
                    </div>

                    <!-- 2. Production & Deliveries -->
                    <div class="glass-card rounded-2xl p-6 transition-all">
                        <div class="w-12 h-12 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center mb-4">
                            <i class="fa-solid fa-industry text-xl"></i>
                        </div>
                        <h4 class="text-base font-bold text-white mb-2">Production & Delivery Challan</h4>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            দৈনিক কাটিং ও সেলাই আউটপুট ট্র্যাকিং, অটোমেটিক ফিনিশড স্টক আপডেট এবং বারকোড সহ সিকিউর গেটপাস চালান।
                        </p>
                    </div>

                    <!-- 3. Biometric HR & Payroll -->
                    <div class="glass-card rounded-2xl p-6 transition-all">
                        <div class="w-12 h-12 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center mb-4">
                            <i class="fa-solid fa-fingerprint text-xl"></i>
                        </div>
                        <h4 class="text-base font-bold text-white mb-2">Biometric HR & Smart Payroll</h4>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            ZKTeco লাইভ ফিঙ্গারপ্রিন্ট সিঙ্ক, হাজিরা, ওভারটাইম হিসাব, অগ্রিম লোন সমন্বয় এবং এক ক্লিকে পে-স্লিপ প্রিন্ট।
                        </p>
                    </div>

                    <!-- 4. Double-Entry Accounts -->
                    <div class="glass-card rounded-2xl p-6 transition-all">
                        <div class="w-12 h-12 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center mb-4">
                            <i class="fa-solid fa-calculator text-xl"></i>
                        </div>
                        <h4 class="text-base font-bold text-white mb-2">Double-Entry Financial Accounts</h4>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            জেনারেল লেজার (খতিয়ান), ট্রায়াল ব্যালেন্স, ক্যাশ ও ব্যাংক বুক, আয়-ব্যয় ভাউচার এবং প্রফিট & লস অ্যানালাইসিস।
                        </p>
                    </div>

                    <!-- 5. Granular Role & Permissions -->
                    <div class="glass-card rounded-2xl p-6 transition-all">
                        <div class="w-12 h-12 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center mb-4">
                            <i class="fa-solid fa-shield-halved text-xl"></i>
                        </div>
                        <h4 class="text-base font-bold text-white mb-2">Dynamic Roles & Access Control</h4>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            মডিউলভিত্তিক সুনির্দিষ্ট পারমিশন (View, Add, Edit, Delete)। অনুমতিহীন মেনু ও বাটন স্বয়ংক্রিয়ভাবে হাইড হয়ে যায়।
                        </p>
                    </div>

                    <!-- 6. Dedicated Employee Portal -->
                    <div class="glass-card rounded-2xl p-6 transition-all border-emerald-500/30">
                        <div class="w-12 h-12 rounded-xl bg-teal-500/20 text-teal-400 flex items-center justify-center mb-4">
                            <i class="fa-solid fa-id-card-clip text-xl"></i>
                        </div>
                        <h4 class="text-base font-bold text-white mb-2">Dedicated Employee Portal</h4>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            প্রত্যেক কর্মীর নিজস্ব লগইন, মাসিক বেতনের স্টেটমেন্ট, অগ্রিম লোনের স্ট্যাটাস, দৈনিক হাজিরা এবং ওভারটাইম হিস্ট্রি।
                        </p>
                    </div>

                </div>

            </div>
        </section>

        <!-- ========================================================= -->
        <!-- EMPLOYEE PORTAL SPOTLIGHT                                 -->
        <!-- ========================================================= -->
        <section id="portal" class="py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="glass-card rounded-3xl p-8 sm:p-12 relative overflow-hidden border-cyan-500/30">
                    <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                        <div class="space-y-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">
                                Worker Self-Service Portal
                            </span>
                            <h3 class="text-2xl sm:text-4xl font-extrabold text-white">
                                প্রত্যেক কর্মচারীর জন্য নিজস্ব ডিজিটাল ড্যাশবোর্ড
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                                নতুন কর্মচারী যুক্ত করার সাথে সাথে তাদের নামে স্বয়ংক্রিয় ইমেইল ও লগইন ক্রেডেনশিয়াল তৈরি হয়। কর্মীরা যেকোনো ডিভাইস থেকে লগইন করে তাদের যাবতীয় তথ্য সরাসরি দেখতে পারেন:
                            </p>
                            <ul class="space-y-2.5 text-xs text-slate-300 font-medium">
                                <li class="flex items-center gap-2.5">
                                    <i class="fa-solid fa-circle-check text-emerald-400"></i>
                                    <span>মাসিক বেতনের পূর্ণাঙ্গ বিবরণ ও ডিজিটাল পে-স্লিপ ডাউনলোড</span>
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <i class="fa-solid fa-circle-check text-emerald-400"></i>
                                    <span>গৃহীত অগ্রিম বেতন / লোনের পরিমাণ ও পরিশোধের হিসাব</span>
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <i class="fa-solid fa-circle-check text-emerald-400"></i>
                                    <span>দৈনিক বায়োমেট্রিক হাজিরা, ইন/আউট টাইম ও ওভারটাইম (OT) রিপোর্ট</span>
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <i class="fa-solid fa-circle-check text-emerald-400"></i>
                                    <span>ছুটির আবেদন ও অনুমোদনের বর্তমান অবস্থা পর্যবেক্ষণ</span>
                                </li>
                            </ul>

                            <div class="pt-2">
                                <a href="{{ route('login') }}?portal=employee" class="btn-glow-emerald px-6 py-3 rounded-xl text-xs font-bold text-white inline-flex items-center gap-2">
                                    <i class="fa-solid fa-user-lock"></i>
                                    <span>কর্মচারী পোর্টালে লগইন করুন</span>
                                </a>
                            </div>
                        </div>

                        <!-- Mockup Card Visual -->
                        <div class="p-5 rounded-2xl bg-[#060c20] border border-cyan-500/20 shadow-2xl space-y-4">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center font-bold text-sm">
                                        TBG
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-white">Employee Self-Service</div>
                                        <div class="text-[10px] text-cyan-400 font-mono">TBG-EMP-001 (Cutting Master)</div>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    Active Verified
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-3 text-xs">
                                <div class="p-3 rounded-xl bg-slate-900/80 border border-slate-800">
                                    <div class="text-[10px] text-slate-400">Current Salary</div>
                                    <div class="text-sm font-bold text-white font-mono mt-1">৳ 24,500.00</div>
                                </div>
                                <div class="p-3 rounded-xl bg-slate-900/80 border border-slate-800">
                                    <div class="text-[10px] text-slate-400">Overtime Worked</div>
                                    <div class="text-sm font-bold text-cyan-300 font-mono mt-1">18.5 Hours</div>
                                </div>
                                <div class="p-3 rounded-xl bg-slate-900/80 border border-slate-800">
                                    <div class="text-[10px] text-slate-400">Advance Balance</div>
                                    <div class="text-sm font-bold text-amber-300 font-mono mt-1">৳ 2,000.00</div>
                                </div>
                                <div class="p-3 rounded-xl bg-slate-900/80 border border-slate-800">
                                    <div class="text-[10px] text-slate-400">Attendance This Month</div>
                                    <div class="text-sm font-bold text-emerald-300 font-mono mt-1">26 / 26 Days</div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </section>

    </main>

    <!-- ========================================================= -->
    <!-- FOOTER                                                    -->
    <!-- ========================================================= -->
    <footer id="contact" class="border-t border-slate-800/80 bg-[#040817] py-8 text-xs text-slate-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            
            <div class="flex items-center gap-3">
                <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                <span>&copy; {{ date('Y') }} <strong>{{ $company->company_name ?? 'Turkey Bangla Garments Ltd' }}</strong>. All rights reserved.</span>
            </div>

            <div class="flex items-center gap-6">
                <span><i class="fa-solid fa-location-dot text-cyan-400 mr-1.5"></i>{{ $company->address ?? 'Tongi, Gazipur, Dhaka' }}</span>
                <span><i class="fa-solid fa-phone text-emerald-400 mr-1.5"></i>{{ $company->phone ?? '+880 1712-345678' }}</span>
            </div>

        </div>
    </footer>

</body>
</html>

