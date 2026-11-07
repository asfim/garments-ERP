<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Database\Seeders\RolePermissionSeeder;
use App\Models\User;

class RoleController extends Controller
{
    /**
     * Display list of roles and permissions
     */
    public function index()
    {
        $roles = Role::with(['permissions', 'users'])->get();
        $modules = RolePermissionSeeder::$modules;
        $allPermissions = Permission::all();

        return view('factory-erp.roles.index', compact('roles', 'modules', 'allPermissions'));
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role = Role::create([
            'name' => trim($request->name),
            'guard_name' => 'web',
        ]);

        if (!empty($request->permissions)) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('factory.roles.index')->with('success', "Role '{$role->name}' created successfully with permissions.");
    }

    /**
     * Update the specified role and its permissions
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        // Protect Super Admin rename
        if ($role->name === 'Super Admin' && $request->name !== 'Super Admin') {
            return back()->with('error', 'Super Admin role cannot be renamed.');
        }

        $role->name = trim($request->name);
        $role->save();

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('factory.roles.index')->with('success', "Role '{$role->name}' permissions updated successfully.");
    }

    /**
     * Delete role
     */
    public function destroy(Role $role)
    {
        if (in_array($role->name, ['Super Admin', 'Admin', 'Employee'])) {
            return back()->with('error', "System core role '{$role->name}' cannot be deleted.");
        }

        $roleName = $role->name;
        $role->delete();

        return redirect()->route('factory.roles.index')->with('success', "Role '{$roleName}' deleted successfully.");
    }
}

