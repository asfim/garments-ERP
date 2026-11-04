<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\AccountVoucher;
use App\Models\AccountTransaction;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\HrEmployee;
use App\Models\CompanySetting;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AccountsController extends Controller
{
    private function getCompany()
    {
        return CompanySetting::first() ?? new CompanySetting([
            'company_name' => 'Turkey Bangla Garments Ltd',
            'address' => 'Tongi Industrial Area, Gazipur, Dhaka',
            'phone' => '+880 1712-345678',
            'email' => 'accounts@turkeybangla.com'
        ]);
    }

    // ==========================================
    // 1. ACCOUNTS DASHBOARD / OVERVIEW
    // ==========================================
    public function dashboard(Request $request, AccountingService $service)
    {
        $today = Carbon::today()->format('Y-m-d');
        $thisMonthStart = Carbon::now()->startOfMonth()->format('Y-m-d');
        $thisMonthEnd = Carbon::now()->endOfMonth()->format('Y-m-d');

        // Period filter (default this month)
        $fromDate = $request->get('from_date', $thisMonthStart);
        $toDate = $request->get('to_date', $today);

        // Liquid Assets (Cash & Bank Accounts)
        $liquidAccounts = Account::where('type', 'asset')
            ->whereIn('category', ['Cash in Hand', 'Bank Account', 'Mobile Banking'])
            ->orderBy('account_code')
            ->get();
        $totalLiquidBalance = $liquidAccounts->sum('current_balance');

        // Customer Receivables (Total Market Due)
        $totalReceivable = DB::table('orders')->selectRaw('SUM(due_amount) as total_due')->value('total_due') ?? 0;

        // Supplier Accounts Payable
        $payableAccount = Account::where('account_code', '2001')->first();
        $totalPayable = $payableAccount ? $payableAccount->current_balance : 0;

        // Current Month Incomes & Expenses
        $monthIncome = (float) AccountVoucher::where('voucher_type', 'income')
            ->whereBetween('voucher_date', [$fromDate, $toDate])
            ->sum('amount');

        $monthExpense = (float) AccountVoucher::where('voucher_type', 'expense')
            ->whereBetween('voucher_date', [$fromDate, $toDate])
            ->sum('amount');

        $netProfit = $monthIncome - $monthExpense;

        // All time stats
        $allTimeIncome = (float) AccountVoucher::where('voucher_type', 'income')->sum('amount');
        $allTimeExpense = (float) AccountVoucher::where('voucher_type', 'expense')->sum('amount');

        // Recent 10 Vouchers
        $recentVouchers = AccountVoucher::with(['debitAccount', 'creditAccount', 'customer', 'supplier', 'employee'])
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        // Recent 10 Transactions (Ledger Stream)
        $recentTransactions = AccountTransaction::with(['account', 'voucher'])
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        // Expense by category breakdown
        $expenseBreakdown = Account::where('type', 'expense')
            ->withSum(['transactions as total_spent' => function ($q) use ($fromDate, $toDate) {
                $q->where('type', 'debit')->whereBetween('transaction_date', [$fromDate, $toDate]);
            }], 'amount')
            ->having('total_spent', '>', 0)
            ->orderByDesc('total_spent')
            ->take(6)
            ->get();

        return view('factory-erp.accounts.dashboard', compact(
            'fromDate', 'toDate',
            'liquidAccounts', 'totalLiquidBalance',
            'totalReceivable', 'totalPayable',
            'monthIncome', 'monthExpense', 'netProfit',
            'allTimeIncome', 'allTimeExpense',
            'recentVouchers', 'recentTransactions',
            'expenseBreakdown'
        ));
    }

    // ==========================================
    // 2. INCOME / REVENUE MANAGEMENT
    // ==========================================
    public function incomeIndex(Request $request)
    {
        $query = AccountVoucher::with(['debitAccount', 'creditAccount', 'customer'])
            ->where('voucher_type', 'income');

        if ($request->filled('from_date')) {
            $query->whereDate('voucher_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('voucher_date', '<=', $request->to_date);
        }
        if ($request->filled('income_account_id')) {
            $query->where('credit_account_id', $request->income_account_id);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('voucher_no', 'like', "%{$s}%")
                  ->orWhere('party_name', 'like', "%{$s}%")
                  ->orWhere('reference_no', 'like', "%{$s}%")
                  ->orWhere('notes', 'like', "%{$s}%");
            });
        }

        $totalFilteredIncome = (clone $query)->sum('amount');
        $vouchers = $query->orderBy('voucher_date', 'desc')->orderBy('id', 'desc')->paginate(20)->withQueryString();

        // Dropdown data
        $incomeAccounts = Account::where('type', 'income')->where('status', 'active')->orderBy('name')->get();
        $liquidAccounts = Account::where('type', 'asset')
            ->whereIn('category', ['Cash in Hand', 'Bank Account', 'Mobile Banking'])
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        $customers = Customer::orderBy('company_name')->get();
        $nextVoucherNo = AccountVoucher::generateVoucherNo('income');

        return view('factory-erp.accounts.income.index', compact(
            'vouchers', 'totalFilteredIncome', 'incomeAccounts', 'liquidAccounts', 'customers', 'nextVoucherNo'
        ));
    }

    public function incomeStore(Request $request, AccountingService $service)
    {
        $validated = $request->validate([
            'voucher_date' => 'required|date',
            'income_account_id' => 'required|exists:accounts,id',
            'deposit_account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'party_name' => 'nullable|string|max:255',
            'customer_id' => 'nullable|exists:customers,id',
            'payment_method' => 'required|string|in:cash,bank,cheque,bkash,nagad,rocket,other',
            'reference_no' => 'nullable|string|max:100',
            'cheque_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('accounts/attachments', 'public');
        }

        // If customer selected and party name empty, autofill party name
        if (!empty($validated['customer_id']) && empty($validated['party_name'])) {
            $c = Customer::find($validated['customer_id']);
            $validated['party_name'] = $c ? $c->company_name : null;
        }

        $voucher = $service->recordIncome($validated, $attachmentPath);

        return redirect()->route('factory.accounts.income.index')
            ->with('success', "Income voucher {$voucher->voucher_no} of ৳ " . number_format($voucher->amount, 2) . " created successfully!");
    }

    public function incomePrint(AccountVoucher $voucher)
    {
        $voucher->load(['debitAccount', 'creditAccount', 'customer', 'createdBy']);
        $company = $this->getCompany();

        return view('factory-erp.accounts.vouchers.print-income', compact('voucher', 'company'));
    }

    // ==========================================
    // 3. EXPENSE MANAGEMENT
    // ==========================================
    public function expenseIndex(Request $request)
    {
        $query = AccountVoucher::with(['debitAccount', 'creditAccount', 'supplier', 'employee'])
            ->where('voucher_type', 'expense');

        if ($request->filled('from_date')) {
            $query->whereDate('voucher_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('voucher_date', '<=', $request->to_date);
        }
        if ($request->filled('expense_account_id')) {
            $query->where('debit_account_id', $request->expense_account_id);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('voucher_no', 'like', "%{$s}%")
                  ->orWhere('party_name', 'like', "%{$s}%")
                  ->orWhere('reference_no', 'like', "%{$s}%")
                  ->orWhere('notes', 'like', "%{$s}%");
            });
        }

        $totalFilteredExpense = (clone $query)->sum('amount');
        $vouchers = $query->orderBy('voucher_date', 'desc')->orderBy('id', 'desc')->paginate(20)->withQueryString();

        // Dropdown data
        $expenseAccounts = Account::where('type', 'expense')->where('status', 'active')->orderBy('name')->get();
        $liquidAccounts = Account::where('type', 'asset')
            ->whereIn('category', ['Cash in Hand', 'Bank Account', 'Mobile Banking'])
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        $suppliers = Supplier::orderBy('name')->get();
        $employees = HrEmployee::where('status', 'active')->orderBy('name')->get();
        $nextVoucherNo = AccountVoucher::generateVoucherNo('expense');

        return view('factory-erp.accounts.expense.index', compact(
            'vouchers', 'totalFilteredExpense', 'expenseAccounts', 'liquidAccounts', 'suppliers', 'employees', 'nextVoucherNo'
        ));
    }

    public function expenseStore(Request $request, AccountingService $service)
    {
        $validated = $request->validate([
            'voucher_date' => 'required|date',
            'expense_account_id' => 'required|exists:accounts,id',
            'paid_from_account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'party_name' => 'nullable|string|max:255',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'employee_id' => 'nullable|exists:hr_employees,id',
            'payment_method' => 'required|string|in:cash,bank,cheque,bkash,nagad,rocket,other',
            'reference_no' => 'nullable|string|max:100',
            'cheque_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('accounts/attachments', 'public');
        }

        // If supplier selected and party name empty, autofill party name
        if (!empty($validated['supplier_id']) && empty($validated['party_name'])) {
            $s = Supplier::find($validated['supplier_id']);
            $validated['party_name'] = $s ? $s->name : null;
        } elseif (!empty($validated['employee_id']) && empty($validated['party_name'])) {
            $e = HrEmployee::find($validated['employee_id']);
            $validated['party_name'] = $e ? ($e->name . " ({$e->employee_code})") : null;
        }

        $voucher = $service->recordExpense($validated, $attachmentPath);

        return redirect()->route('factory.accounts.expense.index')
            ->with('success', "Expense voucher {$voucher->voucher_no} of ৳ " . number_format($voucher->amount, 2) . " recorded successfully!");
    }

    public function expensePrint(AccountVoucher $voucher)
    {
        $voucher->load(['debitAccount', 'creditAccount', 'supplier', 'employee', 'createdBy']);
        $company = $this->getCompany();

        return view('factory-erp.accounts.vouchers.print-expense', compact('voucher', 'company'));
    }

    // ==========================================
    // 4. FUND TRANSFERS (CONTRA VOUCHERS)
    // ==========================================
    public function transferIndex(Request $request)
    {
        $query = AccountVoucher::with(['debitAccount', 'creditAccount'])
            ->where('voucher_type', 'transfer');

        if ($request->filled('from_date')) {
            $query->whereDate('voucher_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('voucher_date', '<=', $request->to_date);
        }

        $totalTransferred = (clone $query)->sum('amount');
        $transfers = $query->orderBy('voucher_date', 'desc')->orderBy('id', 'desc')->paginate(20)->withQueryString();

        $liquidAccounts = Account::where('type', 'asset')
            ->whereIn('category', ['Cash in Hand', 'Bank Account', 'Mobile Banking'])
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        $nextVoucherNo = AccountVoucher::generateVoucherNo('transfer');

        return view('factory-erp.accounts.transfers.index', compact(
            'transfers', 'totalTransferred', 'liquidAccounts', 'nextVoucherNo'
        ));
    }

    public function transferStore(Request $request, AccountingService $service)
    {
        $validated = $request->validate([
            'voucher_date' => 'required|date',
            'from_account_id' => 'required|exists:accounts,id|different:to_account_id',
            'to_account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|in:cash,bank,cheque,bkash,nagad,rocket,other',
            'reference_no' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $voucher = $service->recordTransfer($validated);

        return redirect()->route('factory.accounts.transfers.index')
            ->with('success', "Transfer voucher {$voucher->voucher_no} of ৳ " . number_format($voucher->amount, 2) . " processed successfully!");
    }

    // ==========================================
    // 5. VOUCHER DETAILS & DELETE
    // ==========================================
    public function voucherShow(AccountVoucher $voucher)
    {
        $voucher->load(['debitAccount', 'creditAccount', 'customer', 'supplier', 'employee', 'transactions.account']);
        return response()->json([
            'success' => true,
            'voucher' => $voucher,
        ]);
    }

    public function voucherDestroy(AccountVoucher $voucher, AccountingService $service)
    {
        $voucherNo = $voucher->voucher_no;
        $type = $voucher->voucher_type;
        $service->deleteVoucher($voucher);

        $route = match ($type) {
            'income' => 'factory.accounts.income.index',
            'expense' => 'factory.accounts.expense.index',
            'transfer' => 'factory.accounts.transfers.index',
            default => 'factory.accounts.dashboard',
        };

        return redirect()->route($route)->with('success', "Voucher {$voucherNo} deleted and accounts balances reverted successfully!");
    }

    // ==========================================
    // 6. CHART OF ACCOUNTS & BANK ACCOUNTS
    // ==========================================
    public function chartIndex(Request $request)
    {
        $query = Account::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('account_code', 'like', "%{$s}%")
                  ->orWhere('category', 'like', "%{$s}%")
                  ->orWhere('bank_name', 'like', "%{$s}%")
                  ->orWhere('account_number', 'like', "%{$s}%");
            });
        }

        $accounts = $query->orderBy('account_code')->get();

        // Group counts & totals
        $assets = $accounts->where('type', 'asset');
        $liabilities = $accounts->where('type', 'liability');
        $equities = $accounts->where('type', 'equity');
        $incomes = $accounts->where('type', 'income');
        $expenses = $accounts->where('type', 'expense');

        return view('factory-erp.accounts.chart.index', compact(
            'accounts', 'assets', 'liabilities', 'equities', 'incomes', 'expenses'
        ));
    }

    public function chartStore(Request $request)
    {
        $validated = $request->validate([
            'account_code' => 'required|string|max:20|unique:accounts,account_code',
            'name' => 'required|string|max:255',
            'type' => 'required|in:asset,liability,equity,income,expense',
            'category' => 'required|string|max:100',
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:100',
            'branch_name' => 'nullable|string|max:100',
            'routing_number' => 'nullable|string|max:100',
            'opening_balance' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:500',
        ]);

        $opening = (float)($validated['opening_balance'] ?? 0);
        $account = Account::create([
            'account_code' => $validated['account_code'],
            'name' => $validated['name'],
            'type' => $validated['type'],
            'category' => $validated['category'],
            'bank_name' => $validated['bank_name'] ?? null,
            'account_number' => $validated['account_number'] ?? null,
            'branch_name' => $validated['branch_name'] ?? null,
            'routing_number' => $validated['routing_number'] ?? null,
            'opening_balance' => $opening,
            'current_balance' => $opening,
            'description' => $validated['description'] ?? null,
            'is_system' => false,
            'status' => 'active',
        ]);

        return redirect()->route('factory.accounts.chart.index')
            ->with('success', "Account [{$account->account_code}] {$account->name} created successfully!");
    }

    public function chartUpdate(Request $request, Account $account)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:100',
            'branch_name' => 'nullable|string|max:100',
            'routing_number' => 'nullable|string|max:100',
            'opening_balance' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string|max:500',
        ]);

        $account->update($validated);
        $account->recalculateBalance();

        return redirect()->route('factory.accounts.chart.index')
            ->with('success', "Account [{$account->account_code}] {$account->name} updated successfully!");
    }

    // ==========================================
    // 7. GENERAL LEDGER (খতিয়ান)
    // ==========================================
    public function ledgerIndex(Request $request, AccountingService $service)
    {
        $accounts = Account::orderBy('account_code')->get();
        $selectedAccountId = $request->get('account_id', $accounts->first()?->id);
        
        $fromDate = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', Carbon::now()->format('Y-m-d'));

        $selectedAccount = Account::find($selectedAccountId);
        $ledgerData = null;

        if ($selectedAccount) {
            $ledgerData = $service->getAccountLedger($selectedAccount, $fromDate, $toDate);
        }

        return view('factory-erp.accounts.ledger.index', compact(
            'accounts', 'selectedAccount', 'fromDate', 'toDate', 'ledgerData'
        ));
    }

    // ==========================================
    // 8. FINANCIAL REPORTS (PROFIT & LOSS, TRIAL BALANCE, DAY BOOK)
    // ==========================================
    public function reportsIndex(Request $request, AccountingService $service)
    {
        $tab = $request->get('tab', 'profit_loss');
        $fromDate = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', Carbon::now()->format('Y-m-d'));
        $asOfDate = $request->get('as_of_date', Carbon::now()->format('Y-m-d'));
        $dayBookDate = $request->get('date', Carbon::now()->format('Y-m-d'));

        $profitLossData = null;
        $trialBalanceData = null;
        $dayBookData = null;

        if ($tab === 'profit_loss') {
            $profitLossData = $service->getProfitAndLoss($fromDate, $toDate);
        } elseif ($tab === 'trial_balance') {
            $trialBalanceData = $service->getTrialBalance($asOfDate);
        } elseif ($tab === 'day_book') {
            $dayBookData = $service->getDayBook($dayBookDate);
        }

        return view('factory-erp.accounts.reports.index', compact(
            'tab', 'fromDate', 'toDate', 'asOfDate', 'dayBookDate',
            'profitLossData', 'trialBalanceData', 'dayBookData'
        ));
    }

    // ==========================================
    // 9. SYNC FACTORY ERP DATA INTO ACCOUNTS
    // ==========================================
    public function syncFactoryData(AccountingService $service)
    {
        $result = $service->syncAllFactoryData();

        return redirect()->back()->with('success', "Factory ERP Data Synced! Synced {$result['orders_synced']} Orders, {$result['payrolls_synced']} Payrolls, and {$result['advances_synced']} Advances directly into Accounts.");
    }
}

