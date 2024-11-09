<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\File;
use App\Models\Admin\Rank;
use Illuminate\Http\Request;
use GPBMetadata\Google\Api\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Crypt;
use App\Models\Admin\DocumentCategory;
use Illuminate\Support\Facades\Session;

class RankController extends Controller
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
        ]);

        $perPage = $request->input('limit');
        $page = $request->input('currentPage');

        $query = Rank::query();


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
        $data = Rank::find($validatedId);
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
            'name' => 'required|min:2|max:255',
            'type' => 'required|min:2|max:255',
        ]);

        $data = new Rank(); // Assuming you have a Document model
        $data->name = $validated['name'];
        $data->type = $validated['type'];

        $data->save();

        return response()->json(['data' => $data, 'message' => 'Created successfully.'], 201);
    }

    public function update(Request $request, $id)
    {
        // Validate incoming request
        $validated = $request->validate([
            'name' => 'required|min:2|max:255',
            'type' => 'required|min:2|max:255',
        ]);

        // Find the document
        $data = Rank::find($id);

        // Return error if not found
        if (!$data) {
            return $this->sendError('No data found.', 404);
        }

        // Update the document
        $data->update($validated);

        return response()->json(['data' => $data, 'message' => 'Updated successfully.'], 200); // Use 200 for successful update
    }

    public function delete($id)
    {
        $data = Rank::find($id);

        if (!$data) {
            return $this->sendError('No data found.', 404);
        }
        $data->delete();

        return response()->json(['data' => $data, 'message' => 'Deleted successfully.'], 201);
    }
    public function dropdown_list()
    {
        $data = Rank::orderBy('name','asc')->pluck('name','id');
        return response()->json(['data' => $data], 200);
    }
}
