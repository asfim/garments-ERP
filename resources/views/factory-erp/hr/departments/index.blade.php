@extends('layouts.app')

@section('title', 'Departments & Sections | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-building text-indigo-400"></i>
                Departments & Factory Sections
            </h2>
            <p class="text-xs text-slate-400">Configure factory floor sections, production lines, and department units</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Go to Designations -->
            <a href="{{ route('factory.hr.designations.index') }}" 
               class="px-3.5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-cyan-300 hover:text-cyan-200 border border-slate-700 font-bold text-xs shadow-lg transition-all flex items-center gap-2">
                <i class="fa-solid fa-user-tag text-cyan-400"></i>
                <span>Designations</span>
            </a>

            <!-- Add Department Button -->
            <button type="button" onclick="openCreateDeptModal()"
                    class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-lg shadow-indigo-500/25 transition-all flex items-center gap-2">
                <i class="fa-solid fa-folder-plus"></i>
                <span>Add Department</span>
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="p-3 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs flex items-center gap-2">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
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

    <!-- Departments Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($departments as $dept)
        <div class="glass-panel rounded-2xl border border-slate-700/60 p-4 space-y-4 hover:border-indigo-500/40 transition-all flex flex-col justify-between">
            <div>
                <!-- Header -->
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded font-mono font-bold text-[10px] bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                {{ $dept->code ?? 'SEC' }}
                            </span>
                            <h3 class="text-sm font-bold text-white">{{ $dept->name }}</h3>
                        </div>
                        @if($dept->description)
                            <p class="text-[11px] text-slate-400 mt-1">{{ $dept->description }}</p>
                        @endif
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex items-center gap-1">
                        <button type="button" onclick="openEditDeptModal({{ json_encode($dept) }})" title="Edit Department" class="p-1.5 rounded-lg bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 text-xs">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <form action="{{ route('factory.hr.departments.destroy', $dept->id) }}" method="POST" onsubmit="return confirm('Delete department {{ $dept->name }}?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Delete Department" class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 text-xs">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Stats -->
                <div class="flex items-center gap-3 mt-3 py-2 px-3 rounded-xl bg-slate-900/60 border border-slate-800 text-xs">
                    <div class="flex-1">
                        <span class="text-[10px] text-slate-400 block">Designations</span>
                        <span class="font-mono font-bold text-cyan-300">{{ $dept->designations->count() }} Roles</span>
                    </div>
                    <div class="w-px h-6 bg-slate-700"></div>
                    <div class="flex-1">
                        <span class="text-[10px] text-slate-400 block">Active Staff</span>
                        <span class="font-mono font-bold text-emerald-400">{{ $dept->employees->count() }} Workers</span>
                    </div>
                </div>

                <!-- Designation List Inside Department -->
                <div class="mt-3 space-y-1.5">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Designations / Ranks</span>
                    @forelse($dept->designations as $des)
                    <div class="flex items-center justify-between py-1 px-2.5 rounded-lg bg-[#0a122e] border border-slate-800 text-xs hover:border-slate-700">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-circle text-[6px] text-indigo-400"></i>
                            <span class="text-slate-200 font-medium">{{ $des->title }}</span>
                            @if($des->grade)
                                <span class="text-[9px] text-slate-400 font-mono">({{ $des->grade }})</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] font-mono text-cyan-400 font-semibold">{{ $des->employees->count() }} emp</span>
                            <form action="{{ route('factory.hr.designations.destroy', $des->id) }}" method="POST" onsubmit="return confirm('Delete designation {{ $des->title }}?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-400 hover:text-rose-300 text-[10px]">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="text-[11px] text-slate-500 italic py-1">No designations created yet.</div>
                    @endforelse
                </div>
            </div>

            <!-- Add Designation shortcut for this department -->
            <button type="button" onclick="openCreateDesForDept({{ $dept->id }}, '{{ $dept->name }}')" 
                    class="w-full py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-indigo-300 font-semibold text-xs border border-slate-700 transition-colors flex items-center justify-center gap-1.5">
                <i class="fa-solid fa-plus text-[10px]"></i>
                <span>Add Designation to {{ $dept->name }}</span>
            </button>
        </div>
        @empty
        <div class="col-span-full py-12 text-center text-slate-400 glass-panel rounded-2xl border border-slate-700/60">
            <i class="fa-solid fa-sitemap text-3xl text-slate-500 mb-2 block"></i>
            No departments created yet. Click "Add Department" above to get started.
        </div>
        @endforelse
    </div>

