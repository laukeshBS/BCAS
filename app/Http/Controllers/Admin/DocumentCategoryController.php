<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use GPBMetadata\Google\Api\Auth;
use App\Http\Controllers\Controller;
use App\Models\Admin\DocumentCategory;
use Illuminate\Support\Facades\Session;

class DocumentCategoryController extends Controller
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

        $slide = DocumentCategory::select('*')->paginate($perPage, ['*'], 'page', $page);
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
        $data = DocumentCategory::find($validatedId);
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
            'name' => 'required|min:2|max:255',
            'status' => 'required',
        ]);

        $data = new DocumentCategory();
        $data->name = $validated['name'];
        $data->status = $validated['status'];
        $data->save();

        return response()->json(['data' => $data, 'message' => 'Created successfully.'], 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|min:2|max:255',
            'status' => 'required',
        ]);

        $data = DocumentCategory::find($id);

        if (!$data) {
            return $this->sendError('No data found.', 404);
        }

        $data->update($validated);

        return response()->json(['data' => $data, 'message' => 'Updated successfully.'], 201);
    }
    public function delete($id)
    {
        $data = DocumentCategory::find($id);

        if (!$data) {
            return $this->sendError('No data found.', 404);
        }
        $data->delete();

        return response()->json(['data' => $data, 'message' => 'Deleted successfully.'], 201);
    }
    
    public function document_categories()
    {
        $data = DocumentCategory::where('status',3)->orderBy('name','asc')->pluck('name','id');
        return response()->json(['data' => $data], 200);
    }

}
