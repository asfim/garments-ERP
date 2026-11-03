<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HrBiometricDevice;
use App\Services\ZkTecoService;
use Carbon\Carbon;

class BiometricSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'biometric:sync 
                            {--device= : Specific device ID to sync} 
                            {--loop : Run continuously in a loop as a background service} 
                            {--interval=60 : Sleep interval in seconds when running in loop mode}';

    /**
     * The console command description.
     */
    protected $description = 'Automatically fetch and sync biometric attendance punch logs from ZKTeco devices into attendance records';

    /**
     * Execute the console command.
     */
    public function handle(ZkTecoService $zkService)
    {
        $isLoop = $this->option('loop');
        $interval = max(5, intval($this->option('interval') ?? 60));

        $this->info("======================================================");
        $this->info(" TBG ERP - ZKTeco Biometric Auto-Sync Worker");
        $this->info(" Started at: " . Carbon::now()->toDateTimeString());
        $this->info(" Mode: " . ($isLoop ? "Continuous Daemon (every {$interval}s)" : "Single Run"));
        $this->info("======================================================");

        do {
            $this->runSyncCycle($zkService);

            if ($isLoop) {
                $this->comment("Sleeping {$interval} seconds... (Press Ctrl+C to stop)");
                sleep($interval);
            }
        } while ($isLoop);

        return 0;
    }

    protected function runSyncCycle(ZkTecoService $zkService)
    {
        $deviceId = $this->option('device');
        $query = HrBiometricDevice::where('is_active', true);
        if ($deviceId) {
            $query->where('id', $deviceId);
        }

        $devices = $query->get();

        if ($devices->isEmpty()) {
            $this->warn("[" . Carbon::now()->format('H:i:s') . "] No active biometric devices found to sync.");
            return;
        }

        foreach ($devices as $device) {
            $this->line("[" . Carbon::now()->format('H:i:s') . "] Checking Device: {$device->name} ({$device->device_ip}:{$device->port})...");

            try {
                $conn = $zkService->testConnection($device->device_ip, $device->port, 2);

                if (!$conn['success']) {
                    $device->update(['status' => 'offline']);
                    $this->error("  ✗ Device offline / unreachable ({$conn['message']})");
                    continue;
                }

                $device->update([
                    'status' => 'online',
                    'last_sync_at' => Carbon::now()
                ]);

                $logs = $zkService->fetchLiveLogs($device->device_ip, $device->port, 4);

                if (empty($logs)) {
                    $this->info("  ✓ Device is online ({$conn['latency']}ms). No new unread punch logs.");
                    continue;
                }

                $result = $zkService->syncLogsToAttendance($logs, 'artisan_auto_sync', $device->id);

                $this->info("  ✓ Synced {$result['synced_punches']} punches! Updated attendance for {$result['employees_updated']} employees across {$result['affected_days']} days.");

            } catch (\Exception $e) {
                $device->update(['status' => 'offline']);
                $this->error("  ✗ Error syncing device: " . $e->getMessage());
            }
        }
    }
}
