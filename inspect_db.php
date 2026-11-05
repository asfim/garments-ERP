<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "=== COMPANY SETTING ===\n";
$cmp = App\Models\CompanySetting::first();
if ($cmp) {
    print_r($cmp->toArray());
}

echo "\n=== PRODUCTS ===\n";
$prods = App\Models\Product::with('unit')->get();
foreach ($prods as $p) {
    echo "ID: {$p->id} | {$p->name} | Code: {$p->code} | Sell: {$p->sell_price} | Cost: {$p->production_price} | Unit: " . ($p->unit->short_code ?? 'pcs') . "\n";
}

echo "\n=== INCOME ACCOUNTS ===\n";
$incomeAccs = App\Models\Account::where('type', 'income')->get();
foreach ($incomeAccs as $a) {
    echo "ID: {$a->id} | {$a->name} | Code: {$a->account_code} | Cat: {$a->category}\n";
}

echo "\n=== ASSET / CASH ACCOUNTS ===\n";
$assetAccs = App\Models\Account::where('type', 'asset')->get();
foreach ($assetAccs as $a) {
    echo "ID: {$a->id} | {$a->name} | Code: {$a->account_code} | Cat: {$a->category}\n";
}

