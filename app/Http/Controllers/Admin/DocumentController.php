<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\File;
use Illuminate\Http\Request;
use App\Models\Admin\Document;
use GPBMetadata\Google\Api\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Crypt;
use App\Models\Admin\DocumentCategory;
use Illuminate\Support\Facades\Session;

class DocumentController extends Controller
{
    public $user;

    public function index(Request $request)
    {
    }

    // API
    public function data(Request $request)
    {
        $request->validate([
            'limit' => 'required|integer',
            'currentPage' => 'required|integer',
            'roleIds' => 'array', // Accept an array of role IDs
        ]);

        $perPage = $request->input('limit');
        $page = $request->input('currentPage');
        $roleIds = $request->input('roleIds'); // Get the array of role IDs

        $query = Document::query();

        // If role IDs are provided, filter the documents
        if ($roleIds && count($roleIds) > 0) {
            $query->whereHas('roles', function ($q) use ($roleIds) {
                $q->whereIn('roles.id', $roleIds);
            });
        }

        $data = $query->select('*')->paginate($perPage, ['*'], 'page', $page);

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
    
    public function data_by_id($id)
    {
        $validatedId = filter_var($id, FILTER_VALIDATE_INT);
        if (!$validatedId) {
            return response()->json([
                'error' => 'Invalid ID format'
            ], 400);
        }
        $data = Document::find($validatedId);
        if (!$data) {
            return response()->json([
                'error' => 'Data not found'
            ], 404);
        }
        $data->created_at = date('d-m-Y', strtotime($data->created_at));

        $roleIds = $data->roles->pluck('id');
        $data->roleIds = $roleIds;

        return response()->json($data);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_category_id' => 'required',
            'doc_name' => 'required|min:2|max:255',
            'description' => 'nullable|max:500',
            'doc_type' => 'required',
            'doc' => 'required|file|mimes:pdf|max:20480',
            'status' => 'required',
            'position' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'roles' => 'required|array',
        ]);

        $filePath = null;
        if ($request->hasFile('doc')) {
            $docUpload = $request->file('doc');
            $docPath = time() . '_' . $docUpload->getClientOriginalName();
            $docUpload->move(public_path('admin-doc'), $docPath);
            $filePath = $docPath;
        }

        $data = new Document(); // Assuming you have a Document model
        $data->document_category_id = $validated['document_category_id'];
        $data->doc_name = $validated['doc_name'];
        $data->description = $validated['description'];
        $data->doc_type = $validated['doc_type'];
        $data->doc = $filePath; // Store the file path in the database
        $data->status = $validated['status'];
        $data->start_date = $validated['start_date'];
        $data->end_date = $validated['end_date'];
        $data->position = $validated['position'];
        $data->submitted_by = auth()->id();

        $data->save();

        $data->roles()->attach($request->input('roles'));

        return response()->json(['data' => $data, 'message' => 'Created successfully.'], 201);
    }

    public function update(Request $request, $id)
    {
        // Validate incoming request
        $validated = $request->validate([
            'document_category_id' => 'required',
            'doc_name' => 'required|min:2|max:255',
            'description' => 'nullable|max:500',
            'doc_type' => 'required',
            'doc' => 'nullable|file|mimes:pdf|max:20480',
            'status' => 'required',
            'position' => 'required',
            'start_date' => 'required|date', // Ensure date validation
            'end_date' => 'required|date|after:start_date', // Ensure end date is after start date
            'roles' => 'required|array',
            'submitted_by' => 'nullable|integer', // Make submitted_by optional since it can be set in the controller
        ]);

        // Find the document
        $data = Document::find($id);

        // Return error if not found
        if (!$data) {
            return $this->sendError('No data found.', 404);
        }

        // Handle file upload if a new file is provided
        if ($request->hasFile('doc')) {
            // Delete old file if necessary
            // if ($data->doc) {
            //     File::delete(public_path('admin-doc/' . $data->doc));
            // }

            $docUpload = $request->file('doc');
            $docPath = time() . '_' . $docUpload->getClientOriginalName();
            $docUpload->move(public_path('admin-doc'), $docPath);
            $validated['doc'] = $docPath; // Use new file path in validated data
        } else {
            // If no new file, keep the old file path
            unset($validated['doc']);
        }

        // Update the document
        $data->update($validated);

        // Sync roles instead of attach to avoid duplicates
        if ($request->has('roles')) {
            $data->roles()->sync($request->input('roles'));
        }

        return response()->json(['data' => $data, 'message' => 'Updated successfully.'], 200); // Use 200 for successful update
    }

    public function delete($id)
    {
        $data = Document::find($id);

        if (!$data) {
            return $this->sendError('No data found.', 404);
        }
        $data->delete();

        return response()->json(['data' => $data, 'message' => 'Deleted successfully.'], 201);
    }
    public function show_Document(Request $request,)
    {
        $validated = $request->validate([
            'doc_id' => 'required',
            'role_id' =>  'required',
        ]);
        $document = Document::whereHas('roles', function ($query) use ($validated) {
            $query->where('roles.id', $validated['role_id']);
        })->where('documents.id', $validated['doc_id'])->first();

        if (!$document) {
            return response()->json(['message' => 'Document not found or access denied.'], 403);
        }

        $fullPath = public_path('admin-doc/' . $document->doc);

        if (!file_exists($fullPath)) {
            return response()->json(['message' => 'Document file does not exist.'], 404);
        }

        return response()->file($fullPath);
    }
    

}
