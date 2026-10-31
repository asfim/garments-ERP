<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanySetting;
use App\Models\Unit;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Production;
use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\Stock;
use App\Models\StockLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class FactoryErpController extends Controller
{
    // ==========================================
    // 1. SETTINGS (Company Name, Logo, Address, Phone)
    // ==========================================
    public function settingsIndex()
    {
        $setting = CompanySetting::first() ?? new CompanySetting([
            'company_name' => 'Turkey Bangla Garments Ltd',
            'address' => 'Tongi Industrial Area, Gazipur, Dhaka',
            'phone' => '+880 1712-345678'
        ]);
        return view('factory-erp.settings.index', compact('setting'));
    }

    public function settingsUpdate(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'phone' => 'required|string|max:100',
            'address' => 'required|string|max:500',
            'email' => 'nullable|string|max:100',
            'website' => 'nullable|string|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $setting = CompanySetting::first() ?? new CompanySetting();
        $setting->company_name = $validated['company_name'];
        $setting->phone = $validated['phone'];
        $setting->address = $validated['address'];
        $setting->email = $validated['email'] ?? null;
        $setting->website = $validated['website'] ?? null;

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('company', 'public');
            $setting->logo = $logoPath;
        }

        $setting->save();

        return redirect()->back()->with('success', 'Company settings updated successfully!');
    }

    // ==========================================
    // 2. UNITS (Dynamic Unit Menu)
    // ==========================================
    public function unitsIndex()
    {
        $units = Unit::withCount('products')->latest()->get();
        return view('factory-erp.units.index', compact('units'));
    }

    public function unitsStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:units,name',
            'short_code' => 'nullable|string|max:20'
        ]);

        Unit::create($validated);
        return redirect()->back()->with('success', 'Unit added successfully!');
    }

    public function unitsUpdate(Request $request, $id)
    {
        $unit = Unit::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:units,name,' . $unit->id,
            'short_code' => 'nullable|string|max:20'
        ]);

        $unit->update($validated);
        return redirect()->back()->with('success', 'Unit updated successfully!');
    }

    public function unitsDestroy($id)
    {
        $unit = Unit::findOrFail($id);
        if ($unit->products()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete unit because it is used by existing products.');
        }
        $unit->delete();
        return redirect()->back()->with('success', 'Unit deleted successfully!');
    }

    // ==========================================
    // 3. PRODUCTS (Product List with Unit, Production Price, Sell Price & PDF Export)
    // ==========================================
    public function productsIndex()
    {
        $products = Product::with(['unit', 'stock'])->latest()->get();
        $units = Unit::all();
        $company = CompanySetting::first();
        return view('factory-erp.products.index', compact('products', 'units', 'company'));
    }

    public function productsStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit_id' => 'required|exists:units,id',
            'production_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'description' => 'nullable|string'
        ]);

        $product = Product::create($validated);

        // Auto initialize stock
        Stock::create([
            'product_id' => $product->id,
            'current_stock' => 0,
            'total_produced' => 0,
            'total_delivered' => 0,
            'manual_adjusted' => 0
        ]);

        return redirect()->back()->with('success', 'Product added successfully with stock tracking!');
    }

    public function productsUpdate(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit_id' => 'required|exists:units,id',
            'production_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'description' => 'nullable|string'
        ]);

        $product->update($validated);

        return redirect()->back()->with('success', "Product '{$product->name}' updated successfully!");
    }

    public function productsDestroy($id)
    {
        $product = Product::findOrFail($id);
        $name = $product->name;
        $product->delete();

        return redirect()->back()->with('success', "Product '{$name}' deleted successfully!");
    }

    public function productsPdf()
    {
        $products = Product::with('unit')->get();
        $company = CompanySetting::first() ?? new CompanySetting([
            'company_name' => 'Turkey Bangla Garments Ltd',
            'address' => 'Tongi Industrial Area, Gazipur, Dhaka',
            'phone' => '+880 1712-345678'
        ]);

        $pdf = Pdf::loadView('factory-erp.products.pdf', compact('products', 'company'));
        return $pdf->download('Product-Price-List-' . date('Y-m-d') . '.pdf');
    }

    // ==========================================
    // 4. CUSTOMERS & THEIR FULL ORDER/INVOICE HISTORY
    // ==========================================
    public function customersIndex()
    {
        $customers = Customer::with(['orders'])->latest()->get();
        return view('factory-erp.customers.index', compact('customers'));
    }

    public function customersStore(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'address' => 'nullable|string',
            'contact_person' => 'nullable|string|max:100'
        ]);

        Customer::create($validated);
        return redirect()->back()->with('success', 'Customer created successfully!');
    }

    public function customersUpdate(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'address' => 'nullable|string',
            'contact_person' => 'nullable|string|max:100'
        ]);

        $customer->update($validated);
        return redirect()->back()->with('success', 'Customer details updated successfully!');
    }

    public function customersDestroy($id)
    {
        $customer = Customer::findOrFail($id);
        if ($customer->orders()->count() > 0) {
            return redirect()->back()->with('error', "Cannot delete {$customer->company_name} because they have registered orders. Please remove orders first.");
        }
        $customer->delete();
        return redirect()->back()->with('success', 'Customer deleted successfully!');
    }

    public function customerDetails($id)
    {
        $customer = Customer::with(['orders.items.product.unit', 'deliveries.items.product'])->findOrFail($id);
        return view('factory-erp.customers.details', compact('customer'));
    }

    // ==========================================
    // 5. ORDERS (Customer Dropdown, Dynamic Products, Qty, Total Calc, Invoice, CSV Export)
    // ==========================================
    public function ordersIndex(Request $request)
    {
        $range = $request->get('range', 'this_month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = Order::with(['customer', 'items.product.unit'])->orderBy('order_date', 'desc')->orderBy('id', 'desc');

        if ($range === 'this_month') {
            $query->whereBetween('order_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()]);
        } elseif ($range === 'today') {
            $query->whereDate('order_date', now()->toDateString());
        } elseif ($range === 'last_7_days') {
            $query->whereBetween('order_date', [now()->subDays(6)->toDateString(), now()->toDateString()]);
        } elseif ($range === 'last_month') {
            $query->whereBetween('order_date', [now()->subMonth()->startOfMonth()->toDateString(), now()->subMonth()->endOfMonth()->toDateString()]);
        } elseif ($range === 'this_year') {
            $query->whereBetween('order_date', [now()->startOfYear()->toDateString(), now()->endOfYear()->toDateString()]);
        } elseif ($range === 'custom' && $startDate && $endDate) {
            $query->whereBetween('order_date', [$startDate, $endDate]);
        }

        $orders = $query->get();
        $totalOrdersCount = Order::count();
        $customers = Customer::all();
        $products = Product::with('unit')->get();

        return view('factory-erp.orders.index', compact('orders', 'customers', 'products', 'range', 'startDate', 'endDate', 'totalOrdersCount'));
    }

    public function ordersExportCsv(Request $request)
    {
        $range = $request->get('range', 'this_month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = Order::with(['customer', 'items.product.unit'])->orderBy('order_date', 'desc')->orderBy('id', 'desc');

        if ($range === 'this_month') {
            $query->whereBetween('order_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()]);
        } elseif ($range === 'today') {
            $query->whereDate('order_date', now()->toDateString());
        } elseif ($range === 'last_7_days') {
            $query->whereBetween('order_date', [now()->subDays(6)->toDateString(), now()->toDateString()]);
        } elseif ($range === 'last_month') {
            $query->whereBetween('order_date', [now()->subMonth()->startOfMonth()->toDateString(), now()->subMonth()->endOfMonth()->toDateString()]);
        } elseif ($range === 'this_year') {
            $query->whereBetween('order_date', [now()->startOfYear()->toDateString(), now()->endOfYear()->toDateString()]);
        } elseif ($range === 'custom' && $startDate && $endDate) {
            $query->whereBetween('order_date', [$startDate, $endDate]);
        }

        $orders = $query->get();
        $filename = 'Orders-Export-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            fputcsv($file, [
                'Order ID',
                'Customer Name',
                'Contact Person',
                'Phone Number',
                'Order Date',
                'Product Types Count',
                'Total Amount (BDT)',
                'Paid Amount (BDT)',
                'Due Amount (BDT)',
                'Delivery Status',
                'Order Notes'
            ]);

            foreach ($orders as $ord) {
                fputcsv($file, [
                    $ord->order_number,
                    $ord->customer->company_name ?? 'N/A',
                    $ord->customer->contact_person ?? '',
                    $ord->customer->phone ?? '',
                    date('d-M-Y', strtotime($ord->order_date)),
                    $ord->items->count(),
                    number_format($ord->total_amount, 2, '.', ''),
                    number_format($ord->paid_amount, 2, '.', ''),
                    number_format($ord->due_amount, 2, '.', ''),
                    ucfirst($ord->delivery_status),
                    $ord->notes ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function ordersCreate()
    {
        $customers = Customer::all();
        $products = Product::with('unit')->get();
        $orderNumber = 'ORD-' . date('Y') . '-' . str_pad((Order::count() + 1), 4, '0', STR_PAD_LEFT);
        return view('factory-erp.orders.create', compact('customers', 'products', 'orderNumber'));
    }

    public function ordersStore(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'paid_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.sell_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += ($item['qty'] * $item['sell_price']);
            }

            $paid = $request->paid_amount ?? 0;
            $due = max(0, $totalAmount - $paid);
            $orderNumber = 'ORD-' . date('Y') . '-' . str_pad((Order::count() + 1), 4, '0', STR_PAD_LEFT);

            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_id' => $request->customer_id,
                'order_date' => $request->order_date,
                'total_amount' => $totalAmount,
                'paid_amount' => $paid,
                'due_amount' => $due,
                'delivery_status' => 'pending',
                'notes' => $request->notes
            ]);

            foreach ($request->items as $item) {
                $itemTotal = $item['qty'] * $item['sell_price'];
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'sell_price' => $item['sell_price'],
                    'total_price' => $itemTotal,
                    'delivered_qty' => 0,
                    'due_qty' => $item['qty']
                ]);
            }

            DB::commit();
            return redirect()->route('factory.orders.invoice', $order->id)->with('success', 'Order and Invoice generated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error creating order: ' . $e->getMessage());
        }
    }

    public function orderInvoice($id)
    {
        $order = Order::with(['customer', 'items.product.unit'])->findOrFail($id);
        $company = CompanySetting::first() ?? new CompanySetting([
            'company_name' => 'Turkey Bangla Garments Ltd',
            'address' => 'Tongi Industrial Area, Gazipur, Dhaka',
            'phone' => '+880 1712-345678'
        ]);
        return view('factory-erp.orders.invoice', compact('order', 'company'));
    }

    public function orderInvoicePdf($id)
    {
        $order = Order::with(['customer', 'items.product.unit'])->findOrFail($id);
        $company = CompanySetting::first() ?? new CompanySetting([
            'company_name' => 'Turkey Bangla Garments Ltd',
            'address' => 'Tongi Industrial Area, Gazipur, Dhaka',
            'phone' => '+880 1712-345678'
        ]);

        $pdf = Pdf::loadView('factory-erp.orders.invoice-pdf', compact('order', 'company'));
        return $pdf->download('Invoice-' . $order->order_number . '.pdf');
    }

    public function ordersUpdate(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $validated = $request->validate([
            'paid_amount' => 'required|numeric|min:0',
            'delivery_status' => 'required|in:pending,partial,delivered',
            'notes' => 'nullable|string'
        ]);

        $order->paid_amount = $validated['paid_amount'];
        $order->due_amount = max(0, $order->total_amount - $validated['paid_amount']);
        $order->delivery_status = $validated['delivery_status'];
        $order->notes = $validated['notes'] ?? null;
        $order->save();

        return redirect()->back()->with('success', "Order {$order->order_number} updated successfully!");
    }

    public function ordersDestroy($id)
    {
        $order = Order::with('items')->findOrFail($id);

        $deliveredCount = DeliveryItem::whereIn('order_item_id', $order->items->pluck('id'))->count();
        if ($deliveredCount > 0) {
            return redirect()->back()->with('error', "Cannot delete order {$order->order_number} because delivery shipments have already been issued. Please delete or rollback deliveries first.");
        }

        DB::beginTransaction();
        try {
            $order->items()->delete();
            $order->delete();
            DB::commit();
            return redirect()->back()->with('success', "Order {$order->order_number} and all items deleted successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error deleting order: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 6. PRODUCTIONS (Product Live Select -> Auto Target Qty from Orders -> Unit -> Add to Stock)
    // ==========================================
    public function productionsIndex(Request $request)
    {
        $range = $request->get('range', 'this_month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = Production::with(['product.unit', 'unit'])->orderBy('production_date', 'desc')->orderBy('id', 'desc');

        if ($range === 'this_month') {
            $query->whereBetween('production_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()]);
        } elseif ($range === 'today') {
            $query->whereDate('production_date', now()->toDateString());
        } elseif ($range === 'last_7_days') {
            $query->whereBetween('production_date', [now()->subDays(6)->toDateString(), now()->toDateString()]);
        } elseif ($range === 'last_month') {
            $query->whereBetween('production_date', [now()->subMonth()->startOfMonth()->toDateString(), now()->subMonth()->endOfMonth()->toDateString()]);
        } elseif ($range === 'this_year') {
            $query->whereBetween('production_date', [now()->startOfYear()->toDateString(), now()->endOfYear()->toDateString()]);
        } elseif ($range === 'custom' && $startDate && $endDate) {
            $query->whereBetween('production_date', [$startDate, $endDate]);
        }

        $productions = $query->get();
        $totalProductionsCount = Production::count();
        $products = Product::with('unit')->get();

        // Calculate pending order quantities for each product to set auto target
        $pendingOrderQty = [];
        foreach ($products as $p) {
            $pendingOrderQty[$p->id] = OrderItem::where('product_id', $p->id)->sum('due_qty');
        }

        return view('factory-erp.productions.index', compact('productions', 'products', 'pendingOrderQty', 'range', 'startDate', 'endDate', 'totalProductionsCount'));
    }

    public function productionsStore(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'production_date' => 'required|date',
            'target_qty' => 'required|numeric|min:0',
            'produced_qty' => 'required|numeric|min:1',
            'notes' => 'nullable|string'
        ]);

        $product = Product::with('unit')->findOrFail($validated['product_id']);

        DB::beginTransaction();
        try {
            $production = Production::create([
                'product_id' => $product->id,
                'unit_id' => $product->unit_id,
                'production_date' => $validated['production_date'],
                'target_qty' => $validated['target_qty'],
                'produced_qty' => $validated['produced_qty'],
                'notes' => $validated['notes'] ?? null
            ]);

            // AUTO UPDATE STOCK
            $stock = Stock::firstOrCreate(['product_id' => $product->id], [
                'current_stock' => 0,
                'total_produced' => 0,
                'total_delivered' => 0,
                'manual_adjusted' => 0
            ]);

            $prevStock = $stock->current_stock;
            $stock->current_stock += $validated['produced_qty'];
            $stock->total_produced += $validated['produced_qty'];
            $stock->save();

            StockLog::create([
                'product_id' => $product->id,
                'type' => 'production',
                'qty' => $validated['produced_qty'],
                'previous_stock' => $prevStock,
                'new_stock' => $stock->current_stock,
                'reference_id' => 'PROD-' . $production->id,
                'notes' => 'Produced ' . $validated['produced_qty'] . ' ' . $product->unit->name
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Production recorded! ' . $validated['produced_qty'] . ' ' . $product->unit->name . ' auto-added to Stock.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error saving production: ' . $e->getMessage());
        }
    }

    public function productionsUpdate(Request $request, $id)
    {
        $production = Production::with('product.unit')->findOrFail($id);
        $validated = $request->validate([
            'production_date' => 'required|date',
            'target_qty' => 'required|numeric|min:0',
            'produced_qty' => 'required|numeric|min:1',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $oldProduced = floatval($production->produced_qty);
            $newProduced = floatval($validated['produced_qty']);
            $diff = $newProduced - $oldProduced;

            $production->production_date = $validated['production_date'];
            $production->target_qty = $validated['target_qty'];
            $production->produced_qty = $newProduced;
            $production->notes = $validated['notes'] ?? null;
            $production->save();

            // Adjust Stock by difference
            $stock = Stock::firstOrCreate(['product_id' => $production->product_id]);
            $prevStock = $stock->current_stock;
            $stock->current_stock += $diff;
            $stock->total_produced += $diff;
            $stock->save();

            StockLog::create([
                'product_id' => $production->product_id,
                'type' => 'production_edit',
                'qty' => abs($diff),
                'previous_stock' => $prevStock,
                'new_stock' => $stock->current_stock,
                'reference_id' => 'PROD-' . $production->id,
                'notes' => 'Production batch updated (Qty diff: ' . ($diff >= 0 ? '+' : '') . $diff . ')'
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Production batch updated and stock adjusted!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error updating production: ' . $e->getMessage());
        }
    }

    public function productionsDestroy($id)
    {
        $production = Production::findOrFail($id);
        DB::beginTransaction();
        try {
            $stock = Stock::where('product_id', $production->product_id)->first();
            if ($stock) {
                $prevStock = $stock->current_stock;
                $stock->current_stock = max(0, $stock->current_stock - $production->produced_qty);
                $stock->total_produced = max(0, $stock->total_produced - $production->produced_qty);
                $stock->save();

                StockLog::create([
                    'product_id' => $production->product_id,
                    'type' => 'production_delete',
                    'qty' => $production->produced_qty,
                    'previous_stock' => $prevStock,
                    'new_stock' => $stock->current_stock,
                    'reference_id' => 'PROD-REV-' . $production->id,
                    'notes' => 'Production batch deleted. Stock reversed.'
                ]);
            }

            $production->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Production record deleted and inventory reversed!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error deleting production: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 7. DELIVERIES (Check Available Production/Stock -> Warning if exceeds -> Deduct Stock -> Update Due Qty)
    // ==========================================
    public function deliveriesIndex()
    {
        // Orders with pending deliveries
        $pendingOrders = Order::with(['customer', 'items.product.unit'])
            ->whereIn('delivery_status', ['pending', 'partial'])
            ->latest()
            ->get();

        $deliveries = Delivery::with(['order', 'customer', 'items.product.unit'])->latest()->get();

        return view('factory-erp.deliveries.index', compact('pendingOrders', 'deliveries'));
    }

    public function deliveryCreateForOrder($orderId)
    {
        $order = Order::with(['customer', 'items.product.unit', 'items.product.stock'])->findOrFail($orderId);
        $deliveryNumber = 'DEL-' . date('Y') . '-' . str_pad((Delivery::count() + 1), 4, '0', STR_PAD_LEFT);
        return view('factory-erp.deliveries.create', compact('order', 'deliveryNumber'));
    }

    public function deliveryStore(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'delivery_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.order_item_id' => 'required|exists:order_items,id',
            'items.*.deliver_qty' => 'required|numeric|min:0'
        ]);

        $order = Order::with(['items.product.stock'])->findOrFail($request->order_id);

        // 1. VALIDATION: Check each item against available stock & due qty
        $totalDeliverAmount = 0;
        $hasItemsToDeliver = false;

        foreach ($request->items as $itemInput) {
            $deliverQty = floatval($itemInput['deliver_qty']);
            if ($deliverQty <= 0) continue;

            $hasItemsToDeliver = true;
            $orderItem = OrderItem::with(['product.stock', 'product.unit'])->findOrFail($itemInput['order_item_id']);

            // Check if deliverQty > due_qty
            if ($deliverQty > $orderItem->due_qty) {
                return redirect()->back()->withInput()->with('error', "Warning: Deliver Qty ({$deliverQty}) exceeds Due Qty ({$orderItem->due_qty}) for product '{$orderItem->product->name}'.");
            }

            // Check if deliverQty > available stock in factory
            $currentStock = $orderItem->product->stock ? $orderItem->product->stock->current_stock : 0;
            if ($deliverQty > $currentStock) {
                return redirect()->back()->withInput()->with('error', "Warning! Insufficient Stock: You cannot deliver {$deliverQty} {$orderItem->product->unit->name} of '{$orderItem->product->name}'. Available factory stock is only {$currentStock}!");
            }

            $totalDeliverAmount += ($deliverQty * $orderItem->sell_price);
        }

        if (!$hasItemsToDeliver) {
            return redirect()->back()->withInput()->with('error', 'Please input at least 1 item quantity to deliver.');
        }

        DB::beginTransaction();
        try {
            $delNumber = 'DEL-' . date('Y') . '-' . str_pad((Delivery::count() + 1), 4, '0', STR_PAD_LEFT);

            $delivery = Delivery::create([
                'delivery_number' => $delNumber,
                'order_id' => $order->id,
                'customer_id' => $order->customer_id,
                'delivery_date' => $request->delivery_date,
                'total_delivered_amount' => $totalDeliverAmount,
                'notes' => $request->notes ?? null
            ]);

            $allDelivered = true;

            foreach ($request->items as $itemInput) {
                $deliverQty = floatval($itemInput['deliver_qty']);
                if ($deliverQty <= 0) continue;

                $orderItem = OrderItem::findOrFail($itemInput['order_item_id']);
                $product = Product::findOrFail($orderItem->product_id);

                // Create delivery item
                DeliveryItem::create([
                    'delivery_id' => $delivery->id,
                    'order_item_id' => $orderItem->id,
                    'product_id' => $product->id,
                    'qty' => $deliverQty,
                    'sell_price' => $orderItem->sell_price,
                    'total_price' => $deliverQty * $orderItem->sell_price
                ]);

                // Update Order Item delivered & due qty
                $orderItem->delivered_qty += $deliverQty;
                $orderItem->due_qty = max(0, $orderItem->qty - $orderItem->delivered_qty);
                $orderItem->save();

                if ($orderItem->due_qty > 0) {
                    $allDelivered = false;
                }

                // DEDUCT FROM STOCK
                $stock = Stock::where('product_id', $product->id)->first();
                if ($stock) {
                    $prevStock = $stock->current_stock;
                    $stock->current_stock -= $deliverQty;
                    $stock->total_delivered += $deliverQty;
                    $stock->save();

                    StockLog::create([
                        'product_id' => $product->id,
                        'type' => 'delivery',
                        'qty' => $deliverQty,
                        'previous_stock' => $prevStock,
                        'new_stock' => $stock->current_stock,
                        'reference_id' => $delivery->delivery_number,
                        'notes' => "Delivered to Customer (Order: {$order->order_number})"
                    ]);
                }
            }

            // Check if remaining items in order have any due qty
            $remainingDues = OrderItem::where('order_id', $order->id)->where('due_qty', '>', 0)->count();
            $order->delivery_status = ($remainingDues == 0) ? 'delivered' : 'partial';
            $order->save();

            DB::commit();
            return redirect()->route('factory.deliveries.index')->with('success', "Delivery {$delNumber} processed! Factory Stock and Order Due have been updated.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error recording delivery: ' . $e->getMessage());
        }
    }

    public function deliveriesDestroy($id)
    {
        $delivery = Delivery::with(['items.product', 'order.items'])->findOrFail($id);

        DB::beginTransaction();
        try {
            foreach ($delivery->items as $delItem) {
                // 1. Return stock to warehouse
                $stock = Stock::where('product_id', $delItem->product_id)->first();
                if ($stock) {
                    $prevStock = $stock->current_stock;
                    $stock->current_stock += $delItem->qty;
                    $stock->total_delivered = max(0, $stock->total_delivered - $delItem->qty);
                    $stock->save();

                    StockLog::create([
                        'product_id' => $delItem->product_id,
                        'type' => 'delivery_cancel',
                        'qty' => $delItem->qty,
                        'previous_stock' => $prevStock,
                        'new_stock' => $stock->current_stock,
                        'reference_id' => 'REV-' . $delivery->delivery_number,
                        'notes' => 'Delivery cancelled. Stock returned to warehouse.'
                    ]);
                }

                // 2. Rollback OrderItem delivered_qty & due_qty
                $orderItem = OrderItem::find($delItem->order_item_id);
                if ($orderItem) {
                    $orderItem->delivered_qty = max(0, $orderItem->delivered_qty - $delItem->qty);
                    $orderItem->due_qty = max(0, $orderItem->qty - $orderItem->delivered_qty);
                    $orderItem->save();
                }
            }

            // 3. Re-evaluate order delivery status
            if ($delivery->order) {
                $order = $delivery->order;
                $totalDelivered = $order->items()->sum('delivered_qty');
                $totalDue = $order->items()->sum('due_qty');

                if ($totalDelivered == 0) {
                    $order->delivery_status = 'pending';
                } elseif ($totalDue == 0) {
                    $order->delivery_status = 'delivered';
                } else {
                    $order->delivery_status = 'partial';
                }
                $order->save();
            }

            // 4. Delete delivery items and delivery
            $delivery->items()->delete();
            $delivery->delete();

            DB::commit();
            return redirect()->back()->with('success', "Delivery Challan {$delivery->delivery_number} deleted! Stock returned to warehouse and order dues restored.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error deleting delivery: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 8. STOCKS (Auto Inventory Tracking + Manual Adjustment)
    // ==========================================
    public function stocksIndex()
    {
        $products = Product::with(['unit', 'stock'])->get();
        $stockLogs = StockLog::with('product.unit')->latest()->take(20)->get();
        return view('factory-erp.stocks.index', compact('products', 'stockLogs'));
    }

    public function stocksAdjust(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:manual_add,manual_subtract',
            'qty' => 'required|numeric|min:1',
            'notes' => 'nullable|string'
        ]);

        $product = Product::with('unit')->findOrFail($validated['product_id']);
        $stock = Stock::firstOrCreate(['product_id' => $product->id], [
            'current_stock' => 0,
            'total_produced' => 0,
            'total_delivered' => 0,
            'manual_adjusted' => 0
        ]);

        $qty = floatval($validated['qty']);
        $prevStock = $stock->current_stock;

        if ($validated['type'] === 'manual_subtract' && $qty > $prevStock) {
            return redirect()->back()->with('error', "Cannot subtract {$qty} from current stock of {$prevStock}!");
        }

        if ($validated['type'] === 'manual_add') {
            $stock->current_stock += $qty;
            $stock->manual_adjusted += $qty;
        } else {
            $stock->current_stock -= $qty;
            $stock->manual_adjusted -= $qty;
        }

        $stock->save();

        StockLog::create([
            'product_id' => $product->id,
            'type' => $validated['type'],
            'qty' => $qty,
            'previous_stock' => $prevStock,
            'new_stock' => $stock->current_stock,
            'reference_id' => 'MANUAL-ADJ',
            'notes' => $validated['notes'] ?? 'Manual Stock Adjustment'
        ]);

        return redirect()->back()->with('success', "Stock for {$product->name} manually updated!");
    }
}

