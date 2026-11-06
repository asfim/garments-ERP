@php
    $currentRoute = request()->route() ? request()->route()->getName() : '';
    $currentTabParam = request()->get('tab', 'profit_loss');
    
    if (str_contains($currentRoute, 'profitability')) {
        $activeReportTab = 'profitability';
    } elseif (str_contains($currentRoute, 'inventory-valuation')) {
        $activeReportTab = 'inventory_valuation';
    } else {
        $activeReportTab = $currentTabParam ?: 'profit_loss';
    }
@endphp

<div id="reportTabsNav" class="glass-panel p-2 rounded-2xl border border-slate-800/80 flex flex-wrap gap-2 text-xs">
    <!-- 1. P&L Statement -->
    <a href="{{ route('factory.accounts.reports.index', ['tab' => 'profit_loss']) }}" 
       data-tab="profit_loss"
       onclick="handleReportTabClick(event, this.href)"
       class="report-nav-tab px-3.5 py-2.5 rounded-xl font-bold transition-all flex items-center gap-1.5 {{ $activeReportTab === 'profit_loss' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-lg shadow-emerald-500/25 border border-emerald-400/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60 font-medium' }}">
        <i class="fa-solid fa-chart-line text-sm"></i>
        <span>P&L Statement</span>
    </a>

    <!-- 2. Trial Balance -->
    <a href="{{ route('factory.accounts.reports.index', ['tab' => 'trial_balance']) }}" 
       data-tab="trial_balance"
       onclick="handleReportTabClick(event, this.href)"
       class="report-nav-tab px-3.5 py-2.5 rounded-xl font-bold transition-all flex items-center gap-1.5 {{ $activeReportTab === 'trial_balance' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-lg shadow-emerald-500/25 border border-emerald-400/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60 font-medium' }}">
        <i class="fa-solid fa-scale-balanced text-sm"></i>
        <span>Trial Balance</span>
    </a>

    <!-- 3. Balance Sheet -->
    <a href="{{ route('factory.accounts.reports.index', ['tab' => 'balance_sheet']) }}" 
       data-tab="balance_sheet"
       onclick="handleReportTabClick(event, this.href)"
       class="report-nav-tab px-3.5 py-2.5 rounded-xl font-bold transition-all flex items-center gap-1.5 {{ $activeReportTab === 'balance_sheet' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-lg shadow-emerald-500/25 border border-emerald-400/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60 font-medium' }}">
        <i class="fa-solid fa-landmark text-sm"></i>
        <span>Balance Sheet</span>
    </a>

    <!-- 4. Day Book -->
    <a href="{{ route('factory.accounts.reports.index', ['tab' => 'day_book']) }}" 
       data-tab="day_book"
       onclick="handleReportTabClick(event, this.href)"
       class="report-nav-tab px-3.5 py-2.5 rounded-xl font-bold transition-all flex items-center gap-1.5 {{ $activeReportTab === 'day_book' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-lg shadow-emerald-500/25 border border-emerald-400/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60 font-medium' }}">
        <i class="fa-solid fa-book text-sm"></i>
        <span>Day Book</span>
    </a>

    <!-- 5. Profitability Reports -->
    <a href="{{ route('factory.accounts.profitability.index') }}" 
       data-tab="profitability"
       onclick="handleReportTabClick(event, this.href)"
       class="report-nav-tab px-3.5 py-2.5 rounded-xl font-bold transition-all flex items-center gap-1.5 {{ $activeReportTab === 'profitability' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-lg shadow-emerald-500/25 border border-emerald-400/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60 font-medium' }}">
        <i class="fa-solid fa-chart-column text-sm"></i>
        <span>Profitability Reports</span>
    </a>

    <!-- 6. FIFO Inventory Valuation -->
    <a href="{{ route('factory.accounts.inventory-valuation.index') }}" 
       data-tab="inventory_valuation"
       onclick="handleReportTabClick(event, this.href)"
       class="report-nav-tab px-3.5 py-2.5 rounded-xl font-bold transition-all flex items-center gap-1.5 {{ $activeReportTab === 'inventory_valuation' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-lg shadow-emerald-500/25 border border-emerald-400/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60 font-medium' }}">
        <i class="fa-solid fa-boxes-stacked text-sm"></i>
        <span>FIFO Inventory Valuation</span>
    </a>
</div>

