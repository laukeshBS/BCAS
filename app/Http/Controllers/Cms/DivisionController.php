<?php

namespace App\Http\Controllers\Cms;

use App\Models\Admin;
use App\Models\Cms\Slider;
use App\Models\Cms\Tender;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Models\Cms\Division;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class DivisionController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    // Division
    public function data(Request $request)
    {
        $data = Division::get();

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

        $data = Division::find($validatedId);

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
            'rank' => 'required',
            'phone' => 'required',
            'email' => 'required',
            'division_id' => 'required',
            'region_id' => 'required',
            'lang_code' => 'required',
            'status' => 'required',
        ]);

        $division = new Division();
        $division->name = $validated['name'];
        $division->rank = $validated['rank'];
        $division->phone = $validated['phone'];
        $division->email = $validated['email'];
        $division->division_id = $validated['division_id'];
        $division->region_id = $validated['region_id'];
        $division->lang_code = $validated['lang_code'];
        $division->status = $validated['status'];
        $division->save();
        
        return response()->json($division);
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required',
            'rank' => 'required|max:255',
            'phone' => 'required',
            'email' => 'required',
            'division_id' => 'required',
            'region_id' => 'required',
            'lang_code' => 'required',
            'status' => 'required',
        ]);

        $division = Division::find($id);

        if (!$division) {
            return response()->json([
                'error' => 'Data Not Found.'
            ], 400);
        }

        $division->name = $validated['name'];
        $division->rank = $validated['rank'];
        $division->phone = $validated['phone'];
        $division->email = $validated['email'];
        $division->division_id = $validated['division_id'];
        $division->region_id = $validated['region_id'];
        $division->lang_code = $validated['lang_code'];
        $division->status = $validated['status'];
        $division->save();

        return response()->json($division);
    }
    public function delete($id)
    {
        $division = Division::find($id);

        if (!$division) {
            return response()->json([
                'error' => 'Data Not Found.'
            ], 400);
        }
        $division->delete();

        return response()->json($division);
    }
}
