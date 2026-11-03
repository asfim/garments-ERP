@extends('layouts.app')

@section('title', 'Designations & Job Roles | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-cyan-500/20 text-cyan-400 border border-cyan-500/30 flex items-center justify-center">
                    <i class="fa-solid fa-user-tag text-base"></i>
                </span>
                <span>Designations & Job Roles</span>
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">
                Manage factory job titles, ranks, skill grades (Grade 1-7) and section assignments.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Go to Departments -->
            <a href="{{ route('factory.hr.departments.index') }}" 
               class="px-3.5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-indigo-300 hover:text-indigo-200 border border-slate-700 font-bold text-xs shadow-lg transition-all flex items-center gap-2">
                <i class="fa-solid fa-building text-indigo-400"></i>
                <span>Departments</span>
            </a>

            <!-- Add Designation Button -->
            <button type="button" onclick="openCreateDesModal()"
                    class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-bold text-xs shadow-lg shadow-cyan-500/25 transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                <span>Add Designation</span>
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="p-3.5 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs flex items-center gap-2.5 shadow-lg">
        <i class="fa-solid fa-circle-check text-emerald-400 text-base"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(isset($errors) && $errors->any())
    <div class="p-3.5 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs shadow-lg">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- KPI Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <!-- 1. Total Designations -->
        <div class="glass-panel p-4 rounded-2xl border border-slate-700/60 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Roles</div>
                    <div class="text-2xl font-black text-white mt-1">{{ $totalDesignations }}</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400 text-base group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-user-tag"></i>
                </div>
            </div>
            <div class="mt-2 text-[11px] text-cyan-400 font-medium">
                Active Job Titles
            </div>
        </div>

        <!-- 2. Assigned Employees -->
        <div class="glass-panel p-4 rounded-2xl border border-slate-700/60 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Assigned Workers</div>
                    <div class="text-2xl font-black text-emerald-400 mt-1">{{ $totalAssignedEmployees }}</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 text-base group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <div class="mt-2 text-[11px] text-slate-400">
                Staff placed in roles
            </div>
        </div>

        <!-- 3. Departments Covered -->
        <div class="glass-panel p-4 rounded-2xl border border-slate-700/60 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Sections / Depts</div>
                    <div class="text-2xl font-black text-indigo-400 mt-1">{{ $departments->count() }}</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/30 flex items-center justify-center text-indigo-400 text-base group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-building"></i>
                </div>
            </div>
            <div class="mt-2 text-[11px] text-slate-400">
                Functional units
            </div>
        </div>

        <!-- 4. Quick Action -->
        <div class="glass-panel p-4 rounded-2xl border border-slate-700/60 relative overflow-hidden group flex flex-col justify-between">
            <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Need New Role?</div>
            <button type="button" onclick="openCreateDesModal()" 
                    class="mt-2 w-full py-2 rounded-xl bg-cyan-600/30 hover:bg-cyan-600/50 text-cyan-300 border border-cyan-500/40 font-bold text-xs flex items-center justify-center gap-1.5 transition-all">
                <i class="fa-solid fa-plus-circle"></i>
                <span>+ Add New Title</span>
            </button>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="glass-panel rounded-2xl p-4 border border-slate-700/60">
        <form method="GET" action="{{ route('factory.hr.designations.index') }}" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-4 items-end">
            <!-- Search -->
            <div class="sm:col-span-2">
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Search Designation Title or Grade</label>
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="e.g. Pattern Master, Senior Operator, Grade-3..." 
                           class="w-full bg-[#0a122e] border border-slate-700 rounded-xl pl-9 pr-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                </div>
            </div>

            <!-- Filter by Section / Department -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Section / Department</label>
                <select name="department_id" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ $selectedDepartmentId == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Actions -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs transition-colors flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-filter"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('factory.hr.designations.index') }}" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Designations List Table -->
    <div class="glass-panel rounded-2xl border border-slate-700/60 overflow-hidden shadow-2xl">
        <div class="p-4 border-b border-slate-700/60 flex items-center justify-between">
            <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-list-check text-cyan-400"></i>
                <span>Designations Directory (কারখানার সকল পদবিসমূহ)</span>
            </h3>
            <span class="text-xs text-slate-400 font-mono">{{ $designations->count() }} records found</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-[#09112a] text-[10px] uppercase font-bold text-slate-400 tracking-wider border-b border-slate-700/80">
                    <tr>
                        <th class="py-3 px-4">Designation Title (পদবি)</th>
                        <th class="py-3 px-4">Department / Section</th>
                        <th class="py-3 px-4">Rank / Skill Grade</th>
                        <th class="py-3 px-4 text-center">Active Workers</th>
                        <th class="py-3 px-4">Description / Responsibility</th>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($designations as $des)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <!-- Title -->
                        <td class="py-3 px-4 font-bold text-white">
                            <div class="flex items-center gap-2">
                                <span class="w-7 h-7 rounded-lg bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center text-xs">
                                    <i class="fa-solid fa-user-tag"></i>
                                </span>
                                <div>
                                    <span class="hover:text-cyan-300 transition-colors">{{ $des->title }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Department -->
                        <td class="py-3 px-4">
                            @if($des->department)
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-indigo-500/15 text-indigo-300 border border-indigo-500/30">
                                    <i class="fa-solid fa-building text-[9px] mr-1"></i>
                                    {{ $des->department->name }}
                                </span>
                            @else
                                <span class="text-slate-500 italic">Unassigned</span>
                            @endif
                        </td>

                        <!-- Grade -->
                        <td class="py-3 px-4 font-mono">
                            @if($des->grade)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30">
                                    {{ $des->grade }}
                                </span>
                            @else
                                <span class="text-slate-500">—</span>
                            @endif
                        </td>

                        <!-- Active Workers -->
                        <td class="py-3 px-4 text-center font-mono font-bold text-emerald-400">
                            <a href="{{ route('factory.hr.employees.index', ['department_id' => $des->department_id]) }}" 
                               class="hover:underline inline-flex items-center gap-1.5" title="Filter workers in this department">
                                <i class="fa-solid fa-users text-[10px] text-emerald-500"></i>
                                <span>{{ $des->employees->count() }} Workers</span>
                            </a>
                        </td>

                        <!-- Description -->
                        <td class="py-3 px-4 text-slate-400 text-[11px] max-w-xs truncate" title="{{ $des->description }}">
                            {{ $des->description ?: '—' }}
                        </td>

                        <!-- Actions -->
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <button type="button" onclick="openEditDesModal({{ json_encode($des) }})" 
                                        title="Edit Designation" class="p-1.5 rounded-lg bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 text-xs transition-colors">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                
                                <form action="{{ route('factory.hr.designations.destroy', $des->id) }}" method="POST" onsubmit="return confirm('Delete designation {{ $des->title }}?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete Designation" class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 text-xs transition-colors">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400 font-sans">
                            <i class="fa-solid fa-user-tag text-3xl text-slate-600 block mb-2"></i>
                            No designations found matching the criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ============================================================== -->
<!-- 1. CREATE DESIGNATION MODAL                                    -->
<!-- ============================================================== -->
<div id="createDesModal" class="fixed inset-0 z-50 hidden bg-black/75 backdrop-blur-sm overflow-y-auto p-4 flex items-center justify-center">
    <div class="relative w-full max-w-md bg-[#09112a] border border-cyan-500/30 rounded-2xl shadow-2xl p-6 text-white my-8">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-700/60 mb-4">
            <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-plus text-cyan-400"></i>
                <span>Add Designation / Role (নতুন পদবি তৈরি)</span>
            </h3>
            <button type="button" onclick="closeCreateDesModal()" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('factory.hr.designations.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Section / Department *</label>
                <select name="department_id" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                    <option value="">Select Section / Department</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code ?? 'SEC' }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Designation Title (পদবি নাম) *</label>
                <input type="text" name="title" required placeholder="e.g. Senior Sewing Operator / Pattern Master" 
                       class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Rank / Skill Grade (ঐচ্ছিক)</label>
                <input type="text" name="grade" placeholder="e.g. Grade-3, Executive, Junior" 
                       class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Description / Responsibilities</label>
                <textarea name="description" rows="2" placeholder="Brief job duties or machine operation..." 
                          class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400"></textarea>
            </div>

            <div class="flex justify-end gap-2.5 pt-3 border-t border-slate-700/60">
                <button type="button" onclick="closeCreateDesModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs font-semibold">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs shadow-lg shadow-cyan-500/25 flex items-center gap-1.5">
                    <i class="fa-solid fa-save"></i>
                    <span>Save Designation</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================== -->
<!-- 2. EDIT DESIGNATION MODAL                                      -->
<!-- ============================================================== -->
<div id="editDesModal" class="fixed inset-0 z-50 hidden bg-black/75 backdrop-blur-sm overflow-y-auto p-4 flex items-center justify-center">
    <div class="relative w-full max-w-md bg-[#09112a] border border-blue-500/30 rounded-2xl shadow-2xl p-6 text-white my-8">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-700/60 mb-4">
            <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-cyan-400"></i>
                <span>Edit Designation Details</span>
            </h3>
            <button type="button" onclick="closeEditDesModal()" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="editDesForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Section / Department *</label>
                <select name="department_id" id="editDesDept" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Designation Title *</label>
                <input type="text" name="title" id="editDesTitle" required 
                       class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Rank / Skill Grade</label>
                <input type="text" name="grade" id="editDesGrade" 
                       class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400 font-mono">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Description / Responsibilities</label>
                <textarea name="description" id="editDesDescription" rows="2" 
                          class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400"></textarea>
            </div>

            <div class="flex justify-end gap-2.5 pt-3 border-t border-slate-700/60">
                <button type="button" onclick="closeEditDesModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs font-semibold">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs shadow-lg shadow-cyan-500/25 flex items-center gap-1.5">
                    <i class="fa-solid fa-save"></i>
                    <span>Update Designation</span>
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openCreateDesModal() {
        document.getElementById('createDesModal').classList.remove('hidden');
    }

    function closeCreateDesModal() {
        document.getElementById('createDesModal').classList.add('hidden');
    }

    function openEditDesModal(des) {
        document.getElementById('editDesForm').action = "/factory/hr/designations/" + des.id;
        document.getElementById('editDesDept').value = des.department_id;
        document.getElementById('editDesTitle').value = des.title;
        document.getElementById('editDesGrade').value = des.grade || '';
        document.getElementById('editDesDescription').value = des.description || '';
        document.getElementById('editDesModal').classList.remove('hidden');
    }

    function closeEditDesModal() {
        document.getElementById('editDesModal').classList.add('hidden');
    }
</script>
@endpush

