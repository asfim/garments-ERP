<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompanySetting;
use App\Models\Unit;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Stock;

class FactoryErpSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Company Setting
        CompanySetting::firstOrCreate(['id' => 1], [
            'company_name' => 'Turkey Bangla Garments Ltd',
            'logo' => null,
            'address' => 'Tongi Industrial Area, Gazipur, Dhaka - 1710, Bangladesh',
            'phone' => '+880 1712-345678, +880 1819-876543',
            'email' => 'sales@turkeybangla.com',
            'website' => 'turkeybangla.com'
        ]);

        // 2. Units
        $units = [
            ['name' => 'Pieces', 'short_code' => 'Pcs'],
            ['name' => 'Dozen', 'short_code' => 'Dzn'],
            ['name' => 'Box', 'short_code' => 'Box'],
            ['name' => 'Carton', 'short_code' => 'Ctn'],
            ['name' => 'Kilogram', 'short_code' => 'Kg'],
            ['name' => 'Meter', 'short_code' => 'Mtr']
        ];
        foreach ($units as $u) {
            Unit::firstOrCreate(['short_code' => $u['short_code']], $u);
        }

        $pcs = Unit::where('short_code', 'Pcs')->first();
        $dzn = Unit::where('short_code', 'Dzn')->first();

        // 3. Products
        $products = [
            [
                'name' => "Men's Cotton Innerwear (Premium)",
                'unit_id' => $pcs->id,
                'production_price' => 120.00,
                'sell_price' => 180.00,
                'description' => '100% Combed Cotton, Soft finish'
            ],
            [
                'name' => "Women's Comfort Bra (Seamless)",
                'unit_id' => $pcs->id,
                'production_price' => 180.00,
                'sell_price' => 260.00,
                'description' => 'Breathable fabric with adjustable strap'
            ],
            [
                'name' => "Cotton Crew Socks (Pack of 3)",
                'unit_id' => $dzn->id,
                'production_price' => 350.00,
                'sell_price' => 520.00,
                'description' => 'Reinforced heel & toe'
            ],
            [
                'name' => "Kids Cotton Innerwear Set",
                'unit_id' => $pcs->id,
                'production_price' => 90.00,
                'sell_price' => 140.00,
                'description' => 'Organic soft cotton for sensitive skin'
            ],
            [
                'name' => "Classic Polo T-Shirt",
                'unit_id' => $pcs->id,
                'production_price' => 240.00,
                'sell_price' => 380.00,
                'description' => '220 GSM pique fabric'
            ]
        ];

        foreach ($products as $pData) {
            $prod = Product::firstOrCreate(['name' => $pData['name']], $pData);
            // Create initial stock
            Stock::firstOrCreate(['product_id' => $prod->id], [
                'current_stock' => 500,
                'total_produced' => 500,
                'total_delivered' => 0,
                'manual_adjusted' => 0
            ]);
        }

        // 4. Customers
        $customers = [
            [
                'company_name' => 'Rahim Traders & Showroom',
                'phone' => '+880 1823-112233',
                'address' => 'Shop #14, Islam Plaza, Islampur, Dhaka',
                'contact_person' => 'Md. Rahim Ali'
            ],
            [
                'company_name' => 'M/S Fashion House Ltd',
                'phone' => '+880 1711-445566',
                'address' => 'Holding 45, Gausia Market, Mirpur-10, Dhaka',
                'contact_person' => 'Kamal Hossain'
            ],
            [
                'company_name' => 'New Life Garments Store',
                'phone' => '+880 1912-778899',
                'address' => 'Chawkbazar Main Road, Chittagong',
                'contact_person' => 'Ashraf Ullah'
            ]
        ];

        foreach ($customers as $c) {
            Customer::firstOrCreate(['company_name' => $c['company_name']], $c);
        }
    }
}

