<?php

namespace App\Http\Controllers\Cms\Division;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\Document;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use App\Models\Cms\Division\DivisionGallery;
use GPBMetadata\Google\Api\Auth;

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
                if ($item->media) {
                    $item->media = asset('public/'.$item->media);
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
        ]);
        if ($request->hasFile('doc')) {
            $docUpload = $request->file('doc');
            $docPath = time() . '_' . $docUpload->getClientOriginalName();
            $docUpload->move(public_path('admin-doc'), $docPath);
            $filePath = $docPath;
        }
        $data = new Document(); // Assuming you have a Form model
        $data->document_category_id = $validated['document_category_id'];
        $data->doc_name = $validated['doc_name'];
        $data->description = $validated['description'];
        $data->doc_type = $validated['doc_type'];
        $data->doc = $filePath;
        $data->status = $validated['status'];
        $data->position = $validated['position'];
        $data->submitted_by = Auth::user()->id;
        
        $data->save();

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
            'submitted_by' => Auth::user()->id,
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
}
