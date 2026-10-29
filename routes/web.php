<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FactoryErpController;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

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
    Route::delete('/units/{id}', [FactoryErpController::class, 'unitsDestroy'])->name('units.destroy');

    // 3. Products (Product list, prices, PDF export, edit & delete)
    Route::get('/products', [FactoryErpController::class, 'productsIndex'])->name('products.index');
    Route::post('/products', [FactoryErpController::class, 'productsStore'])->name('products.store');
    Route::put('/products/{id}', [FactoryErpController::class, 'productsUpdate'])->name('products.update');
    Route::delete('/products/{id}', [FactoryErpController::class, 'productsDestroy'])->name('products.destroy');
    Route::get('/products/pdf', [FactoryErpController::class, 'productsPdf'])->name('products.pdf');

    // 4. Customers & History
    Route::get('/customers', [FactoryErpController::class, 'customersIndex'])->name('customers.index');
    Route::post('/customers', [FactoryErpController::class, 'customersStore'])->name('customers.store');
    Route::get('/customers/{id}', [FactoryErpController::class, 'customerDetails'])->name('customers.details');

    // 5. Orders & Invoices
    Route::get('/orders', [FactoryErpController::class, 'ordersIndex'])->name('orders.index');
    Route::get('/orders/create', [FactoryErpController::class, 'ordersCreate'])->name('orders.create');
    Route::post('/orders', [FactoryErpController::class, 'ordersStore'])->name('orders.store');
    Route::get('/orders/{id}/invoice', [FactoryErpController::class, 'orderInvoice'])->name('orders.invoice');
    Route::get('/orders/{id}/invoice/pdf', [FactoryErpController::class, 'orderInvoicePdf'])->name('orders.invoice.pdf');

    // 6. Productions (Auto target from orders -> Add stock)
    Route::get('/productions', [FactoryErpController::class, 'productionsIndex'])->name('productions.index');
    Route::post('/productions', [FactoryErpController::class, 'productionsStore'])->name('productions.store');

    // 7. Deliveries (Check stock, warnings, deduct stock, update due)
    Route::get('/deliveries', [FactoryErpController::class, 'deliveriesIndex'])->name('deliveries.index');
    Route::get('/deliveries/create/{orderId}', [FactoryErpController::class, 'deliveryCreateForOrder'])->name('deliveries.create');
    Route::post('/deliveries', [FactoryErpController::class, 'deliveryStore'])->name('deliveries.store');

    // 8. Stocks (Auto updated & manual adjustments)
    Route::get('/stocks', [FactoryErpController::class, 'stocksIndex'])->name('stocks.index');
    Route::post('/stocks/adjust', [FactoryErpController::class, 'stocksAdjust'])->name('stocks.adjust');
});
