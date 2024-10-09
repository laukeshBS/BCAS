<?php

namespace App\Http\Controllers\Cms\Division;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cms\Division\Training\DivisionCourse;

class CourseController extends Controller
{
    public $user;

    // API
    public function data(Request $request)
    {
        $perPage = $request->input('limit');
        $page = $request->input('currentPage');

        $slide = DivisionCourse::select('*') ->paginate($perPage, ['*'], 'page', $page);
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
        // Validate the ID
        $validatedId = filter_var($id, FILTER_VALIDATE_INT);
        if (!$validatedId) {
            return response()->json([
                'error' => 'Invalid ID format'
            ], 400);
        }

        // Retrieve the data by ID
        $data = DivisionCourse::find($validatedId);

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
            'slug' => 'required|min:2|max:255',
            'description' => 'nullable|max:500',
            'division' => 'required',
            'position' => 'required',
            'status' => 'required',
            'lang_code' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'center_id' => 'required',
            'is_news' => 'required',
            'category_id' => 'required',
            'created_by' => 'required',
            
        ]);

        // Create a new form instance
        $data = new DivisionCourse(); // Assuming you have a Form model
        $data->title = $validated['title'];
        $data->slug = $validated['slug'];
        $data->description = $validated['description'];
        $data->division = $validated['division'];
        $data->position = $validated['position'];
        $data->status = $validated['status'];
        $data->lang_code = $validated['lang_code'];
        $data->start_date = $validated['start_date'];
        $data->end_date = $validated['end_date'];
        $data->center_id = $validated['center_id'];
        $data->is_news = $validated['is_news'];
        $data->category_id = $validated['category_id'];
        $data->created_by = $validated['created_by'];
        
        $data->save();

        return response()->json(['data' => $data, 'message' => 'Created successfully.'], 201);
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|min:2|max:255',
            'slug' => 'required|min:2|max:255',
            'description' => 'nullable|max:500',
            'division' => 'required',
            'position' => 'required',
            'status' => 'required',
            'lang_code' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'center_id' => 'required',
            'is_news' => 'required',
            'category_id' => 'required',
            'created_by' => 'required',
        ]);
        $data = DivisionCourse::find($id);

        if (!$data) {
            return $this->sendError('No data found.', 404);
        }
        $data->update($validated);

        return response()->json(['data' => $data, 'message' => 'Updated successfully.'], 201);
    }
    public function delete($id)
    {
        $data = DivisionCourse::find($id);

        if (!$data) {
            return $this->sendError('No data found.', 404);
        }
        $data->delete();

        return response()->json(['data' => $data, 'message' => 'Deleted successfully.'], 201);
    }
    
}
