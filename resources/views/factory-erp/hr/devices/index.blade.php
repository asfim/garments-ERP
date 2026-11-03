@extends('layouts.app')

@section('title', 'ZKTeco Biometric Devices | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-cyan-500/20 text-cyan-400 border border-cyan-500/30 flex items-center justify-center">
                    <i class="fa-solid fa-fingerprint text-base"></i>
                </span>
                <span>ZKTeco Biometric Devices & Attendance Sync (হাজিরা মেশিন)</span>
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">
                Manage factory biometric machines (TCP/IP Port 4370) or upload USB flash drive <code class="text-cyan-300 font-mono">attlog.dat</code> files for 100% automated daily attendance & overtime calculation.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Link to Daily Attendance -->
            <a href="{{ route('factory.hr.attendance.index') }}" 
               class="px-3.5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-amber-400 hover:text-amber-300 border border-slate-700 font-bold text-xs shadow-lg transition-all flex items-center gap-2">
                <i class="fa-solid fa-clipboard-user"></i>
                <span>Daily Attendance</span>
            </a>

            <!-- Quick Action: Import USB Log -->
            <button type="button" onclick="openUsbModal()"
                    class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-500 hover:to-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-500/25 transition-all flex items-center gap-2">
                <i class="fa-solid fa-file-import"></i>
                <span>Import USB (attlog.dat)</span>
            </button>

            <!-- Add Device Button -->
            <button type="button" onclick="openCreateDeviceModal()" 
                    class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-500 hover:to-cyan-500 text-white font-bold text-xs shadow-lg shadow-cyan-500/25 transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                <span>Add Biometric Device</span>
            </button>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
    <div class="p-3.5 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs flex items-center gap-2.5 shadow-lg">
        <i class="fa-solid fa-circle-check text-emerald-400 text-base"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('info'))
    <div class="p-3.5 rounded-2xl bg-blue-500/20 border border-blue-500/40 text-blue-300 text-xs flex items-center gap-2.5 shadow-lg">
        <i class="fa-solid fa-circle-info text-blue-400 text-base"></i>
        <span>{{ session('info') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="p-3.5 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs flex items-center gap-2.5 shadow-lg">
        <i class="fa-solid fa-triangle-exclamation text-rose-400 text-base"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    @if(isset($errors) && $errors->any())
    <div class="p-3.5 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs shadow-lg">
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- REAL-TIME BIOMETRIC AUTO-SYNC CONTROLLER -->
    <div class="glass-panel rounded-2xl border border-cyan-500/40 p-4 shadow-xl bg-gradient-to-r from-blue-950/40 via-cyan-950/30 to-indigo-950/40">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            
            <!-- Left Info & Status -->
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-cyan-500/20 border border-cyan-400/40 flex items-center justify-center text-cyan-300 text-lg flex-shrink-0 shadow-lg shadow-cyan-500/20">
                    <i class="fa-solid fa-bolt text-cyan-400" id="autoSyncIcon"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-extrabold text-white">Live Biometric Auto-Sync (স্বয়ংক্রিয় লাইভ সিঙ্ক)</h3>
                        <span id="autoSyncStatusBadge" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                            <span>ACTIVE</span>
                        </span>
                    </div>
                    <p class="text-xs text-slate-300 mt-0.5 flex flex-wrap items-center gap-2">
                        <span>Pulls punches automatically from active machines without manual clicks.</span>
                        <span class="text-slate-600">•</span>
                        <span id="autoSyncCountdownText" class="text-cyan-300 font-mono text-[11px] font-bold">Next sync in 60s</span>
                    </p>
                </div>
            </div>

            <!-- Right Controls: Toggle, Interval & Instant Trigger -->
            <div class="flex flex-wrap items-center gap-3">
                <!-- Interval Selector -->
                <div class="flex items-center gap-1.5 bg-[#070d1e] px-3 py-1.5 rounded-xl border border-slate-700/80 text-xs">
                    <span class="text-slate-400 text-[11px]">Interval:</span>
                    <select id="autoSyncIntervalSelect" onchange="changeAutoSyncInterval(this.value)" class="bg-transparent text-cyan-300 font-bold focus:outline-none cursor-pointer text-xs">
                        <option value="15" class="bg-slate-900 text-white">Every 15s</option>
                        <option value="30" class="bg-slate-900 text-white">Every 30s</option>
                        <option value="60" class="bg-slate-900 text-white" selected>Every 60s (1 min)</option>
                        <option value="120" class="bg-slate-900 text-white">Every 2 mins</option>
                        <option value="300" class="bg-slate-900 text-white">Every 5 mins</option>
                    </select>
                </div>

                <!-- Instant Trigger Now -->
                <button type="button" onclick="triggerImmediateAutoSync()" id="btnTriggerAutoSync" 
                        class="px-3.5 py-2 rounded-xl bg-cyan-600/30 hover:bg-cyan-600/50 text-cyan-300 border border-cyan-500/40 font-bold text-xs flex items-center gap-1.5 transition-all">
                    <i class="fa-solid fa-arrows-rotate" id="triggerSyncIcon"></i>
                    <span>Sync Now</span>
                </button>

                <!-- Toggle Switch -->
                <label class="relative inline-flex items-center cursor-pointer select-none">
                    <input type="checkbox" id="autoSyncToggle" checked onchange="toggleAutoSync(this.checked)" class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                    <span class="ml-2 text-xs font-bold text-white peer-checked:text-emerald-300" id="autoSyncToggleLabel">ON</span>
                </label>
            </div>

        </div>

        <!-- Live Status Bar -->
        <div class="mt-3 pt-3 border-t border-slate-800/80 flex flex-wrap items-center justify-between text-xs text-slate-400 gap-2">
            <div class="flex items-center gap-3">
                <span id="lastAutoSyncTime" class="font-mono text-slate-300">
                    <i class="fa-solid fa-clock text-slate-500 mr-1"></i>
                    Last auto-sync: <span class="text-cyan-400 font-bold">Just now</span>
                </span>
                <span class="text-slate-600">•</span>
                <span id="autoSyncSummary" class="text-emerald-400 font-medium">Ready</span>
            </div>
            
            <div class="flex items-center gap-2 text-[11px] font-mono text-slate-400">
                <span>Background Daemon CLI:</span>
                <code class="px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-amber-300">php artisan biometric:sync --loop</code>
            </div>
        </div>
    </div>

    <!-- KPI Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <!-- 1. Total Machines -->
        <div class="glass-panel p-4 rounded-2xl border border-slate-700/60 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Devices</div>
                    <div class="text-2xl font-black text-white mt-1">{{ $totalDevices }}</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400 text-base group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-fingerprint"></i>
                </div>
            </div>
            <div class="mt-2 text-[11px] text-cyan-400/90 font-medium flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>{{ $activeDevices }} Active in Factory</span>
            </div>
        </div>

        <!-- 2. Biometric Enrolled Workers -->
        <div class="glass-panel p-4 rounded-2xl border border-slate-700/60 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Biometric ID Mapped</div>
                    <div class="text-2xl font-black text-emerald-400 mt-1">{{ $biometricEmployeesCount }} <span class="text-xs font-normal text-slate-400">/ {{ $totalEmployees }}</span></div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 text-base group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-id-badge"></i>
                </div>
            </div>
            <div class="mt-2 text-[11px] text-slate-400 flex items-center justify-between">
                <span>Enrolled employees</span>
                <span class="font-bold text-white">{{ $totalEmployees > 0 ? round(($biometricEmployeesCount / $totalEmployees) * 100) : 0 }}%</span>
            </div>
        </div>

        <!-- 3. Total Punches -->
        <div class="glass-panel p-4 rounded-2xl border border-slate-700/60 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Punch Logs</div>
                    <div class="text-2xl font-black text-indigo-400 mt-1">{{ number_format($totalLogsCount) }}</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/30 flex items-center justify-center text-indigo-400 text-base group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
            </div>
            <div class="mt-2 text-[11px] text-slate-400">
                Processed biometric scans
            </div>
        </div>

        <!-- 4. Sync Methods -->
        <div class="glass-panel p-4 rounded-2xl border border-slate-700/60 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Supported Methods</div>
                    <div class="text-sm font-bold text-teal-300 mt-1">TCP/IP + USB Flash</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/30 flex items-center justify-center text-teal-400 text-base group-hover:scale-110 transition-transform">
                    <i class="fa-brands fa-usb"></i>
                </div>
            </div>
            <div class="mt-2 text-[11px] text-teal-400/90 font-mono">
                Port 4370 & attlog.dat
            </div>
        </div>
    </div>

    <!-- Registered Devices Section -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-server text-cyan-400"></i>
                <span>Registered ZKTeco Devices (কারখানার বায়োমেট্রিক মেশিনসমূহ)</span>
            </h3>
            <span class="text-xs text-slate-400">{{ $devices->count() }} devices registered</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($devices as $dev)
            <div class="glass-panel rounded-2xl border border-slate-700/60 p-5 flex flex-col justify-between hover:border-cyan-500/40 transition-all shadow-xl group relative">
                
                <!-- Status Badge Header -->
                <div>
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <div>
                            <h4 class="font-bold text-white text-sm group-hover:text-cyan-300 transition-colors flex items-center gap-2">
                                <i class="fa-solid fa-fingerprint text-cyan-400"></i>
                                <span>{{ $dev->name }}</span>
                            </h4>
                            <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                                {{ $dev->device_model ?: 'ZKTeco K40 / Standard' }}
                            </div>
                        </div>

                        <!-- Status badge -->
                        <span id="device-badge-{{ $dev->id }}" 
                              class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $dev->status === 'online' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' : ($dev->status === 'offline' ? 'bg-rose-500/20 text-rose-300 border-rose-500/30' : 'bg-slate-700/50 text-slate-300 border-slate-600') }}">
                            <span class="inline-block w-1.5 h-1.5 rounded-full {{ $dev->status === 'online' ? 'bg-emerald-400 animate-pulse' : 'bg-slate-400' }} mr-1"></span>
                            {{ strtoupper($dev->status) }}
                        </span>
                    </div>

                    <!-- Connection Specs -->
                    <div class="bg-[#060c1d] rounded-xl p-3 border border-slate-800 space-y-1.5 text-xs font-mono">
                        <div class="flex justify-between items-center text-slate-300">
                            <span class="text-slate-500 text-[11px]">IP Address:</span>
                            <span class="text-cyan-400 font-bold">{{ $dev->device_ip }}:{{ $dev->port }}</span>
                        </div>
                        <div class="flex justify-between items-center text-slate-300">
                            <span class="text-slate-500 text-[11px]">Protocol:</span>
                            <span class="text-slate-300">{{ $dev->protocol ?: 'TCP/IP (Socket)' }}</span>
                        </div>
                        <div class="flex justify-between items-center text-slate-300">
                            <span class="text-slate-500 text-[11px]">Location / Floor:</span>
                            <span class="text-slate-300 font-sans truncate max-w-[160px]" title="{{ $dev->notes }}">{{ $dev->notes ?: 'Main Gate' }}</span>
                        </div>
                        <div class="flex justify-between items-center text-slate-300 pt-1 border-t border-slate-800/80">
                            <span class="text-slate-500 text-[11px]">Last Synced:</span>
                            <span class="text-amber-400 text-[11px]">
                                {{ $dev->last_sync_at ? $dev->last_sync_at->diffForHumans() : 'Never' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center text-slate-300">
                            <span class="text-slate-500 text-[11px]">Logged Scans:</span>
                            <span class="text-emerald-400 font-bold">{{ number_format($dev->logs_count) }} punches</span>
                        </div>
                    </div>

                    <!-- Live Ping Response Indicator -->
                    <div id="ping-indicator-{{ $dev->id }}" class="mt-2.5 hidden text-xs p-2 rounded-lg font-mono"></div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1.5">
                        <!-- Test Connection AJAX -->
                        <button type="button" 
                                onclick="testConnection({{ $dev->id }}, '{{ $dev->device_ip }}', {{ $dev->port }})" 
                                id="btn-test-{{ $dev->id }}"
                                class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-cyan-400 hover:text-cyan-300 border border-slate-700 font-semibold text-[11px] transition-all flex items-center gap-1.5">
                            <i class="fa-solid fa-wifi text-[10px]"></i>
                            <span>Test Ping</span>
                        </button>

                        <!-- Live Sync POST -->
                        <form action="{{ route('factory.hr.devices.sync', $dev->id) }}" method="POST" onsubmit="this.querySelector('button').disabled = true;">
                            @csrf
                            <button type="submit" 
                                    class="px-2.5 py-1.5 rounded-lg bg-cyan-600/20 hover:bg-cyan-600/40 text-cyan-300 border border-cyan-500/30 font-semibold text-[11px] transition-all flex items-center gap-1.5">
                                <i class="fa-solid fa-rotate text-[10px]"></i>
                                <span>Sync Now</span>
                            </button>
                        </form>
                    </div>

                    <div class="flex items-center gap-1">
                        <!-- Edit Button -->
                        <button type="button" 
                                onclick="openEditDeviceModal({{ json_encode($dev) }})" 
                                class="p-1.5 text-slate-400 hover:text-cyan-400 rounded-lg hover:bg-slate-800 transition-colors" title="Edit Device">
                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                        </button>

                        <!-- Delete Button -->
                        <form action="{{ route('factory.hr.devices.destroy', $dev->id) }}" method="POST" onsubmit="return confirm('Delete this biometric device configuration?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-400 rounded-lg hover:bg-slate-800 transition-colors" title="Delete Device">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
            @empty
            <div class="col-span-full p-8 rounded-2xl glass-panel text-center text-slate-400 border border-slate-800">
                <i class="fa-solid fa-fingerprint text-3xl text-cyan-400 mb-2"></i>
                <h4 class="font-bold text-white text-sm">No Biometric Machines Registered</h4>
                <p class="text-xs text-slate-400 mt-1 max-w-md mx-auto">
                    Add your factory's ZKTeco biometric machine IP address (default port 4370) or import punch logs via pendrive.
                </p>
                <button type="button" onclick="openCreateDeviceModal()" class="mt-4 px-4 py-2 rounded-xl bg-cyan-600 text-white font-bold text-xs">
                    + Add First Device
                </button>
            </div>
            @endforelse
        </div>
    </div>

    <!-- USB File & Paste Import Card -->
    <div class="glass-panel rounded-2xl border border-slate-700/60 p-5 shadow-xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-4 pb-3 border-b border-slate-800">
            <div>
                <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-teal-500/20 text-teal-400 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-file-arrow-up"></i>
                    </span>
                    <span>Direct USB Flash Drive Import (পেনড্রাইভ দিয়ে হাজিরা লগ আপলোড)</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">
                    If your factory network is isolated or without static IP, plug a pendrive into the ZKTeco machine, download attendance data (<code class="text-teal-300 font-mono">attlog.dat</code>), and upload it here.
                </p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-teal-500/10 text-teal-300 border border-teal-500/30">
                Supports: attlog.dat, .txt, .csv
            </span>
        </div>

        <form action="{{ route('factory.hr.devices.import-usb') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Option A: File Upload -->
                <div class="p-4 rounded-xl bg-[#060c1d] border border-slate-800 flex flex-col justify-between">
                    <div>
                        <label class="block text-xs font-bold text-teal-300 mb-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-upload"></i>
                            <span>Upload attlog.dat or TXT/CSV</span>
                        </label>
                        <p class="text-[11px] text-slate-400 mb-3">
                            Directly select the exported file from your pendrive:
                        </p>
                        <input type="file" name="log_file" accept=".dat,.txt,.csv" 
                               class="w-full text-xs text-slate-300 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-600 file:text-white hover:file:bg-teal-500 cursor-pointer bg-slate-900/60 p-2 rounded-xl border border-slate-700/80">
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-800 text-[11px] text-slate-400">
                        <span class="font-bold text-slate-300">File format:</span> Tab or comma separated (e.g. <code class="text-teal-300">1 \t 2026-11-03 08:14:22</code>)
                    </div>
                </div>

                <!-- Option B: Direct Paste -->
                <div class="p-4 rounded-xl bg-[#060c1d] border border-slate-800">
                    <label class="block text-xs font-bold text-cyan-300 mb-1 flex items-center gap-1.5">
                        <i class="fa-solid fa-paste"></i>
                        <span>Or Paste Attendance Log Text</span>
                    </label>
                    <p class="text-[11px] text-slate-400 mb-2">
                        Paste lines directly from the log file:
                    </p>
                    <textarea name="log_text" rows="3" placeholder="1	2026-11-03 08:14:02	1	0	1	0&#10;2	2026-11-03 08:18:45	1	0	1	0" 
                              class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono text-[11px]"></textarea>
                </div>
            </div>

            <!-- Target Device & Submit -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
                <div class="w-full sm:w-auto flex items-center gap-2">
                    <label class="text-xs text-slate-400 whitespace-nowrap">Associate with Machine:</label>
                    <select name="device_id" class="bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                        <option value="">Auto / Any Device</option>
                        @foreach($devices as $dev)
                            <option value="{{ $dev->id }}">{{ $dev->name }} ({{ $dev->device_ip }})</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" 
                        class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-500 hover:to-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-500/25 transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-bolt"></i>
                    <span>Process & Sync Attendance Records</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Recent Biometric Punch Logs Table -->
    <div class="glass-panel rounded-2xl border border-slate-700/60 overflow-hidden shadow-2xl">
        <div class="p-4 border-b border-slate-700/60 flex items-center justify-between">
            <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-indigo-400"></i>
                <span>Live Raw Punch Stream (সর্বশেষ ১০০টি বায়োমেট্রিক পাঞ্চ লগ)</span>
            </h3>
            <span class="text-xs text-slate-400 font-mono">{{ $recentLogs->count() }} records shown</span>
        </div>

        <div class="overflow-x-auto max-h-[420px] overflow-y-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="sticky top-0 bg-[#09112a] text-[10px] uppercase font-bold text-slate-400 tracking-wider border-b border-slate-700/80 z-10">
                    <tr>
                        <th class="py-2.5 px-3">Punch Time (সময়)</th>
                        <th class="py-2.5 px-3">Machine User ID</th>
                        <th class="py-2.5 px-3">Employee Name</th>
                        <th class="py-2.5 px-3">Department & Role</th>
                        <th class="py-2.5 px-3">Device / Machine</th>
                        <th class="py-2.5 px-3">Source</th>
                        <th class="py-2.5 px-3 text-center">Type</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80 font-mono" id="livePunchTableBody">
                    @forelse($recentLogs as $log)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="py-2.5 px-3 whitespace-nowrap">
                            <span class="font-bold text-white">{{ $log->punch_time ? $log->punch_time->format('d M, Y') : '—' }}</span>
                            <span class="text-cyan-400 font-bold ml-1">{{ $log->punch_time ? $log->punch_time->format('h:i:s A') : '—' }}</span>
                        </td>
                        <td class="py-2.5 px-3">
                            <span class="px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-300 border border-cyan-500/30 font-bold">
                                #{{ $log->device_user_id }}
                            </span>
                        </td>
                        <td class="py-2.5 px-3 font-sans">
                            @if($log->employee)
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-lg bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center font-bold text-[10px] uppercase">
                                        {{ substr($log->employee->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('factory.hr.employees.show', $log->employee->id) }}" class="font-bold text-white hover:text-cyan-300 transition-colors">
                                            {{ $log->employee->name }}
                                        </a>
                                        <div class="text-[10px] text-slate-400 font-mono">{{ $log->employee->employee_code }}</div>
                                    </div>
                                </div>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] bg-amber-500/10 text-amber-300 border border-amber-500/20">
                                    Unmapped (Worker #{{ $log->device_user_id }})
                                </span>
                            @endif
                        </td>
                        <td class="py-2.5 px-3 font-sans">
                            @if($log->employee)
                                <div class="text-slate-200 font-medium">{{ $log->employee->department->name ?? '—' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $log->employee->designation->title ?? '—' }}</div>
                            @else
                                <span class="text-slate-500">—</span>
                            @endif
                        </td>
                        <td class="py-2.5 px-3 font-sans">
                            <span class="text-slate-300">{{ $log->device->name ?? 'ZKTeco K40' }}</span>
                        </td>
                        <td class="py-2.5 px-3">
                            @if($log->source === 'live_sync')
                                <span class="text-[10px] px-2 py-0.5 rounded bg-blue-500/20 text-blue-300 border border-blue-500/30 font-sans">Live LAN</span>
                            @elseif($log->source === 'usb_import')
                                <span class="text-[10px] px-2 py-0.5 rounded bg-teal-500/20 text-teal-300 border border-teal-500/30 font-sans">USB Pendrive</span>
                            @elseif($log->source === 'adms_push')
                                <span class="text-[10px] px-2 py-0.5 rounded bg-purple-500/20 text-purple-300 border border-purple-500/30 font-sans">ADMS Cloud</span>
                            @else
                                <span class="text-[10px] text-slate-400">{{ $log->source }}</span>
                            @endif
                        </td>
                        <td class="py-2.5 px-3 text-center">
                            <span class="text-[10px] px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700">
                                {{ $log->punch_type ?: 'Check-In' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400 font-sans">
                            <i class="fa-solid fa-inbox text-2xl mb-1 text-slate-600 block"></i>
                            No biometric punches recorded yet. Run Live Sync or Import USB Log to view records.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ============================================================== -->
<!-- 1. CREATE DEVICE MODAL                                         -->
<!-- ============================================================== -->
<div id="createDeviceModal" class="fixed inset-0 z-50 hidden bg-black/75 backdrop-blur-sm overflow-y-auto p-4 flex items-center justify-center">
    <div class="relative w-full max-w-lg bg-[#09112a] border border-cyan-500/30 rounded-2xl shadow-2xl p-6 text-white my-8">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-700/60 mb-4">
            <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-plus text-cyan-400"></i>
                <span>Add ZKTeco Biometric Device (মেশিন যোগ করুন)</span>
            </h3>
            <button type="button" onclick="closeCreateDeviceModal()" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('factory.hr.devices.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Device Name / Label *</label>
                <input type="text" name="name" required placeholder="e.g. Main Gate Biometric Reader" 
                       class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-slate-300 mb-1">IP Address *</label>
                    <input type="text" name="ip_address" required placeholder="192.168.1.201" 
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Port *</label>
                    <input type="number" name="port" required value="4370" 
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Device Model</label>
                    <input type="text" name="model" placeholder="ZKTeco K40 / MB20 / IN01" 
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Protocol</label>
                    <select name="protocol" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                        <option value="TCP/IP">TCP/IP (Standard)</option>
                        <option value="UDP">UDP</option>
                        <option value="ADMS">ADMS / Cloud Push</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Factory Location / Floor</label>
                <input type="text" name="location" placeholder="Floor 1 Entrance / Cutting Section" 
                       class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" id="createIsActive" value="1" checked class="rounded bg-slate-900 border-slate-700 text-cyan-600 focus:ring-0">
                <label for="createIsActive" class="text-xs text-slate-300 select-none">Active device (ready for synchronization)</label>
            </div>

            <div class="flex justify-end gap-2.5 pt-4 border-t border-slate-700/60">
                <button type="button" onclick="closeCreateDeviceModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs font-semibold">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs shadow-lg shadow-cyan-500/25 flex items-center gap-1.5">
                    <i class="fa-solid fa-save"></i>
                    <span>Save Device</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================== -->
<!-- 2. EDIT DEVICE MODAL                                           -->
<!-- ============================================================== -->
<div id="editDeviceModal" class="fixed inset-0 z-50 hidden bg-black/75 backdrop-blur-sm overflow-y-auto p-4 flex items-center justify-center">
    <div class="relative w-full max-w-lg bg-[#09112a] border border-blue-500/30 rounded-2xl shadow-2xl p-6 text-white my-8">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-700/60 mb-4">
            <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-cyan-400"></i>
                <span>Edit Biometric Device Details</span>
            </h3>
            <button type="button" onclick="closeEditDeviceModal()" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="editDeviceForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Device Name / Label *</label>
                <input type="text" name="name" id="editDeviceName" required 
                       class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-slate-300 mb-1">IP Address *</label>
                    <input type="text" name="ip_address" id="editDeviceIp" required 
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Port *</label>
                    <input type="number" name="port" id="editDevicePort" required 
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Device Model</label>
                    <input type="text" name="model" id="editDeviceModel" 
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Protocol</label>
                    <select name="protocol" id="editDeviceProtocol" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                        <option value="TCP/IP">TCP/IP (Standard)</option>
                        <option value="UDP">UDP</option>
                        <option value="ADMS">ADMS / Cloud Push</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Factory Location / Floor</label>
                <input type="text" name="location" id="editDeviceLocation" 
                       class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" id="editDeviceIsActive" value="1" class="rounded bg-slate-900 border-slate-700 text-cyan-600 focus:ring-0">
                <label for="editDeviceIsActive" class="text-xs text-slate-300 select-none">Active device</label>
            </div>

            <div class="flex justify-end gap-2.5 pt-4 border-t border-slate-700/60">
                <button type="button" onclick="closeEditDeviceModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs font-semibold">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs shadow-lg shadow-cyan-500/25 flex items-center gap-1.5">
                    <i class="fa-solid fa-save"></i>
                    <span>Update Device</span>
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openCreateDeviceModal() {
        document.getElementById('createDeviceModal').classList.remove('hidden');
    }

    function closeCreateDeviceModal() {
        document.getElementById('createDeviceModal').classList.add('hidden');
    }

    function openEditDeviceModal(dev) {
        document.getElementById('editDeviceForm').action = "/factory/hr/devices/" + dev.id;
        document.getElementById('editDeviceName').value = dev.name;
        document.getElementById('editDeviceIp').value = dev.device_ip || dev.ip_address || '';
        document.getElementById('editDevicePort').value = dev.port || 4370;
        document.getElementById('editDeviceModel').value = dev.device_model || dev.model || '';
        document.getElementById('editDeviceProtocol').value = dev.protocol || 'TCP/IP';
        document.getElementById('editDeviceLocation').value = dev.notes || dev.location || '';
        document.getElementById('editDeviceIsActive').checked = dev.is_active ? true : false;

        document.getElementById('editDeviceModal').classList.remove('hidden');
    }

    function closeEditDeviceModal() {
        document.getElementById('editDeviceModal').classList.add('hidden');
    }

    function openUsbModal() {
        window.scrollTo({ top: 380, behavior: 'smooth' });
    }

    function testConnection(deviceId, ip, port) {
        const btn = document.getElementById('btn-test-' + deviceId);
        const indicator = document.getElementById('ping-indicator-' + deviceId);
        const badge = document.getElementById('device-badge-' + deviceId);

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-[10px]"></i> <span>Testing...</span>';
        
        indicator.classList.remove('hidden', 'bg-emerald-500/20', 'text-emerald-300', 'bg-rose-500/20', 'text-rose-300', 'border-emerald-500/40', 'border-rose-500/40');
        indicator.classList.add('bg-slate-800', 'text-slate-300', 'border', 'border-slate-700');
        indicator.innerHTML = 'Connecting to ' + ip + ':' + port + '...';

        fetch('/factory/hr/devices/' + deviceId + '/test', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(async response => {
            const data = await response.json();
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-wifi text-[10px]"></i> <span>Test Ping</span>';

            indicator.classList.remove('bg-slate-800', 'text-slate-300');
            if (response.ok && data.success) {
                indicator.classList.add('bg-emerald-500/20', 'text-emerald-300', 'border-emerald-500/40');
                indicator.innerHTML = '<i class="fa-solid fa-check text-emerald-400 mr-1"></i> Online (' + data.latency + ' ms latency)';
                
                badge.className = 'px-2.5 py-1 rounded-full text-[10px] font-bold border bg-emerald-500/20 text-emerald-300 border-emerald-500/30';
                badge.innerHTML = '<span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse mr-1"></span> ONLINE';
            } else {
                indicator.classList.add('bg-rose-500/20', 'text-rose-300', 'border-rose-500/40');
                indicator.innerHTML = '<i class="fa-solid fa-xmark text-rose-400 mr-1"></i> Offline / Unreachable (' + (data.message || 'Timed out') + ')';

                badge.className = 'px-2.5 py-1 rounded-full text-[10px] font-bold border bg-rose-500/20 text-rose-300 border-rose-500/30';
                badge.innerHTML = '<span class="inline-block w-1.5 h-1.5 rounded-full bg-slate-400 mr-1"></span> OFFLINE';
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-wifi text-[10px]"></i> <span>Test Ping</span>';
            indicator.classList.remove('bg-slate-800', 'text-slate-300');
            indicator.classList.add('bg-rose-500/20', 'text-rose-300', 'border-rose-500/40');
            indicator.innerHTML = '<i class="fa-solid fa-xmark text-rose-400 mr-1"></i> Network error';
        });
    }

    // ==========================================================
    // REAL-TIME BIOMETRIC AUTO-SYNC ENGINE
    // ==========================================================
    let autoSyncEnabled = localStorage.getItem('tbg_biometric_autosync') !== 'false';
    let autoSyncInterval = parseInt(localStorage.getItem('tbg_biometric_interval') || '60');
    let autoSyncCountdown = autoSyncInterval;
    let autoSyncTimer = null;
    let autoSyncCountdownTimer = null;
    let isSyncing = false;

    function initAutoSync() {
        const toggle = document.getElementById('autoSyncToggle');
        const select = document.getElementById('autoSyncIntervalSelect');
        if (toggle) toggle.checked = autoSyncEnabled;
        if (select) select.value = autoSyncInterval;

        updateAutoSyncUI();
        if (autoSyncEnabled) {
            startAutoSyncTimers();
        }
    }

    function toggleAutoSync(checked) {
        autoSyncEnabled = checked;
        localStorage.setItem('tbg_biometric_autosync', checked ? 'true' : 'false');
        updateAutoSyncUI();

        if (autoSyncEnabled) {
            autoSyncCountdown = autoSyncInterval;
            startAutoSyncTimers();
            triggerImmediateAutoSync();
        } else {
            stopAutoSyncTimers();
        }
    }

    function changeAutoSyncInterval(val) {
        autoSyncInterval = parseInt(val);
        localStorage.setItem('tbg_biometric_interval', autoSyncInterval);
        autoSyncCountdown = autoSyncInterval;
        if (autoSyncEnabled) {
            startAutoSyncTimers();
        }
    }

    function updateAutoSyncUI() {
        const badge = document.getElementById('autoSyncStatusBadge');
        const label = document.getElementById('autoSyncToggleLabel');
        const icon = document.getElementById('autoSyncIcon');
        const countdownText = document.getElementById('autoSyncCountdownText');

        if (autoSyncEnabled) {
            if (badge) {
                badge.className = "px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 flex items-center gap-1.5";
                badge.innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span> <span>ACTIVE</span>';
            }
            if (label) {
                label.textContent = "ON";
                label.className = "ml-2 text-xs font-bold text-emerald-300";
            }
            if (icon) icon.className = "fa-solid fa-bolt text-cyan-400";
        } else {
            if (badge) {
                badge.className = "px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-700 text-slate-300 border border-slate-600 flex items-center gap-1.5";
                badge.innerHTML = '<span class="w-2 h-2 rounded-full bg-slate-400"></span> <span>PAUSED</span>';
            }
            if (label) {
                label.textContent = "OFF";
                label.className = "ml-2 text-xs font-bold text-slate-400";
            }
            if (icon) icon.className = "fa-solid fa-pause text-slate-400";
            if (countdownText) countdownText.textContent = "Auto-sync paused";
        }
    }

    function startAutoSyncTimers() {
        stopAutoSyncTimers();

        autoSyncCountdownTimer = setInterval(() => {
            if (!autoSyncEnabled) return;
            autoSyncCountdown--;
            const countdownText = document.getElementById('autoSyncCountdownText');
            if (countdownText) {
                countdownText.textContent = `Next sync in ${autoSyncCountdown}s`;
            }

            if (autoSyncCountdown <= 0) {
                autoSyncCountdown = autoSyncInterval;
                triggerAutoSyncRequest(false);
            }
        }, 1000);
    }

    function stopAutoSyncTimers() {
        if (autoSyncCountdownTimer) clearInterval(autoSyncCountdownTimer);
        if (autoSyncTimer) clearTimeout(autoSyncTimer);
    }

    function triggerImmediateAutoSync() {
        autoSyncCountdown = autoSyncInterval;
        triggerAutoSyncRequest(true);
    }

    function triggerAutoSyncRequest(isManual = false) {
        if (isSyncing) return;
        isSyncing = true;

        const syncIcon = document.getElementById('triggerSyncIcon');
        const summary = document.getElementById('autoSyncSummary');
        const lastTimeSpan = document.getElementById('lastAutoSyncTime');

        if (syncIcon) syncIcon.classList.add('fa-spin');
        if (summary) {
            summary.className = "text-cyan-400 font-medium";
            summary.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin text-[10px]"></i> Syncing active devices...';
        }

        fetch('/factory/hr/devices/auto-sync', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(async res => {
            isSyncing = false;
            if (syncIcon) syncIcon.classList.remove('fa-spin');

            const data = await res.json();
            if (res.ok && data.success) {
                if (lastTimeSpan) {
                    lastTimeSpan.innerHTML = `<i class="fa-solid fa-clock text-slate-500 mr-1"></i> Last auto-sync: <span class="text-cyan-400 font-bold">${data.timestamp}</span>`;
                }

                if (summary) {
                    if (data.total_synced_punches > 0) {
                        summary.className = "text-emerald-400 font-bold";
                        summary.innerHTML = `✓ Synced ${data.total_synced_punches} new punches (${data.total_employees_updated} employees updated)!`;
                    } else {
                        summary.className = "text-slate-400 font-medium";
                        summary.innerHTML = `✓ Connected (${data.devices.length} devices checked, no new unread punches)`;
                    }
                }

                if (data.devices) {
                    data.devices.forEach(dev => {
                        const badge = document.getElementById('device-badge-' + dev.id);
                        if (badge) {
                            if (dev.status === 'online') {
                                badge.className = 'px-2.5 py-1 rounded-full text-[10px] font-bold border bg-emerald-500/20 text-emerald-300 border-emerald-500/30';
                                badge.innerHTML = '<span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse mr-1"></span> ONLINE';
                            } else {
                                badge.className = 'px-2.5 py-1 rounded-full text-[10px] font-bold border bg-rose-500/20 text-rose-300 border-rose-500/30';
                                badge.innerHTML = '<span class="inline-block w-1.5 h-1.5 rounded-full bg-slate-400 mr-1"></span> OFFLINE';
                            }
                        }
                    });
                }

                if (data.total_synced_punches > 0 && data.recent_logs && data.recent_logs.length > 0) {
                    renderRecentLogsTable(data.recent_logs);
                }
            } else {
                if (summary) {
                    summary.className = "text-rose-400 font-medium";
                    summary.innerHTML = 'Device check completed';
                }
            }
        })
        .catch(err => {
            isSyncing = false;
            if (syncIcon) syncIcon.classList.remove('fa-spin');
            if (summary) {
                summary.className = "text-rose-400 font-medium";
                summary.innerHTML = 'Network sync error';
            }
        });
    }

    function renderRecentLogsTable(logs) {
        const tbody = document.getElementById('livePunchTableBody');
        if (!tbody) return;

        let html = '';
        logs.forEach(log => {
            const empHtml = log.employee_name ? `
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-lg bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center font-bold text-[10px] uppercase">
                        ${log.employee_name.substring(0, 2)}
                    </div>
                    <div>
                        <span class="font-bold text-white">${log.employee_name}</span>
                        <div class="text-[10px] text-slate-400 font-mono">${log.employee_code || ''}</div>
                    </div>
                </div>
            ` : `<span class="px-2 py-0.5 rounded text-[10px] bg-amber-500/10 text-amber-300 border border-amber-500/20">Unmapped (Worker #${log.device_user_id})</span>`;

            const deptHtml = log.department ? `
                <div class="text-slate-200 font-medium">${log.department}</div>
                <div class="text-[10px] text-slate-400">${log.designation || '—'}</div>
            ` : '<span class="text-slate-500">—</span>';

            html += `
                <tr class="hover:bg-slate-800/40 transition-colors bg-emerald-500/10">
                    <td class="py-2.5 px-3 whitespace-nowrap">
                        <span class="font-bold text-white">${log.punch_time}</span>
                    </td>
                    <td class="py-2.5 px-3">
                        <span class="px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-300 border border-cyan-500/30 font-bold">
                            #${log.device_user_id}
                        </span>
                    </td>
                    <td class="py-2.5 px-3 font-sans">${empHtml}</td>
                    <td class="py-2.5 px-3 font-sans">${deptHtml}</td>
                    <td class="py-2.5 px-3 font-sans text-slate-300">${log.device_name || 'ZKTeco'}</td>
                    <td class="py-2.5 px-3">
                        <span class="text-[10px] px-2 py-0.5 rounded bg-blue-500/20 text-blue-300 border border-blue-500/30 font-sans">
                            ${log.source}
                        </span>
                    </td>
                    <td class="py-2.5 px-3 text-center">
                        <span class="text-[10px] px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700">
                            ${log.punch_type || 'Check-In'}
                        </span>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
    }

    document.addEventListener('DOMContentLoaded', initAutoSync);
</script>
@endpush
