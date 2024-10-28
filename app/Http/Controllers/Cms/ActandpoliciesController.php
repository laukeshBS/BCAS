<?php

namespace App\Http\Controllers\Cms;

use App\Models\Admin;
use App\Models\Cms\Slider;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\Cms\ActAndPolicies;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class ActandpoliciesController extends Controller
{
    public $user;

    public function __construct()
    {
        // $this->middleware(function ($request, $next) {
        //     $this->user = Auth::guard('admin')->user();
        //     return $next($request);
        // });
    }

    public function data(Request $request)
    {
        $perPage = $request->input('limit');
        $page = $request->input('currentPage');

        $slide = ActAndPolicies::select('*')->orderBy('id', 'desc')->paginate($perPage, ['*'], 'page', $page);
        if ($slide->isNotEmpty()) {
            $slide->transform(function ($item) {
                $item->created_at = date('d-m-Y', strtotime($item->created_at));
                if ($item->document) {
                    $item->document = asset('public/documents/'.$item->document);
                }
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
        // Validate the ID
        $validatedId = filter_var($id, FILTER_VALIDATE_INT);
        if (!$validatedId) {
            return response()->json([
                'error' => 'Invalid ID format'
            ], 400);
        }

        // Retrieve the data by ID
        $data = ActAndPolicies::find($validatedId);

        // Return a 404 response if data is not found
        if (!$data) {
            return response()->json([
                'error' => 'Data not found'
            ], 404);
        }
        // $data->start_date = date('d-m-Y', strtotime($data->start_date));
        // $data->end_date = date('d-m-Y', strtotime($data->end_date));
        $data->created_at = date('d-m-Y', strtotime($data->created_at));
        $data->document = asset('public/documents/' . $data->document) ;

        // Return the data as JSON
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|max:500',
            'status' => 'required',
            'lang_code' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'document' => 'required|file|mimes:pdf|max:2048',
        ]);

        // Handle file upload
        if ($request->hasFile('document')) {

            $docUpload = $request->file('document');
            $docPath = time() . '_' . $docUpload->getClientOriginalName();
            $docUpload->move(public_path('documents/actsAndPolicies/'), $docPath);
            $filePath = 'actsAndPolicies/'.$docPath;
        }

        // Create a new Act and Policy instance
        $actandpolicy = new ActAndPolicies();
        $actandpolicy->title = $validated['title'];
        $actandpolicy->description = $validated['description'];
        $actandpolicy->status = $validated['status'];
        $actandpolicy->lang_code = $validated['lang_code'];
        $actandpolicy->start_date = $validated['start_date'];
        $actandpolicy->end_date = $validated['end_date'];
        $actandpolicy->document = $filePath; // Store file path in the database
        $actandpolicy->save();
        
        // Return the data as JSON
        return response()->json($actandpolicy);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|max:500',
            'status' => 'required',
            'lang_code' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'document' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $actandpolicy = ActAndPolicies::find($id);

        if (!$actandpolicy) {
            return response()->json(['error' => 'Not Found.'], 404);
        }
        if ($request->hasFile('document')) {
            $docUpload = $request->file('document');
            $docPath = time() . '_' . $docUpload->getClientOriginalName();
            $docUpload->move(public_path('documents/actsAndPolicies/'), $docPath);
            $validated['document'] = 'actsAndPolicies/' . $docPath;
        }
        // Use fill to update attributes
        $actandpolicy->fill($validated);

        $actandpolicy->save();

        return response()->json($actandpolicy);
    }

    public function delete($id)
    {
        // Find the actandpolicy by id
        $actandpolicy = ActAndPolicies::find($id);

        if (!$actandpolicy) {
            return response()->json([
                'error' => 'Not Found.'
            ], 400);
        }

        // Delete the actandpolicy
        $actandpolicy->delete();

        // Return the data as JSON
        return response()->json($actandpolicy);
    }
}
