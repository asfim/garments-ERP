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
@php $isCust = request()->routeIs('factory.customers.index') || request()->routeIs('factory.customers.details'); @endphp
<a href="{{ route('factory.customers.index') }}" 
   class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all {{ $isCust ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold shadow-lg shadow-blue-500/25 border border-cyan-400/40' : 'text-slate-300 hover:text-white hover:bg-slate-800/50 font-medium border border-transparent group' }}">
    <span class="w-6 h-6 rounded-lg {{ $isCust ? 'bg-white/20 text-white' : 'bg-cyan-500/10 text-cyan-400 group-hover:bg-cyan-500/20' }} flex items-center justify-center">
        <i class="fa-solid fa-user-group text-[11px]"></i>
    </span>
    <span>Customer</span>
</a>

<!-- 3.1 Due Payment -->
@php $isDue = request()->routeIs('factory.customers.due.*'); @endphp
<a href="{{ route('factory.customers.due.index') }}" 
   class="flex items-center justify-between px-3 py-2 rounded-xl text-xs transition-all {{ $isDue ? 'bg-gradient-to-r from-rose-600 via-pink-600 to-amber-600 text-white font-bold shadow-lg shadow-rose-500/25 border border-rose-400/40' : 'text-slate-300 hover:text-white hover:bg-slate-800/50 font-medium border border-transparent group' }}">
    <div class="flex items-center gap-3">
        <span class="w-6 h-6 rounded-lg {{ $isDue ? 'bg-white/20 text-white' : 'bg-rose-500/10 text-rose-400 group-hover:bg-rose-500/20' }} flex items-center justify-center">
            <i class="fa-solid fa-hand-holding-dollar text-[11px]"></i>
        </span>
        <span class="{{ $isDue ? 'text-white font-bold' : 'text-rose-300 font-semibold group-hover:text-white' }}">Due Payment</span>
    </div>
    <span class="px-1.5 py-0.2 rounded-full text-[9px] font-mono font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">
        Due
    </span>
</a>

<!-- 4. Supplier -->
@php $isSupp = request()->routeIs('factory.suppliers.*'); @endphp
<a href="{{ route('factory.suppliers.index') }}" 
   class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all {{ $isSupp ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold shadow-lg shadow-blue-500/25 border border-cyan-400/40' : 'text-slate-300 hover:text-white hover:bg-slate-800/50 font-medium border border-transparent group' }}">
    <span class="w-6 h-6 rounded-lg {{ $isSupp ? 'bg-white/20 text-white' : 'bg-indigo-500/10 text-indigo-400 group-hover:bg-indigo-500/20' }} flex items-center justify-center">
        <i class="fa-solid fa-truck-ramp-box text-[11px]"></i>
    </span>
    <span>Supplier</span>
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

<!-- ACCOUNTS & FINANCE MANAGEMENT SECTION -->
<div class="pt-3 pb-1">
    <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-amber-400/90 flex items-center justify-between border-t border-slate-800/80">
        <span class="flex items-center gap-1.5">
            <i class="fa-solid fa-coins text-[10px]"></i>
            Accounts & Finance
        </span>
        <span class="px-1.5 py-0.2 rounded text-[8px] bg-amber-500/20 text-amber-300 border border-amber-500/30 font-mono font-bold">ACC</span>
    </div>
</div>

<!-- ACC 1. Accounts Dashboard -->
@php $isAccDash = request()->routeIs('factory.accounts.dashboard'); @endphp
<a href="{{ route('factory.accounts.dashboard') }}" 
   class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all {{ $isAccDash ? 'bg-gradient-to-r from-amber-600 to-orange-600 text-white font-bold shadow-lg shadow-amber-500/25 border border-amber-400/40' : 'text-slate-300 hover:text-white hover:bg-slate-800/50 font-medium border border-transparent group' }}">
    <span class="w-6 h-6 rounded-lg {{ $isAccDash ? 'bg-white/20 text-white' : 'bg-amber-500/10 text-amber-400 group-hover:bg-amber-500/20' }} flex items-center justify-center">
        <i class="fa-solid fa-chart-pie text-[11px]"></i>
    </span>
    <span>Accounts Dashboard</span>
</a>

<!-- ACC 2. Income -->
@php $isIncome = request()->routeIs('factory.accounts.income.*'); @endphp
<a href="{{ route('factory.accounts.income.index') }}" 
   class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all {{ $isIncome ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold shadow-lg shadow-emerald-500/25 border border-emerald-400/40' : 'text-slate-300 hover:text-white hover:bg-slate-800/50 font-medium border border-transparent group' }}">
    <span class="w-6 h-6 rounded-lg {{ $isIncome ? 'bg-white/20 text-white' : 'bg-emerald-500/10 text-emerald-400 group-hover:bg-emerald-500/20' }} flex items-center justify-center">
        <i class="fa-solid fa-arrow-trend-up text-[11px]"></i>
    </span>
    <span>Income (আয়)</span>
</a>

<!-- ACC 3. Expense -->
@php $isExpense = request()->routeIs('factory.accounts.expense.*'); @endphp
<a href="{{ route('factory.accounts.expense.index') }}" 
   class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all {{ $isExpense ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-lg shadow-rose-500/25 border border-rose-400/40' : 'text-slate-300 hover:text-white hover:bg-slate-800/50 font-medium border border-transparent group' }}">
    <span class="w-6 h-6 rounded-lg {{ $isExpense ? 'bg-white/20 text-white' : 'bg-rose-500/10 text-rose-400 group-hover:bg-rose-500/20' }} flex items-center justify-center">
        <i class="fa-solid fa-money-bill-wave text-[11px]"></i>
    </span>
    <span>Expense (ব্যয়)</span>
</a>

{{-- ACC 4. Journal Vouchers (জাবেদা) [Commented out as per user request]
@php $isJournal = request()->routeIs('factory.accounts.journals.*'); @endphp
<a href="{{ route('factory.accounts.journals.index') }}" 
   class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all {{ $isJournal ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold shadow-lg shadow-blue-500/25 border border-blue-400/40' : 'text-slate-300 hover:text-white hover:bg-slate-800/50 font-medium border border-transparent group' }}">
    <span class="w-6 h-6 rounded-lg {{ $isJournal ? 'bg-white/20 text-white' : 'bg-blue-500/10 text-blue-400 group-hover:bg-blue-500/20' }} flex items-center justify-center">
        <i class="fa-solid fa-file-signature text-[11px]"></i>
    </span>
    <span>Journal Voucher (জাবেদা)</span>
</a>
--}}

<!-- ACC 5. Chart of Accounts -->
@php $isChart = request()->routeIs('factory.accounts.chart.*'); @endphp
<a href="{{ route('factory.accounts.chart.index') }}" 
   class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all {{ $isChart ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold shadow-lg shadow-indigo-500/25 border border-indigo-400/40' : 'text-slate-300 hover:text-white hover:bg-slate-800/50 font-medium border border-transparent group' }}">
    <span class="w-6 h-6 rounded-lg {{ $isChart ? 'bg-white/20 text-white' : 'bg-indigo-500/10 text-indigo-400 group-hover:bg-indigo-500/20' }} flex items-center justify-center">
        <i class="fa-solid fa-book-bookmark text-[11px]"></i>
    </span>
    <span>Chart of Accounts</span>
</a>

<!-- ACC 6. General Ledger (খতিয়ান ও সহকারী) -->
@php $isLedger = request()->routeIs('factory.accounts.ledger.*') || request()->routeIs('factory.accounts.ledgers.*'); @endphp
<a href="{{ route('factory.accounts.ledger.index') }}" 
   class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all {{ $isLedger ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold shadow-lg shadow-purple-500/25 border border-purple-400/40' : 'text-slate-300 hover:text-white hover:bg-slate-800/50 font-medium border border-transparent group' }}">
    <span class="w-6 h-6 rounded-lg {{ $isLedger ? 'bg-white/20 text-white' : 'bg-purple-500/10 text-purple-400 group-hover:bg-purple-500/20' }} flex items-center justify-center">
        <i class="fa-solid fa-receipt text-[11px]"></i>
    </span>
    <span>General Ledger (খতিয়ান)</span>
</a>

<!-- ACC 7. Financial Reports -->
@php $isReports = request()->routeIs('factory.accounts.reports.*') || request()->routeIs('factory.accounts.inventory-valuation.*') || request()->routeIs('factory.accounts.profitability.*'); @endphp
<a href="{{ route('factory.accounts.reports.index') }}" 
   class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all {{ $isReports ? 'bg-gradient-to-r from-teal-600 to-emerald-600 text-white font-bold shadow-lg shadow-teal-500/25 border border-teal-400/40' : 'text-slate-300 hover:text-white hover:bg-slate-800/50 font-medium border border-transparent group' }}">
    <span class="w-6 h-6 rounded-lg {{ $isReports ? 'bg-white/20 text-white' : 'bg-teal-500/10 text-teal-400 group-hover:bg-teal-500/20' }} flex items-center justify-center">
        <i class="fa-solid fa-file-invoice-dollar text-[11px]"></i>
    </span>
    <span>Financial Reports</span>
</a>

<!-- HR & PAYROLL MANAGEMENT SECTION -->
<div class="pt-3 pb-1">
    <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-emerald-400/90 flex items-center justify-between border-t border-slate-800/80">
        <span class="flex items-center gap-1.5">
            <i class="fa-solid fa-user-tie text-[10px]"></i>
            HR & Payroll
        </span>
        <span class="px-1.5 py-0.2 rounded text-[8px] bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-mono font-bold">HRM</span>
    </div>
</div>

<!-- HR 1. Dashboard -->
@php $isHrDash = request()->routeIs('factory.hr.index'); @endphp
<a href="{{ route('factory.hr.index') }}" 
   class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all {{ $isHrDash ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold shadow-lg shadow-emerald-500/25 border border-emerald-400/40' : 'text-slate-300 hover:text-white hover:bg-slate-800/50 font-medium border border-transparent group' }}">
    <span class="w-6 h-6 rounded-lg {{ $isHrDash ? 'bg-white/20 text-white' : 'bg-emerald-500/10 text-emerald-400 group-hover:bg-emerald-500/20' }} flex items-center justify-center">
        <i class="fa-solid fa-gauge-high text-[11px]"></i>
    </span>
    <span>HR Dashboard</span>
</a>

<!-- HR 2. Employees -->
@php $isEmp = request()->routeIs('factory.hr.employees.*'); @endphp
<a href="{{ route('factory.hr.employees.index') }}" 
   class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all {{ $isEmp ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold shadow-lg shadow-blue-500/25 border border-cyan-400/40' : 'text-slate-300 hover:text-white hover:bg-slate-800/50 font-medium border border-transparent group' }}">
    <span class="w-6 h-6 rounded-lg {{ $isEmp ? 'bg-white/20 text-white' : 'bg-cyan-500/10 text-cyan-400 group-hover:bg-cyan-500/20' }} flex items-center justify-center">
        <i class="fa-solid fa-users-gear text-[11px]"></i>
    </span>
    <span>Employees</span>
</a>

<!-- HR 3. Attendance -->
@php $isAtt = request()->routeIs('factory.hr.attendance.*'); @endphp
<a href="{{ route('factory.hr.attendance.index') }}" 
   class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all {{ $isAtt ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold shadow-lg shadow-blue-500/25 border border-cyan-400/40' : 'text-slate-300 hover:text-white hover:bg-slate-800/50 font-medium border border-transparent group' }}">
    <span class="w-6 h-6 rounded-lg {{ $isAtt ? 'bg-white/20 text-white' : 'bg-amber-500/10 text-amber-400 group-hover:bg-amber-500/20' }} flex items-center justify-center">
        <i class="fa-solid fa-clipboard-user text-[11px]"></i>
    </span>
    <span>Attendance</span>
</a>

<!-- HR 3.1 Biometric Devices (ZKTeco) -->
@php $isBio = request()->routeIs('factory.hr.devices.*'); @endphp
<a href="{{ route('factory.hr.devices.index') }}" 
   class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all {{ $isBio ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold shadow-lg shadow-blue-500/25 border border-cyan-400/40' : 'text-slate-300 hover:text-white hover:bg-slate-800/50 font-medium border border-transparent group' }}">
    <span class="w-6 h-6 rounded-lg {{ $isBio ? 'bg-white/20 text-white' : 'bg-cyan-500/10 text-cyan-400 group-hover:bg-cyan-500/20' }} flex items-center justify-center">
        <i class="fa-solid fa-fingerprint text-[11px]"></i>
    </span>
    <span>Biometric Devices</span>
</a>

<!-- HR 4. Leave Management -->
@php $isLeave = request()->routeIs('factory.hr.leaves.*'); @endphp
<a href="{{ route('factory.hr.leaves.index') }}" 
   class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all {{ $isLeave ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold shadow-lg shadow-blue-500/25 border border-cyan-400/40' : 'text-slate-300 hover:text-white hover:bg-slate-800/50 font-medium border border-transparent group' }}">
    <span class="w-6 h-6 rounded-lg {{ $isLeave ? 'bg-white/20 text-white' : 'bg-purple-500/10 text-purple-400 group-hover:bg-purple-500/20' }} flex items-center justify-center">
        <i class="fa-solid fa-calendar-check text-[11px]"></i>
    </span>
    <span>Leaves</span>
</a>

<!-- HR 4.1 Advance Salary & Loans -->
@php $isAdv = request()->routeIs('factory.hr.advances.*'); @endphp
<a href="{{ route('factory.hr.advances.index') }}" 
   class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all {{ $isAdv ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold shadow-lg shadow-blue-500/25 border border-cyan-400/40' : 'text-slate-300 hover:text-white hover:bg-slate-800/50 font-medium border border-transparent group' }}">
    <span class="w-6 h-6 rounded-lg {{ $isAdv ? 'bg-white/20 text-white' : 'bg-rose-500/10 text-rose-400 group-hover:bg-rose-500/20' }} flex items-center justify-center">
        <i class="fa-solid fa-hand-holding-dollar text-[11px]"></i>
    </span>
    <span>Advance Salary</span>
</a>

<!-- HR 5. Payroll & Payslips -->
@php $isPay = request()->routeIs('factory.hr.payroll.*'); @endphp
<a href="{{ route('factory.hr.payroll.index') }}" 
   class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all {{ $isPay ? 'bg-gradient-to-r from-emerald-600 to-cyan-600 text-white font-bold shadow-lg shadow-emerald-500/25 border border-emerald-400/40' : 'text-slate-300 hover:text-white hover:bg-slate-800/50 font-medium border border-transparent group' }}">
    <span class="w-6 h-6 rounded-lg {{ $isPay ? 'bg-white/20 text-white' : 'bg-emerald-500/10 text-emerald-400 group-hover:bg-emerald-500/20' }} flex items-center justify-center">
        <i class="fa-solid fa-file-invoice-dollar text-[11px]"></i>
    </span>
    <span>Payroll & Payslip</span>
</a>

<!-- HR 6. Departments -->
@php $isDept = request()->routeIs('factory.hr.departments.*'); @endphp
<a href="{{ route('factory.hr.departments.index') }}" 
   class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all {{ $isDept ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold shadow-lg shadow-blue-500/25 border border-cyan-400/40' : 'text-slate-300 hover:text-white hover:bg-slate-800/50 font-medium border border-transparent group' }}">
    <span class="w-6 h-6 rounded-lg {{ $isDept ? 'bg-white/20 text-white' : 'bg-indigo-500/10 text-indigo-400 group-hover:bg-indigo-500/20' }} flex items-center justify-center">
        <i class="fa-solid fa-building text-[11px]"></i>
    </span>
    <span>Departments</span>
</a>

<!-- HR 7. Designations -->
@php $isDes = request()->routeIs('factory.hr.designations.*'); @endphp
<a href="{{ route('factory.hr.designations.index') }}" 
   class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all {{ $isDes ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold shadow-lg shadow-blue-500/25 border border-cyan-400/40' : 'text-slate-300 hover:text-white hover:bg-slate-800/50 font-medium border border-transparent group' }}">
    <span class="w-6 h-6 rounded-lg {{ $isDes ? 'bg-white/20 text-white' : 'bg-cyan-500/10 text-cyan-400 group-hover:bg-cyan-500/20' }} flex items-center justify-center">
        <i class="fa-solid fa-user-tag text-[11px]"></i>
    </span>
    <span>Designations</span>
</a>

<div class="pt-2 pb-1">
    <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-blue-400/80 border-t border-slate-800/80">
        Configuration
    </div>
</div>

<!-- 8. Settings -->
@php $isSet = request()->routeIs('factory.settings.*'); @endphp
<a href="{{ route('factory.settings.index') }}" 
   class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all {{ $isSet ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold shadow-lg shadow-blue-500/25 border border-cyan-400/40' : 'text-slate-300 hover:text-white hover:bg-slate-800/50 font-medium border border-transparent group' }}">
    <span class="w-6 h-6 rounded-lg {{ $isSet ? 'bg-white/20 text-white' : 'bg-rose-500/10 text-rose-400 group-hover:bg-rose-500/20' }} flex items-center justify-center">
        <i class="fa-solid fa-gears text-[11px]"></i>
    </span>
    <span>Settings</span>
</a>
