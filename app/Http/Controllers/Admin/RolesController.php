<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin_api')->user();
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        if (is_null($this->user) || !$this->user->can('role.view')) {
            return response()->json(['error' => 'Unauthorized to view roles'], 403);
        }
        //dd(Auth::guard('admin_api')->user());
        $roles = Role::paginate(5);
        return response()->json(['roles' => $roles], 200);
    }
    public function all_permissions()
    {
        if (is_null($this->user) || !$this->user->can('role.view')) {
            return response()->json(['error' => 'Unauthorized to view roles'], 403);
        }
        //dd(Auth::guard('admin_api')->user());
        $all_permissions = Permission::all();
       
        return response()->json(['all_permissions' => $all_permissions], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('role.create')) {
            return response()->json(['error' => 'Unauthorized to create role'], 403);
        }

        $request->validate([
            'name' => 'required|max:100|unique:roles'
        ], [
            'name.required' => 'Please provide a role name'
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'admin_api']);

        $permissions = $request->input('permissions');
        if (!empty($permissions)) {
            $role->syncPermissions($permissions);
        }

        return response()->json(['message' => 'Role created successfully', 'role' => $role], 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        if (is_null($this->user) || !$this->user->can('role.edit')) {
            return response()->json(['error' => 'Unauthorized to edit role'], 403);
        }

        $request->validate([
            'name' => 'required|max:100|unique:roles,name,' . $id
        ], [
            'name.required' => 'Please provide a role name'
        ]);

        $role = Role::findById($id, 'admin');
        if (!$role) {
            return response()->json(['error' => 'Role not found'], 404);
        }

        $role->name = $request->name;
        $role->save();

        $permissions = $request->input('permissions');
        if (!empty($permissions)) {
            $role->syncPermissions($permissions);
        }

        return response()->json(['message' => 'Role updated successfully', 'role' => $role], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        if (is_null($this->user) || !$this->user->can('role.delete')) {
            return response()->json(['error' => 'Unauthorized to delete role'], 403);
        }

        $role = Role::findById($id, 'admin');
        if (!$role) {
            return response()->json(['error' => 'Role not found'], 404);
        }

        if ($id === 1) { // Assuming ID 1 is for Super Admin role
            return response()->json(['error' => 'Cannot delete Super Admin role'], 403);
        }

        $role->delete();

        return response()->json(['message' => 'Role deleted successfully'], 200);
    }
}
