<?php

namespace App\Http\Controllers\Cms;

use App\Models\Admin;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Models\Cms\Region;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class RegionController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    // Region
    public function data(Request $request)
    {
        $data = Region::get();

        $data->transform(function ($item) {
            $item->created_at = date('d-m-Y', strtotime($item->created_at));
            return $item;
        });

        return response()->json($data);
    }
    public function region_list(Request $request)
    {
        $lang_code = $request->input('lang_code');
        $data = Region::where('lang_code', $lang_code)->get();

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

        $data = Region::find($validatedId);

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
            'name' => 'required|string|max:255',
            'status' => 'required|string',
            'lang_code' => 'required|string|max:10',
        ]);

        $region = new Region();
        $region->name = $validated['name'];
        $region->status = $validated['status'];
        $region->lang_code = $validated['lang_code'];
        $region->save();
        
        return response()->json($region, 200);
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|string',
            'lang_code' => 'required|string|max:10',
        ]);

        $region = Region::find($id);

        if (!$region) {
            return response()->json([
                'error' => 'Data Not Found.'
            ], 400);
        }

        $region->fill($validated);
        $region->save();

        return response()->json($region, 200);
    }
    public function delete($id)
    {
        $region = Region::find($id);

        if (!$region) {
            return response()->json([
                'error' => 'Data Not Found.'
            ], 400);
        }
        $region->delete();

        return response()->json($region);
    }
}
