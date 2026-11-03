@extends('layouts.app')

@section('title', 'Add New Employee | Factory ERP')

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
                <span class="text-cyan-400 font-bold">Add New Employee</span>
            </div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-cyan-500/20 text-cyan-400 border border-cyan-500/30 flex items-center justify-center">
                    <i class="fa-solid fa-user-plus text-base"></i>
                </span>
                <span>Register New Employee / Worker (নতুন কর্মচারী নিবন্ধন)</span>
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">
                Fill in the garment worker details, job placement, salary structure, and biometric fingerprint machine ID.
            </p>
        </div>

        <div>
            <a href="{{ route('factory.hr.employees.index') }}" 
               class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 font-bold text-xs shadow-lg transition-all flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back to Employee List</span>
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
    <form action="{{ route('factory.hr.employees.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

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
                    <input type="text" name="employee_code" value="{{ old('employee_code', $nextEmployeeCode) }}" required
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono font-bold">
                    <span class="text-[10px] text-slate-400 mt-1 block">Unique company ID number</span>
                </div>

                <!-- Biometric Device User ID -->
                <div>
                    <label class="block text-xs font-semibold text-cyan-300 mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-fingerprint text-cyan-400"></i>
                        <span>Biometric User ID (হাজিরা মেশিন আইডি)</span>
                    </label>
                    <input type="text" name="device_user_id" value="{{ old('device_user_id') }}" placeholder="e.g. 101 or 1"
                           class="w-full bg-[#0a122e] border border-cyan-500/40 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                    <span class="text-[10px] text-cyan-400/80 mt-1 block">ZKTeco machine fingerprint ID</span>
                </div>

                <!-- Full Name -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Full Name (পূর্ণ নাম) *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Md. Rafiqul Islam"
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400">
                </div>

                <!-- Photo Upload with Preview -->
                <div class="md:col-span-4 p-4 rounded-xl bg-[#060c1d] border border-slate-800 flex flex-col sm:flex-row items-center gap-4">
                    <div id="createPhotoPreviewContainer" class="w-16 h-16 rounded-xl bg-slate-800 border-2 border-dashed border-cyan-500/40 overflow-hidden flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-user text-slate-500 text-2xl" id="createPhotoIcon"></i>
                        <img id="createPhotoPreview" class="w-full h-full object-cover hidden" alt="Worker Photo Preview">
                    </div>
                    <div class="flex-1 text-center sm:text-left">
                        <label class="block text-xs font-bold text-white mb-1">Worker Photograph (কর্মচারীর পাসপোর্ট ছবি)</label>
                        <p class="text-[11px] text-slate-400 mb-2">Upload JPG, PNG or WEBP (Max 3MB). Will appear on ID cards and payroll slips.</p>
                        <input type="file" name="photo" accept="image/*" onchange="previewPhoto(this, 'createPhotoPreview', 'createPhotoIcon')"
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
                    <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="01711-xxxxxx"
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                </div>

                <!-- Emergency Phone -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Emergency Contact Number</label>
                    <input type="text" name="emergency_phone" value="{{ old('emergency_phone') }}" placeholder="Guardian / relative number"
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                </div>

                <!-- Email Address -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Email Address (ঐচ্ছিক)</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="name@domain.com"
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400">
                </div>

                <!-- NID -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">National ID (NID নম্বর)</label>
                    <input type="text" name="nid" value="{{ old('nid') }}" placeholder="10 or 17 digit NID"
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                </div>

                <!-- Gender -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Gender (লিঙ্গ) *</label>
                    <select name="gender" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400">
                        <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male (পুরুষ)</option>
                        <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female (মহিলা)</option>
                        <option value="Other" {{ old('gender') === 'Other' ? 'selected' : '' }}>Other (অন্যান্য)</option>
                    </select>
                </div>

                <!-- Blood Group -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Blood Group (রক্তের গ্রুপ)</label>
                    <select name="blood_group" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                        <option value="">Select Blood Group</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                            <option value="{{ $bg }}" {{ old('blood_group') === $bg ? 'selected' : '' }}>{{ $bg }}</option>
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
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Section / Department (শাখা / বিভাগ) *</label>
                    <select name="department_id" id="createDeptSelect" required onchange="filterDesignations('create')"
                            class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400">
                        <option value="">Select Section / Department</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }} ({{ $dept->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Designation Selection with inline quick-add -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold text-slate-300">Designation / Role (পদবি) *</label>
                        <button type="button" onclick="openQuickAddDesModal('create')" 
                                class="text-[11px] font-bold text-cyan-400 hover:text-cyan-300 flex items-center gap-1 hover:underline">
                            <i class="fa-solid fa-plus-circle text-[10px]"></i>
                            <span>+ Add Role</span>
                        </button>
                    </div>
                    <select name="designation_id" id="createDesSelect" required
                            class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400">
                        <option value="">Select Department First</option>
                    </select>
                    <p id="createDesNotice" class="text-[10px] text-emerald-400 mt-1 hidden">
                        <i class="fa-solid fa-check text-[9px]"></i> Showing designations for selected section
                    </p>
                </div>

                <!-- Joining Date -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Joining Date (যোগদানের তারিখ) *</label>
                    <input type="date" name="joining_date" value="{{ old('joining_date', date('Y-m-d')) }}" required
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
                </div>

                <!-- Employment Type -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Employment Type (চুক্তির ধরন) *</label>
                    <select name="employment_type" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400">
                        <option value="permanent" {{ old('employment_type') === 'permanent' ? 'selected' : '' }}>Permanent (স্থায়ী)</option>
                        <option value="contractual" {{ old('employment_type') === 'contractual' ? 'selected' : '' }}>Contractual (চুক্তিভিত্তিক)</option>
                        <option value="daily" {{ old('employment_type') === 'daily' ? 'selected' : '' }}>Daily Basis (দৈনিক ভিত্তিক)</option>
                        <option value="probation" {{ old('employment_type') === 'probation' ? 'selected' : '' }}>Probation (শিক্ষানবিস)</option>
                    </select>
                </div>

                <!-- Shift -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Shift (শিফট) *</label>
                    <select name="shift" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400">
                        <option value="General" {{ old('shift') === 'General' ? 'selected' : '' }}>General Shift (সকাল ৮:০০ - বিকাল ৫:০০)</option>
                        <option value="Day" {{ old('shift') === 'Day' ? 'selected' : '' }}>Day Shift</option>
                        <option value="Night" {{ old('shift') === 'Night' ? 'selected' : '' }}>Night Shift (রাতের শিফট)</option>
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Status (কাজের অবস্থা) *</label>
                    <select name="status" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400">
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active (কর্মরত)</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="resigned" {{ old('status') === 'resigned' ? 'selected' : '' }}>Resigned (অব্যাহতি)</option>
                        <option value="terminated" {{ old('status') === 'terminated' ? 'selected' : '' }}>Terminated</option>
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
                    <input type="number" step="0.01" name="basic_salary" id="createBasic" required value="{{ old('basic_salary', 0) }}" oninput="calcGross('create')"
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white font-mono font-bold focus:outline-none focus:border-emerald-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">House Rent (বাড়ি ভাড়া ৳)</label>
                    <input type="number" step="0.01" name="house_rent" id="createHouse" value="{{ old('house_rent', 0) }}" oninput="calcGross('create')"
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white font-mono focus:outline-none focus:border-emerald-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Medical (চিকিৎসা ভাতা ৳)</label>
                    <input type="number" step="0.01" name="medical_allowance" id="createMed" value="{{ old('medical_allowance', 0) }}" oninput="calcGross('create')"
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white font-mono focus:outline-none focus:border-emerald-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Conveyance (যাতায়াত ৳)</label>
                    <input type="number" step="0.01" name="conveyance_allowance" id="createConv" value="{{ old('conveyance_allowance', 0) }}" oninput="calcGross('create')"
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white font-mono focus:outline-none focus:border-emerald-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-emerald-400 mb-1.5">Gross Total (মোট বেতন ৳)</label>
                    <input type="text" id="createGross" readonly value="0.00"
                           class="w-full bg-[#050b1c] border-2 border-emerald-500/40 rounded-xl px-3.5 py-2.5 text-xs text-emerald-300 font-mono font-black">
                </div>
            </div>
        </div>

        <!-- 5. Address Details -->
        <div class="glass-panel rounded-2xl border border-slate-700/60 p-6 shadow-xl">
            <h3 class="text-sm font-extrabold text-white flex items-center gap-2 pb-3 mb-5 border-b border-slate-700/60">
                <span class="w-6 h-6 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-map-location-dot"></i>
                </span>
                <span>Address Details (ঠিকানা)</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Present Address (বর্তমান ঠিকানা)</label>
                    <textarea name="present_address" rows="3" placeholder="Local residence / Gazipur / Tongi address..."
                              class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400">{{ old('present_address') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Permanent Address (স্থায়ী ঠিকানা)</label>
                    <textarea name="permanent_address" rows="3" placeholder="Village, Post office, Upazila, District..."
                              class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400">{{ old('permanent_address') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between p-4 rounded-2xl glass-panel border border-slate-700/60">
            <a href="{{ route('factory.hr.employees.index') }}" 
               class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs transition-colors">
                Cancel & Return
            </a>

            <button type="submit" 
                    class="px-7 py-3 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-extrabold text-xs shadow-lg shadow-cyan-500/25 transition-all flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-sm"></i>
                <span>Save & Register Employee</span>
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
        filterDesignations('create', '{{ old('designation_id') }}');
        calcGross('create');
    });
</script>
@endpush

