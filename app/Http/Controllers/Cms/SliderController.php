<?php

namespace App\Http\Controllers\Cms;

use App\Models\Admin;
use App\Models\Cms\Slider;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class SliderController extends Controller
{
    public $user;

    public function __construct() {}

    public function slider_by_slug(Request $request){
        // Validate the request to ensure 'slug' is provided
        $request->validate([
            'slug' => 'required|string',
            'lang_code' => 'required|string'
        ]);
        $langCode = $request->input('lang_code', 'en');
        $slider = Slider::where('slug', $request->slug)
                    ->with(['slides' => function($query) use ($langCode) {
                        $query->where('lang_code', $langCode);
                    }])
                    ->first();
        // Base URL for the storage folder
        // $baseUrl = url('storage');
        // Append base URL to media paths
        // $slider->slides->each(function ($slide) use ($baseUrl) {
        //     if ($slide->media) {
        //         $slide->media = url(Storage::url('app/public/' . $slide->media)) ;
        //         // $slide->media = $baseUrl . '/app/public/' . $slide->media;
        //     }
        // });
        return response()->json($slider);
    }
    public function cms_data(Request $request)
    {
        $perPage = $request->input('limit');
        $page = $request->input('currentPage');

        $slider = Slider::select('*') ->paginate($perPage, ['*'], 'page', $page);
        if ($slider->isNotEmpty()) {
            $slider->transform(function ($item) {
                $item->created_at = date('d-m-Y', strtotime($item->created_at));
                return $item;
            });
        }

            return response()->json([
                'title' => 'Slider List',
                'data' => $slider->items(), 
                'total' => $slider->total(), 
                'current_page' => $slider->currentPage(), 
                'last_page' => $slider->lastPage(), 
                'per_page' => $slider->perPage(), 
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
        $data = Slider::find($validatedId);

        // Return a 404 response if data is not found
        if (!$data) {
            return response()->json([
                'error' => 'Data not found'
            ], 404);
        }
        
        $data->created_at = date('d-m-Y', strtotime($data->created_at));

    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'max:500',
            'status' => 'required',
        ]);

        // Generate a slug from the title
        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $count = 1;

        // Ensure the slug is unique
        while (Slider::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        // Create a new slider instance
        $slider = new Slider();
        $slider->title = $validated['title'];
        $slider->description = $validated['description'];
        $slider->status = $validated['status'];
        $slider->slug = $slug;
        $slider->save();

        return response()->json(['data' => $slider, 'message' => 'Slider Created successfully.'], 201);
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'max:500',
            'status' => 'required',
        ]);

        $slider = Slider::find($id);

        if (!$slider) {
            return $this->sendError('No Slider found.', 404);
        }
        if ($slider->title !== $validated['title']) {
            // Generate a unique slug from the new title
            $slug = Str::slug($validated['title']);
            $originalSlug = $slug;
            $count = 1;
    
            // Ensure the slug is unique
            while (Slider::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }
    
            $slider->slug = $slug;
        }

        $slider->title = $request->input('title');
        $slider->description = $request->input('description');
        $slider->status = $request->input('status');
        $slider->save();

        return response()->json(['data' => $slider, 'message' => 'Slider Updated successfully.'], 201);
    }
    public function delete($id)
    {
        $slider = Slider::find($id);

        if (!$slider) {
            return $this->sendError('No Slider found.', 404);
        }

        $slider->delete();

        return response()->json(['data' => $slider, 'message' => 'Slider Deleted successfully.'], 201);
    }
    // public function store_slider_api(Request $request)
    // {
    //     $validated = $request->validate([
    //         'title' => 'required|max:255',
    //         'description' => 'max:500',
    //         'status' => 'required',
    //     ]);

    //     // Generate a slug from the title
    //     $slug = Str::slug($validated['title']);
    //     $originalSlug = $slug;
    //     $count = 1;

    //     // Ensure the slug is unique
    //     while (Slider::where('slug', $slug)->exists()) {
    //         $slug = $originalSlug . '-' . $count;
    //         $count++;
    //     }

    //     // Create a new slider instance
    //     $slider = new Slider();
    //     $slider->title = $validated['title'];
    //     $slider->description = $validated['description'];
    //     $slider->status = $validated['status'];
    //     $slider->slug = $slug;
    //     $slider->save();

    //     return response()->json($slider);
    // }
    
}
