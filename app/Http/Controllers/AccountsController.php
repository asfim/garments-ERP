<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\AccountVoucher;
use App\Models\AccountTransaction;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\HrEmployee;
use App\Models\Product;
use App\Models\CompanySetting;
use App\Models\AccountingMapping;
use App\Services\AccountingService;
use App\Services\JournalPostingService;
use App\Services\VoucherReversalService;
use App\Services\FifoInventoryService;
use App\Services\FinancialReportingService;
use App\Services\ProfitabilityReportingService;
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
        $query = AccountVoucher::with(['debitAccount', 'creditAccount', 'customer', 'createdBy'])
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

        // Dropdown data: User rule: Deposit To strictly only one Cash Account (Cash in Hand / প্রধান ক্যাশ)
        $incomeAccounts = Account::where('type', 'income')->where('status', 'active')->orderBy('name')->get();
        $liquidAccounts = Account::where('type', 'asset')
            ->where('category', 'Cash in Hand')
            ->where('status', 'active')
            ->where(function ($q) {
                $q->where('account_code', '1001')
                  ->orWhere('name', 'like', '%প্রধান ক্যাশ%')
                  ->orWhere('name', 'like', '%Cash in Hand%');
            })
            ->take(1)
            ->get();
        if ($liquidAccounts->isEmpty()) {
            $liquidAccounts = Account::where('type', 'asset')->where('category', 'Cash in Hand')->take(1)->get();
        }
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

    public function incomeHeadStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:Operating Income,Service Income,Other Income',
            'account_code' => 'nullable|string|max:20|unique:accounts,account_code',
            'description' => 'nullable|string|max:500',
        ]);

        $code = !empty($validated['account_code']) ? trim($validated['account_code']) : null;
        if (!$code) {
            $existingCodes = Account::where('type', 'income')->pluck('account_code')->toArray();
            for ($i = 4005; $i <= 4098; $i++) {
                if (!in_array((string)$i, $existingCodes)) {
                    $code = (string)$i;
                    break;
                }
            }
            if (!$code) {
                for ($i = 4101; $i <= 4999; $i++) {
                    if (!in_array((string)$i, $existingCodes)) {
                        $code = (string)$i;
                        break;
                    }
                }
            }
        }

        $account = Account::create([
            'account_code' => $code,
            'name' => $validated['name'],
            'type' => 'income',
            'category' => $validated['category'],
            'opening_balance' => 0.00,
            'current_balance' => 0.00,
            'status' => 'active',
            'is_system' => false,
            'description' => $validated['description'] ?? null,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Income Head '{$account->name}' created successfully!",
                'account' => [
                    'id' => $account->id,
                    'account_code' => $account->account_code,
                    'name' => $account->name,
                    'category' => $account->category,
                ]
            ]);
        }

        return redirect()->back()->with('success', "New Income Head '{$account->name}' ({$account->account_code}) added successfully!");
    }

    public function incomeHeadUpdate(Request $request, Account $account)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:Operating Income,Service Income,Other Income',
            'account_code' => 'nullable|string|max:20|unique:accounts,account_code,' . $account->id,
            'description' => 'nullable|string|max:500',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
        ];

        if (!empty($validated['account_code'])) {
            $updateData['account_code'] = trim($validated['account_code']);
        }

        $account->update($updateData);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Income Head '{$account->name}' updated successfully!",
                'account' => [
                    'id' => $account->id,
                    'account_code' => $account->account_code,
                    'name' => $account->name,
                    'category' => $account->category,
                    'description' => $account->description,
                ]
            ]);
        }

        return redirect()->back()->with('success', "Income Head '{$account->name}' updated successfully!");
    }

    public function incomeHeadDestroy(Request $request, Account $account)
    {
        if ($account->is_system) {
            $msg = "সিস্টেম নির্ধারিত প্রধান আয় অ্যাকাউন্ট মুছে ফেলা যাবে না।";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        $hasVouchers = AccountVoucher::where('credit_account_id', $account->id)
            ->orWhere('debit_account_id', $account->id)
            ->exists();
        $hasTxns = $account->transactions()->exists();

        if ($hasVouchers || $hasTxns) {
            $msg = "এই আয়ের খাতের অধীনে ইতিমধ্যে ভাউচার বা লেনদেন রয়েছে, তাই এটি মুছে ফেলা যাবে না।";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        $name = $account->name;
        $id = $account->id;
        $account->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Income Head '{$name}' deleted successfully!",
                'id' => $id,
            ]);
        }

        return redirect()->back()->with('success', "Income Head '{$name}' deleted successfully!");
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
        if ($voucher->source_module === \App\Models\Delivery::class) {
            return redirect()->back()->with('error', "Cannot delete delivery sales voucher [{$voucher->voucher_no}] directly. To cancel this delivery, delete the delivery challan in Deliveries module.");
        }

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
    // 8. FINANCIAL REPORTS (PROFIT & LOSS, TRIAL BALANCE, DAY BOOK, BALANCE SHEET)
    // ==========================================
    public function reportsIndex(Request $request, AccountingService $service, FinancialReportingService $finService)
    {
        $tab = $request->get('tab', 'profit_loss');
        $fromDate = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', Carbon::now()->format('Y-m-d'));
        $asOfDate = $request->get('as_of_date', Carbon::now()->format('Y-m-d'));
        $dayBookDate = $request->get('date', Carbon::now()->format('Y-m-d'));

        $profitLossData = null;
        $trialBalanceData = null;
        $dayBookData = null;
        $balanceSheetData = null;

        if ($tab === 'profit_loss') {
            $profitLossData = $service->getProfitAndLoss($fromDate, $toDate);
        } elseif ($tab === 'trial_balance') {
            $trialBalanceData = $service->getTrialBalance($asOfDate);
        } elseif ($tab === 'day_book') {
            $dayBookData = $service->getDayBook($dayBookDate);
        } elseif ($tab === 'balance_sheet') {
            $balanceSheetData = $finService->getBalanceSheet($asOfDate);
        }

        return view('factory-erp.accounts.reports.index', compact(
            'tab', 'fromDate', 'toDate', 'asOfDate', 'dayBookDate',
            'profitLossData', 'trialBalanceData', 'dayBookData', 'balanceSheetData'
        ));
    }

    // ==========================================
    // 9. MULTI-LINE JOURNAL VOUCHERS
    // ==========================================
    public function journalsIndex(Request $request)
    {
        $query = AccountVoucher::with(['lines.account', 'reversedBy', 'customer', 'supplier', 'employee'])
            ->where('voucher_type', 'journal');

        if ($request->filled('voucher_no')) {
            $query->where('voucher_no', 'like', "%{$request->voucher_no}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('voucher_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('voucher_date', '<=', $request->to_date);
        }

        $totalAmount = (clone $query)->sum('amount');
        $journals = $query->orderBy('voucher_date', 'desc')->orderBy('id', 'desc')->paginate(20)->withQueryString();

        return view('factory-erp.accounts.journals.index', compact('journals', 'totalAmount'));
    }

    public function journalsCreate()
    {
        $accounts = Account::where('status', 'active')->orderBy('account_code')->get();
        $customers = Customer::orderBy('company_name')->get();
        $suppliers = Supplier::orderBy('company_name')->get();
        $employees = HrEmployee::where('status', 'active')->orderBy('first_name')->get();
        $nextVoucherNo = AccountVoucher::generateVoucherNo('journal');

        return view('factory-erp.accounts.journals.create', compact('accounts', 'customers', 'suppliers', 'employees', 'nextVoucherNo'));
    }

    public function journalsStore(Request $request, JournalPostingService $postingService)
    {
        $validated = $request->validate([
            'voucher_date' => 'required|date',
            'notes' => 'required|string|max:500',
            'reference_no' => 'nullable|string|max:100',
            'customer_id' => 'nullable|exists:customers,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'employee_id' => 'nullable|exists:hr_employees,id',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:accounts,id',
            'lines.*.type' => 'required|in:debit,credit',
            'lines.*.amount' => 'required|numeric|min:0.01',
            'lines.*.narration' => 'nullable|string|max:255',
        ]);

        $debitLines = [];
        $creditLines = [];
        $totalDr = 0.0;
        $totalCr = 0.0;

        foreach ($validated['lines'] as $line) {
            $amt = (float) $line['amount'];
            if ($line['type'] === 'debit') {
                $debitLines[] = [
                    'account_id' => $line['account_id'],
                    'amount' => $amt,
                    'narration' => $line['narration'] ?? null,
                ];
                $totalDr += $amt;
            } else {
                $creditLines[] = [
                    'account_id' => $line['account_id'],
                    'amount' => $amt,
                    'narration' => $line['narration'] ?? null,
                ];
                $totalCr += $amt;
            }
        }

        if (count($debitLines) === 0 || count($creditLines) === 0) {
            return redirect()->back()->withInput()->with('error', 'A journal entry must contain at least one debit line and one credit line.');
        }

        if (abs($totalDr - $totalCr) > 0.01) {
            return redirect()->back()->withInput()->with('error', sprintf(
                'Journal is unbalanced! Total Debit (৳ %.2f) must equal Total Credit (৳ %.2f). Difference: ৳ %.2f',
                $totalDr, $totalCr, abs($totalDr - $totalCr)
            ));
        }

        try {
            $voucher = $postingService->postJournal([
                'voucher_type' => 'journal',
                'voucher_date' => $validated['voucher_date'],
                'notes' => $validated['notes'],
                'reference_no' => $validated['reference_no'] ?? null,
                'customer_id' => $validated['customer_id'] ?? null,
                'supplier_id' => $validated['supplier_id'] ?? null,
                'employee_id' => $validated['employee_id'] ?? null,
                'debit_lines' => $debitLines,
                'credit_lines' => $creditLines,
            ]);

            return redirect()->route('factory.accounts.journals.index')
                ->with('success', "Journal voucher {$voucher->voucher_no} posted successfully! Total ৳ " . number_format($voucher->amount, 2));
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to post journal: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 10. VOUCHER REVERSAL
    // ==========================================
    public function voucherReverse(AccountVoucher $voucher, Request $request, VoucherReversalService $reversalService)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:255',
        ]);

        try {
            $reversalVoucher = $reversalService->reverseVoucher($voucher, $validated['reason'] ?? 'Reversed by user');
            return redirect()->back()->with('success', "Voucher {$voucher->voucher_no} reversed successfully via contra-voucher {$reversalVoucher->voucher_no}!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Reversal failed: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 11. ACCOUNTING MAPPINGS
    // ==========================================
    public function mappingsIndex()
    {
        $mappings = AccountingMapping::with('account')->get();
        $accounts = Account::where('status', 'active')->orderBy('account_code')->get();

        return view('factory-erp.accounts.mappings.index', compact('mappings', 'accounts'));
    }

    public function mappingsUpdate(Request $request)
    {
        $validated = $request->validate([
            'mappings' => 'required|array',
            'mappings.*' => 'required|exists:accounts,id',
        ]);

        foreach ($validated['mappings'] as $key => $accountId) {
            AccountingMapping::where('mapping_key', $key)->update(['account_id' => $accountId]);
        }

        return redirect()->route('factory.accounts.mappings.index')
            ->with('success', 'Accounting system mappings updated successfully!');
    }

    // ==========================================
    // 12. INVENTORY VALUATION (FIFO LAYERS)
    // ==========================================
    public function inventoryValuationIndex(Request $request, FifoInventoryService $fifoService)
    {
        $productId = $request->filled('product_id') ? (int)$request->product_id : null;
        $valuationData = $fifoService->getInventoryValuationReport($productId);
        $products = Product::orderBy('name')->get();

        return view('factory-erp.accounts.inventory-valuation.index', compact('valuationData', 'products', 'productId'));
    }

    // ==========================================
    // 13. PROFITABILITY REPORTS
    // ==========================================
    public function profitabilityIndex(Request $request, ProfitabilityReportingService $profitService)
    {
        $tab = $request->get('tab', 'product');
        $fromDate = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', Carbon::now()->format('Y-m-d'));
        $productId = $request->filled('product_id') ? (int)$request->product_id : null;
        $customerId = $request->filled('customer_id') ? (int)$request->customer_id : null;

        $productData = null;
        $deliveryData = null;

        if ($tab === 'product') {
            $productData = $profitService->getProductProfitability($fromDate, $toDate, $productId);
        } else {
            $deliveryData = $profitService->getDeliveryProfitability($fromDate, $toDate, $customerId);
        }

        $products = Product::orderBy('name')->get();
        $customers = Customer::orderBy('company_name')->get();

        return view('factory-erp.accounts.profit-reports.index', compact(
            'tab', 'fromDate', 'toDate', 'productId', 'customerId',
            'productData', 'deliveryData', 'products', 'customers'
        ));
    }

    // ==========================================
    // 14. SUB-LEDGERS (CUSTOMER, SUPPLIER, EMPLOYEE)
    // ==========================================
    public function customerLedgerIndex(Request $request, FinancialReportingService $reportingService)
    {
        $customers = Customer::orderBy('company_name')->get();
        $selectedCustomerId = $request->get('customer_id', $customers->first()?->id);
        $fromDate = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', Carbon::now()->format('Y-m-d'));

        $selectedCustomer = Customer::find($selectedCustomerId);
        $ledgerData = null;

        if ($selectedCustomer) {
            $ledgerData = $reportingService->getCustomerLedger($selectedCustomer, $fromDate, $toDate);
        }

        return view('factory-erp.accounts.ledgers.customer', compact('customers', 'selectedCustomer', 'fromDate', 'toDate', 'ledgerData'));
    }

    public function supplierLedgerIndex(Request $request, FinancialReportingService $reportingService)
    {
        $suppliers = Supplier::orderBy('company_name')->get();
        $selectedSupplierId = $request->get('supplier_id', $suppliers->first()?->id);
        $fromDate = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', Carbon::now()->format('Y-m-d'));

        $selectedSupplier = Supplier::find($selectedSupplierId);
        $ledgerData = null;

        if ($selectedSupplier) {
            $ledgerData = $reportingService->getSupplierLedger($selectedSupplier, $fromDate, $toDate);
        }

        return view('factory-erp.accounts.ledgers.supplier', compact('suppliers', 'selectedSupplier', 'fromDate', 'toDate', 'ledgerData'));
    }

    public function employeeLedgerIndex(Request $request, FinancialReportingService $reportingService)
    {
        $employees = HrEmployee::orderBy('first_name')->get();
        $selectedEmployeeId = $request->get('employee_id', $employees->first()?->id);
        $fromDate = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', Carbon::now()->format('Y-m-d'));

        $selectedEmployee = HrEmployee::find($selectedEmployeeId);
        $ledgerData = null;

        if ($selectedEmployee) {
            $ledgerData = $reportingService->getEmployeeLedger($selectedEmployee, $fromDate, $toDate);
        }

        return view('factory-erp.accounts.ledgers.employee', compact('employees', 'selectedEmployee', 'fromDate', 'toDate', 'ledgerData'));
    }

    // ==========================================
    // 15. SYNC FACTORY ERP DATA INTO ACCOUNTS
    // ==========================================
    public function syncFactoryData(AccountingService $service)
    {
        $result = $service->syncAllFactoryData();

        return redirect()->back()->with('success', "Factory ERP Data Synced! Synced {$result['orders_synced']} Orders, {$result['payrolls_synced']} Payrolls, and {$result['advances_synced']} Advances directly into Accounts.");
    }
}

