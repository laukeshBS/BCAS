<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\User;
use App\Models\Admin\Admin;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
    public function cms_data(Request $request)
    {
        // Validate request parameters
        $request->validate([
            'status' => 'nullable|string',
            'lang_code' => 'nullable|string',
            'limit' => 'nullable|integer|min:1', // Limit between 1 and 100
            'currentPage' => 'nullable'
        ]);

        // Get parameters from the request with defaults
        $approve_status = $request->input('status');
        $lang_code = $request->input('lang_code');
        $perPage = $request->input('limit', 10); // Default limit to 5
        $page = $request->input('currentPage', 1); // Default page number to 1

        // Initialize query builder
        $query = Role::select('*');

        // Apply filters if provided
        if (!empty($approve_status)) {
            $query->where('status', $approve_status);
        }

        if (!empty($lang_code)) {
            $query->where('lang_code', $lang_code);
        }

        // Get the paginated list
        $list = $query->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'title' => 'Role List',
            'data' => $list->items(), // Get items for the current page
            'total' => $list->total(), // Total number of items
            'current_page' => $list->currentPage(), // Current page number
            'last_page' => $list->lastPage(), // Last page number
            'per_page' => $list->perPage(), // Items per page
        ]);
    }
    public function all_permissions()
    {
        if (is_null($this->user)) {
            return response()->json(['error' => 'Unauthorized to view roles'], 403);
        }

        // Check if the user is the super admin
        if ($this->user->id == 1) {
            // Get all admins with their roles and permissions
            $admins = Admin::with('roles.permissions')->get();
            
            // Collect permissions from all admins
            $permissions = $admins->flatMap(function ($admin) {
                return $admin->roles->flatMap->permissions;
            });
        } else {
            // Get the specific admin
            $user_permissions = Admin::with('roles.permissions')->find($this->user->id);

            // Check if the user has roles
            if ($user_permissions && $user_permissions->roles->isNotEmpty()) {
                $permissions = $user_permissions->roles->flatMap->permissions;
            } else {
                $permissions = [];
            }
        }

        return response()->json(['all_permissions' => $permissions], 200);
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
        // // Check if the user is authorized to edit roles
        // if (is_null($this->user) || !$this->user->can('role.edit')) {
        //     return response()->json([
        //         'message' => 'Sorry !! You are Unauthorized to edit any role !',
        //         'status' => false
        //     ], 403);
        // }
    
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
        // if (is_null($this->user) || !$this->user->can('role.edit')) {
        //     return response()->json(['error' => 'Unauthorized to edit role'], 403);
        // }

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
    public function role_list()
    {
        $roles = Role::pluck('name','id');
        return response()->json(['data' => $roles], 200);
    }

}
