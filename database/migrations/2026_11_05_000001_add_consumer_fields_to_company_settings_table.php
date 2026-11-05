<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('company_settings', 'tagline')) {
                $table->string('tagline')->nullable()->default('Quality is our Priority')->after('company_name');
            }
            if (!Schema::hasColumn('company_settings', 'trade_licence')) {
                $table->string('trade_licence')->nullable()->default('019268')->after('tagline');
            }
            if (!Schema::hasColumn('company_settings', 'tin_no')) {
                $table->string('tin_no')->nullable()->default('369123229836')->after('trade_licence');
            }
            if (!Schema::hasColumn('company_settings', 'hotline')) {
                $table->string('hotline')->nullable()->default('+8801891-153903')->after('phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('company_settings', 'tagline')) $cols[] = 'tagline';
            if (Schema::hasColumn('company_settings', 'trade_licence')) $cols[] = 'trade_licence';
            if (Schema::hasColumn('company_settings', 'tin_no')) $cols[] = 'tin_no';
            if (Schema::hasColumn('company_settings', 'hotline')) $cols[] = 'hotline';
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};

