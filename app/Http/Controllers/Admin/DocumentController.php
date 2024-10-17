<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Admin\Document;
use GPBMetadata\Google\Api\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Crypt;
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
        $perPage = $request->input('limit');
        $page = $request->input('currentPage');

        $slide = Document::select('*') ->paginate($perPage, ['*'], 'page', $page);
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
        $data = Document::find($validatedId);
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
            'submitted_by' => auth()->id(),
        ]);
        $data = Document::find($id);

        if (!$data) {
            return $this->sendError('No data found.', 404);
        }

        // Handle file upload
        if ($request->hasFile('doc')) {
            $docUpload = $request->file('doc');
            $docPath = time() . '_' . $docUpload->getClientOriginalName();
            $docUpload->move(public_path('admin-doc'), $docPath);
            $validated['doc'] = $docPath;
        }

        $data->update($validated);

        return response()->json(['data' => $data, 'message' => 'Updated successfully.'], 201);
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
