<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
class SearchContoller extends Controller
{
    
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }
    public function index(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('admin.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any admin !');
        }
        $search = $request->input('search');
        $type = $request->input('type');
        if($type==='admins'){
            
            $admins = Admin::where('name', 'like', "%$search%")
            ->orWhere('username', 'like', "%$search%")
            ->paginate(2);            
            return view('admin.pages.admins.index', compact('admins'));
        }
        
    }
}
