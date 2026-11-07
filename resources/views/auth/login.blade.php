<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | {{ $company->company_name ?? 'Turkey Bangla Garments Ltd' }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Noto+Sans+Bengali:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', '"Noto Sans Bengali"', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #050a1b;
            background-image: 
                radial-gradient(at 0% 0%, rgba(6, 182, 212, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(168, 85, 247, 0.15) 0px, transparent 50%);
            background-attachment: fixed;
        }

        .glass-box {
            background: linear-gradient(135deg, rgba(13, 22, 48, 0.88), rgba(6, 12, 30, 0.95));
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.7);
        }

        .btn-glow {
            background: linear-gradient(135deg, #06b6d4, #2563eb);
            box-shadow: 0 0 25px rgba(6, 182, 212, 0.45);
            transition: all 0.3s ease;
        }
        .btn-glow:hover {
            box-shadow: 0 0 35px rgba(6, 182, 212, 0.75);
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 select-none">

    <div class="w-full max-w-md space-y-6">
        
        <!-- Top Back to Home Button -->
        <div class="flex items-center justify-between text-xs">
            <a href="{{ route('home') }}" class="text-slate-400 hover:text-cyan-400 transition-colors flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back to Home Page</span>
            </a>
            <span class="text-[11px] font-mono text-cyan-400/80">ERP SECURE LOGIN</span>
        </div>

        <!-- Main Glass Card -->
        <div class="glass-box rounded-3xl p-7 sm:p-9 text-white relative overflow-hidden">
            
            <!-- Header Brand & Title -->
            <div class="text-center space-y-3 mb-6">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-cyan-500 via-blue-600 to-indigo-600 p-[2px] shadow-lg shadow-cyan-500/30 mb-1">
                    <div class="w-full h-full bg-[#070d22] rounded-[14px] flex items-center justify-center">
                        <i class="fa-solid fa-industry text-2xl text-cyan-400"></i>
                    </div>
                </div>
                <div>
                    <h2 class="text-lg font-extrabold text-white tracking-tight">
                        {{ $company->company_name ?? 'Turkey Bangla Garments Ltd' }}
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Enterprise Industrial ERP & HR Portal</p>
                </div>
            </div>

            <!-- Portal Switching Tabs (Admin / Staff vs Employee / Worker) -->
            <div class="grid grid-cols-2 p-1 rounded-2xl bg-[#040817] border border-slate-800 mb-6">
                <button type="button" id="tabAdmin" onclick="switchPortal('admin')"
                        class="py-2.5 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-cyan-600 text-white shadow-md shadow-cyan-500/20">
                    <i class="fa-solid fa-user-shield text-[11px]"></i>
                    <span>Management</span>
                </button>
                <button type="button" id="tabEmployee" onclick="switchPortal('employee')"
                        class="py-2.5 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 text-slate-400 hover:text-white">
                    <i class="fa-solid fa-id-badge text-[11px]"></i>
                    <span>Employee / Worker</span>
                </button>
            </div>

            <!-- Error Notifications -->
            @if ($errors->any())
            <div class="p-3.5 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs mb-5 flex items-start gap-2.5">
                <i class="fa-solid fa-circle-exclamation text-rose-400 mt-0.5"></i>
                <div class="space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Login Form -->
            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Login Identifier (Email or Employee ID) -->
                <div>
                    <label id="identifierLabel" class="block text-xs font-bold text-slate-300 mb-1.5 flex items-center justify-between">
                        <span>Email Address / ইউজার ইমেইল</span>
                        <span id="identifierHint" class="text-[10px] text-cyan-400 font-mono font-normal">e.g. admin@turkeybangla.com</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs" id="identifierIcon">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <input type="text" name="login_identifier" id="loginIdentifier" required
                               value="{{ old('login_identifier') }}"
                               placeholder="admin@turkeybangla.com"
                               class="w-full bg-[#060c20] border border-slate-700 focus:border-cyan-400 rounded-xl pl-10 pr-3.5 py-3 text-xs text-white placeholder-slate-500 focus:outline-none transition-all font-medium">
                    </div>
                </div>

                <!-- Password with Eye Toggle Button -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-bold text-slate-300">Password / পাসওয়ার্ড</label>
                        <span class="text-[10px] text-slate-400">Case-sensitive</span>
                    </div>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="loginPassword" required
                               placeholder="••••••••"
                               class="w-full bg-[#060c20] border border-slate-700 focus:border-cyan-400 rounded-xl pl-10 pr-11 py-3 text-xs text-white placeholder-slate-500 focus:outline-none transition-all">
                        
                        <!-- Password Visibility Toggle Button -->
                        <button type="button" id="togglePasswordBtn" onclick="togglePasswordVisibility()" title="Show/Hide Password"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-cyan-400 transition-colors text-xs p-1">
                            <i class="fa-solid fa-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Help -->
                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-300">
                        <input type="checkbox" name="remember" class="rounded border-slate-700 bg-[#060c20] text-cyan-500 focus:ring-0">
                        <span class="text-[11px]">Remember login session</span>
                    </label>
                    <span class="text-[11px] text-slate-400">Support: 01712-345678</span>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" id="submitBtn"
                            class="btn-glow w-full py-3.5 rounded-xl text-xs font-extrabold text-white flex items-center justify-center gap-2">
                        <span>Sign In to System (প্রবেশ করুন)</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </div>

            </form>

            <!-- Quick Demo Credentials Chips for Easy Testing -->
            <div class="mt-6 pt-5 border-t border-slate-800/80">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2.5 text-center">
                    Quick One-Click Test Logins
                </div>
                <div class="grid grid-cols-2 gap-2 text-[11px]">
                    <button type="button" onclick="fillCredentials('admin@turkeybangla.com', 'admin123', 'admin')"
                            class="p-2 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-700/80 text-left hover:border-cyan-500/50 transition-all group">
                        <div class="font-bold text-cyan-300 flex items-center gap-1.5">
                            <i class="fa-solid fa-shield-halved text-[10px]"></i>
                            <span>Admin Demo</span>
                        </div>
                        <div class="text-[9px] text-slate-400 font-mono mt-0.5 truncate">admin@turkeybangla.com</div>
                    </button>

                    <button type="button" onclick="fillCredentials('TBG-EMP-001', 'password', 'employee')"
                            class="p-2 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-700/80 text-left hover:border-emerald-500/50 transition-all group">
                        <div class="font-bold text-emerald-300 flex items-center gap-1.5">
                            <i class="fa-solid fa-user text-[10px]"></i>
                            <span>Worker Demo</span>
                        </div>
                        <div class="text-[9px] text-slate-400 font-mono mt-0.5 truncate">TBG-EMP-001</div>
                    </button>
                </div>
            </div>

        </div>

        <!-- Security Badge Footer -->
        <div class="text-center text-[11px] text-slate-500 flex items-center justify-center gap-2">
            <i class="fa-solid fa-lock text-emerald-400"></i>
            <span>End-to-End Encrypted Session & Role-Based Access Control</span>
        </div>

    </div>

    <script>
        // Toggle Password Show/Hide with Eye Button
        function togglePasswordVisibility() {
            const pwdInput = document.getElementById('loginPassword');
            const icon = document.getElementById('togglePasswordIcon');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                pwdInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Switch between Admin & Employee portal mode UI
        function switchPortal(type) {
            const tabAdmin = document.getElementById('tabAdmin');
            const tabEmployee = document.getElementById('tabEmployee');
            const identifierLabel = document.getElementById('identifierLabel');
            const identifierHint = document.getElementById('identifierHint');
            const identifierInput = document.getElementById('loginIdentifier');
            const identifierIcon = document.getElementById('identifierIcon');

            if (type === 'employee') {
                tabEmployee.className = "py-2.5 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-500/20";
                tabAdmin.className = "py-2.5 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 text-slate-400 hover:text-white";
                
                identifierLabel.firstElementChild.innerText = "Employee ID / কোড বা ইমেইল";
                identifierHint.innerText = "e.g. TBG-EMP-001 or email";
                identifierInput.placeholder = "TBG-EMP-001";
                identifierIcon.innerHTML = '<i class="fa-solid fa-id-badge"></i>';
            } else {
                tabAdmin.className = "py-2.5 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-cyan-600 text-white shadow-md shadow-cyan-500/20";
                tabEmployee.className = "py-2.5 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 text-slate-400 hover:text-white";
                
                identifierLabel.firstElementChild.innerText = "Email Address / ইউজার ইমেইল";
                identifierHint.innerText = "e.g. admin@turkeybangla.com";
                identifierInput.placeholder = "admin@turkeybangla.com";
                identifierIcon.innerHTML = '<i class="fa-solid fa-envelope"></i>';
            }
        }

        // Fill credentials helper
        function fillCredentials(id, pass, type) {
            switchPortal(type);
            document.getElementById('loginIdentifier').value = id;
            document.getElementById('loginPassword').value = pass;
        }

        // Check URL parameter on load
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('portal') === 'employee') {
                switchPortal('employee');
            }
        });
    </script>
</body>
</html>