</div>

<!-- Modal: Add Department -->
<div id="createDeptModal" class="fixed inset-0 z-50 hidden bg-black/75 backdrop-blur-sm overflow-y-auto p-4 flex items-center justify-center">
    <div class="relative w-full max-w-md bg-[#09112a] border border-indigo-500/40 rounded-2xl shadow-2xl p-6 text-white">
        <div class="flex items-center justify-between pb-3 border-b border-slate-700/60 mb-4">
            <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-folder-plus text-indigo-400"></i>
                Add Factory Department
            </h3>
            <button type="button" onclick="closeCreateDeptModal()" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="{{ route('factory.hr.departments.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Department Name *</label>
                <input type="text" name="name" required placeholder="e.g. Sewing Line 01 / Printing" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Code</label>
                <input type="text" name="code" placeholder="e.g. SEW / PRN" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white uppercase font-mono focus:outline-none focus:border-indigo-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Description</label>
                <textarea name="description" rows="2" placeholder="Department purpose & scope..." class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-400"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-700/60">
                <button type="button" onclick="closeCreateDeptModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs">Save Department</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Department -->
<div id="editDeptModal" class="fixed inset-0 z-50 hidden bg-black/75 backdrop-blur-sm overflow-y-auto p-4 flex items-center justify-center">
    <div class="relative w-full max-w-md bg-[#09112a] border border-blue-500/40 rounded-2xl shadow-2xl p-6 text-white">
        <div class="flex items-center justify-between pb-3 border-b border-slate-700/60 mb-4">
            <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-pen text-blue-400"></i>
                Edit Department
            </h3>
            <button type="button" onclick="closeEditDeptModal()" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="editDeptForm" method="POST" class="space-y-3">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Department Name *</label>
                <input type="text" name="name" id="editDeptName" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Code</label>
                <input type="text" name="code" id="editDeptCode" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white uppercase font-mono focus:outline-none focus:border-indigo-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Description</label>
                <textarea name="description" id="editDeptDesc" rows="2" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-400"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-700/60">
                <button type="button" onclick="closeEditDeptModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs">Update Department</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Add Designation -->
<div id="createDesModal" class="fixed inset-0 z-50 hidden bg-black/75 backdrop-blur-sm overflow-y-auto p-4 flex items-center justify-center">
    <div class="relative w-full max-w-md bg-[#09112a] border border-cyan-500/40 rounded-2xl shadow-2xl p-6 text-white">
        <div class="flex items-center justify-between pb-3 border-b border-slate-700/60 mb-4">
            <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-user-tag text-cyan-400"></i>
                Add Designation / Role
            </h3>
            <button type="button" onclick="closeCreateDesModal()" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="{{ route('factory.hr.designations.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Department *</label>
                <select name="department_id" id="desModalDeptSelect" required class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                    <option value="">Select Department</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Designation Title *</label>
                <input type="text" name="title" required placeholder="e.g. Senior Machine Operator" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Rank / Grade</label>
                <input type="text" name="grade" placeholder="e.g. Grade-3 / Officer" class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Description</label>
                <textarea name="description" rows="2" placeholder="Job responsibilities..." class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-700/60">
                <button type="button" onclick="closeCreateDesModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs">Save Designation</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openCreateDeptModal() {
        document.getElementById('createDeptModal').classList.remove('hidden');
    }
    function closeCreateDeptModal() {
        document.getElementById('createDeptModal').classList.add('hidden');
    }

    function openEditDeptModal(dept) {
        document.getElementById('editDeptForm').action = "/factory/hr/departments/" + dept.id;
        document.getElementById('editDeptName').value = dept.name;
        document.getElementById('editDeptCode').value = dept.code || '';
        document.getElementById('editDeptDesc').value = dept.description || '';
        document.getElementById('editDeptModal').classList.remove('hidden');
    }
    function closeEditDeptModal() {
        document.getElementById('editDeptModal').classList.add('hidden');
    }

    function openCreateDesModal() {
        document.getElementById('createDesModal').classList.remove('hidden');
    }
    function closeCreateDesModal() {
        document.getElementById('createDesModal').classList.add('hidden');
    }

    function openCreateDesForDept(deptId, deptName) {
        document.getElementById('desModalDeptSelect').value = deptId;
        openCreateDesModal();
    }
</script>
@endpush
@endsection

