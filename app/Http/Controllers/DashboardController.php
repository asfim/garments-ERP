<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Production;
use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\Stock;
use App\Models\StockLog;
use App\Models\CompanySetting;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // 1. Company Setting
        $company = CompanySetting::first() ?? new CompanySetting([
            'company_name' => 'Turkey Bangla Garments Ltd',
            'address' => 'Tongi Industrial Area, Gazipur, Dhaka',
            'phone' => '+880 1712-345678'
        ]);

        // 2. KPI: Total Sales (Today & All-time)
        $todaySales = Order::whereDate('order_date', $today)->sum('total_amount');
        $yesterdaySales = Order::whereDate('order_date', $yesterday)->sum('total_amount');
        $allTimeSales = Order::sum('total_amount');
        
        // Sales change %
        $salesGrowth = 0;
        if ($yesterdaySales > 0) {
            $salesGrowth = round((($todaySales - $yesterdaySales) / $yesterdaySales) * 100);
        } elseif ($todaySales > 0) {
            $salesGrowth = 100;
        }

        // 3. KPI: Total Production / Purchase (Today & All-time)
        $todayProductionQty = Production::whereDate('production_date', $today)->sum('produced_qty');
        $allTimeProductionQty = Production::sum('produced_qty');

        $todayProductionValue = DB::table('productions')
            ->join('products', 'productions.product_id', '=', 'products.id')
            ->whereDate('productions.production_date', $today)
            ->sum(DB::raw('productions.produced_qty * products.production_price'));
            
        $allTimeProductionValue = DB::table('productions')
            ->join('products', 'productions.product_id', '=', 'products.id')
            ->sum(DB::raw('productions.produced_qty * products.production_price'));

        // 4. KPI: Customer Due
        $totalCustomerDue = Order::sum('due_amount');
        $totalCustomersWithDue = Order::where('due_amount', '>', 0)->distinct('customer_id')->count('customer_id');

        // 5. KPI: Total Stock Value & Stock Items
        $totalStockItems = Stock::sum('current_stock');
        $totalStockValue = DB::table('stocks')
            ->join('products', 'stocks.product_id', '=', 'products.id')
            ->sum(DB::raw('stocks.current_stock * products.sell_price'));

        // 6. Stock Breakdown (In Stock, Low Stock, Out of Stock)
        $stocks = Stock::with('product')->get();
        $inStockCount = 0;
        $lowStockCount = 0;
        $outOfStockCount = 0;
        $inStockQty = 0;
        $lowStockQty = 0;
        $outOfStockQty = 0;

        foreach ($stocks as $s) {
            $qty = floatval($s->current_stock);
            if ($qty > 50) {
                $inStockCount++;
                $inStockQty += $qty;
            } elseif ($qty > 0) {
                $lowStockCount++;
                $lowStockQty += $qty;
            } else {
                $outOfStockCount++;
                $outOfStockQty += 1;
            }
        }
        $totalProductsCount = max(1, Product::count());

        // 7. Last 7 Days Sales Chart Data
        $chartDates = [];
        $chartSales = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $label = $date->format('d M');
            $chartDates[] = $label;
            
            $daySale = Order::whereDate('order_date', $dateStr)->sum('total_amount');
            $chartSales[] = floatval($daySale);
        }

        // 8. Sales by Product / Category (Donut chart)
        $productSales = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.total_price) as total_amount'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_amount')
            ->take(5)
            ->get();

        $donutLabels = [];
        $donutData = [];
        $donutColors = ['#00f2fe', '#a855f7', '#3b82f6', '#10b981', '#ec4899'];
        $donutTotal = $productSales->sum('total_amount');

        if ($donutTotal > 0) {
            foreach ($productSales as $idx => $ps) {
                $donutLabels[] = $ps->name;
                $pct = round(($ps->total_amount / $donutTotal) * 100);
                $donutData[] = $pct;
            }
        } else {
            // Default distribution from current products if no sales yet
            $allProducts = Product::take(5)->get();
            $pCount = max(1, $allProducts->count());
            foreach ($allProducts as $idx => $p) {
                $donutLabels[] = $p->name;
                $donutData[] = round(100 / $pCount);
            }
        }

        // 9. Recent Orders
        $recentOrders = Order::with('customer')->latest()->take(5)->get();

        // 10. Top Selling Products
        $topSelling = OrderItem::select('product_id', DB::raw('SUM(qty) as total_sold_qty'), DB::raw('SUM(total_price) as total_sold_amount'))
            ->groupBy('product_id')
            ->with('product.unit')
            ->orderByDesc('total_sold_qty')
            ->take(4)
            ->get();

        $topProducts = null;
        if ($topSelling->isEmpty()) {
            $topProducts = Product::with(['unit', 'stock'])->take(4)->get();
        }

        // 11. Due Summary
        $totalOrderAmount = Order::sum('total_amount');
        $totalPaidAmount = Order::sum('paid_amount');
        $collectionProgress = $totalOrderAmount > 0 ? round(($totalPaidAmount / $totalOrderAmount) * 100) : 100;

        // 12. Recent Activities (Union of recent orders, productions, deliveries, and adjustments)
        $activities = collect();

        // Latest orders
        foreach (Order::with('customer')->latest()->take(3)->get() as $ord) {
            $activities->push([
                'type' => 'order',
                'title' => 'New Sale Invoice #' . $ord->order_number,
                'subtitle' => '৳ ' . number_format($ord->total_amount, 2) . ' • ' . ($ord->customer->company_name ?? 'Client'),
                'icon' => 'fa-solid fa-file-invoice-dollar',
                'bg' => 'bg-blue-500/20 text-cyan-400',
                'time' => $ord->created_at,
            ]);
        }

        // Latest deliveries
        foreach (Delivery::with(['order', 'customer'])->latest()->take(3)->get() as $del) {
            $activities->push([
                'type' => 'delivery',
                'title' => 'Delivery Challan #' . $del->delivery_number,
                'subtitle' => '৳ ' . number_format($del->total_delivered_amount, 2) . ' • ' . ($del->customer->company_name ?? 'Client'),
                'icon' => 'fa-solid fa-truck-fast',
                'bg' => 'bg-emerald-500/20 text-emerald-400',
                'time' => $del->created_at,
            ]);
        }

        // Latest productions
        foreach (Production::with(['product.unit'])->latest()->take(3)->get() as $pr) {
            $activities->push([
                'type' => 'production',
                'title' => 'Production: ' . ($pr->product->name ?? 'Product'),
                'subtitle' => '+' . number_format($pr->produced_qty) . ' ' . ($pr->product->unit->short_code ?? 'Pcs') . ' auto-added to Stock',
                'icon' => 'fa-solid fa-boxes-packing',
                'bg' => 'bg-purple-500/20 text-purple-400',
                'time' => $pr->created_at,
            ]);
        }

        // Latest stock logs
        foreach (StockLog::with('product')->latest()->take(3)->get() as $log) {
            $activities->push([
                'type' => 'stock',
                'title' => 'Stock Log: ' . ($log->product->name ?? 'Item'),
                'subtitle' => ucfirst(str_replace('_', ' ', $log->type)) . ' (' . $log->qty . ' units)',
                'icon' => 'fa-solid fa-warehouse',
                'bg' => 'bg-amber-500/20 text-amber-400',
                'time' => $log->created_at,
            ]);
        }

        $recentActivities = $activities->sortByDesc('time')->take(6);

        return view('dashboard', compact(
            'company',
            'todaySales',
            'allTimeSales',
            'salesGrowth',
            'todayProductionQty',
            'allTimeProductionQty',
            'todayProductionValue',
            'allTimeProductionValue',
            'totalCustomerDue',
            'totalCustomersWithDue',
            'totalStockItems',
            'totalStockValue',
            'inStockCount',
            'lowStockCount',
            'outOfStockCount',
            'inStockQty',
            'lowStockQty',
            'outOfStockQty',
            'totalProductsCount',
            'chartDates',
            'chartSales',
            'donutLabels',
            'donutData',
            'donutColors',
            'donutTotal',
            'productSales',
            'recentOrders',
            'topSelling',
            'topProducts',
            'totalOrderAmount',
            'totalPaidAmount',
            'collectionProgress',
            'recentActivities'
        ));
    }
}

