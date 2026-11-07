@extends('layouts.app')

@section('title', 'Roles & Permissions Management | Factory ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Breadcrumb & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-cyan-400 transition-colors flex items-center gap-1">
                    <i class="fa-solid fa-house"></i>
                    <span>Dashboard</span>
                </a>
                <span>/</span>
                <span class="text-cyan-400 font-bold">Roles & Permissions</span>
            </div>
            <h2 class="text-xl font-extrabold text-white flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-gradient-to-tr from-cyan-500 to-blue-600 text-white flex items-center justify-center text-sm shadow-lg shadow-cyan-500/20">
                    <i class="fa-solid fa-shield-halved"></i>
                </span>
                <span>System Roles & Module Permissions (রোল ও মেনু পারমিশন)</span>
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">
                Manage granular module-level access control (View, Create, Edit, Delete). Unauthorized menus will automatically hide from users.
            </p>
        </div>

        <div>
            <button type="button" onclick="openCreateRoleModal()"
                    class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-500 hover:to-cyan-500 text-white font-bold text-xs shadow-lg shadow-cyan-500/20 transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Create New Role (নতুন রোল তৈরি)</span>
            </button>
        </div>
    </div>

    <!-- Notifications -->
    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs flex items-center gap-2 shadow-lg">
        <i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs flex items-center gap-2 shadow-lg">
        <i class="fa-solid fa-circle-exclamation text-rose-400 text-sm"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- Roles Grid Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($roles as $role)
        <div class="glass-panel rounded-2xl border border-slate-700/60 p-5 shadow-xl flex flex-col justify-between space-y-4 hover:border-cyan-500/40 transition-all">
            
            <div>
                <div class="flex items-start justify-between gap-3 mb-2">
                    <div class="flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-cyan-500/20 to-blue-500/20 border border-cyan-500/30 flex items-center justify-center text-cyan-400 font-bold text-sm">
                            <i class="fa-solid fa-user-gear"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                                <span>{{ $role->name }}</span>
                                @if(in_array($role->name, ['Super Admin', 'Admin', 'Employee']))
                                    <span class="px-2 py-0.2 rounded-full text-[9px] font-mono font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">CORE</span>
                                @endif
                            </h3>
                            <span class="text-[11px] text-slate-400 font-mono">
                                {{ $role->users->count() }} user(s) assigned
                            </span>
                        </div>
                    </div>

                    @if(!in_array($role->name, ['Super Admin', 'Admin', 'Employee']))
                    <form action="{{ route('factory.roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this role?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 text-xs transition-colors" title="Delete Role">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                    @endif
                </div>

                <div class="mt-3 pt-3 border-t border-slate-800">
                    <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-2">
                        Granted Permissions ({{ $role->permissions->count() }})
                    </div>
                    
                    @if($role->name === 'Super Admin')
                        <span class="px-2.5 py-1 rounded-xl text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 inline-block">
                            <i class="fa-solid fa-check-double mr-1"></i> Full System Access (All Permissions Granted)
                        </span>
                    @else
                        <div class="flex flex-wrap gap-1 max-h-32 overflow-y-auto pr-1 text-[10px]">
                            @forelse($role->permissions->take(15) as $perm)
                                <span class="px-2 py-0.5 rounded-lg bg-[#0a142c] text-slate-300 border border-slate-700/80 font-mono">
                                    {{ $perm->name }}
                                </span>
                            @empty
                                <span class="text-slate-500 italic">No permissions assigned yet</span>
                            @endforelse

                            @if($role->permissions->count() > 15)
                                <span class="px-2 py-0.5 rounded-lg bg-cyan-500/20 text-cyan-300 font-bold">
                                    +{{ $role->permissions->count() - 15 }} more
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="pt-2">
                <button type="button" onclick="openEditRoleModal({{ json_encode($role) }}, {{ json_encode($role->permissions->pluck('name')) }})"
                        class="w-full py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white border border-slate-700 font-bold text-xs transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-cyan-400"></i>
                    <span>Edit Permissions</span>
                </button>
            </div>

        </div>
        @endforeach
    </div>

</div>

<!-- CREATE ROLE MODAL -->
<div id="createRoleModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm p-4 flex items-center justify-center overflow-y-auto">
    <div class="relative w-full max-w-3xl bg-[#081026] border border-cyan-500/40 rounded-3xl shadow-2xl p-6 text-white my-8 max-h-[90vh] flex flex-col">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-800 mb-4 flex-shrink-0">
            <h3 class="text-base font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-shield-plus text-cyan-400"></i>
                <span>Create New Custom Role (নতুন রোল তৈরি করুন)</span>
            </h3>
            <button type="button" onclick="closeCreateRoleModal()" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('factory.roles.store') }}" method="POST" class="space-y-4 flex-1 overflow-y-auto pr-1">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1">Role Name (রোলের নাম) *</label>
                <input type="text" name="name" required placeholder="e.g. Production Supervisor / Store Incharge"
                       class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400 font-bold">
            </div>

            <!-- Permission Matrix By Module -->
            <div class="space-y-3 pt-2">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-cyan-300 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-list-check"></i>
                        <span>Select Module Permissions (View, Create, Edit, Delete)</span>
                    </label>
                    <button type="button" onclick="toggleAllCheckboxes('createRoleModal')" class="text-[11px] text-cyan-400 hover:underline font-bold">
                        Toggle Select All
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-96 overflow-y-auto p-1">
                    @foreach($modules as $mKey => $mData)
                    <div class="p-3 rounded-xl bg-[#050b1c] border border-slate-800 space-y-2">
                        <div class="flex items-center justify-between font-bold text-xs text-white pb-1 border-b border-slate-800/80">
                            <span>{{ $mData['label'] }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-1.5 text-[11px]">
                            @foreach($mData['permissions'] as $pName)
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white">
                                <input type="checkbox" name="permissions[]" value="{{ $pName }}" class="rounded bg-[#0a122e] border-slate-700 text-cyan-500 focus:ring-0">
                                <span class="capitalize">{{ str_replace('_', ' ', explode('_', $pName, 2)[0]) }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-800 flex-shrink-0">
                <button type="button" onclick="closeCreateRoleModal()" class="px-4 py-2.5 rounded-xl bg-slate-800 text-slate-300 text-xs">Cancel</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold text-xs shadow-lg">Save New Role</button>
            </div>
        </form>

    </div>
</div>

<!-- EDIT ROLE PERMISSIONS MODAL -->
<div id="editRoleModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm p-4 flex items-center justify-center overflow-y-auto">
    <div class="relative w-full max-w-3xl bg-[#081026] border border-cyan-500/40 rounded-3xl shadow-2xl p-6 text-white my-8 max-h-[90vh] flex flex-col">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-800 mb-4 flex-shrink-0">
            <h3 class="text-base font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-cyan-400"></i>
                <span>Edit Role Permissions (<span id="editRoleTitle" class="text-cyan-300"></span>)</span>
            </h3>
            <button type="button" onclick="closeEditRoleModal()" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="editRoleForm" method="POST" class="space-y-4 flex-1 overflow-y-auto pr-1">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1">Role Name *</label>
                <input type="text" name="name" id="editRoleName" required
                       class="w-full bg-[#0a122e] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400 font-bold">
            </div>

            <!-- Permission Matrix By Module -->
            <div class="space-y-3 pt-2">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-cyan-300 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-list-check"></i>
                        <span>Configure Allowed Permissions</span>
                    </label>
                    <button type="button" onclick="toggleAllCheckboxes('editRoleModal')" class="text-[11px] text-cyan-400 hover:underline font-bold">
                        Toggle Select All
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-96 overflow-y-auto p-1">
                    @foreach($modules as $mKey => $mData)
                    <div class="p-3 rounded-xl bg-[#050b1c] border border-slate-800 space-y-2">
                        <div class="flex items-center justify-between font-bold text-xs text-white pb-1 border-b border-slate-800/80">
                            <span>{{ $mData['label'] }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-1.5 text-[11px]">
                            @foreach($mData['permissions'] as $pName)
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white">
                                <input type="checkbox" name="permissions[]" value="{{ $pName }}" class="edit-perm-check rounded bg-[#0a122e] border-slate-700 text-cyan-500 focus:ring-0">
                                <span class="capitalize">{{ str_replace('_', ' ', explode('_', $pName, 2)[0]) }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-800 flex-shrink-0">
                <button type="button" onclick="closeEditRoleModal()" class="px-4 py-2.5 rounded-xl bg-slate-800 text-slate-300 text-xs">Cancel</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold text-xs shadow-lg">Save Permissions</button>
            </div>
        </form>

    </div>
</div>

@push('scripts')
<script>
    function openCreateRoleModal() {
        document.getElementById('createRoleModal').classList.remove('hidden');
    }
    function closeCreateRoleModal() {
        document.getElementById('createRoleModal').classList.add('hidden');
    }

    function openEditRoleModal(role, grantedPermissions) {
        document.getElementById('editRoleForm').action = "/factory/roles/" + role.id;
        document.getElementById('editRoleTitle').innerText = role.name;
        document.getElementById('editRoleName').value = role.name;

        // Reset check states
        const checkboxes = document.querySelectorAll('.edit-perm-check');
        checkboxes.forEach(cb => {
            cb.checked = grantedPermissions.includes(cb.value);
        });

        document.getElementById('editRoleModal').classList.remove('hidden');
    }

    function closeEditRoleModal() {
        document.getElementById('editRoleModal').classList.add('hidden');
    }

    function toggleAllCheckboxes(modalId) {
        const modal = document.getElementById(modalId);
        const checkboxes = modal.querySelectorAll('input[type="checkbox"]');
        const anyUnchecked = Array.from(checkboxes).some(cb => !cb.checked);
        checkboxes.forEach(cb => cb.checked = anyUnchecked);
    }
</script>
@endpush
@endsection

