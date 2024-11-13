<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminsController extends Controller
{
    public $user;

    // public function __construct()
    // {
    //     $this->middleware(function ($request, $next) {
    //         $this->user = Auth::guard('admin_api')->user();
    //         return $next($request);
    //     });
    // }
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //dd(Auth::guard('admin_api'));
        if (is_null($this->user) || !$this->user->can('admin.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any admin !');
        }

        $admins = Admin::paginate(10);
        
        return response()->json(['admins' =>  $admins]);
       // return view('admin.pages.admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('admin.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any admin !');
        }

        $roles  = Role::all();
        return view('admin.pages.admins.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('admin.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any admin !');
        }

        // Validation Data
        $request->validate([
            'name' => 'required|max:50',
            'email' => 'required|max:100|email|unique:mysql_admin.admins',
            'username' => 'required|max:100|unique:mysql_admin.admins',
            'password' => 'required|min:6|confirmed',
        ]);

        // Create New Admin
        $admin = new Admin();
        $admin->name = $request->name;
        $admin->username = $request->username;
        $admin->email = $request->email;
        $admin->password = Hash::make($request->password);
        $admin->save();
        $lastInsertID = $admin->save();
        $user_login_id=Auth::guard('admin')->user()->id;
        $action_by_role=Auth::guard('admin')->user()->username;
        if($lastInsertID > 0){
            $logs_data = array(
                'module_item_title'     =>  $request->name,
                'module_item_id'        =>  $lastInsertID,
                'action_by'             =>  $user_login_id,
                'old_data'              =>  json_encode($admin),
                'new_data'              =>  json_encode($admin),
                'action_name'           =>  'Add Admin ',
                //'page_category'         =>  '',
                'lang_id'               =>   "en",
                'action_type'        	=>  'Admin Model',
                'approve_status'        =>  clean_single_input(Auth::guard('admin')->user()->status),
                'action_by_role'        =>  $action_by_role
            );
            audit_trails($logs_data);
        }
        if ($request->roles) {
            $admin->assignRole($request->roles);
        }

        session()->flash('success', 'Admin has been created !!');
        return redirect()->route('admin.admins.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        if (is_null($this->user) || !$this->user->can('admin.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any admin !');
        }

        $admin = Admin::find($id);
        $roles  = Role::all();
        return view('admin.pages.admins.edit', compact('admin', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        if (is_null($this->user) || !$this->user->can('admin.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any admin !');
        }

        // TODO: You can delete this in your local. This is for heroku publish.
        // This is only for Super Admin role,
        // so that no-one could delete or disable it by somehow.
        if ($id === 1) {
            session()->flash('error', 'Sorry !! You are not authorized to update this Admin as this is the Super Admin. Please create new one if you need to test !');
            return back();
        }

        // Create New Admin
        $admin = Admin::find($id);

        // Validation Data
        $request->validate([
            'name' => 'required|max:50',
            'email' => 'required|max:100|email|unique:mysql_admin.admins,email,' . $id,
            'password' => 'nullable|min:6|confirmed',
        ]);


        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->username = $request->username;
        if ($request->password) {
            $admin->password = Hash::make($request->password);
        }
        $admin->save();

        $admin->roles()->detach();
        if ($request->roles) {
            $admin->assignRole($request->roles);
        }

        session()->flash('success', 'Admin has been updated !!');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        if (is_null($this->user) || !$this->user->can('admin.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any admin !');
        }

        // TODO: You can delete this in your local. This is for heroku publish.
        // This is only for Super Admin role,
        // so that no-one could delete or disable it by somehow.
        if ($id === 1) {
            session()->flash('error', 'Sorry !! You are not authorized to delete this Admin as this is the Super Admin. Please create new one if you need to test !');
            return back();
        }

        $admin = Admin::find($id);
        if (!is_null($admin)) {
            $admin->delete();
        }

        session()->flash('success', 'Admin has been deleted !!');
        return back();
    }


    // API
    public function cms_data(Request $request)
    {
        $request->validate([
            'limit' => 'required|integer',
            'currentPage' => 'required|integer',
        ]);

        $perPage = $request->input('limit');
        $page = $request->input('currentPage');

        $query = Admin::query();

        $data = $query->with('roles')->select('*')->paginate($perPage, ['*'], 'page', $page);

        if ($data->isNotEmpty()) {
            $data->transform(function ($item) {
                $item->created_at = date('d-m-Y', strtotime($item->created_at));
                return $item;
            });
        }

        return response()->json([
            'title' => 'List',
            'data' => $data->items(),
            'total' => $data->total(),
            'current_page' => $data->currentPage(),
            'last_page' => $data->lastPage(),
            'per_page' => $data->perPage(),
        ]);
    }
    
    public function data_by_id($id)
    {
        $validatedId = filter_var($id, FILTER_VALIDATE_INT);
        if (!$validatedId) {
            return response()->json([
                'error' => 'Invalid ID format'
            ], 400);
        }

        // Eager load roles to reduce queries
        $data = Admin::with('roles')->find($validatedId);

        if (!$data) {
            return response()->json([
                'error' => 'Data not found'
            ], 404);
        }

        // Format created_at date
        $data->created_at = date('d-m-Y', strtotime($data->created_at));

        // Get role IDs
        $roleIds = $data->roles->pluck('id');
        $data->roleIds = $roleIds;

        // Optionally include role names
        $roleNames = $data->roles->pluck('name');
        $data->roleNames = $roleNames;

        return response()->json($data);
    }

    public function Cms_store(Request $request)
    {
        // Validation Data
        $request->validate([
            'name' => 'required|max:50',
            'email' => 'required|max:100|email',
            'username' => 'required|max:100',
            'phone'     => 'required',
            'rank'     => 'nullable',
        ]);

        // Create New Admin
        $admin = new Admin();
        $admin->name = $request->name;
        $admin->username = $request->username;
        $admin->email = $request->email;
        $admin->phone = $request->phone;
        if ($request->rank) {
            $admin->rank = $request->rank;
        }
        
        $admin->save();
        
        $lastInsertID = $admin->save();
        $user_login_id=Auth::user()->id;
        $action_by_role=Auth::user()->username;
        if($lastInsertID > 0){
            $logs_data = array(
                'module_item_title'     =>  $request->name,
                'module_item_id'        =>  $lastInsertID,
                'action_by'             =>  $user_login_id,
                'old_data'              =>  json_encode($admin),
                'new_data'              =>  json_encode($admin),
                'action_name'           =>  'Add Admin ',
                //'page_category'         =>  '',
                'lang_id'               =>   "en",
                'action_type'        	=>  'Admin Model',
                'approve_status'        =>  clean_single_input(Auth::user()->status),
                'action_by_role'        =>  $action_by_role
            );
            audit_trails($logs_data);
        }
        // Assign roles by their names
        if ($request->filled('roles')) {
            $roleNames = Role::whereIn('id', $request->roles)->pluck('name')->toArray();
            
            if (empty($roleNames)) {
                return response()->json(['error' => 'Invalid roles provided.'], 400);
            }

            $admin->assignRole($roleNames);
        }

        return response()->json(['data' => $admin, 'message' => 'Created successfully.'], 201);
    }

    public function Cms_update(Request $request, $id)
    {
        // Create New Admin
        $admin = Admin::find($id);

        // Validation Data
        $request->validate([
            'name' => 'required|max:50',
            'email' => 'required|max:100|email',
            'username' => 'required|max:100',
            'phone'     => 'required',
            'rank'     => 'nullable',
        ]);


        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->username = $request->username;
        $admin->phone = $request->phone;
        if ($request->rank) {
            $admin->rank = $request->rank;
        }
        $admin->save();

        $admin->roles()->detach();
        // Assign roles by their names
        if ($request->filled('roles')) {
            $roleNames = Role::whereIn('id', $request->roles)->pluck('name')->toArray();
            
            if (empty($roleNames)) {
                return response()->json(['error' => 'Invalid roles provided.'], 400);
            }

            $admin->assignRole($roleNames);
        }
        return response()->json(['data' => $admin, 'message' => 'Updated successfully.'], 200);
    }

    public function delete($id)
    {
        $data = Admin::find($id);

        if (!$data) {
            return $this->sendError('No data found.', 404);
        }
        $data->delete();

        return response()->json(['data' => $data, 'message' => 'Deleted successfully.'], 201);
    }
    public function updateStatus(Request $request, $userId)
    {
        // Find the user by ID
        $user = Admin::findOrFail($userId);

        // Validate that the status is either 1 (active) or 2 (inactive)
        $request->validate([
            'status' => 'required|in:3,2'
        ]);

        // Update the user's status
        $user->status = $request->status;
        $user->save();

        return response()->json([
            'message' => 'User status updated successfully.',
            'status' => $user->status === 2 ? 'Active' : 'Inactive'
        ]);
    }
}
