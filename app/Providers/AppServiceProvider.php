<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\CompanySetting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share company setting globally with all blade views
        View::composer('*', function ($view) {
            try {
                static $company = null;
                if ($company === null && Schema::hasTable('company_settings')) {
                    $company = CompanySetting::first() ?? new CompanySetting([
                        'company_name' => 'Turkey Bangla Garments Ltd',
                        'address' => 'Tongi Industrial Area, Gazipur, Dhaka',
                        'phone' => '+880 1712-345678',
                        'email' => 'info@turkeybangla.com'
                    ]);
                }
                $view->with('globalCompany', $company);
            } catch (\Throwable $e) {
                // Ignore during command line or migrations
            }
        });
    }
}
