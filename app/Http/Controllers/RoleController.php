<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:role list')->only(['index']);
        $this->middleware('can:role edit')->only(['edit', 'update']);
    }

    public function index()
    {
        $roles = Role::with('permissions')->get();
        return view('backend.role.index', compact('roles'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all()->groupBy(function ($perm) {
            $parts = explode(' ', $perm->name, 2);
            return $parts[0] ?? 'general';
        });

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('backend.role.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === 'user') {
            return redirect()->route('roles.index')
                ->with('error', 'Permissions cannot be assigned to the user role.');
        }

        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $permissions = $request->input('permissions', []);

        $validNames = Permission::pluck('name')->toArray();
        $invalid = array_diff($permissions, $validNames);
        if (!empty($invalid)) {
            return back()->withErrors(['permissions' => 'Invalid permission(s): ' . implode(', ', $invalid)]);
        }

        $role->syncPermissions($permissions);

        return redirect()->route('roles.index')
            ->with('success', 'Permissions updated for role "' . $role->name . '" successfully.');
    }
}
