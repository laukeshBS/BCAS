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
        $roles = Role::paginate(15);
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
            'name' => ['required', 'max:100', 'unique:mysql_admin.roles,name'],
        ], [
            'name.required' => 'Please provide a role name',
            'name.max' => 'Role name cannot exceed 100 characters',
            'name.unique' => 'This role name already exists',
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'admin']);

        $permissions = $request->input('permissions');
        //dd($permissions);
        if (!empty($permissions)) {
            $role->syncPermissions($permissions);
        }

        return response()->json(['message' => 'Role created successfully', 'role' => $role], 201);
    }
    public function edit(int $id)
    {
        // Check if the user is authorized to edit roles
        if (is_null($this->user) || !$this->user->can('role.edit')) {
            return response()->json([
                'message' => 'Sorry !! You are Unauthorized to edit any role !',
                'status' => false
            ], 403);
        }
    
        // Fetch the role using its ID
        $role = Role::findById($id, 'admin');
    
        // Fetch all permissions and group them
        $all_permissions = Permission::all();
        $permission_groups = User::getPermissionGroups();
    
        // Prepare the response data
        $data = [
            'role' => $role,
            'all_permissions' => $all_permissions,
            'permission_groups' => $permission_groups
        ];
    
        // Return the response as JSON
        return response()->json([
            'data' => $data,
            'message' => 'Role data retrieved successfully',
            'status' => true
        ], 200);
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
            'name' => 'required|max:100|unique:mysql_admin.roles,name,' . $id
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
