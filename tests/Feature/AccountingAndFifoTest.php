<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Account;
use App\Models\AccountVoucher;
use App\Models\Product;
use App\Models\Production;
use App\Models\Stock;
use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\Customer;
use App\Models\InventoryCostLayer;
use App\Models\InventoryCostAllocation;
use App\Services\JournalPostingService;
use App\Services\VoucherReversalService;
use App\Services\FifoInventoryService;
use App\Services\FinancialReportingService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use InvalidArgumentException;

class AccountingAndFifoTest extends TestCase
{
    protected JournalPostingService $journalService;
    protected VoucherReversalService $reversalService;
    protected FifoInventoryService $fifoService;
    protected FinancialReportingService $reportingService;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'erp',
        ]);
        \Illuminate\Support\Facades\DB::purge('mysql');
        \Illuminate\Support\Facades\DB::connection('mysql')->beginTransaction();

        $this->journalService = app(JournalPostingService::class);
        $this->reversalService = app(VoucherReversalService::class);
        $this->fifoService = app(FifoInventoryService::class);
        $this->reportingService = app(FinancialReportingService::class);
    }

    protected function tearDown(): void
    {
        \Illuminate\Support\Facades\DB::connection('mysql')->rollBack();
        parent::tearDown();
    }

    /**
     * Test 1: Balanced journal voucher posts cleanly with equal total debit and credit
     */
    public function test_balanced_journal_posts_successfully(): void
    {
        $cashAcc = Account::where('account_code', '1001')->first() ?? Account::create([
            'account_code' => '1001',
            'name' => 'Cash in Hand',
            'type' => 'asset',
            'status' => 'active'
        ]);

        $salesAcc = Account::where('account_code', '4001')->first() ?? Account::create([
            'account_code' => '4001',
            'name' => 'Sales Revenue',
            'type' => 'income',
            'status' => 'active'
        ]);

        $voucher = $this->journalService->postJournal([
            'voucher_type' => 'journal',
            'voucher_date' => date('Y-m-d'),
            'notes' => 'Test Balanced Sales Journal',
            'debit_lines' => [
                ['account_id' => $cashAcc->id, 'amount' => 5000.00, 'narration' => 'Cash received']
            ],
            'credit_lines' => [
                ['account_id' => $salesAcc->id, 'amount' => 5000.00, 'narration' => 'Product sales']
            ]
        ]);

        $this->assertNotNull($voucher);
        $this->assertEquals('posted', $voucher->status);
        $this->assertEquals(5000.00, (float)$voucher->amount);
        $this->assertCount(2, $voucher->lines);
    }

    /**
     * Test 2: Unbalanced journal throws InvalidArgumentException and does not post
     */
    public function test_unbalanced_journal_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $cashAcc = Account::where('account_code', '1001')->first();
        $salesAcc = Account::where('account_code', '4001')->first();

        $this->journalService->postJournal([
            'voucher_type' => 'journal',
            'voucher_date' => date('Y-m-d'),
            'notes' => 'Unbalanced Journal',
            'debit_lines' => [
                ['account_id' => $cashAcc->id, 'amount' => 5000.00]
            ],
            'credit_lines' => [
                ['account_id' => $salesAcc->id, 'amount' => 4500.00] // 500 discrepancy
            ]
        ]);
    }

    /**
     * Test 3: Multi-line compound journal with multiple debits and credits
     */
    public function test_multi_line_compound_journal_posts_properly(): void
    {
        $cashAcc = Account::where('account_code', '1001')->first();
        $bankAcc = Account::where('account_code', '1002')->first() ?? Account::create([
            'account_code' => '1002',
            'name' => 'Bank Account',
            'type' => 'asset',
            'status' => 'active'
        ]);
        $salesAcc = Account::where('account_code', '4001')->first();

        $voucher = $this->journalService->postJournal([
            'voucher_type' => 'journal',
            'voucher_date' => date('Y-m-d'),
            'notes' => 'Compound Split Payment Journal',
            'debit_lines' => [
                ['account_id' => $cashAcc->id, 'amount' => 3000.00],
                ['account_id' => $bankAcc->id, 'amount' => 7000.00],
            ],
            'credit_lines' => [
                ['account_id' => $salesAcc->id, 'amount' => 10000.00],
            ]
        ]);

        $this->assertEquals(10000.00, (float)$voucher->amount);
        $this->assertCount(3, $voucher->lines);
    }

    /**
     * Test 4: Voucher Reversal creates contra-voucher and marks original as reversed
     */
    public function test_voucher_reversal_creates_opposite_contra_voucher(): void
    {
        $cashAcc = Account::where('account_code', '1001')->first();
        $salesAcc = Account::where('account_code', '4001')->first();

        $original = $this->journalService->postJournal([
            'voucher_type' => 'journal',
            'voucher_date' => date('Y-m-d'),
            'notes' => 'To Be Reversed',
            'debit_lines' => [
                ['account_id' => $cashAcc->id, 'amount' => 2000.00]
            ],
            'credit_lines' => [
                ['account_id' => $salesAcc->id, 'amount' => 2000.00]
            ]
        ]);

        $reversal = $this->reversalService->reverseVoucher($original, 'Incorrect entry test');

        $this->assertNotNull($reversal);
        $this->assertEquals('reversed', $original->fresh()->status);
        $this->assertTrue((bool)$original->fresh()->is_reversed);
        $this->assertEquals($reversal->id, $original->fresh()->reversed_by_voucher_id);

        // Contra voucher debits Sales and credits Cash
        $contraDebit = $reversal->lines()->where('type', 'debit')->first();
        $contraCredit = $reversal->lines()->where('type', 'credit')->first();

        $this->assertEquals($salesAcc->id, $contraDebit->account_id);
        $this->assertEquals($cashAcc->id, $contraCredit->account_id);
    }

    /**
     * Test 5: FIFO Layer Allocation consumes oldest batches first and calculates correct COGS
     */
    public function test_fifo_inventory_consumes_oldest_layers_first(): void
    {
        $product = Product::create([
            'name' => 'FIFO Test Product ' . uniqid(),
            'unit_id' => 1,
            'production_price' => 200.00,
            'sell_price' => 350.00,
            'description' => 'Test Product',
        ]);

        Stock::create([
            'product_id' => $product->id,
            'current_stock' => 200.0,
            'total_produced' => 200.0,
            'total_delivered' => 0.0,
        ]);

        // Layer 1: 100 units @ 200 BDT
        $layer1 = $this->fifoService->createCostLayer(
            product: $product,
            quantity: 100,
            unitCost: 200.00,
            batchNumber: 'TEST-BATCH-001-' . uniqid(),
            receivedDate: '2026-01-01'
        );

        // Layer 2: 100 units @ 250 BDT
        $layer2 = $this->fifoService->createCostLayer(
            product: $product,
            quantity: 100,
            unitCost: 250.00,
            batchNumber: 'TEST-BATCH-002-' . uniqid(),
            receivedDate: '2026-01-02'
        );

        // Create dummy delivery item for 150 units
        $customer = Customer::first();
        $order = \App\Models\Order::first();
        $delivery = Delivery::create([
            'delivery_number' => 'DEL-FIFO-TEST-' . time(),
            'order_id' => $order?->id ?? 1,
            'customer_id' => $customer?->id ?? 1,
            'delivery_date' => date('Y-m-d'),
            'total_delivered_amount' => 150 * 350,
            'notes' => 'Test Delivery',
        ]);

        $orderItem = \App\Models\OrderItem::first();
        $deliveryItem = DeliveryItem::create([
            'delivery_id' => $delivery->id,
            'order_item_id' => $orderItem?->id ?? 1,
            'product_id' => $product->id,
            'qty' => 150,
            'sell_price' => 350.00,
            'total_price' => 150 * 350,
        ]);

        // Allocate 150 units via FIFO
        $result = $this->fifoService->allocateFifo($product, 150, $deliveryItem);

        // Expected COGS: 100 * 200 (20,000) + 50 * 250 (12,500) = 32,500 BDT
        $this->assertEquals(32500.00, $result['total_cogs']);
        $this->assertCount(2, $result['allocations']);

        // Check Layer 1 exhausted
        $layer1->refresh();
        $this->assertEquals(0.0, (float)$layer1->remaining_qty);
        $this->assertEquals('exhausted', $layer1->status);

        // Check Layer 2 partially consumed
        $layer2->refresh();
        $this->assertEquals(50.0, (float)$layer2->remaining_qty);
        $this->assertEquals('active', $layer2->status);

        // Rollback test: deleting delivery item should restore layers back to 100 each
        $this->fifoService->rollbackAllocations($deliveryItem);

        $layer1->refresh();
        $layer2->refresh();
        $this->assertEquals(100.0, (float)$layer1->remaining_qty);
        $this->assertEquals('active', $layer1->status);
        $this->assertEquals(100.0, (float)$layer2->remaining_qty);
        $this->assertEquals('active', $layer2->status);

        // Clean up test data
        $deliveryItem->delete();
        $delivery->delete();
        $layer1->delete();
        $layer2->delete();
        Stock::where('product_id', $product->id)->delete();
        $product->delete();
    }

    /**
     * Test 6: Reconcile all products verifies 0 discrepancies between FIFO layers and Stock table
     */
    public function test_stock_reconciliation_verifies_zero_discrepancies(): void
    {
        $discrepancies = $this->fifoService->reconcileAllProducts();
        $this->assertEmpty($discrepancies, 'FIFO inventory layers must match Stock on hand exactly.');
    }

    /**
     * Test 7: Compound Payroll Journal relieves Advance Receivable and Cash
     */
    public function test_payroll_compound_journal_relieves_advance_receivable(): void
    {
        $salaryExpAcc = Account::where('account_code', '5003')->first();
        $advanceAcc = Account::where('account_code', '1060')->first();
        $cashAcc = Account::where('account_code', '1001')->first();

        // Gross Salary: 30,000 | Advance Deduction: 5,000 | Net Paid: 25,000
        $voucher = $this->journalService->postJournal([
            'voucher_type' => 'journal',
            'voucher_date' => date('Y-m-d'),
            'notes' => 'Payroll Payment with Advance Recovery',
            'debit_lines' => [
                ['account_id' => $salaryExpAcc->id, 'amount' => 30000.00, 'narration' => 'Gross salary expense']
            ],
            'credit_lines' => [
                ['account_id' => $advanceAcc->id, 'amount' => 5000.00, 'narration' => 'Advance recovery'],
                ['account_id' => $cashAcc->id, 'amount' => 25000.00, 'narration' => 'Net salary paid in cash']
            ]
        ]);

        $this->assertEquals(30000.00, (float)$voucher->amount);
        $this->assertCount(3, $voucher->lines);
        $this->assertEquals('posted', $voucher->status);
    }

    /**
     * Test 8: Trial balance equality check (Total Debit equals Total Credit)
     */
    public function test_trial_balance_is_strictly_balanced(): void
    {
        $trialBalance = $this->reportingService->getTrialBalance();

        $this->assertTrue($trialBalance['is_balanced'], 'Trial balance must satisfy Total Debit == Total Credit.');
        $this->assertEquals(0.00, $trialBalance['difference']);
    }

    /**
     * Test 9: Profit and Loss Statement calculates Gross and Net Profit correctly
     */
    public function test_profit_and_loss_statement_structure(): void
    {
        $pnl = $this->reportingService->getProfitAndLoss();

        $this->assertArrayHasKey('gross_sales', $pnl);
        $this->assertArrayHasKey('cogs', $pnl);
        $this->assertArrayHasKey('gross_profit', $pnl);
        $this->assertArrayHasKey('operating_expenses', $pnl);
        $this->assertArrayHasKey('net_profit', $pnl);
        $this->assertEquals(
            round($pnl['gross_sales'] - $pnl['cogs'], 2),
            $pnl['gross_profit']
        );
    }

    /**
     * Test 10: Balance Sheet structure satisfies Assets = Liabilities + Equity + Period Retained Earnings
     */
    public function test_balance_sheet_equality(): void
    {
        $balanceSheet = $this->reportingService->getBalanceSheet();

        $this->assertArrayHasKey('total_assets', $balanceSheet);
        $this->assertArrayHasKey('total_liabilities', $balanceSheet);
        $this->assertArrayHasKey('total_equity', $balanceSheet);
        $this->assertArrayHasKey('period_earnings', $balanceSheet);
        $this->assertTrue($balanceSheet['is_balanced'], 'Balance sheet must satisfy Assets == Liabilities + Equity.');
    }
}
