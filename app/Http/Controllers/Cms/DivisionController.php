<?php

namespace App\Http\Controllers\Cms;

use App\Models\Admin;
use App\Models\Cms\Division;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
        $perPage = $request->input('limit');
        $page = $request->input('currentPage');

        $slide = Division::select('*') ->paginate($perPage, ['*'], 'page', $page);
        if ($slide->isNotEmpty()) {
            $slide->transform(function ($item) {
                $item->created_at = date('d-m-Y', strtotime($item->created_at));
                return $item;
            });
        }
        return response()->json([
            'title' => 'List',
            'data' => $slide->items(), 
            'total' => $slide->total(), 
            'current_page' => $slide->currentPage(), 
            'last_page' => $slide->lastPage(), 
            'per_page' => $slide->perPage(), 
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
            'name' => 'required|string|max:255',
            'status' => 'required|string',
            'lang_code' => 'required|string|max:10',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|max:255',
            'address' => 'nullable|string|max:255',
            'fax' => 'nullable|string|max:15',
            'epabx' => 'nullable|string|max:15',
        ]);

        try {
            $division = Division::create($validated);

            return response()->json($division, 201); // 201 Created
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to create division'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|string',
            'lang_code' => 'required|string|max:10',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|max:255',
            'address' => 'nullable|string|max:255',
            'fax' => 'nullable|string|max:15',
            'epabx' => 'nullable|string|max:15',
        ]);

        try {
            $division = Division::findOrFail($id); // Find the division or fail

            $division->update($validated); // Mass assignment for update

            return response()->json($division, 200); // 200 OK
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Division not found'], 404); // 404 Not Found
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to update division'], 500); // 500 Internal Server Error
        }
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
