<?php

namespace App\Http\Controllers\Cms\Common;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;
use App\Models\Cms\Common\CommonTitle as CommonTitle;

class CommonTitleController extends Controller
{
    /**
     * Display a listing of the CommonTitle.
     */
    public $user;

    public function __construct(){}

    public function index(Request $request)
    {
        // Get parameters from the request
        $approve_status = $request->input('status');
        $lang_code = $request->input('lang_code');
        $perPage = $request->input('limit'); // Default limit to 5
        $page = $request->input('currentPage'); // Default page number to 1

        // Initialize query builder
        $query = CommonTitle::whereNotNull('title');

        // Apply filters if provided
        if (!empty($approve_status)) {
            $query->where('status', $approve_status);
        }

        if (!empty($lang_code)) {
            $query->where('lang_code', $lang_code);
        }
        
        // Get the paginated list
        $list = $query->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'title' => 'CommonTitle List',
            'data' => $list->items(), // Get items for the current page
            'total' => $list->total(), // Total number of items
            'current_page' => $list->currentPage(), // Current page number
            'last_page' => $list->lastPage(), // Last page number
            'per_page' => $list->perPage(), // Items per page
        ]);
    }
    public function data_by_id($id)
    {
        // Validate the ID
        $validatedId = filter_var($id, FILTER_VALIDATE_INT);
        if (!$validatedId) {
            return response()->json([
                'error' => 'Invalid ID format'
            ], 400);
        }

        // Retrieve the data by ID
        $data = CommonTitle::find($validatedId);

        // Return a 404 response if data is not found
        if (!$data) {
            return response()->json([
                'error' => 'Data not found'
            ], 404);
        }
        $data->created_at = date('d-m-Y', strtotime($data->created_at));

        // Return the data as JSON
        return response()->json($data);
    }

    public function store(Request $request)
    {
        Log::info($request->all());
        $validated = $request->validate([
            'title' => 'required|max:255',
            'slugs' => 'nullable|max:255', // Changed from slugs to slug
            'status' => 'required|integer', // Ensure status is an integer
            'lang_code' => 'required|string|max:2', // Assuming lang_code is a 2-letter code
        ]);

        // Create a new Act and Policy instance
        $query = new CommonTitle();
        $query->title = $validated['title'];
        $query->slugs = $validated['slugs'];
        $query->status = $validated['status'];
        $query->lang_code = $validated['lang_code'];
        $query->save();
        
        // Return the data as JSON
        return response()->json($query);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'slugs' => 'max:500',
            'status' => 'required',
        ]);

        $query = CommonTitle::find($id);

        if (!$query) {
            return response()->json([
                'error' => 'Not Found.'
            ], 400);
        }

        $query->title = $request->input('title');
        $query->slugs = $request->input('slugs');
        $query->status = $request->input('status');

        $query->save();

        // Return the data as JSON
        return response()->json($query);
    }

    public function delete($id)
    {
        // Find by id
        $query = CommonTitle::find($id);

        if (!$query) {
            return response()->json([
                'error' => 'Not Found.'
            ], 400);
        }

        // Delete data
        $query->delete();

        // Return the data as JSON
        return response()->json($query);
    }
}
