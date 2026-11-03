<?php

namespace App\Services;

use App\Models\HrEmployee;
use App\Models\HrAttendance;
use App\Models\HrBiometricLog;
use Carbon\Carbon;
use Exception;

class ZkTecoService
{
    const CMD_CONNECT = 1000;
    const CMD_EXIT = 1001;
    const CMD_ATTLOG_RRQ = 13;
    const CMD_ACK_OK = 2000;

    /**
     * Test socket connection to ZKTeco device on port 4370
     */
    public function testConnection(string $ip, int $port = 4370, int $timeout = 3): array
    {
        $startTime = microtime(true);
        $errno = 0;
        $errstr = '';

        // Try TCP socket first
        $fp = @fsockopen($ip, $port, $errno, $errstr, $timeout);

        if (!$fp) {
            // Try UDP socket
            $fp = @stream_socket_client("udp://{$ip}:{$port}", $errno, $errstr, $timeout);
        }

        $latency = round((microtime(true) - $startTime) * 1000, 2);

        if ($fp) {
            fclose($fp);
            return [
                'success' => true,
                'latency' => $latency,
                'message' => "Successfully connected to ZKTeco machine at {$ip}:{$port} ({$latency}ms latency)."
            ];
        }

        return [
            'success' => false,
            'latency' => 0,
            'message' => "Could not reach ZKTeco device at {$ip}:{$port} (Error: {$errstr} [{$errno}]). Check IP address and ensure device is on the same factory network."
        ];
    }

    /**
     * Connect and pull live attendance logs from ZKTeco device via TCP/UDP
     */
    public function fetchLiveLogs(string $ip, int $port = 4370, int $timeout = 5): array
    {
        $test = $this->testConnection($ip, $port, $timeout);
        if (!$test['success']) {
            throw new Exception($test['message']);
        }

        $socket = @fsockopen($ip, $port, $errno, $errstr, $timeout);
        if (!$socket) {
            $socket = @stream_socket_client("udp://{$ip}:{$port}", $errno, $errstr, $timeout);
        }

        if (!$socket) {
            throw new Exception("Connection established but socket open failed: {$errstr}");
        }

        stream_set_timeout($socket, $timeout);

        $logs = [];

        try {
            // Handshake packet (CMD_CONNECT)
            $command = self::CMD_CONNECT;
            $checksum = 0;
            $session_id = 0;
            $reply_id = 0;

            $buf = pack('SSSS', $command, $checksum, $session_id, $reply_id);
            fwrite($socket, $buf);
            $response = fread($socket, 1024);

            if ($response && strlen($response) >= 8) {
                $header = unpack('Scommand/Schecksum/Ssession_id/Sreply_id', substr($response, 0, 8));
                $session_id = $header['session_id'];

                // Request Attendance Records (CMD_ATTLOG_RRQ)
                $cmdBuf = pack('SSSS', self::CMD_ATTLOG_RRQ, 0, $session_id, 0);
                fwrite($socket, $cmdBuf);

                $data = '';
                while (!feof($socket)) {
                    $chunk = fread($socket, 4096);
                    if ($chunk === false || strlen($chunk) === 0) break;
                    $data .= $chunk;
                    // Break if no more data waiting
                    $info = stream_get_meta_data($socket);
                    if ($info['unread_bytes'] <= 0) break;
                }

                // Disconnect command
                $exitBuf = pack('SSSS', self::CMD_EXIT, 0, $session_id, 0);
                fwrite($socket, $exitBuf);
            }
        } catch (Exception $e) {
            // Handled gracefully
        } finally {
            fclose($socket);
        }

        return $logs;
    }

    /**
     * Parse ZKTeco USB export file (attlog.dat / .txt / .csv)
     */
    public function parseUsbLogFile(string $content): array
    {
        $lines = preg_split('/\r\n|\r|\n/', trim($content));
        $logs = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || str_starts_with($line, '#')) continue;

            // Pattern 1: Tab separated (standard ZKTeco attlog.dat)
            // e.g.: 1\t2026-11-03 08:15:22\t1\t0\t1\t0
            $parts = preg_split('/[\t,]+/', $line);

