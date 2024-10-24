<?php

namespace App\Http\Controllers\Cms;

use App\Models\Admin;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Models\Cms\OrganizationStructure;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class OrganizationStructureController  extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    // OrganizationStructure
    public function data(Request $request)
    {
        $data = OrganizationStructure::get();

        $data->transform(function ($item) {
            $item->created_at = date('d-m-Y', strtotime($item->created_at));
            return $item;
        });

        return response()->json($data);
    }
    public function organization_list(Request $request)
    {
        $lang_code = $request->input('lang_code');
        $data = OrganizationStructure::where('lang_code', $lang_code)->get();

        $data->transform(function ($item) {
            $item->created_at = date('d-m-Y', strtotime($item->created_at));
            return $item;
        });

        return response()->json($data);
    }
    public function data_by_id($id)
    {
        $validatedId = filter_var($id, FILTER_VALIDATE_INT);
        if (!$validatedId) {
            return response()->json([
                'error' => 'Invalid ID format'
            ], 400);
        }

        $data = OrganizationStructure::find($validatedId);

        if (!$data) {
            return response()->json([
                'error' => 'Data not found'
            ], 404);
        }
        $data->created_at = date('d-m-Y', strtotime($data->created_at));

        return response()->json($data);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'status' => 'required',
        ]);

        $region = new OrganizationStructure();
        $region->name = $validated['name'];
        $region->status = $validated['status'];
        $region->save();
        
        return response()->json($region);
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required',
            'status' => 'required',
        ]);

        $region = OrganizationStructure::find($id);

        if (!$region) {
            return response()->json([
                'error' => 'Data Not Found.'
            ], 400);
        }

        $region->name = $validated['name'];
        $region->status = $validated['status'];
        $region->save();

        return response()->json($region);
    }
    public function delete($id)
    {
        $region = OrganizationStructure::find($id);

        if (!$region) {
            return response()->json([
                'error' => 'Data Not Found.'
            ], 400);
        }
        $region->delete();

        return response()->json($region);
    }
}
