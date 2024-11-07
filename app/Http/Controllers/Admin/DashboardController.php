<?php

namespace App\Http\Controllers\Admin;

use App\Models\Cms\Menu;
use App\Models\Admin\Admin;
use App\Models\Cms\Contact;
use Illuminate\Http\Request;
use App\Models\Admin\Document;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\DocumentCategory;
use App\Models\Cms\Common\CommonTitle;
use Spatie\Permission\Models\Permission;

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
    public function cms_data()
    {

        // Correct method usage for connecting to a specific connection
        $ao = Document::where('document_category_id',1)->count();
        $ac = Document::where('document_category_id',2)->count();
        $corrigendum = Document::where('document_category_id',3)->count();
        $addendum_Order = Document::where('document_category_id',4)->count();
        $data = Document::join('document_categories', 'document_categories.id', '=', 'documents.document_category_id')
        ->selectRaw('document_categories.name as category_name, YEAR(documents.created_at) as year, COUNT(documents.id) as document_count')
        ->groupBy('document_categories.name', 'year')
        ->orderBy('year', 'asc')  // Order by year (ascending)
        ->orderBy('document_categories.name', 'asc') // Order by category name (ascending)
        ->get();

        // $data = [
        //     'ao' => $ao,
        //     'ac' => $ac,
        //     'corrigendum' => $corrigendum,
        //     'addendum_Order' => $addendum_Order,
        //     'documentCategoryCounts' => $documentCategoryCounts
        // ];

        return response()->json($data);
    }
    public function cms_count_data()
    {

        // Correct method usage for connecting to a specific connection
        $ao = Document::where('document_category_id',1)->count();
        $ac = Document::where('document_category_id',2)->count();
        $corrigendum = Document::where('document_category_id',3)->count();
        $addendum_Order = Document::where('document_category_id',4)->count();
        

        $data = [
            'ao' => $ao,
            'ac' => $ac,
            'corrigendum' => $corrigendum,
            'addendum_Order' => $addendum_Order,
        ];

        return response()->json([
            'data' => $data,
            'message' => 'Dashboard data retrieved successfully',
            'status' => true
        ], 200);
    }
}
