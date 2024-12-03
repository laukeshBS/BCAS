<?php

namespace App\Http\Controllers\Cms;

use App\Models\Admin;
use App\Models\Cms\Slider;
use App\Models\Cms\Tender;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Models\Cms\Contact;
use App\Models\Cms\Division;
use App\Models\Cms\Region;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class ContactController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    // contact
    public function index(Request $request)
    {
        $lang_code = $request->input('lang_code');
        $division_id = $request->input('division_id');
        $region_id = $request->input('region_id');
        $type = $request->input('type');

        if ($type == 1) {
            // Query for Division model
            $query = Division::with('contacts')->where('lang_code', $lang_code);
            if ($division_id) {
                $query->where('id', $division_id);
            }
            $query->orderBy('position');
            $data = $query->get();
        } else if ($type == 2) {
            // Query for Region model
            $query = Region::with('contacts')->where('lang_code', $lang_code);
            if ($region_id) {
                $query->where('id', $region_id);
            }
            $query->orderBy('positions','asc');
            $data = $query->get();
        } else {
            // Return an empty array if type is not 1 or 2
            $data = [];
        }
        return response()->json($data);
    }


    public function data(Request $request)
    {
        // Validate request parameters
        $request->validate([
            'status' => 'nullable|string',
            'lang_code' => 'nullable|string',
            'limit' => 'nullable|integer|min:1', // Limit between 1 and 100
            'currentPage' => 'nullable'
        ]);

        // Get parameters from the request with defaults
        $approve_status = $request->input('status');
        $lang_code = $request->input('lang_code');
        $perPage = $request->input('limit', 10); // Default limit to 5
        $page = $request->input('currentPage', 1); // Default page number to 1

        // Initialize query builder
        $query = Contact::with(['division', 'region']);

        // Apply filters if provided
        if (!empty($approve_status)) {
            $query->where('status', $approve_status);
        }

        if (!empty($lang_code)) {
            $query->where('lang_code', $lang_code);
        }

        // Get the paginated list
        $list = $query->orderBy('id', 'desc')->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'title' => 'Contact List',
            'data' => $list->items(), // Get items for the current page
            'total' => $list->total(), // Total number of items
            'current_page' => $list->currentPage(), // Current page number
            'last_page' => $list->lastPage(), // Last page number
            'per_page' => $list->perPage(), // Items per page
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

        $data = Contact::find($validatedId);

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
        // Validate request parameters
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rank' => 'required|string|max:255',
            'phone' => 'required|string|max:20', // Adjust max length as needed
            'email' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'division_id' => 'nullable', // Ensure valid division
            'region_id' => 'nullable', // Ensure valid region
            'lang_code' => 'required|string|max:10', // Adjust max length as needed
            'status' => 'required|string|max:50', // Adjust max length as needed
            'positions' => 'nullable|string|max:255', // Adjust max length as needed
        ]);

        try {
            // Use mass assignment
            $contact = Contact::create($validated);

            return response()->json($contact, 201); // Return 201 Created status
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create contact.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // Validate request parameters
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rank' => 'required|string|max:255',
            'phone' => 'required|string|max:20', // Adjust max length as needed
            'email' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'division_id' => 'nullable', // Ensure valid division
            'region_id' => 'nullable', // Ensure valid region
            'lang_code' => 'required|string|max:10', // Adjust max length as needed
            'status' => 'required|string|max:50', // Adjust max length as needed
            'positions' => 'nullable|string|max:255', // Adjust max length as needed
        ]);

        // Find the contact
        $contact = Contact::find($id);

        if (!$contact) {
            return response()->json([
                'error' => 'Data Not Found.'
            ], 404); // Use 404 Not Found
        }

        // Update the contact using mass assignment
        $contact->update($validated);

        return response()->json($contact, 200); // Return 200 OK status
    }

    public function delete($id)
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return response()->json([
                'error' => 'Data Not Found.'
            ], 400);
        }
        $contact->delete();

        return response()->json($contact);
    }
}
