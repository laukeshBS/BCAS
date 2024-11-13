<?php

namespace App\Http\Controllers\Cms;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Models\Cms\Gallery;
use GPBMetadata\Google\Api\Auth;

class MainGalleryController extends Controller
{
    public $user;

    public function index(Request $request)
    {
    }

    // API
    public function data(Request $request)
    {
        $lang_code = $request->input('lang_code'); // Optional lang_code
        $perPage = $request->input('limit'); // Optional limit (for controlling page size)
        $page = $request->input('page', 1); // Optional page number (default to 1 if not provided)

        $query = Gallery::select('*');

        // Apply lang_code filter if provided
        if ($lang_code) {
            $query->where('lang_code', $lang_code);
        }

        // Apply status filter and order
        $query->where('status', 3)
            ->orderBy('id', 'desc');

        // Apply pagination if 'limit' is provided
        if ($perPage) {
            $data = $query->paginate($perPage, ['*'], 'page', $page); // Paginate the results
        } else {
            // If no limit is provided, return the full set (not recommended for large data sets)
            $data = $query->get();
        }

        // Check if no data found
        if ($data->isEmpty()) {
            return response()->json(['message' => 'No data found'], 404);
        }

        // Transform data as needed (e.g., format created_at)
        $data->getCollection()->transform(function ($item) {
            $item->created_at = date('d-m-Y', strtotime($item->created_at));
            return $item; // Return the transformed item
        });

        return response()->json([
            'title' => 'List',
            'data' => $data->items(), 
            'total' => $data->total(), 
            'current_page' => $data->currentPage(), 
            'last_page' => $data->lastPage(), 
            'per_page' => $data->perPage(), 
        ]);
    }


    public function cms_data(Request $request)
    {
        $perPage = $request->input('limit');
        $lang_code = $request->input('lang_code');
        $page = $request->input('currentPage');

        $slide = Gallery::select('*') ->paginate($perPage, ['*'], 'page', $page);
        if ($slide->isNotEmpty()) {
            $slide->transform(function ($item) {
                $item->created_at = date('d-m-Y', strtotime($item->created_at));
                // if ($item->media) {
                //     $item->media = asset('public/'.$item->media);
                // }
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
    public function cms_data_by_id($id)
    {
        // Validate the ID
        $validatedId = filter_var($id, FILTER_VALIDATE_INT);
        if (!$validatedId) {
            return response()->json([
                'error' => 'Invalid ID format'
            ], 400);
        }

        // Retrieve the data by ID
        $data = Gallery::find($validatedId);

        // Return a 404 response if data is not found
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
            'title' => 'required|min:2|max:255',
            'description' => 'nullable|max:500',
            'image' => 'required|file|mimes:jpg,jpeg,png|max:20480',
            'position' => 'required',
            'status' => 'required',
            'lang_code' => 'required',
            
        ]);

        // Handle file upload
        if ($request->hasFile('image')) {
            $docUpload = $request->file('image');
            $docPath = time() . '_' . $docUpload->getClientOriginalName();
            $docUpload->move(public_path('gallery'), $docPath);
            $filePath = $docPath;
        }

        // Create a new form instance
        $data = new Gallery(); // Assuming you have a Form model
        $data->title = $validated['title'];
        $data->description = $validated['description'];
        $data->position = $validated['position'];
        $data->status = $validated['status'];
        $data->lang_code = $validated['lang_code'];
        // $data->created_by = Auth::user()->id;?
        $data->image = $filePath;
        
        $data->save();

        return response()->json(['data' => $data, 'message' => 'Created successfully.'], 200);
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|min:2|max:255',
            'description' => 'nullable|max:500',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:20480',
            'position' => 'required',
            'status' => 'required',
            'lang_code' => 'required',
            // 'created_by' => Auth::user()->id,
        ]);
        $data = Gallery::find($id);

        if (!$data) {
            return $this->sendError('No data found.', 404);
        }

        // Handle file upload
        if ($request->hasFile('image')) {
            $docUpload = $request->file('image');
            $docPath = time() . '_' . $docUpload->getClientOriginalName();
            $docUpload->move(public_path('gallery'), $docPath);
            $validated['image'] = $docPath;
        }

        $data->update($validated);

        return response()->json(['data' => $data, 'message' => 'Updated successfully.'], 200);
    }
    public function delete($id)
    {
        $data = Gallery::find($id);

        if (!$data) {
            return $this->sendError('No data found.', 404);
        }
        $data->delete();

        return response()->json(['data' => $data, 'message' => 'Deleted successfully.'], 200);
    }
}
