<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add device_user_id (biometric machine ID) to hr_employees
        if (!Schema::hasColumn('hr_employees', 'device_user_id')) {
            Schema::table('hr_employees', function (Blueprint $table) {
                $table->string('device_user_id')->nullable()->after('employee_code')->index();
            });
        }

        // 2. HR Biometric Devices table
        Schema::create('hr_biometric_devices', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. Main Gate Fingerprint Reader
            $table->string('device_ip')->default('192.168.1.201');
            $table->integer('port')->default(4370);
            $table->string('device_model')->default('ZKTeco K40 / MB20');
            $table->string('protocol')->default('TCP'); // TCP, UDP, ADMS_CLOUD
            $table->string('status')->default('unknown'); // online, offline, unknown
            $table->dateTime('last_sync_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 3. Raw Biometric Punch Logs
        Schema::create('hr_biometric_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->nullable()->constrained('hr_biometric_devices')->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('hr_employees')->nullOnDelete();
            $table->string('device_user_id'); // User ID on biometric machine
            $table->dateTime('punch_time');
            $table->string('punch_type')->default('Check-In'); // Check-In, Check-Out, Break, OT
            $table->string('source')->default('live_sync'); // live_sync, usb_import, adms_push
            $table->timestamps();

            $table->unique(['device_user_id', 'punch_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_biometric_logs');
        Schema::dropIfExists('hr_biometric_devices');
        if (Schema::hasColumn('hr_employees', 'device_user_id')) {
            Schema::table('hr_employees', function (Blueprint $table) {
                $table->dropColumn('device_user_id');
            });
        }
    }
};

