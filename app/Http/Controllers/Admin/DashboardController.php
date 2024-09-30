<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;
use App\Models\Cms\Menu;
use App\Models\Cms\Contact;
use App\Models\Cms\Common\CommonTitle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    public function index()
    {
        if (is_null($this->user) || !$this->user->can('dashboard.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view dashboard!');
        }

        // Correct method usage for connecting to a specific connection
        $total_roles = Role::onWriteConnection('bcas_admin')->count();
        $total_admins = Admin::count();
        $total_menus = Menu::count();
        $CommonTitle = CommonTitle::count();
        $total_permissions = Permission::count();
        $total_contacts = Contact::count();

        return view('admin.pages.dashboard.index', compact('total_admins','CommonTitle', 'total_roles', 'total_menus', 'total_permissions'));
    }
}
