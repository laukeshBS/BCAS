<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Admin;
use Illuminate\Http\Request;
use App\Models\Admin\AuditTrail;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuditController extends Controller
{
    
    public function index(Request $request)
    {
        $perPage = $request->input('limit');
        $page = $request->input('currentPage');

        $audit = AuditTrail::select('*')->orderBy('id', 'desc')->paginate($perPage, ['*'], 'page', $page);
        
        return response()->json([
            'title' => 'List',
            'data' => $audit->items(),
            'total' => $audit->total(),
            'current_page' => $audit->currentPage(),
            'last_page' => $audit->lastPage(),
            'per_page' => $audit->perPage(),
        ]);
    }
}
