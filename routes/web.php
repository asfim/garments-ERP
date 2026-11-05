<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FactoryErpController;
use App\Http\Controllers\HrController;
use App\Http\Controllers\AccountsController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::match(['GET', 'POST'], '/iclock/cdata', [HrController::class, 'devicesAdmsPush'])->name('iclock.cdata');

// ==========================================
// FACTORY ERP ROUTES
// ==========================================
Route::prefix('factory')->name('factory.')->group(function () {
    
    // 1. Settings (Company info for PDF/Invoices)
    Route::get('/settings', [FactoryErpController::class, 'settingsIndex'])->name('settings.index');
    Route::post('/settings', [FactoryErpController::class, 'settingsUpdate'])->name('settings.update');

    // 2. Units (Dynamic Unit Management)
    Route::get('/units', [FactoryErpController::class, 'unitsIndex'])->name('units.index');
    Route::post('/units', [FactoryErpController::class, 'unitsStore'])->name('units.store');
    Route::put('/units/{id}', [FactoryErpController::class, 'unitsUpdate'])->name('units.update');
    Route::delete('/units/{id}', [FactoryErpController::class, 'unitsDestroy'])->name('units.destroy');

    // 3. Products (Product list, prices, PDF export, edit & delete)
    Route::get('/products', [FactoryErpController::class, 'productsIndex'])->name('products.index');
    Route::post('/products', [FactoryErpController::class, 'productsStore'])->name('products.store');
    Route::put('/products/{id}', [FactoryErpController::class, 'productsUpdate'])->name('products.update');
    Route::delete('/products/{id}', [FactoryErpController::class, 'productsDestroy'])->name('products.destroy');
    Route::get('/products/pdf', [FactoryErpController::class, 'productsPdf'])->name('products.pdf');

    // 4. Customers & History (with Edit & Delete & Due Payments)
    Route::get('/customers', [FactoryErpController::class, 'customersIndex'])->name('customers.index');
    Route::get('/customers/due-payments', [FactoryErpController::class, 'customersDueIndex'])->name('customers.due.index');
    Route::get('/customers/{id}/due-invoices', [FactoryErpController::class, 'customerDueInvoices'])->name('customers.due.invoices');
    Route::post('/customers/{id}/due-payment', [FactoryErpController::class, 'customerDuePaymentStore'])->name('customers.due.payment.store');
    Route::post('/customers/{customerId}/invoice-payment/{orderId}', [FactoryErpController::class, 'customerSingleInvoicePayment'])->name('customers.invoice.payment.store');
    Route::post('/customers', [FactoryErpController::class, 'customersStore'])->name('customers.store');
    Route::get('/customers/{id}', [FactoryErpController::class, 'customerDetails'])->name('customers.details');
    Route::put('/customers/{id}', [FactoryErpController::class, 'customersUpdate'])->name('customers.update');
    Route::delete('/customers/{id}', [FactoryErpController::class, 'customersDestroy'])->name('customers.destroy');

    // 4.1 Suppliers (Supplier name, phone, address, product dropdown with live search, CSV & PDF Export, Edit & Delete)
    Route::get('/suppliers', [FactoryErpController::class, 'suppliersIndex'])->name('suppliers.index');
    Route::get('/suppliers/export/csv', [FactoryErpController::class, 'suppliersExportCsv'])->name('suppliers.export.csv');
    Route::get('/suppliers/export/pdf', [FactoryErpController::class, 'suppliersExportPdf'])->name('suppliers.export.pdf');
    Route::post('/suppliers', [FactoryErpController::class, 'suppliersStore'])->name('suppliers.store');
    Route::put('/suppliers/{id}', [FactoryErpController::class, 'suppliersUpdate'])->name('suppliers.update');
    Route::delete('/suppliers/{id}', [FactoryErpController::class, 'suppliersDestroy'])->name('suppliers.destroy');

    // 5. Orders & Invoices (with Edit, Delete & CSV / PDF Export)
    Route::get('/orders', [FactoryErpController::class, 'ordersIndex'])->name('orders.index');
    Route::get('/orders/export/csv', [FactoryErpController::class, 'ordersExportCsv'])->name('orders.export.csv');
    Route::get('/orders/export/pdf', [FactoryErpController::class, 'ordersExportPdf'])->name('orders.export.pdf');
    Route::get('/orders/create', [FactoryErpController::class, 'ordersCreate'])->name('orders.create');
    Route::post('/orders', [FactoryErpController::class, 'ordersStore'])->name('orders.store');
    Route::put('/orders/{id}', [FactoryErpController::class, 'ordersUpdate'])->name('orders.update');
    Route::delete('/orders/{id}', [FactoryErpController::class, 'ordersDestroy'])->name('orders.destroy');
    Route::get('/orders/{id}/invoice', [FactoryErpController::class, 'orderInvoice'])->name('orders.invoice');
    Route::get('/orders/{id}/invoice/pdf', [FactoryErpController::class, 'orderInvoicePdf'])->name('orders.invoice.pdf');

    // 6. Productions (Auto target from orders -> Add stock, with Edit & Delete & CSV / PDF Export)
    Route::get('/productions', [FactoryErpController::class, 'productionsIndex'])->name('productions.index');
    Route::get('/productions/export/csv', [FactoryErpController::class, 'productionsExportCsv'])->name('productions.export.csv');
    Route::get('/productions/export/pdf', [FactoryErpController::class, 'productionsExportPdf'])->name('productions.export.pdf');
    Route::post('/productions', [FactoryErpController::class, 'productionsStore'])->name('productions.store');
    Route::put('/productions/{id}', [FactoryErpController::class, 'productionsUpdate'])->name('productions.update');
    Route::delete('/productions/{id}', [FactoryErpController::class, 'productionsDestroy'])->name('productions.destroy');

    // 7. Deliveries (Check stock, warnings, deduct stock, update due, with Delete/Rollback & CSV / PDF Export)
    Route::get('/deliveries', [FactoryErpController::class, 'deliveriesIndex'])->name('deliveries.index');
    Route::get('/deliveries/export/csv', [FactoryErpController::class, 'deliveriesExportCsv'])->name('deliveries.export.csv');
    Route::get('/deliveries/export/pdf', [FactoryErpController::class, 'deliveriesExportPdf'])->name('deliveries.export.pdf');
    Route::get('/deliveries/create/{orderId}', [FactoryErpController::class, 'deliveryCreateForOrder'])->name('deliveries.create');
    Route::post('/deliveries', [FactoryErpController::class, 'deliveryStore'])->name('deliveries.store');
    Route::delete('/deliveries/{id}', [FactoryErpController::class, 'deliveriesDestroy'])->name('deliveries.destroy');

    // 8. Stocks (Auto updated & manual adjustments & CSV / PDF Export with Edit & Delete)
    Route::get('/stocks', [FactoryErpController::class, 'stocksIndex'])->name('stocks.index');
    Route::get('/stocks/export/csv', [FactoryErpController::class, 'stocksExportCsv'])->name('stocks.export.csv');
    Route::get('/stocks/export/pdf', [FactoryErpController::class, 'stocksExportPdf'])->name('stocks.export.pdf');
    Route::post('/stocks/adjust', [FactoryErpController::class, 'stocksAdjust'])->name('stocks.adjust');
    Route::put('/stocks/balance/{productId}', [FactoryErpController::class, 'stocksBalanceUpdate'])->name('stocks.balance.update');
    Route::delete('/stocks/balance/{productId}', [FactoryErpController::class, 'stocksBalanceReset'])->name('stocks.balance.reset');
    Route::put('/stocks/logs/{id}', [FactoryErpController::class, 'stocksLogUpdate'])->name('stocks.logs.update');
    Route::delete('/stocks/logs/{id}', [FactoryErpController::class, 'stocksLogDestroy'])->name('stocks.logs.destroy');

    // ==========================================
    // 9. HR & PAYROLL MANAGEMENT ROUTES
    // ==========================================
    Route::prefix('hr')->name('hr.')->group(function () {
        // HR Dashboard
        Route::get('/', [HrController::class, 'index'])->name('index');

        // Employees
        Route::get('/employees', [HrController::class, 'employeesIndex'])->name('employees.index');
        Route::get('/employees/create', [HrController::class, 'employeesCreate'])->name('employees.create');
        Route::get('/employees/export/csv', [HrController::class, 'employeesExportCsv'])->name('employees.export.csv');
        Route::get('/employees/export/pdf', [HrController::class, 'employeesExportPdf'])->name('employees.export.pdf');
        Route::post('/employees', [HrController::class, 'employeesStore'])->name('employees.store');
        Route::get('/employees/{id}', [HrController::class, 'employeesShow'])->name('employees.show');
        Route::get('/employees/{id}/edit', [HrController::class, 'employeesEdit'])->name('employees.edit');
        Route::put('/employees/{id}', [HrController::class, 'employeesUpdate'])->name('employees.update');
        Route::delete('/employees/{id}', [HrController::class, 'employeesDestroy'])->name('employees.destroy');

        // Departments
        Route::get('/departments', [HrController::class, 'departmentsIndex'])->name('departments.index');
        Route::get('/departments/{departmentId}/designations', [HrController::class, 'getDesignationsByDepartment'])->name('departments.designations');
        Route::post('/departments', [HrController::class, 'departmentsStore'])->name('departments.store');
        Route::put('/departments/{id}', [HrController::class, 'departmentsUpdate'])->name('departments.update');
        Route::delete('/departments/{id}', [HrController::class, 'departmentsDestroy'])->name('departments.destroy');

        // Designations
        Route::get('/designations', [HrController::class, 'designationsIndex'])->name('designations.index');
        Route::post('/designations', [HrController::class, 'designationsStore'])->name('designations.store');
        Route::put('/designations/{id}', [HrController::class, 'designationsUpdate'])->name('designations.update');
        Route::delete('/designations/{id}', [HrController::class, 'designationsDestroy'])->name('designations.destroy');

        // Attendance
        Route::get('/attendance', [HrController::class, 'attendanceIndex'])->name('attendance.index');
        Route::post('/attendance', [HrController::class, 'attendanceStore'])->name('attendance.store');
        Route::post('/attendance/bulk', [HrController::class, 'attendanceBulkStore'])->name('attendance.bulk.store');
        Route::get('/attendance/export/csv', [HrController::class, 'attendanceExportCsv'])->name('attendance.export.csv');
        Route::get('/attendance/export/pdf', [HrController::class, 'attendanceExportPdf'])->name('attendance.export.pdf');

        // Leave Management
        Route::get('/leaves', [HrController::class, 'leavesIndex'])->name('leaves.index');
        Route::post('/leaves', [HrController::class, 'leavesStore'])->name('leaves.store');
        Route::patch('/leaves/{id}/status', [HrController::class, 'leavesUpdateStatus'])->name('leaves.status.update');
        Route::delete('/leaves/{id}', [HrController::class, 'leavesDestroy'])->name('leaves.destroy');

        // Advance Salary & Loans
        Route::get('/advances', [HrController::class, 'advancesIndex'])->name('advances.index');
        Route::post('/advances', [HrController::class, 'advancesStore'])->name('advances.store');
        Route::patch('/advances/{id}/status', [HrController::class, 'advancesUpdateStatus'])->name('advances.status.update');
        Route::delete('/advances/{id}', [HrController::class, 'advancesDestroy'])->name('advances.destroy');

        // Biometric Devices (ZKTeco)
        Route::get('/devices', [HrController::class, 'devicesIndex'])->name('devices.index');
        Route::post('/devices', [HrController::class, 'devicesStore'])->name('devices.store');
        Route::put('/devices/{id}', [HrController::class, 'devicesUpdate'])->name('devices.update');
        Route::delete('/devices/{id}', [HrController::class, 'devicesDestroy'])->name('devices.destroy');
        Route::post('/devices/{id}/test', [HrController::class, 'devicesTestConnection'])->name('devices.test');
        Route::post('/devices/{id}/sync', [HrController::class, 'devicesLiveSync'])->name('devices.sync');
        Route::match(['GET', 'POST'], '/devices/auto-sync', [HrController::class, 'devicesAutoSync'])->name('devices.auto-sync');
        Route::post('/devices/import-usb', [HrController::class, 'devicesImportUsb'])->name('devices.import-usb');
        Route::match(['GET', 'POST'], '/devices/adms-push', [HrController::class, 'devicesAdmsPush'])->name('devices.adms-push');

        // Payroll & Payslips
        Route::get('/payroll', [HrController::class, 'payrollIndex'])->name('payroll.index');
        Route::post('/payroll/generate', [HrController::class, 'payrollGenerate'])->name('payroll.generate');
        Route::put('/payroll/{id}/payment', [HrController::class, 'payrollUpdatePayment'])->name('payroll.payment.update');
        Route::get('/payroll/{id}/payslip', [HrController::class, 'payrollPayslip'])->name('payroll.payslip');
        Route::get('/payroll/{id}/payslip/pdf', [HrController::class, 'payrollPayslipPdf'])->name('payroll.payslip.pdf');
        Route::get('/payroll/export/csv', [HrController::class, 'payrollExportCsv'])->name('payroll.export.csv');
        Route::get('/payroll/export/pdf', [HrController::class, 'payrollExportPdf'])->name('payroll.export.pdf');
    });

    // ==========================================
    // 8. ACCOUNTS & FINANCE MODULE
    // ==========================================
    Route::prefix('accounts')->name('accounts.')->group(function () {
        // Accounts Dashboard
        Route::get('/', [AccountsController::class, 'dashboard'])->name('dashboard');

        // Income Management
        Route::get('/income', [AccountsController::class, 'incomeIndex'])->name('income.index');
        Route::post('/income', [AccountsController::class, 'incomeStore'])->name('income.store');
        Route::post('/income-heads', [AccountsController::class, 'incomeHeadStore'])->name('income-heads.store');
        Route::get('/income/{voucher}/print', [AccountsController::class, 'incomePrint'])->name('income.print');

        // Expense Management
        Route::get('/expense', [AccountsController::class, 'expenseIndex'])->name('expense.index');
        Route::post('/expense', [AccountsController::class, 'expenseStore'])->name('expense.store');
        Route::get('/expense/{voucher}/print', [AccountsController::class, 'expensePrint'])->name('expense.print');

        // Fund Transfers (Contra Vouchers)
        Route::get('/transfers', [AccountsController::class, 'transferIndex'])->name('transfers.index');
        Route::post('/transfers', [AccountsController::class, 'transferStore'])->name('transfers.store');

        // Universal Voucher Show, Reversal & Delete
        Route::get('/vouchers/{voucher}', [AccountsController::class, 'voucherShow'])->name('vouchers.show');
        Route::post('/vouchers/{voucher}/reverse', [AccountsController::class, 'voucherReverse'])->name('vouchers.reverse');
        Route::delete('/vouchers/{voucher}', [AccountsController::class, 'voucherDestroy'])->name('vouchers.destroy');

        // Multi-line Journal Vouchers
        Route::get('/journals', [AccountsController::class, 'journalsIndex'])->name('journals.index');
        Route::get('/journals/create', [AccountsController::class, 'journalsCreate'])->name('journals.create');
        Route::post('/journals', [AccountsController::class, 'journalsStore'])->name('journals.store');

        // Chart of Accounts
        Route::get('/chart', [AccountsController::class, 'chartIndex'])->name('chart.index');
        Route::post('/chart', [AccountsController::class, 'chartStore'])->name('chart.store');
        Route::put('/chart/{account}', [AccountsController::class, 'chartUpdate'])->name('chart.update');

        // Accounting System Mappings
        Route::get('/mappings', [AccountsController::class, 'mappingsIndex'])->name('mappings.index');
        Route::post('/mappings', [AccountsController::class, 'mappingsUpdate'])->name('mappings.update');

        // General Ledger (খতিয়ান)
        Route::get('/ledger', [AccountsController::class, 'ledgerIndex'])->name('ledger.index');

        // Partner Sub-Ledgers
        Route::get('/ledgers/customer', [AccountsController::class, 'customerLedgerIndex'])->name('ledgers.customer');
        Route::get('/ledgers/supplier', [AccountsController::class, 'supplierLedgerIndex'])->name('ledgers.supplier');
        Route::get('/ledgers/employee', [AccountsController::class, 'employeeLedgerIndex'])->name('ledgers.employee');

        // FIFO Inventory Valuation
        Route::get('/inventory-valuation', [AccountsController::class, 'inventoryValuationIndex'])->name('inventory-valuation.index');

        // Profitability Reports (Product-wise & Delivery-wise)
        Route::get('/profitability', [AccountsController::class, 'profitabilityIndex'])->name('profitability.index');

        // Financial Reports (Profit & Loss, Trial Balance, Day Book, Balance Sheet)
        Route::get('/reports', [AccountsController::class, 'reportsIndex'])->name('reports.index');

        // Sync Factory ERP Data
        Route::match(['GET', 'POST'], '/sync-factory', [AccountsController::class, 'syncFactoryData'])->name('sync-factory');
    });
});
