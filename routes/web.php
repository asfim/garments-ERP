<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FactoryErpController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

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

    // 4. Customers & History (with Edit & Delete)
    Route::get('/customers', [FactoryErpController::class, 'customersIndex'])->name('customers.index');
    Route::post('/customers', [FactoryErpController::class, 'customersStore'])->name('customers.store');
    Route::get('/customers/{id}', [FactoryErpController::class, 'customerDetails'])->name('customers.details');
    Route::put('/customers/{id}', [FactoryErpController::class, 'customersUpdate'])->name('customers.update');
    Route::delete('/customers/{id}', [FactoryErpController::class, 'customersDestroy'])->name('customers.destroy');

    // 5. Orders & Invoices (with Edit, Delete & CSV Export)
    Route::get('/orders', [FactoryErpController::class, 'ordersIndex'])->name('orders.index');
    Route::get('/orders/export/csv', [FactoryErpController::class, 'ordersExportCsv'])->name('orders.export.csv');
    Route::get('/orders/create', [FactoryErpController::class, 'ordersCreate'])->name('orders.create');
    Route::post('/orders', [FactoryErpController::class, 'ordersStore'])->name('orders.store');
    Route::put('/orders/{id}', [FactoryErpController::class, 'ordersUpdate'])->name('orders.update');
    Route::delete('/orders/{id}', [FactoryErpController::class, 'ordersDestroy'])->name('orders.destroy');
    Route::get('/orders/{id}/invoice', [FactoryErpController::class, 'orderInvoice'])->name('orders.invoice');
    Route::get('/orders/{id}/invoice/pdf', [FactoryErpController::class, 'orderInvoicePdf'])->name('orders.invoice.pdf');

    // 6. Productions (Auto target from orders -> Add stock, with Edit & Delete & CSV Export)
    Route::get('/productions', [FactoryErpController::class, 'productionsIndex'])->name('productions.index');
    Route::get('/productions/export/csv', [FactoryErpController::class, 'productionsExportCsv'])->name('productions.export.csv');
    Route::post('/productions', [FactoryErpController::class, 'productionsStore'])->name('productions.store');
    Route::put('/productions/{id}', [FactoryErpController::class, 'productionsUpdate'])->name('productions.update');
    Route::delete('/productions/{id}', [FactoryErpController::class, 'productionsDestroy'])->name('productions.destroy');

    // 7. Deliveries (Check stock, warnings, deduct stock, update due, with Delete/Rollback & CSV Export)
    Route::get('/deliveries', [FactoryErpController::class, 'deliveriesIndex'])->name('deliveries.index');
    Route::get('/deliveries/export/csv', [FactoryErpController::class, 'deliveriesExportCsv'])->name('deliveries.export.csv');
    Route::get('/deliveries/create/{orderId}', [FactoryErpController::class, 'deliveryCreateForOrder'])->name('deliveries.create');
    Route::post('/deliveries', [FactoryErpController::class, 'deliveryStore'])->name('deliveries.store');
    Route::delete('/deliveries/{id}', [FactoryErpController::class, 'deliveriesDestroy'])->name('deliveries.destroy');

    // 8. Stocks (Auto updated & manual adjustments)
    Route::get('/stocks', [FactoryErpController::class, 'stocksIndex'])->name('stocks.index');
    Route::post('/stocks/adjust', [FactoryErpController::class, 'stocksAdjust'])->name('stocks.adjust');
});
