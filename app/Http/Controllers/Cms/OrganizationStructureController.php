<?php

namespace App\Http\Controllers\Cms;

use App\Models\Admin;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use App\Models\Cms\OrganizationStructure;
use Illuminate\Support\Facades\Validator;

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
        $data = OrganizationStructure::where('lang_code', $lang_code)->where('status', 3)->get();

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

    // CMS Api
    public function cms_data(Request $request)
    {
        $request->validate([
            'limit' => 'required|integer',
            'currentPage' => 'required|integer',
        ]);

        $perPage = $request->input('limit');
        $page = $request->input('currentPage');

        $query = OrganizationStructure::query();

        $data = $query->select('*')->orderBy('id', 'desc')->paginate($perPage, ['*'], 'page', $page);

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

    public function cms_store(Request $request): mixed
    {
        // Define validation rules
        $rules = [
            'organization'    => 'required|string|max:255',
            'roles'       => 'required|string|max:255',
            'lang_code'          => 'required|string|max:10',
            'positions'  => 'required|string|max:255',
            'status'         => 'required',
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error',
                'messages' => $validator->errors()->toArray()
            ], 422);  // 422 Unprocessable Entity
        }

        // Prepare data for insertion
        $data = $request->only([
            'organization','roles','lang_code','positions','status'
        ]);

        // Create new Organization Structure record
        try {
            $OrganizationStructure = OrganizationStructure::create($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error creating the Organization Structure record.',
                'message' => $e->getMessage()
            ], 500);  // 500 Internal Server Error
        }

        // Return JSON response with success message
        return response()->json([
            'data' => $OrganizationStructure,
            'message' => 'Created successfully.'
        ], 201);  // 201 Created
    }

    public function cms_update(Request $request, $id): mixed
    {
        // Define validation rules
        $rules = [
            'organization'    => 'required|string|max:255',
            'roles'       => 'required|string|max:255',
            'lang_code'          => 'required|string|max:10',
            'positions'  => 'required|string|max:255',
            'status'         => 'required',
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error',
                'messages' => $validator->errors()->toArray()
            ], 422);  // 422 Unprocessable Entity
        }

        // Find the existing Organization Structure record by ID
        $OrganizationStructure = OrganizationStructure::find($id);

        if (!$OrganizationStructure) {
            return response()->json([
                'error' => 'Record not found.',
                'message' => 'The requested Organization Structure record does not exist.'
            ], 404);  // 404 Not Found
        }

        // Prepare data for update
        $data = $request->only([
            'organization','roles','lang_code','positions','status'
        ]);

        // Update the existing Organization Structure record
        try {
            $OrganizationStructure->update($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error updating the Organization Structure record.',
                'message' => $e->getMessage()
            ], 500);  // 500 Internal Server Error
        }

        // Return JSON response with success message
        return response()->json([
            'data' => $OrganizationStructure,
            'message' => 'Updated successfully.'
        ], 200);  // 200 OK
    }

    public function cms_delete($id)
    {
        // Find the Organization Structure by id
        $OrganizationStructure = OrganizationStructure::find($id);

        if (!$OrganizationStructure) {
            return response()->json([
                'error' => 'Not Found.'
            ], 400);
        }

        // Delete the Organization Structure
        $OrganizationStructure->delete();

        // Return the data as JSON
        return response()->json(['data' => $OrganizationStructure, 'message' => 'Deleted successfully.'], 200);
    }
}