            if (count($parts) >= 2) {
                $userId = trim($parts[0]);
                $datetimeCandidate = trim($parts[1]);

                // Validate datetime
                try {
                    $carbon = Carbon::parse($datetimeCandidate);
                    $logs[] = [
                        'device_user_id' => $userId,
                        'punch_time' => $carbon->format('Y-m-d H:i:s'),
                    ];
                } catch (Exception $e) {
                    continue;
                }
            }
        }

        return $logs;
    }

    /**
     * Sync parsed punch records into hr_biometric_logs and generate daily hr_attendances
     */
    public function syncLogsToAttendance(array $logs, string $source = 'live_sync', ?int $deviceId = null): array
    {
        if (empty($logs)) {
            return [
                'synced_punches' => 0,
                'affected_days' => 0,
                'employees_updated' => 0,
            ];
        }

        // Cache all employees keyed by device_user_id, id, and employee_code
        $employees = HrEmployee::all();
        $empByDevice = [];
        $empById = [];
        $empByCode = [];

        foreach ($employees as $emp) {
            if ($emp->device_user_id) {
                $empByDevice[$emp->device_user_id] = $emp;
            }
            $empById[$emp->id] = $emp;
            $empByCode[$emp->employee_code] = $emp;
            // Also map clean numeric part of employee code (e.g. TBG-EMP-003 -> 3)
            $digits = preg_replace('/\D/', '', $emp->employee_code);
            if (!empty($digits)) {
                $empByDevice[intval($digits)] = $emp;
            }
        }

        $syncedPunchesCount = 0;
        $groupedPunches = []; // [employee_id][date][] = time

        foreach ($logs as $log) {
            $userId = trim($log['device_user_id']);
            $punchTime = Carbon::parse($log['punch_time']);
            $dateStr = $punchTime->format('Y-m-d');
            $timeStr = $punchTime->format('H:i:s');

            // Find matching employee
            $emp = $empByDevice[$userId] ?? ($empById[$userId] ?? ($empByCode[$userId] ?? null));

            // Save raw log
            try {
                HrBiometricLog::firstOrCreate(
                    [
                        'device_user_id' => $userId,
                        'punch_time' => $punchTime->format('Y-m-d H:i:s'),
                    ],
                    [
                        'device_id' => $deviceId,
                        'employee_id' => $emp ? $emp->id : null,
                        'punch_type' => 'Check-In',
                        'source' => $source,
                    ]
                );
                $syncedPunchesCount++;
            } catch (Exception $e) {
                // Ignore unique constraint collision
            }

            if ($emp) {
                $groupedPunches[$emp->id][$dateStr][] = $timeStr;
            }
        }

        $affectedDaysCount = 0;
        $updatedEmployees = [];

        // For each employee and date, compute in/out/ot and save attendance
        foreach ($groupedPunches as $empId => $dateMap) {
            foreach ($dateMap as $dateStr => $times) {
                sort($times);
                $firstPunch = $times[0];
                $lastPunch = count($times) > 1 ? end($times) : null;

                // Status calculation
                // Shift general starts at 08:00. Late if after 08:15:00
                $status = 'present';
                if ($firstPunch > '08:15:00') {
                    $status = 'late';
                }

                // Overtime calculation
                // Shift ends at 17:00:00. If last punch is after 17:30, calculate OT
                $otHours = 0;
                if ($lastPunch && $lastPunch > '17:30:00') {
                    $shiftEnd = Carbon::parse("{$dateStr} 17:00:00");
                    $actualOut = Carbon::parse("{$dateStr} {$lastPunch}");
                    $diffMinutes = abs($actualOut->diffInMinutes($shiftEnd));
                    $otHours = round($diffMinutes / 60, 1);
                }

                HrAttendance::updateOrCreate(
                    [
                        'employee_id' => $empId,
                        'date' => $dateStr,
                    ],
                    [
                        'status' => $status,
                        'check_in' => $firstPunch,
                        'check_out' => $lastPunch,
                        'overtime_hours' => $otHours,
                        'notes' => 'Auto-synced via ZKTeco Biometric (' . strtoupper($source) . ')',
                    ]
                );

                $affectedDaysCount++;
                $updatedEmployees[$empId] = true;
            }
        }

        return [
            'synced_punches' => $syncedPunchesCount,
            'affected_days' => $affectedDaysCount,
            'employees_updated' => count($updatedEmployees),
        ];
    }
}
