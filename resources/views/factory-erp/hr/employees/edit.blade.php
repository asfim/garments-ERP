@extends('layouts.app')

@section('title', 'Edit Employee | Factory ERP')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <!-- Top Breadcrumb & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('factory.hr.employees.index') }}" class="hover:text-cyan-400 transition-colors flex items-center gap-1">
                    <i class="fa-solid fa-users"></i>
                    <span>Employee Directory</span>
                </a>
                <span>/</span>
                <span class="text-cyan-400 font-bold">Edit Employee ({{ $employee->employee_code }})</span>
            </div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-blue-500/20 text-cyan-400 border border-blue-500/30 flex items-center justify-center">
                    <i class="fa-solid fa-user-pen text-base"></i>
                </span>
                <span>Edit Employee: {{ $employee->name }} (<span class="font-mono text-cyan-300">{{ $employee->employee_code }}</span>)</span>
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">
                Update employee personal information, placement, salary allowances, or biometric device user ID.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('factory.hr.employees.show', $employee->id) }}" 
               class="px-4 py-2.5 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 border border-emerald-500/30 font-bold text-xs shadow-lg transition-all flex items-center gap-2">
                <i class="fa-solid fa-address-card"></i>
                <span>View Full Profile</span>
            </a>

            <a href="{{ route('factory.hr.employees.index') }}" 
               class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 font-bold text-xs shadow-lg transition-all flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back to List</span>
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(isset($errors) && $errors->any())
    <div class="p-4 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs shadow-lg space-y-1">
        <div class="font-bold flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation text-rose-400"></i>
            <span>Please correct the errors below:</span>
        </div>
        <ul class="list-disc list-inside space-y-0.5 pl-4">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Main Registration Form -->
    <form action="{{ route('factory.hr.employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- 1. Identity & Profile Information -->
        <div class="glass-panel rounded-2xl border border-slate-700/60 p-6 shadow-xl">
            <h3 class="text-sm font-extrabold text-white flex items-center gap-2 pb-3 mb-5 border-b border-slate-700/60">
                <span class="w-6 h-6 rounded-lg bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-id-card"></i>
                </span>
                <span>Basic Identity & Photo (পরিচিতি ও ছবি)</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                
                <!-- Employee Code -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Employee ID / Code *</label>
                    <input type="text" name="employee_code" value="{{ old('employee_code', $employee->employee_code) }}" required
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono font-bold">
                    <span class="text-[10px] text-slate-400 mt-1 block">Unique company ID number</span>
                </div>

                <!-- Biometric Device User ID -->
                <div>
                    <label class="block text-xs font-semibold text-cyan-300 mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-fingerprint text-cyan-400"></i>
                        <span>Biometric User ID (হাজিরা মেশিন আইডি)</span>
                    </label>
                    <input type="text" name="device_user_id" value="{{ old('device_user_id', $employee->device_user_id) }}" placeholder="e.g. 101 or 1"
                           class="w-full bg-[#0a122e] border border-cyan-500/40 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                    <span class="text-[10px] text-cyan-400/80 mt-1 block">ZKTeco machine fingerprint ID</span>
                </div>

                <!-- Full Name -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Full Name (পূর্ণ নাম) *</label>
                    <input type="text" name="name" value="{{ old('name', $employee->name) }}" required placeholder="e.g. Md. Rafiqul Islam"
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400">
                </div>

                <!-- Photo Upload with Preview -->
                <div class="md:col-span-4 p-4 rounded-xl bg-[#060c1d] border border-slate-800 flex flex-col sm:flex-row items-center gap-4">
                    <div id="editPhotoPreviewContainer" class="w-16 h-16 rounded-xl bg-slate-800 border-2 border-cyan-500/40 overflow-hidden flex items-center justify-center flex-shrink-0">
                        @if($employee->photo && file_exists(public_path('storage/' . $employee->photo)))
                            <img id="editPhotoPreview" src="{{ asset('storage/' . $employee->photo) }}" class="w-full h-full object-cover" alt="Worker Photo">
                            <i class="fa-solid fa-user text-slate-500 text-2xl hidden" id="editPhotoIcon"></i>
                        @else
                            <i class="fa-solid fa-user text-slate-500 text-2xl" id="editPhotoIcon"></i>
                            <img id="editPhotoPreview" class="w-full h-full object-cover hidden" alt="Worker Photo Preview">
                        @endif
                    </div>
                    <div class="flex-1 text-center sm:text-left">
                        <label class="block text-xs font-bold text-white mb-1">Update Photograph (ছবি পরিবর্তন করতে পারেন)</label>
                        <p class="text-[11px] text-slate-400 mb-2">Leave blank if you do not want to change the existing photo.</p>
                        <input type="file" name="photo" accept="image/*" onchange="previewPhoto(this, 'editPhotoPreview', 'editPhotoIcon')"
                               class="text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-cyan-600 file:text-white hover:file:bg-cyan-500 cursor-pointer">
                    </div>
                </div>

            </div>
        </div>

        <!-- 2. Personal & Contact Information -->
        <div class="glass-panel rounded-2xl border border-slate-700/60 p-6 shadow-xl">
            <h3 class="text-sm font-extrabold text-white flex items-center gap-2 pb-3 mb-5 border-b border-slate-700/60">
                <span class="w-6 h-6 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-phone"></i>
                </span>
                <span>Contact & Personal Details (যোগাযোগ ও ব্যক্তিগত তথ্য)</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Phone -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Phone Number (মোবাইল নম্বর) *</label>
                    <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}" required placeholder="01711-xxxxxx"
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                </div>

                <!-- Emergency Phone -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Emergency Contact Number</label>
                    <input type="text" name="emergency_phone" value="{{ old('emergency_phone', $employee->emergency_phone) }}" placeholder="Guardian / relative number"
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                </div>

                <!-- Email Address -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $employee->email) }}" placeholder="name@domain.com"
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400">
                </div>

                <!-- NID -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">National ID (NID নম্বর)</label>
                    <input type="text" name="nid" value="{{ old('nid', $employee->nid) }}" placeholder="10 or 17 digit NID"
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                </div>

                <!-- Gender -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Gender (লিঙ্গ) *</label>
                    <select name="gender" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400">
                        <option value="Male" {{ old('gender', $employee->gender) === 'Male' ? 'selected' : '' }}>Male (পুরুষ)</option>
                        <option value="Female" {{ old('gender', $employee->gender) === 'Female' ? 'selected' : '' }}>Female (মহিলা)</option>
                        <option value="Other" {{ old('gender', $employee->gender) === 'Other' ? 'selected' : '' }}>Other (অন্যান্য)</option>
                    </select>
                </div>

                <!-- Blood Group -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Blood Group (রক্তের গ্রুপ)</label>
                    <select name="blood_group" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                        <option value="">Select Blood Group</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                            <option value="{{ $bg }}" {{ old('blood_group', $employee->blood_group) === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- 3. Department, Designation & Job Details -->
        <div class="glass-panel rounded-2xl border border-slate-700/60 p-6 shadow-xl">
            <div class="flex items-center justify-between pb-3 mb-5 border-b border-slate-700/60">
                <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-sitemap"></i>
                    </span>
                    <span>Department, Role & Placement (বিভাগ ও পদবি)</span>
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Department Selection -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Section / Department *</label>
                    <select name="department_id" id="editDeptSelect" required onchange="filterDesignations('edit')"
                            class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400">
                        <option value="">Select Section / Department</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }} ({{ $dept->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Designation Selection with inline quick-add -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold text-slate-300">Designation / Role (পদবি) *</label>
                        <button type="button" onclick="openQuickAddDesModal('edit')" 
                                class="text-[11px] font-bold text-cyan-400 hover:text-cyan-300 flex items-center gap-1 hover:underline">
                            <i class="fa-solid fa-plus-circle text-[10px]"></i>
                            <span>+ Add Role</span>
                        </button>
                    </div>
                    <select name="designation_id" id="editDesSelect" required
                            class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400">
                        <option value="">Select Department First</option>
                    </select>
                    <p id="editDesNotice" class="text-[10px] text-emerald-400 mt-1 hidden">
                        <i class="fa-solid fa-check text-[9px]"></i> Showing designations for selected section
                    </p>
                </div>

                <!-- Joining Date -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Joining Date *</label>
                    <input type="date" name="joining_date" value="{{ old('joining_date', $employee->joining_date ? $employee->joining_date->format('Y-m-d') : '') }}" required
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                </div>

                <!-- Employment Type -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Employment Type *</label>
                    <select name="employment_type" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400">
                        <option value="permanent" {{ old('employment_type', $employee->employment_type) === 'permanent' ? 'selected' : '' }}>Permanent (স্থায়ী)</option>
                        <option value="contractual" {{ old('employment_type', $employee->employment_type) === 'contractual' ? 'selected' : '' }}>Contractual (চুক্তিভিত্তিক)</option>
                        <option value="daily" {{ old('employment_type', $employee->employment_type) === 'daily' ? 'selected' : '' }}>Daily Basis (দৈনিক ভিত্তিক)</option>
                        <option value="probation" {{ old('employment_type', $employee->employment_type) === 'probation' ? 'selected' : '' }}>Probation (শিক্ষানবিস)</option>
                    </select>
                </div>

                <!-- Shift -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Shift *</label>
                    <select name="shift" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400">
                        <option value="General" {{ old('shift', $employee->shift) === 'General' ? 'selected' : '' }}>General Shift (সকাল ৮:০০ - বিকাল ৫:০০)</option>
                        <option value="Day" {{ old('shift', $employee->shift) === 'Day' ? 'selected' : '' }}>Day Shift</option>
                        <option value="Night" {{ old('shift', $employee->shift) === 'Night' ? 'selected' : '' }}>Night Shift (রাতের শিফট)</option>
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Status *</label>
                    <select name="status" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400">
                        <option value="active" {{ old('status', $employee->status) === 'active' ? 'selected' : '' }}>Active (কর্মরত)</option>
                        <option value="inactive" {{ old('status', $employee->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="resigned" {{ old('status', $employee->status) === 'resigned' ? 'selected' : '' }}>Resigned (অব্যাহতি)</option>
                        <option value="terminated" {{ old('status', $employee->status) === 'terminated' ? 'selected' : '' }}>Terminated</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 4. Salary Structure & Allowances -->
        <div class="glass-panel rounded-2xl border border-slate-700/60 p-6 shadow-xl">
            <h3 class="text-sm font-extrabold text-white flex items-center gap-2 pb-3 mb-5 border-b border-slate-700/60">
                <span class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </span>
                <span>Salary Breakdown & Allowances (বেতন কাঠামো ও ভাতা)</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Basic Salary (মূল বেতন ৳) *</label>
                    <input type="number" step="0.01" name="basic_salary" id="editBasic" required value="{{ old('basic_salary', $employee->basic_salary) }}" oninput="calcGross('edit')"
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white font-mono font-bold focus:outline-none focus:border-emerald-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">House Rent (বাড়ি ভাড়া ৳)</label>
                    <input type="number" step="0.01" name="house_rent" id="editHouse" value="{{ old('house_rent', $employee->house_rent) }}" oninput="calcGross('edit')"
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white font-mono focus:outline-none focus:border-emerald-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Medical (চিকিৎসা ভাতা ৳)</label>
                    <input type="number" step="0.01" name="medical_allowance" id="editMed" value="{{ old('medical_allowance', $employee->medical_allowance) }}" oninput="calcGross('edit')"
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white font-mono focus:outline-none focus:border-emerald-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Conveyance (যাতায়াত ৳)</label>
                    <input type="number" step="0.01" name="conveyance_allowance" id="editConv" value="{{ old('conveyance_allowance', $employee->conveyance_allowance) }}" oninput="calcGross('edit')"
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white font-mono focus:outline-none focus:border-emerald-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-emerald-400 mb-1.5">Gross Total (মোট বেতন ৳)</label>
                    <input type="text" id="editGross" readonly value="{{ number_format($employee->gross_salary, 2, '.', '') }}"
                           class="w-full bg-[#050b1c] border-2 border-emerald-500/40 rounded-xl px-3.5 py-2.5 text-xs text-emerald-300 font-mono font-black">
                </div>
            </div>
        </div>

        <!-- 5. Payment & Banking Details -->
        <div class="glass-panel rounded-2xl border border-slate-700/60 p-6 shadow-xl">
            <h3 class="text-sm font-extrabold text-white flex items-center gap-2 pb-3 mb-5 border-b border-slate-700/60">
                <span class="w-6 h-6 rounded-lg bg-teal-500/20 text-teal-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-credit-card"></i>
                </span>
                <span>Payment & Banking Details (পরিশোধ পদ্ধতি)</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Payment Method *</label>
                    <select name="payment_method" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400">
                        <option value="Cash" {{ old('payment_method', $employee->payment_method) === 'Cash' ? 'selected' : '' }}>Cash (নগদ)</option>
                        <option value="Bank" {{ old('payment_method', $employee->payment_method) === 'Bank' ? 'selected' : '' }}>Bank Account (ব্যাংক)</option>
                        <option value="bKash" {{ old('payment_method', $employee->payment_method) === 'bKash' ? 'selected' : '' }}>bKash (বিকাশ)</option>
                        <option value="Nagad" {{ old('payment_method', $employee->payment_method) === 'Nagad' ? 'selected' : '' }}>Nagad (নগদ)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Bank Name / Provider</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $employee->bank_name) }}" placeholder="e.g. Dutch Bangla Bank Ltd / bKash"
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Account / Mobile Number</label>
                    <input type="text" name="account_number" value="{{ old('account_number', $employee->account_number) }}" placeholder="Bank A/C or Wallet Number"
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                </div>
            </div>
        </div>

        <!-- 6. Address Details -->
        <div class="glass-panel rounded-2xl border border-slate-700/60 p-6 shadow-xl">
            <h3 class="text-sm font-extrabold text-white flex items-center gap-2 pb-3 mb-5 border-b border-slate-700/60">
                <span class="w-6 h-6 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-map-location-dot"></i>
                </span>
                <span>Address Details (ঠিকানা)</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Present Address</label>
                    <textarea name="present_address" rows="3" placeholder="Local residence / Gazipur / Tongi address..."
                              class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400">{{ old('present_address', $employee->present_address) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Permanent Address</label>
                    <textarea name="permanent_address" rows="3" placeholder="Village, Post office, Upazila, District..."
                              class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400">{{ old('permanent_address', $employee->permanent_address) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between p-4 rounded-2xl glass-panel border border-slate-700/60">
            <a href="{{ route('factory.hr.employees.index') }}" 
               class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs transition-colors">
                Cancel
            </a>

            <button type="submit" 
                    class="px-7 py-3 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-extrabold text-xs shadow-lg shadow-cyan-500/25 transition-all flex items-center gap-2">
                <i class="fa-solid fa-save text-sm"></i>
                <span>Update Employee Information</span>
            </button>
        </div>

    </form>
</div>

<!-- Quick Add Designation Modal (Inline Popup) -->
<div id="quickAddDesModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm overflow-y-auto p-4 flex items-center justify-center">
    <div class="relative w-full max-w-md bg-[#09112a] border border-cyan-500/40 rounded-2xl shadow-2xl p-6 text-white">
        <div class="flex items-center justify-between pb-3 border-b border-slate-700/60 mb-4">
            <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-user-tag text-cyan-400"></i>
                <span>Add New Designation / Role (নতুন পদবি যোগ)</span>
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
                <input type="text" id="quickDesTitle" required placeholder="e.g. Pattern Master / Overlock Operator" 
                       class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Rank / Grade (ঐচ্ছিক)</label>
                <input type="text" id="quickDesGrade" placeholder="e.g. Grade-3 / Senior" 
                       class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
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

@endsection

@push('scripts')
<script>
    const allDesignations = @json($designations);
    let currentDesPrefix = 'edit';

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
        .then(async response => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa-solid fa-plus"></i> <span>Save & Select</span>';
            
            if (!response.ok) {
                const errData = await response.json();
                throw new Error(errData.message || 'Validation failed');
            }
            return response.json();
        })
        .then(newDes => {
            allDesignations.push(newDes);
            document.getElementById(currentDesPrefix + 'DeptSelect').value = deptId;
            filterDesignations(currentDesPrefix, newDes.id);
            closeQuickAddDesModal();
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa-solid fa-plus"></i> <span>Save & Select</span>';
            console.error(err);
            alert('Failed to save designation: ' + err.message);
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

    // Initialize on load
    document.addEventListener('DOMContentLoaded', function() {
        filterDesignations('edit', '{{ old('designation_id', $employee->designation_id) }}');
        calcGross('edit');
    });
</script>
@endpush

