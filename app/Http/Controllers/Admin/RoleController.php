<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Permission;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RoleController extends Controller
{
    public function index()
    {
        return view('admin.roles.index');
    }

    public function create()
    {
        $permissions = Permission::orderBy('module')->orderBy('name')->get()->groupBy('module');
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'role_name' => 'required|string|max:50',
            'role_abbreviation' => 'required|string|max:30|unique:roles,role_abbreviation',
            'role_description' => 'nullable|string|max:250',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = new Role();
        $role->role_name = $request->role_name;
        $role->role_abbreviation = $request->role_abbreviation;
        $role->role_description = $request->role_description ?? '';
        $role->save();

        $role->permissions()->sync($request->input('permissions', []));

        flash('Role has been created successfully!')->success();
        return redirect()->route('list.roles');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::orderBy('module')->orderBy('name')->get()->groupBy('module');
        $rolePermissionIds = $role->permissions->pluck('id')->toArray();
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissionIds'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $this->validate($request, [
            'role_name' => 'required|string|max:50',
            'role_abbreviation' => 'required|string|max:30|unique:roles,role_abbreviation,' . $role->id,
            'role_description' => 'nullable|string|max:250',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->role_name = $request->role_name;
        $role->role_abbreviation = $request->role_abbreviation;
        $role->role_description = $request->role_description ?? '';
        $role->save();

        $role->permissions()->sync($request->input('permissions', []));

        flash('Role has been updated successfully!')->success();
        return redirect()->route('list.roles');
    }

    public function destroy(Request $request)
    {
        $id = $request->input('id');
        try {
            $role = Role::findOrFail($id);
            if ($role->admins()->count() > 0) {
                echo 'in_use';
                return;
            }
            $role->permissions()->detach();
            $role->delete();
            echo 'ok';
        } catch (ModelNotFoundException $e) {
            echo 'notok';
        }
    }

    public function fetchData()
    {
        $roles = Role::withCount('admins')->select('roles.id', 'roles.role_name', 'roles.role_abbreviation', 'roles.role_description');
        return \DataTables::of($roles)
            ->addColumn('action', function ($role) {
                return '<a class="btn btn-sm btn-primary me-2" href="' . route('edit.role', ['id' => $role->id]) . '"><i class="ri ri-edit-box-line me-1"></i> Edit</a>' .
                    '<a class="btn btn-sm btn-danger" href="javascript:void(0);" onclick="deleteRole(' . $role->id . ');"><i class="ri ri-delete-bin-line me-1"></i> Delete</a>';
            })
            ->addColumn('admins_count', function ($role) {
                return $role->admins_count;
            })
            ->setRowId(function ($role) {
                return 'role_dt_row_' . $role->id;
            })
            ->make(true);
    }
}
