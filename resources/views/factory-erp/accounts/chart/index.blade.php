@extends('layouts.app')

@section('title', 'Chart of Accounts | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-book-bookmark text-indigo-400"></i>
                Chart of Accounts & Banks
            </h2>
            <p class="text-xs text-slate-400">Organized structure of Assets, Liabilities, Equity, Revenue, and Expense heads</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('factory.accounts.mappings.index') }}" 
               class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-400 font-bold text-xs border border-slate-700 transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-sliders"></i>
                <span>System Mappings</span>
            </a>
            <button type="button" onclick="openAddAccountModal()" 
                    class="px-4 py-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold text-xs shadow-lg shadow-indigo-500/25 transition-all flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-plus-circle"></i>
                <span>+ Add Account / Bank</span>
            </button>
            <a href="{{ route('factory.accounts.dashboard') }}" 
               class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Dashboard</span>
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="p-3.5 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs flex items-center gap-2">
        <i class="fa-solid fa-circle-check text-sm"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="p-3.5 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs flex items-center gap-2">
        <i class="fa-solid fa-triangle-exclamation text-sm"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- CATEGORY OVERVIEW TILES -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
        <!-- Assets -->
        <a href="{{ route('factory.accounts.chart.index', ['type' => 'asset']) }}" 
           class="glass-panel p-3.5 rounded-2xl border {{ request('type') == 'asset' ? 'border-cyan-400 bg-cyan-950/30' : 'border-slate-800 hover:border-cyan-500/40' }} transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-400">1. Assets</span>
                <span class="w-6 h-6 rounded-lg bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-coins"></i>
                </span>
            </div>
            <div>
                <span class="text-lg font-black text-white font-mono block">৳ {{ number_format($assets->sum('current_balance'), 2) }}</span>
                <span class="text-[10px] text-slate-400 font-mono">{{ $assets->count() }} Accounts</span>
            </div>
        </a>

        <!-- Liabilities -->
        <a href="{{ route('factory.accounts.chart.index', ['type' => 'liability']) }}" 
           class="glass-panel p-3.5 rounded-2xl border {{ request('type') == 'liability' ? 'border-rose-400 bg-rose-950/30' : 'border-slate-800 hover:border-rose-500/40' }} transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-rose-400">2. Liabilities</span>
                <span class="w-6 h-6 rounded-lg bg-rose-500/20 text-rose-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </span>
            </div>
            <div>
                <span class="text-lg font-black text-white font-mono block">৳ {{ number_format($liabilities->sum('current_balance'), 2) }}</span>
                <span class="text-[10px] text-slate-400 font-mono">{{ $liabilities->count() }} Accounts</span>
            </div>
        </a>

        <!-- Equity -->
        <a href="{{ route('factory.accounts.chart.index', ['type' => 'equity']) }}" 
           class="glass-panel p-3.5 rounded-2xl border {{ request('type') == 'equity' ? 'border-purple-400 bg-purple-950/30' : 'border-slate-800 hover:border-purple-500/40' }} transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-purple-400">3. Equity</span>
                <span class="w-6 h-6 rounded-lg bg-purple-500/20 text-purple-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-vault"></i>
                </span>
            </div>
            <div>
                <span class="text-lg font-black text-white font-mono block">৳ {{ number_format($equities->sum('current_balance'), 2) }}</span>
                <span class="text-[10px] text-slate-400 font-mono">{{ $equities->count() }} Accounts</span>
            </div>
        </a>

        <!-- Income -->
        <a href="{{ route('factory.accounts.chart.index', ['type' => 'income']) }}" 
           class="glass-panel p-3.5 rounded-2xl border {{ request('type') == 'income' ? 'border-emerald-400 bg-emerald-950/30' : 'border-slate-800 hover:border-emerald-500/40' }} transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400">4. Income</span>
                <span class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-arrow-trend-up"></i>
                </span>
            </div>
            <div>
                <span class="text-lg font-black text-emerald-400 font-mono block">৳ {{ number_format($incomes->sum('current_balance'), 2) }}</span>
                <span class="text-[10px] text-slate-400 font-mono">{{ $incomes->count() }} Heads</span>
            </div>
        </a>

        <!-- Expenses -->
        <a href="{{ route('factory.accounts.chart.index', ['type' => 'expense']) }}" 
           class="glass-panel p-3.5 rounded-2xl border {{ request('type') == 'expense' ? 'border-amber-400 bg-amber-950/30' : 'border-slate-800 hover:border-amber-500/40' }} transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400">5. Expenses</span>
                <span class="w-6 h-6 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-arrow-trend-down"></i>
                </span>
            </div>
            <div>
                <span class="text-lg font-black text-rose-400 font-mono block">৳ {{ number_format($expenses->sum('current_balance'), 2) }}</span>
                <span class="text-[10px] text-slate-400 font-mono">{{ $expenses->count() }} Heads</span>
            </div>
        </a>
    </div>

    <!-- FILTER & SEARCH BAR -->
    <div class="glass-panel p-4 rounded-2xl border border-slate-800/80">
        <form method="GET" action="{{ route('factory.accounts.chart.index') }}" class="flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('factory.accounts.chart.index') }}" 
                   class="px-3 py-1.5 rounded-xl font-bold transition-all {{ !request('type') ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                    All ({{ $accounts->count() }})
                </a>
                <a href="{{ route('factory.accounts.chart.index', ['type' => 'asset']) }}" 
                   class="px-3 py-1.5 rounded-xl font-bold transition-all {{ request('type') == 'asset' ? 'bg-cyan-600 text-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                    Assets ({{ $assets->count() }})
                </a>
                <a href="{{ route('factory.accounts.chart.index', ['type' => 'liability']) }}" 
                   class="px-3 py-1.5 rounded-xl font-bold transition-all {{ request('type') == 'liability' ? 'bg-rose-600 text-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                    Liabilities ({{ $liabilities->count() }})
                </a>
                <a href="{{ route('factory.accounts.chart.index', ['type' => 'equity']) }}" 
                   class="px-3 py-1.5 rounded-xl font-bold transition-all {{ request('type') == 'equity' ? 'bg-purple-600 text-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                    Equity ({{ $equities->count() }})
                </a>
                <a href="{{ route('factory.accounts.chart.index', ['type' => 'income']) }}" 
                   class="px-3 py-1.5 rounded-xl font-bold transition-all {{ request('type') == 'income' ? 'bg-emerald-600 text-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                    Income ({{ $incomes->count() }})
                </a>
                <a href="{{ route('factory.accounts.chart.index', ['type' => 'expense']) }}" 
                   class="px-3 py-1.5 rounded-xl font-bold transition-all {{ request('type') == 'expense' ? 'bg-amber-600 text-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                    Expense ({{ $expenses->count() }})
                </a>
            </div>

            <div class="flex items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search code, name, bank..." 
                       class="bg-slate-900/80 border border-slate-700/80 rounded-xl px-3 py-1.5 text-slate-200 focus:outline-none focus:border-indigo-500 text-xs w-60">
                <button type="submit" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold transition-all">
                    Search
                </button>
                @if(request()->filled('search'))
                <a href="{{ route('factory.accounts.chart.index') }}" class="text-slate-400 hover:text-rose-400 text-xs underline">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- CHART OF ACCOUNTS TABLE -->
    <div class="glass-panel rounded-2xl border border-slate-800/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/60 text-slate-400 uppercase text-[10px] font-bold border-b border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4">Code</th>
                        <th class="py-3.5 px-4">Account Name</th>
                        <th class="py-3.5 px-4">Type</th>
                        <th class="py-3.5 px-4">Category</th>
                        <th class="py-3.5 px-4">Bank / A/C Info</th>
                        <th class="py-3.5 px-4 text-right">Opening Bal</th>
                        <th class="py-3.5 px-4 text-right">Current Balance (৳)</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($accounts as $acc)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="py-3.5 px-4 font-mono font-bold text-amber-400">
                            #{{ $acc->account_code }}
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="text-white font-bold block">{{ $acc->name }}</span>
                            @if($acc->description)
                            <span class="text-[11px] text-slate-400 truncate max-w-xs block">{{ $acc->description }}</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border {{ $acc->type_badge_class }}">
                                {{ ucfirst($acc->type) }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 font-medium text-slate-300">
                            {{ $acc->category }}
                        </td>
                        <td class="py-3.5 px-4 font-mono text-[11px] text-slate-400">
                            @if($acc->bank_name || $acc->account_number)
                                <span class="text-cyan-400 font-semibold block">{{ $acc->bank_name }}</span>
                                @if($acc->account_number)
                                <span>A/C: {{ $acc->account_number }}</span>
                                @endif
                            @else
                                <span class="text-slate-600">-</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-right font-mono text-slate-400">
                            ৳ {{ number_format($acc->opening_balance, 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-right font-mono font-black text-sm {{ $acc->current_balance >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                            ৳ {{ number_format($acc->current_balance, 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @if($acc->status === 'active')
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                Active
                            </span>
                            @else
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase bg-slate-800 text-slate-400 border border-slate-700">
                                Inactive
                            </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('factory.accounts.ledger.index', ['account_id' => $acc->id]) }}" 
                                   class="p-1.5 rounded-lg bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 text-xs" title="View Ledger">
                                    <i class="fa-solid fa-receipt"></i>
                                </a>
                                <button onclick='openEditAccountModal(@json($acc))' 
                                        class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs transition-all" title="Edit Account">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-slate-500">
                            <i class="fa-solid fa-folder-open text-3xl mb-2 opacity-50 block"></i>
                            No accounts found matching your selection.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- MODAL: ADD NEW ACCOUNT -->
<div id="addAccountModal" onclick="if(event.target === this) closeAddAccountModal()" class="fixed inset-0 z-[9999] bg-slate-950/85 hidden flex items-center justify-center p-4 overflow-y-auto transform-gpu">
    <div onclick="event.stopPropagation()" class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-xl max-h-[90vh] overflow-y-auto shadow-2xl p-6 relative">
        <button type="button" onclick="closeAddAccountModal()" class="absolute top-5 right-5 text-slate-400 hover:text-white text-lg cursor-pointer">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="flex items-center gap-3 border-b border-slate-800 pb-4 mb-5">
            <div class="w-10 h-10 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center">
                <i class="fa-solid fa-book-bookmark text-lg"></i>
            </div>
            <div>
                <h3 class="text-base font-extrabold text-white">Create Account Head / Bank</h3>
                <p class="text-xs text-slate-400">Add asset, liability, equity, income, or expense accounts</p>
            </div>
        </div>

        <form action="{{ route('factory.accounts.chart.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <!-- Account Code -->
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Account Code <span class="text-rose-400">*</span></label>
                    <input type="text" name="account_code" placeholder="e.g. 1012 or 5012" required
                           class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 font-mono font-bold focus:outline-none focus:border-indigo-500">
                </div>

                <!-- Account Type -->
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Account Type <span class="text-rose-400">*</span></label>
                    <select name="type" id="newAccountType" onchange="toggleBankFields()" required
                            class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-indigo-500">
                        <option value="asset">Asset (Cash / Bank / Receivable)</option>
                        <option value="liability">Liability (Payable / Loan)</option>
                        <option value="equity">Equity (Owner Capital / Retained)</option>
                        <option value="income">Income (Revenue / Sales)</option>
                        <option value="expense">Expense (Operating / Direct Bills)</option>
                    </select>
                </div>
            </div>

            <!-- Account Name -->
            <div>
                <label class="block text-slate-300 font-bold mb-1">Account Name <span class="text-rose-400">*</span></label>
                <input type="text" name="name" placeholder="e.g. Brac Bank CD A/C or Printing Maintenance" required
                       class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-indigo-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <!-- Category -->
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Category <span class="text-rose-400">*</span></label>
                    <input type="text" name="category" placeholder="e.g. Bank Account or Direct Expense" required
                           class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-indigo-500">
                </div>

                <!-- Opening Balance -->
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Opening Balance (৳)</label>
                    <input type="number" step="0.01" name="opening_balance" value="0.00"
                           class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 font-mono focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <!-- Bank Specific Fields (Optional) -->
            <div id="bankFieldsSection" class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-3">
                <span class="text-[11px] font-bold text-cyan-400 uppercase tracking-wider block">Bank Details (if Bank Account)</span>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-400 text-[10px] uppercase font-bold mb-1">Bank Name</label>
                        <input type="text" name="bank_name" placeholder="e.g. City Bank / DBBL"
                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-slate-200">
                    </div>
                    <div>
                        <label class="block text-slate-400 text-[10px] uppercase font-bold mb-1">Account Number</label>
                        <input type="text" name="account_number" placeholder="e.g. 150.110.9823"
                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-slate-200">
                    </div>
                    <div>
                        <label class="block text-slate-400 text-[10px] uppercase font-bold mb-1">Branch Name</label>
                        <input type="text" name="branch_name" placeholder="e.g. Tongi Branch"
                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-slate-200">
                    </div>
                    <div>
                        <label class="block text-slate-400 text-[10px] uppercase font-bold mb-1">Routing Number</label>
                        <input type="text" name="routing_number" placeholder="e.g. 09027123"
                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-slate-200">
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-slate-300 font-bold mb-1">Description / Note</label>
                <input type="text" name="description" placeholder="Brief note about this account head"
                       class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-indigo-500">
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <button type="button" onclick="closeAddAccountModal()" 
                        class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold transition-all">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold shadow-lg shadow-indigo-500/25 transition-all">
                    <i class="fa-solid fa-check mr-1.5"></i> Save Account
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: EDIT ACCOUNT -->
<div id="editAccountModal" onclick="if(event.target === this) closeEditAccountModal()" class="fixed inset-0 z-[9999] bg-slate-950/85 hidden flex items-center justify-center p-4 overflow-y-auto transform-gpu">
    <div onclick="event.stopPropagation()" class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-xl max-h-[90vh] overflow-y-auto shadow-2xl p-6 relative">
        <button type="button" onclick="closeEditAccountModal()" class="absolute top-5 right-5 text-slate-400 hover:text-white text-lg cursor-pointer">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="flex items-center gap-3 border-b border-slate-800 pb-4 mb-5">
            <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center">
                <i class="fa-solid fa-pen-to-square text-lg"></i>
            </div>
            <div>
                <h3 class="text-base font-extrabold text-white">Edit Account Head</h3>
                <p class="text-xs text-slate-400" id="editAccountCodeBadge">Editing Account</p>
            </div>
        </div>

        <form id="editAccountForm" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')

            <!-- Account Name -->
            <div>
                <label class="block text-slate-300 font-bold mb-1">Account Name <span class="text-rose-400">*</span></label>
                <input type="text" name="name" id="editName" required
                       class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-amber-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <!-- Category -->
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Category <span class="text-rose-400">*</span></label>
                    <input type="text" name="category" id="editCategory" required
                           class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-amber-500">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Status <span class="text-rose-400">*</span></label>
                    <select name="status" id="editStatus" required
                            class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-amber-500">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <!-- Opening Balance -->
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Opening Balance (৳)</label>
                    <input type="number" step="0.01" name="opening_balance" id="editOpeningBalance"
                           class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 font-mono focus:outline-none focus:border-amber-500">
                </div>
                <!-- Bank Name -->
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Bank Name (if applicable)</label>
                    <input type="text" name="bank_name" id="editBankName"
                           class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-amber-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <!-- Account Number -->
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Account Number</label>
                    <input type="text" name="account_number" id="editAccountNumber"
                           class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-amber-500">
                </div>
                <!-- Branch Name -->
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Branch Name</label>
                    <input type="text" name="branch_name" id="editBranchName"
                           class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-amber-500">
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-slate-300 font-bold mb-1">Description / Note</label>
                <input type="text" name="description" id="editDescription"
                       class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl px-3 py-2.5 text-slate-200 focus:outline-none focus:border-amber-500">
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <button type="button" onclick="closeEditAccountModal()" 
                        class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold transition-all">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white font-bold shadow-lg shadow-amber-500/25 transition-all">
                    <i class="fa-solid fa-check mr-1.5"></i> Update Account
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddAccountModal() {
    const m = document.getElementById('addAccountModal');
    if (m) m.classList.remove('hidden');
}
function closeAddAccountModal() {
    const m = document.getElementById('addAccountModal');
    if (m) m.classList.add('hidden');
}
function openEditAccountModal(acc) {
    document.getElementById('editAccountForm').action = '/factory/accounts/chart/' + acc.id;
    document.getElementById('editAccountCodeBadge').innerText = 'Code: #' + acc.account_code + ' (' + (acc.type || '').toUpperCase() + ')';
    document.getElementById('editName').value = acc.name || '';
    document.getElementById('editCategory').value = acc.category || '';
    document.getElementById('editStatus').value = acc.status || 'active';
    document.getElementById('editOpeningBalance').value = acc.opening_balance || '0.00';
    document.getElementById('editBankName').value = acc.bank_name || '';
    document.getElementById('editAccountNumber').value = acc.account_number || '';
    document.getElementById('editBranchName').value = acc.branch_name || '';
    document.getElementById('editDescription').value = acc.description || '';

    const m = document.getElementById('editAccountModal');
    if (m) m.classList.remove('hidden');
}
function closeEditAccountModal() {
    const m = document.getElementById('editAccountModal');
    if (m) m.classList.add('hidden');
}

// Close modals on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAddAccountModal();
        closeEditAccountModal();
    }
});
</script>
@endsection

