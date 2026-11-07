@extends('layouts.app')

@section('title', 'Employee Management | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-users text-cyan-400"></i>
                Employee & Worker Directory (কর্মচারী তালিকা)
            </h2>
            <p class="text-xs text-slate-400">Manage garments factory operators, supervisors, staff records, salaries and profiles</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Add Employee Button (Direct URL) -->
            <a href="{{ route('factory.hr.employees.create') }}"
               class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-bold text-xs shadow-lg shadow-cyan-500/25 transition-all flex items-center gap-2">
                <i class="fa-solid fa-user-plus"></i>
                <span>Add Employee</span>
            </a>

            <!-- Download CSV -->
            <a href="{{ route('factory.hr.employees.export.csv') }}" 
               class="px-3.5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-emerald-400 hover:text-emerald-300 border border-slate-700 hover:border-emerald-500/40 font-bold text-xs shadow-lg transition-all flex items-center gap-2">
                <i class="fa-solid fa-file-csv text-sm"></i>
                <span>Download CSV</span>
            </a>

            <!-- Download PDF -->
            <a href="{{ route('factory.hr.employees.export.pdf') }}" 
               class="px-3.5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-rose-400 hover:text-rose-300 border border-slate-700 hover:border-rose-500/40 font-bold text-xs shadow-lg transition-all flex items-center gap-2">
                <i class="fa-solid fa-file-pdf text-sm"></i>
                <span>Download PDF</span>
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="p-3 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs flex items-center gap-2">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="p-3 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs flex items-center gap-2">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif
    @if(isset($errors) && $errors->any())
    <div class="p-3 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Filters Bar -->
    <div class="glass-panel rounded-2xl p-4 border border-slate-700/60">
        <form method="GET" action="{{ route('factory.hr.employees.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Search Worker / Code</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Name, Phone, ID..." 
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-cyan-400">
                    <i class="fa-solid fa-magnifying-glass absolute right-3 top-2.5 text-slate-500 text-xs"></i>
                </div>
            </div>

            <!-- Department -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Department</label>
                <select name="department_id" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Status</label>
                <select name="status" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="resigned" {{ request('status') == 'resigned' ? 'selected' : '' }}>Resigned</option>
                    <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                </select>
            </div>

            <!-- Shift -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Shift</label>
                <select name="shift" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                    <option value="">All Shifts</option>
                    <option value="Day" {{ request('shift') == 'Day' ? 'selected' : '' }}>Day Shift</option>
                    <option value="Night" {{ request('shift') == 'Night' ? 'selected' : '' }}>Night Shift</option>
                    <option value="General" {{ request('shift') == 'General' ? 'selected' : '' }}>General Shift</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs transition-colors flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-filter"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('factory.hr.employees.index') }}" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Employees Table -->
    <div class="glass-panel rounded-2xl border border-slate-700/60 overflow-hidden">
        <div class="p-4 border-b border-slate-700/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-cyan-400"></i>
                    Employee Directory
                </h3>
                <span class="text-[11px] text-cyan-300 font-mono">
                    Showing <span id="employeeVisibleCount" class="font-bold">{{ min(10, $employees->count()) }}</span> of <span id="employeeTotalCount" class="font-bold">{{ $employees->count() }}</span> Employees
                </span>
            </div>

            <!-- Live Search Box (Worker ID, Name, Phone, Department, Designation) -->
            <div class="relative w-full sm:w-72">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="employeeSearchInput" placeholder="Live search employee, ID, phone..." 
                       class="w-full bg-[#070d1e] text-xs text-white placeholder-slate-500 rounded-xl pl-8 pr-7 py-2 border border-slate-700 focus:border-cyan-400 focus:outline-none transition-all">
                <button type="button" id="employeeClearSearchBtn" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white hidden text-xs">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <div class="erp-table-scroll rounded-2xl border border-slate-800/80 bg-slate-950/20">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="sticky top-0 z-10 bg-[#0c1429] text-slate-300 uppercase text-[10px] font-bold border-b border-slate-700/80 backdrop-blur-md shadow-sm">
                    <tr>
                        <th class="py-3 px-3">Worker ID</th>
                        <th class="py-3 px-3">Employee Name</th>
                        <th class="py-3 px-3">Department & Designation</th>
                        <th class="py-3 px-3">Phone & Emergency</th>
                        <th class="py-3 px-3">Shift & Type</th>
                        <th class="py-3 px-3 text-right">Gross Salary</th>
                        <th class="py-3 px-3 text-center">Status</th>
                        <th class="py-3 px-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="employeeTableBody" class="divide-y divide-slate-800/80">
                    @forelse($employees as $emp)
                    <tr class="employee-row hover:bg-slate-800/40 transition-colors"
                        data-search="{{ strtolower($emp->employee_code . ' ' . $emp->name . ' ' . $emp->phone . ' ' . ($emp->emergency_phone ?? '') . ' ' . ($emp->department->name ?? '') . ' ' . ($emp->designation->title ?? '') . ' ' . ($emp->shift ?? '') . ' ' . ($emp->status ?? '')) }}">
                        <td class="py-3 px-3 font-mono font-bold text-cyan-400">
                            <div>{{ $emp->employee_code }}</div>
                            @if($emp->device_user_id)
                                <div class="mt-0.5">
                                    <span class="inline-flex items-center gap-1 text-[9px] px-1.5 py-0.2 rounded bg-cyan-500/10 text-cyan-300 border border-cyan-500/30" title="Biometric Machine User ID">
                                        <i class="fa-solid fa-fingerprint text-[8px]"></i>
                                        ZK: #{{ $emp->device_user_id }}
                                    </span>
                                </div>
                            @endif
                        </td>
                        <td class="py-3 px-3">
                            <div class="flex items-center gap-2.5">
                                @if($emp->photo && file_exists(public_path('storage/' . $emp->photo)))
                                    <img src="{{ asset('storage/' . $emp->photo) }}" class="w-8 h-8 rounded-lg object-cover ring-1 ring-cyan-500/40">
                                @else
                                    <div class="w-8 h-8 rounded-lg bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center font-bold text-xs uppercase">
                                        {{ substr($emp->name, 0, 2) }}
                                    </div>
                                @endif
                                <div>
                                    <a href="{{ route('factory.hr.employees.show', $emp->id) }}" class="font-bold text-white hover:text-cyan-300 transition-colors flex items-center gap-1.5">
                                        <span>{{ $emp->name }}</span>
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[9px] text-cyan-400 opacity-60"></i>
                                    </a>
                                    <div class="text-[10px] text-slate-400 font-mono">
                                        Joined: {{ $emp->joining_date ? $emp->joining_date->format('d M, Y') : '—' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-3">
                            <div class="font-semibold text-slate-200">{{ $emp->department->name ?? 'N/A' }}</div>
                            <div class="text-[10px] text-indigo-400 font-medium">{{ $emp->designation->title ?? 'N/A' }}</div>
                        </td>
                        <td class="py-3 px-3 font-mono">
                            <div class="text-slate-200">{{ $emp->phone }}</div>
                            @if($emp->emergency_phone)
                                <div class="text-[10px] text-slate-400">Emg: {{ $emp->emergency_phone }}</div>
                            @endif
                        </td>
                        <td class="py-3 px-3">
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                                {{ $emp->shift }}
                            </span>
                            <div class="text-[10px] text-slate-400 capitalize mt-0.5">{{ $emp->employment_type }}</div>
                        </td>
                        <td class="py-3 px-3 text-right font-mono font-bold text-emerald-400">
                            ৳{{ number_format($emp->gross_salary, 2) }}
                        </td>
                        <td class="py-3 px-3 text-center">
                            @if($emp->status === 'active')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Active</span>
                            @elseif($emp->status === 'inactive')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-700 text-slate-300">Inactive</span>
                            @elseif($emp->status === 'resigned')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">Resigned</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">Terminated</span>
                            @endif
                        </td>
                        <td class="py-3 px-3 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <!-- 360 Profile & Attendance Calendar -->
                                <a href="{{ route('factory.hr.employees.show', $emp->id) }}" 
                                   title="View Full Profile & Attendance Calendar" class="p-1.5 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 text-xs transition-colors">
                                    <i class="fa-solid fa-address-card"></i>
                                </a>

                                <!-- View Profile Modal -->
                                <button type="button" onclick="openViewEmployeeModal({{ json_encode($emp) }})" 
                                        title="Quick ID Card" class="p-1.5 rounded-lg bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 text-xs transition-colors">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                
                                <!-- Edit (Direct URL) -->
                                <a href="{{ route('factory.hr.employees.edit', $emp->id) }}" 
                                   title="Edit Employee" class="p-1.5 rounded-lg bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 text-xs transition-colors">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                <!-- Delete -->
                                <form action="{{ route('factory.hr.employees.destroy', $emp->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete employee {{ $emp->name }}? All related records will also be removed.')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete Employee" class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 text-xs transition-colors">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-slate-400 text-xs">
                            <i class="fa-solid fa-users-slash text-2xl text-slate-500 mb-2 block"></i>
                            No employees found matching the filters.
                        </td>
                    </tr>
                    @endforelse

                    <!-- No Search Results Row -->
                    <tr id="employeeNoResultRow" class="hidden">
                        <td colspan="8" class="py-8 text-center text-slate-400 text-xs">
                            <i class="fa-solid fa-users-slash text-2xl text-slate-500 mb-2 block"></i>
                            No employees match your search query.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Universal Load More Bar -->
        @include('factory-erp.common.table-load-more', [
            'id' => 'employee',
            'total' => $employees->count(),
            'pageSize' => 10,
            'itemName' => 'Employees',
            'color' => 'cyan'
        ])
    </div>

</div>

<!-- ============================================================== -->
<!-- 1. CREATE EMPLOYEE MODAL                                       -->
<!-- ============================================================== -->
<div id="createEmployeeModal" class="fixed inset-0 z-50 hidden bg-black/75 backdrop-blur-sm overflow-y-auto p-4 flex items-center justify-center">
    <div class="relative w-full max-w-4xl bg-[#09112a] border border-cyan-500/30 rounded-2xl shadow-2xl p-6 text-white my-8">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-700/60 mb-5">
            <h3 class="text-base font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-cyan-400"></i>
                Add New Garment Worker / Employee (নতুন কর্মচারী নিবন্ধন)
            </h3>
            <button type="button" onclick="closeCreateEmployeeModal()" class="text-slate-400 hover:text-white text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('factory.hr.employees.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <!-- Section 1: Basic & Contact Info -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Employee ID / Code</label>
                    <input type="text" name="employee_code" placeholder="Leave empty for auto (TBG-EMP-XXX)" 
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-cyan-300 mb-1 flex items-center gap-1.5">
                        <i class="fa-solid fa-fingerprint text-cyan-400"></i>
                        Biometric Machine User ID (মেশিন আইডি)
                    </label>
                    <input type="text" name="device_user_id" placeholder="e.g. 101 or 1" 
                           class="w-full bg-[#0a122e] border border-cyan-500/40 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Full Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Md. Rafiqul Islam" 
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Phone Number *</label>
                    <input type="text" name="phone" required placeholder="01711-xxxxxx" 
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Emergency Phone</label>
                    <input type="text" name="emergency_phone" placeholder="Guardian / Relative phone" 
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Email Address</label>
                    <input type="email" name="email" placeholder="name@domain.com" 
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">National ID (NID)</label>
                    <input type="text" name="nid" placeholder="10 or 17 digit NID" 
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Gender *</label>
                    <select name="gender" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Blood Group</label>
                    <select name="blood_group" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                        <option value="">Unknown</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Worker Photo (ছবি আপলোড)</label>
                    <div class="flex items-center gap-2.5">
                        <div id="createPhotoPreviewContainer" class="w-10 h-10 rounded-xl bg-slate-800 border border-slate-700 overflow-hidden flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-user text-slate-500 text-base" id="createPhotoIcon"></i>
                            <img id="createPhotoPreview" class="w-full h-full object-cover hidden" alt="Preview">
                        </div>
                        <div class="flex-1">
                            <input type="file" name="photo" accept="image/*" onchange="previewPhoto(this, 'createPhotoPreview', 'createPhotoIcon')" 
                                   class="w-full text-xs text-slate-400 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:bg-cyan-600 file:text-white hover:file:bg-cyan-500 cursor-pointer">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Department, Designation & Job Details -->
            <div class="pt-3 border-t border-slate-700/60 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Department *</label>
                    <select name="department_id" id="createDeptSelect" required onchange="filterDesignations('create')" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                        <option value="">Select Department</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-300">Designation *</label>
                        <button type="button" onclick="openQuickAddDesModal('create')" class="text-[10px] text-cyan-400 hover:text-cyan-300 font-bold flex items-center gap-1 transition-colors">
                            <i class="fa-solid fa-plus-circle"></i>
                            <span>+ New Role</span>
                        </button>
                    </div>
                    <select name="designation_id" id="createDesSelect" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                        <option value="">Select Department First</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Joining Date *</label>
                    <input type="date" name="joining_date" required value="{{ date('Y-m-d') }}" 
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Employment Type *</label>
                    <select name="employment_type" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                        <option value="permanent">Permanent</option>
                        <option value="contractual">Contractual</option>
                        <option value="daily">Daily Worker</option>
                        <option value="probation">Probation</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Floor Shift *</label>
                    <select name="shift" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                        <option value="General">General Shift</option>
                        <option value="Day">Day Shift</option>
                        <option value="Night">Night Shift</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Status *</label>
                    <select name="status" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="resigned">Resigned</option>
                        <option value="terminated">Terminated</option>
                    </select>
                </div>
            </div>

            <!-- Section 3: Salary Breakdown -->
            <div class="pt-3 border-t border-slate-700/60">
                <div class="text-xs font-bold text-cyan-300 mb-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-coins"></i>
                    <span>Salary Structure (বেতন বিবরণী)</span>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Basic Salary (৳) *</label>
                        <input type="number" step="0.01" name="basic_salary" id="createBasic" required value="0" oninput="calcGross('create')" 
                               class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-cyan-400">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">House Rent (৳)</label>
                        <input type="number" step="0.01" name="house_rent" id="createHouse" value="0" oninput="calcGross('create')" 
                               class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-cyan-400">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Medical (৳)</label>
                        <input type="number" step="0.01" name="medical_allowance" id="createMed" value="0" oninput="calcGross('create')" 
                               class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-cyan-400">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Conveyance (৳)</label>
                        <input type="number" step="0.01" name="conveyance_allowance" id="createConv" value="0" oninput="calcGross('create')" 
                               class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-cyan-400">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-emerald-400 mb-1">Gross Salary (৳)</label>
                        <input type="text" id="createGross" readonly value="0.00" 
                               class="w-full bg-[#050b1c] border border-emerald-500/40 rounded-xl px-3 py-2 text-xs text-emerald-300 font-mono font-bold">
                    </div>
                </div>
            </div>

            <!-- Section 4: Address -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Present Address</label>
                    <textarea name="present_address" rows="2" placeholder="Floor address / Gazipur address..." class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Permanent Address</label>
                    <textarea name="permanent_address" rows="2" placeholder="Village, Post, Thana, District..." class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400"></textarea>
                </div>
            </div>

            <!-- Footer Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-700/60">
                <button type="button" onclick="closeCreateEmployeeModal()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs shadow-lg shadow-cyan-500/25 flex items-center gap-2">
                    <i class="fa-solid fa-save"></i>
                    <span>Save Employee</span>
                </button>
            </div>
        </form>

    </div>
</div>

<!-- ============================================================== -->
<!-- 2. EDIT EMPLOYEE MODAL                                         -->
<!-- ============================================================== -->
<div id="editEmployeeModal" class="fixed inset-0 z-50 hidden bg-black/75 backdrop-blur-sm overflow-y-auto p-4 flex items-center justify-center">
    <div class="relative w-full max-w-4xl bg-[#09112a] border border-blue-500/30 rounded-2xl shadow-2xl p-6 text-white my-8">
        
        <div class="flex items-center justify-between pb-4 border-b border-slate-700/60 mb-5">
            <h3 class="text-base font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-blue-400"></i>
                Edit Employee Details (<span id="editEmpNameBadge" class="text-cyan-300 font-mono"></span>)
            </h3>
            <button type="button" onclick="closeEditEmployeeModal()" class="text-slate-400 hover:text-white text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="editEmployeeForm" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Employee ID / Code *</label>
                    <input type="text" name="employee_code" id="editCode" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-cyan-300 mb-1 flex items-center gap-1.5">
                        <i class="fa-solid fa-fingerprint text-cyan-400"></i>
                        Biometric Machine User ID (মেশিন আইডি)
                    </label>
                    <input type="text" name="device_user_id" id="editDeviceUserId" placeholder="e.g. 101 or 1" class="w-full bg-[#0a122e] border border-cyan-500/40 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Full Name *</label>
                    <input type="text" name="name" id="editName" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Phone Number *</label>
                    <input type="text" name="phone" id="editPhone" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Emergency Phone</label>
                    <input type="text" name="emergency_phone" id="editEmgPhone" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Email Address</label>
                    <input type="email" name="email" id="editEmail" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">National ID (NID)</label>
                    <input type="text" name="nid" id="editNid" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Gender *</label>
                    <select name="gender" id="editGender" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Blood Group</label>
                    <select name="blood_group" id="editBlood" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                        <option value="">Unknown</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Worker Photo (ছবি পরিবর্তন)</label>
                    <div class="flex items-center gap-2.5">
                        <div id="editPhotoPreviewContainer" class="w-10 h-10 rounded-xl bg-slate-800 border border-slate-700 overflow-hidden flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-user text-slate-500 text-base" id="editPhotoIcon"></i>
                            <img id="editPhotoPreview" class="w-full h-full object-cover hidden" alt="Preview">
                        </div>
                        <div class="flex-1">
                            <input type="file" name="photo" accept="image/*" onchange="previewPhoto(this, 'editPhotoPreview', 'editPhotoIcon')" 
                                   class="w-full text-xs text-slate-400 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:bg-blue-600 file:text-white hover:file:bg-blue-500 cursor-pointer">
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-700/60 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Department *</label>
                    <select name="department_id" id="editDeptSelect" required onchange="filterDesignations('edit')" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-300">Designation *</label>
                        <button type="button" onclick="openQuickAddDesModal('edit')" class="text-[10px] text-cyan-400 hover:text-cyan-300 font-bold flex items-center gap-1 transition-colors">
                            <i class="fa-solid fa-plus-circle"></i>
                            <span>+ New Role</span>
                        </button>
                    </div>
                    <select name="designation_id" id="editDesSelect" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                        <option value="">Select Designation</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Joining Date *</label>
                    <input type="date" name="joining_date" id="editJoiningDate" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Employment Type *</label>
                    <select name="employment_type" id="editType" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                        <option value="permanent">Permanent</option>
                        <option value="contractual">Contractual</option>
                        <option value="daily">Daily Worker</option>
                        <option value="probation">Probation</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Shift *</label>
                    <select name="shift" id="editShift" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                        <option value="General">General Shift</option>
                        <option value="Day">Day Shift</option>
                        <option value="Night">Night Shift</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Status *</label>
                    <select name="status" id="editStatus" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="resigned">Resigned</option>
                        <option value="terminated">Terminated</option>
                    </select>
                </div>
            </div>

            <!-- Salary -->
            <div class="pt-3 border-t border-slate-700/60">
                <div class="text-xs font-bold text-cyan-300 mb-2">Salary Structure</div>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Basic Salary (৳) *</label>
                        <input type="number" step="0.01" name="basic_salary" id="editBasic" required oninput="calcGross('edit')" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-cyan-400">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">House Rent (৳)</label>
                        <input type="number" step="0.01" name="house_rent" id="editHouse" oninput="calcGross('edit')" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-cyan-400">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Medical (৳)</label>
                        <input type="number" step="0.01" name="medical_allowance" id="editMed" oninput="calcGross('edit')" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-cyan-400">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Conveyance (৳)</label>
                        <input type="number" step="0.01" name="conveyance_allowance" id="editConv" oninput="calcGross('edit')" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-cyan-400">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-emerald-400 mb-1">Gross Salary (৳)</label>
                        <input type="text" id="editGross" readonly class="w-full bg-[#050b1c] border border-emerald-500/40 rounded-xl px-3 py-2 text-xs text-emerald-300 font-mono font-bold">
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Present Address</label>
                    <textarea name="present_address" id="editPresentAddress" rows="2" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Permanent Address</label>
                    <textarea name="permanent_address" id="editPermanentAddress" rows="2" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-700/60">
                <button type="button" onclick="closeEditEmployeeModal()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs shadow-lg shadow-cyan-500/25 flex items-center gap-2">
                    <i class="fa-solid fa-save"></i>
                    <span>Update Employee</span>
                </button>
            </div>
        </form>

    </div>
</div>

<!-- ============================================================== -->
<!-- 3. VIEW EMPLOYEE PROFILE MODAL                                 -->
<!-- ============================================================== -->
<div id="viewEmployeeModal" class="fixed inset-0 z-50 hidden bg-black/75 backdrop-blur-sm overflow-y-auto p-4 flex items-center justify-center">
    <div class="relative w-full max-w-2xl bg-[#09112a] border border-cyan-500/40 rounded-2xl shadow-2xl p-6 text-white my-8">
        <div class="flex items-center justify-between pb-3 border-b border-slate-700/60 mb-4">
            <h3 class="text-base font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-id-card text-cyan-400"></i>
                Garment Worker ID Card (কর্মচারী প্রোফাইল)
            </h3>
            <button type="button" onclick="closeViewEmployeeModal()" class="text-slate-400 hover:text-white text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row items-center gap-4 p-4 rounded-xl bg-gradient-to-r from-blue-950/60 to-indigo-950/60 border border-cyan-500/30">
                <div id="viewEmpAvatar" class="w-16 h-16 rounded-xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-300 font-bold text-xl uppercase overflow-hidden">
                </div>
                <div class="text-center sm:text-left flex-1">
                    <h4 id="viewEmpName" class="text-lg font-bold text-white"></h4>
                    <p id="viewEmpDes" class="text-xs text-cyan-300 font-semibold"></p>
                    <p id="viewEmpDept" class="text-[11px] text-slate-300"></p>
                    <div class="mt-1 flex flex-wrap gap-2 justify-center sm:justify-start">
                        <span id="viewEmpCode" class="px-2 py-0.5 rounded font-mono text-[10px] bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 font-bold"></span>
                        <span id="viewEmpStatus" class="px-2 py-0.5 rounded text-[10px] font-bold"></span>
                        <span id="viewEmpShift" class="px-2 py-0.5 rounded text-[10px] bg-slate-800 text-slate-300"></span>
                    </div>
                </div>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                    <span class="text-slate-400 block text-[10px]">Phone</span>
                    <span id="viewEmpPhone" class="font-mono font-bold text-white"></span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                    <span class="text-slate-400 block text-[10px]">Emergency Phone</span>
                    <span id="viewEmpEmgPhone" class="font-mono text-slate-200"></span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                    <span class="text-slate-400 block text-[10px]">National ID (NID)</span>
                    <span id="viewEmpNid" class="font-mono text-slate-200"></span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                    <span class="text-slate-400 block text-[10px]">Blood Group</span>
                    <span id="viewEmpBlood" class="font-bold text-rose-400"></span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                    <span class="text-slate-400 block text-[10px]">Gender</span>
                    <span id="viewEmpGender" class="font-semibold text-slate-200"></span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                    <span class="text-slate-400 block text-[10px]">Joining Date</span>
                    <span id="viewEmpJoining" class="font-mono text-slate-200"></span>
                </div>
            </div>

            <!-- Salary Structure Card -->
            <div class="p-3.5 rounded-xl bg-[#070e24] border border-emerald-500/30">
                <div class="text-xs font-bold text-emerald-400 mb-2 flex items-center justify-between">
                    <span>Monthly Salary Breakdown</span>
                    <span class="text-[10px] text-slate-400" id="viewEmpPaymentMethod"></span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 text-center text-xs">
                    <div class="p-2 rounded-lg bg-slate-900/80">
                        <div class="text-[10px] text-slate-400">Basic</div>
                        <div id="viewEmpBasic" class="font-mono font-bold text-white mt-0.5"></div>
                    </div>
                    <div class="p-2 rounded-lg bg-slate-900/80">
                        <div class="text-[10px] text-slate-400">House Rent</div>
                        <div id="viewEmpHouse" class="font-mono text-slate-300 mt-0.5"></div>
                    </div>
                    <div class="p-2 rounded-lg bg-slate-900/80">
                        <div class="text-[10px] text-slate-400">Medical</div>
                        <div id="viewEmpMed" class="font-mono text-slate-300 mt-0.5"></div>
                    </div>
                    <div class="p-2 rounded-lg bg-slate-900/80">
                        <div class="text-[10px] text-slate-400">Conveyance</div>
                        <div id="viewEmpConv" class="font-mono text-slate-300 mt-0.5"></div>
                    </div>
                    <div class="p-2 rounded-lg bg-emerald-500/10 border border-emerald-500/30">
                        <div class="text-[10px] text-emerald-400 font-bold">Total Gross</div>
                        <div id="viewEmpGross" class="font-mono font-extrabold text-emerald-300 mt-0.5"></div>
                    </div>
                </div>
            </div>

            <!-- Address Card -->
            <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800 text-xs space-y-1.5">
                <div>
                    <span class="text-slate-400 text-[10px] block">Present Address:</span>
                    <span id="viewEmpPresent" class="text-slate-200"></span>
                </div>
                <div>
                    <span class="text-slate-400 text-[10px] block">Permanent Address:</span>
                    <span id="viewEmpPermanent" class="text-slate-200"></span>
                </div>
            </div>
        </div>

        <div class="mt-5 flex justify-end">
            <button type="button" onclick="closeViewEmployeeModal()" class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs">
                Close
            </button>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- 4. QUICK ADD DESIGNATION MODAL (INLINE POPUP)                  -->
<!-- ============================================================== -->
<div id="quickAddDesModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm overflow-y-auto p-4 flex items-center justify-center">
    <div class="relative w-full max-w-md bg-[#09112a] border border-cyan-500/40 rounded-2xl shadow-2xl p-6 text-white">
        <div class="flex items-center justify-between pb-3 border-b border-slate-700/60 mb-4">
            <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-user-tag text-cyan-400"></i>
                Add New Designation / Role (নতুন পদবি যোগ)
            </h3>
            <button type="button" onclick="closeQuickAddDesModal()" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="quickAddDesForm" onsubmit="submitQuickAddDesignation(event)" class="space-y-3">
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Section / Department *</label>
                <select id="quickDesDeptSelect" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                    <option value="">Select Section</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Designation Title *</label>
                <input type="text" id="quickDesTitle" required placeholder="e.g. Pattern Master / Overlock Operator" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Rank / Grade (ঐচ্ছিক)</label>
                <input type="text" id="quickDesGrade" placeholder="e.g. Grade-3 / Senior" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-700/60">
                <button type="button" onclick="closeQuickAddDesModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs font-semibold">Cancel</button>
                <button type="submit" id="quickDesSubmitBtn" class="px-4 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs shadow-lg transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-plus"></i>
                    <span>Save & Select</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const allDesignations = @json($designations);
    let currentDesPrefix = 'create';

    function openQuickAddDesModal(prefix) {
        currentDesPrefix = prefix;
        const currentDeptId = document.getElementById(prefix + 'DeptSelect').value;
        if (currentDeptId) {
            document.getElementById('quickDesDeptSelect').value = currentDeptId;
        }
        document.getElementById('quickDesTitle').value = '';
        document.getElementById('quickDesGrade').value = '';
        document.getElementById('quickAddDesModal').classList.remove('hidden');
    }

    function closeQuickAddDesModal() {
        document.getElementById('quickAddDesModal').classList.add('hidden');
    }

    function submitQuickAddDesignation(e) {
        e.preventDefault();
        const deptId = document.getElementById('quickDesDeptSelect').value;
        const title = document.getElementById('quickDesTitle').value.trim();
        const grade = document.getElementById('quickDesGrade').value.trim();

        if (!deptId || !title) {
            alert('Please select a department and enter designation title.');
            return;
        }

        const submitBtn = document.getElementById('quickDesSubmitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';

        fetch("{{ route('factory.hr.designations.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                department_id: deptId,
                title: title,
                grade: grade
            })
        })
        .then(res => res.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa-solid fa-plus"></i> <span>Save & Select</span>';
            if (data.success && data.designation) {
                allDesignations.push(data.designation);
                document.getElementById(currentDesPrefix + 'DeptSelect').value = deptId;
                filterDesignations(currentDesPrefix, data.designation.id);
                closeQuickAddDesModal();
            } else {
                alert('Error creating designation.');
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa-solid fa-plus"></i> <span>Save & Select</span>';
            console.error(err);
            alert('Failed to save designation.');
        });
    }

    function previewPhoto(input, imgId, iconId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById(imgId);
                const icon = document.getElementById(iconId);
                img.src = e.target.result;
                img.classList.remove('hidden');
                if (icon) icon.classList.add('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function filterDesignations(prefix, selectedDesId = null) {
        const deptId = document.getElementById(prefix + 'DeptSelect').value;
        const desSelect = document.getElementById(prefix + 'DesSelect');
        const notice = document.getElementById(prefix + 'DesNotice');

        desSelect.innerHTML = '<option value="">Select Designation</option>';

        if (!deptId) {
            desSelect.innerHTML = '<option value="">Select Department First</option>';
            if (notice) notice.classList.add('hidden');
            return;
        }

        const filtered = allDesignations.filter(d => d.department_id == deptId);

        if (filtered.length === 0) {
            desSelect.innerHTML = '<option value="">No designations for this section</option>';
            if (notice) notice.classList.add('hidden');
            return;
        }

        if (notice) notice.classList.remove('hidden');

        filtered.forEach(d => {
            const opt = document.createElement('option');
            opt.value = d.id;
            opt.textContent = d.title + (d.grade ? ' (' + d.grade + ')' : '');
            if (selectedDesId && selectedDesId == d.id) {
                opt.selected = true;
            }
            desSelect.appendChild(opt);
        });
    }

    function calcGross(prefix) {
        const basic = parseFloat(document.getElementById(prefix + 'Basic').value) || 0;
        const house = parseFloat(document.getElementById(prefix + 'House').value) || 0;
        const med = parseFloat(document.getElementById(prefix + 'Med').value) || 0;
        const conv = parseFloat(document.getElementById(prefix + 'Conv').value) || 0;
        const total = basic + house + med + conv;
        document.getElementById(prefix + 'Gross').value = total.toFixed(2);
    }

    function openCreateEmployeeModal() {
        document.getElementById('createEmployeeModal').classList.remove('hidden');
        filterDesignations('create');
        calcGross('create');
    }

    function closeCreateEmployeeModal() {
        document.getElementById('createEmployeeModal').classList.add('hidden');
    }

    function openEditEmployeeModal(emp) {
        document.getElementById('editEmployeeForm').action = "/factory/hr/employees/" + emp.id;
        document.getElementById('editEmpNameBadge').innerText = emp.employee_code + ' - ' + emp.name;
        document.getElementById('editCode').value = emp.employee_code;
        document.getElementById('editDeviceUserId').value = emp.device_user_id || '';
        document.getElementById('editName').value = emp.name;
        document.getElementById('editPhone').value = emp.phone;
        document.getElementById('editEmgPhone').value = emp.emergency_phone || '';
        document.getElementById('editEmail').value = emp.email || '';
        document.getElementById('editNid').value = emp.nid || '';
        document.getElementById('editGender').value = emp.gender || 'Male';
        document.getElementById('editBlood').value = emp.blood_group || '';
        document.getElementById('editDeptSelect').value = emp.department_id || '';

        // Dynamically load designations for this employee's department and select their current designation
        filterDesignations('edit', emp.designation_id);

        document.getElementById('editJoiningDate').value = emp.joining_date ? emp.joining_date.substring(0, 10) : '';
        document.getElementById('editType').value = emp.employment_type || 'permanent';
        document.getElementById('editShift').value = emp.shift || 'General';
        document.getElementById('editStatus').value = emp.status || 'active';
        if (document.getElementById('editPayMethod')) document.getElementById('editPayMethod').value = emp.payment_method || 'Cash';
        if (document.getElementById('editBankName')) document.getElementById('editBankName').value = emp.bank_name || '';

        // Photo preview
        const editImg = document.getElementById('editPhotoPreview');
        const editIcon = document.getElementById('editPhotoIcon');
        if (emp.photo) {
            editImg.src = '/storage/' + emp.photo;
            editImg.classList.remove('hidden');
            if (editIcon) editIcon.classList.add('hidden');
        } else {
            editImg.classList.add('hidden');
            if (editIcon) editIcon.classList.remove('hidden');
        }

        document.getElementById('editBasic').value = emp.basic_salary || 0;
        document.getElementById('editHouse').value = emp.house_rent || 0;
        document.getElementById('editMed').value = emp.medical_allowance || 0;
        document.getElementById('editConv').value = emp.conveyance_allowance || 0;
        calcGross('edit');

        document.getElementById('editPresentAddress').value = emp.present_address || '';
        document.getElementById('editPermanentAddress').value = emp.permanent_address || '';

        document.getElementById('editEmployeeModal').classList.remove('hidden');
    }

    function closeEditEmployeeModal() {
        document.getElementById('editEmployeeModal').classList.add('hidden');
    }

    function openViewEmployeeModal(emp) {
        document.getElementById('viewEmpName').innerText = emp.name;
        document.getElementById('viewEmpCode').innerText = emp.employee_code;
        document.getElementById('viewEmpDes').innerText = emp.designation ? emp.designation.title : 'N/A';
        document.getElementById('viewEmpDept').innerText = emp.department ? emp.department.name : 'N/A';
        document.getElementById('viewEmpPhone').innerText = emp.phone;
        document.getElementById('viewEmpEmgPhone').innerText = emp.emergency_phone || '—';
        document.getElementById('viewEmpNid').innerText = emp.nid || '—';
        document.getElementById('viewEmpBlood').innerText = emp.blood_group || 'N/A';
        document.getElementById('viewEmpGender').innerText = emp.gender || 'N/A';
        document.getElementById('viewEmpShift').innerText = (emp.shift || 'General') + ' Shift';
        document.getElementById('viewEmpJoining').innerText = emp.joining_date ? emp.joining_date.substring(0, 10) : '—';

        // Avatar
        const avatarDiv = document.getElementById('viewEmpAvatar');
        if (emp.photo) {
            avatarDiv.innerHTML = `<img src="/storage/${emp.photo}" class="w-full h-full object-cover">`;
        } else {
            avatarDiv.innerHTML = emp.name.substring(0, 2);
        }

        // Status
        const statusSpan = document.getElementById('viewEmpStatus');
        statusSpan.innerText = (emp.status || 'active').toUpperCase();
        if (emp.status === 'active') {
            statusSpan.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30';
        } else {
            statusSpan.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30';
        }

        // Salary
        document.getElementById('viewEmpBasic').innerText = '৳' + Number(emp.basic_salary || 0).toLocaleString();
        document.getElementById('viewEmpHouse').innerText = '৳' + Number(emp.house_rent || 0).toLocaleString();
        document.getElementById('viewEmpMed').innerText = '৳' + Number(emp.medical_allowance || 0).toLocaleString();
        document.getElementById('viewEmpConv').innerText = '৳' + Number(emp.conveyance_allowance || 0).toLocaleString();
        document.getElementById('viewEmpGross').innerText = '৳' + Number(emp.gross_salary || 0).toLocaleString();
        document.getElementById('viewEmpPaymentMethod').innerText = 'Method: ' + (emp.payment_method || 'Cash');

        // Address
        document.getElementById('viewEmpPresent').innerText = emp.present_address || '—';
        document.getElementById('viewEmpPermanent').innerText = emp.permanent_address || '—';

        document.getElementById('viewEmployeeModal').classList.remove('hidden');
    }

    function closeViewEmployeeModal() {
        document.getElementById('viewEmployeeModal').classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', () => {
        initErpTableManager({
            id: 'employee',
            tableBody: '#employeeTableBody',
            searchInput: '#employeeSearchInput',
            pageSize: 10
        });
    });
</script>
@endpush
@endsection

