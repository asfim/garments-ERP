<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanySetting;
use App\Models\Order;
use App\Models\Production;
use App\Models\HrEmployee;
use App\Models\HrAttendance;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $company = CompanySetting::first() ?? new CompanySetting([
            'company_name' => 'Turkey Bangla Garments Ltd',
            'tagline' => 'Next-Gen Industrial Garments ERP & HR Solution',
            'address' => 'Tongi Industrial Area, Gazipur, Dhaka - 1710, Bangladesh',
            'phone' => '+880 1712-345678',
            'email' => 'info@turkeybangla.com'
        ]);

        $today = Carbon::today()->format('Y-m-d');

        // Quick high-level stats for the landing page
        $stats = [
            'totalEmployees' => HrEmployee::where('status', 'active')->count(),
            'presentToday' => HrAttendance::whereDate('date', $today)->where('status', 'present')->count(),
            'totalOrders' => Order::count(),
            'totalProduction' => Production::sum('produced_qty')
        ];

        return view('home', compact('company', 'stats'));
    }
}

