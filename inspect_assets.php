<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$accounts = App\Models\Account::where('type', 'asset')->get(['id', 'account_code', 'name', 'category', 'current_balance']);
echo json_encode($accounts, JSON_PRETTY_PRINT);
