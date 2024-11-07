<?php

namespace App\Http\Controllers\Cms;

use App\Models\Admin;
use App\Models\Cms\Slide;
use App\Models\Cms\Slider;
use Illuminate\Http\Request;
use App\Models\Admin\Language;
use Yajra\DataTables\DataTables;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class SlideController extends Controller
{
    public $user;

    // API
    public function data()
    {
        $slide = Slide::select('*')
        ->get()
            ->map(function ($item) {
                $item->created_at = date('d-m-Y', strtotime($item->created_at));
                return $item;
            });

        return response()->json($slide);
    }
    public function cms_data(Request $request)
    {
        $perPage = $request->input('limit');
        $page = $request->input('currentPage');

        $slide = Slide::select('*') ->paginate($perPage, ['*'], 'page', $page);
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
        $data = Slide::find($validatedId);

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
            'slider_id' => 'required|exists:sliders,id',
            'lang_code' => 'required|exists:languages,lang_code',
            'title' => 'required|min:2|max:255',
            'description' => 'nullable|max:500',
            'url' => 'nullable|url',
            'media_type' => 'required',
            'media' => 'required|file|mimes:jpg,jpeg,png,mp4,mpeg,ogg|max:20480',
            'order_index' => 'required|integer',
            'status' => 'required',
        ]);

        $filePath = null;

        // Handle file upload
        if ($request->hasFile('media')) {
            $docUpload = $request->file('doc_upload');
            $docPath = 'slides/' . time() . '_' . $docUpload->getClientOriginalName();
            $docUpload->move(public_path('slides'), $docPath);
            $filePath = $docPath;
        }
        // Create a new form instance
        $slide = new Slide(); // Assuming you have a Form model
        $slide->slider_id = $validated['slider_id'];
        $slide->title = $validated['title'];
        $slide->description = $validated['description'];
        $slide->media_type = $validated['media_type'];
        $slide->media = $filePath; // Store file path in the database
        $slide->order_index = $validated['order_index'];
        $slide->lang_code = $validated['lang_code'];
        $slide->status = $validated['status'];
        $slide->save();

        return response()->json(['data' => $slide, 'message' => 'Slide created successfully.'], 201);
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'slider_id' => 'required|exists:sliders,id',
            'lang_code' => 'required|exists:languages,lang_code',
            'title' => 'required|min:2|max:255',
            'description' => 'nullable|max:500',
            'url' => 'nullable|url',
            'media_type' => 'required',
            'media' => 'nullable|file|mimes:jpg,jpeg,png,mp4,mpeg,ogg|max:20480',
            'order_index' => 'required|integer',
            'status' => 'required',
        ]);
        $slide = Slide::find($id);

        if (!$slide) {
            return $this->sendError('No Slide found.', 404);
        }
        $inputs=[
            'slider_id' => $request->input('slider_id'),
            'lang_code' => $request->input('lang_code'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'url' => $request->input('url'),
            'media_type' => $request->input('media_type'),
            'order_index' => $request->input('order_index'),
            'status' => $request->input('status'),
        ];
        if ($request->hasFile('media')) {
            $docUpload = $request->file('doc_upload');
            $docPath = 'slides/' . time() . '_' . $docUpload->getClientOriginalName();
            $docUpload->move(public_path('slides'), $docPath);
            $inputs['media'] = $docPath;
        }

        $slide->update($inputs);

        return response()->json(['data' => $slide, 'message' => 'Slide Updated successfully.'], 201);
    }
    public function delete($id)
    {
        $slide = Slide::find($id);

        if (!$slide) {
            return $this->sendError('No Slide found.', 404);
        }
        $slide->delete();

        return response()->json(['data' => $slide, 'message' => 'Slide Deleted successfully.'], 201);
    }
}
