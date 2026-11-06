<?php
$files = [
    'resources/views/factory-erp/accounts/income/index.blade.php',
    'resources/views/factory-erp/accounts/expense/index.blade.php',
    'resources/views/factory-erp/accounts/ledger/index.blade.php',
    'resources/views/factory-erp/accounts/ledgers/customer.blade.php',
    'resources/views/factory-erp/accounts/ledgers/supplier.blade.php',
    'resources/views/factory-erp/accounts/ledgers/employee.blade.php',
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    echo "=== File: $file ===\n";
    $lines = file($file);
    foreach ($lines as $idx => $line) {
        if (preg_match('/[\x{0980}-\x{09FF}]/u', $line)) {
            echo ($idx + 1) . ": " . trim($line) . "\n";
        }
    }
}
