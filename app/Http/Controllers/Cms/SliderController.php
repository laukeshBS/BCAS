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
use Spatie\Permission\Models\Permission;

class SliderController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    public function index()
    {
        // if (is_null($this->user) || !$this->user->can('slider-list.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized to view dashboard !');
        // }

        return view('backend.cms.slider.list');
    }
    public function data()
    {
        $slider = Slider::select('*')
        ->get()
            ->map(function ($item) {
                // Format the created_at date field
                $item->created_at = date('d-m-Y', strtotime($item->created_at));
                return $item;
            });

        return DataTables::of($slider)->make(true);
    }
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
        $baseUrl = url('storage');
        // Append base URL to media paths
        $slider->slides->each(function ($slide) use ($baseUrl) {
            if ($slide->media) {
                $slide->media = $baseUrl . '/app/public/' . $slide->media;
            }
        });
        return response()->json($slider);
    }
    public function add_slider()
    {
        // if (is_null($this->user) || !$this->user->can('slider-add.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized to view dashboard !');
        // }

        return view('backend.cms.slider..add');
    }
    public function store_slider(Request $request)
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

        $request->session()->flash('success', 'Slider added successfully!');

        return redirect()->route('cms.slider');
    }
    public function edit_slider($id)
    {
        // if (is_null($this->user) || !$this->user->can('slider-add.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized to view dashboard !');
        // }
        $slider = Slider::find($id);

        if (!$slider) {
            return redirect()->route('cms.slider')->with('error', 'Slider not found.');
        }

        return view('backend.cms.slider.edit', compact('slider'));
    }
    public function update_slider(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'max:500',
            'status' => 'required',
        ]);

        $slider = Slider::find($id);

        if (!$slider) {
            return redirect()->route('cms.slider.list')->with('error', 'Slider '.$id.' not found.');
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

    return redirect()->route('cms.slider')->with('success', 'Slider updated successfully.');
    }
    public function delete_slider($id)
    {
        // Find the slider by id
        $slider = Slider::find($id);

        if (!$slider) {
            return redirect()->route('cms.slider')->with('error', 'Slider '.$id.' not found.');
        }

        // Delete the slider
        $slider->delete();

        // Redirect back with success message
        return redirect()->route('cms.slider')->with('success', 'Slider deleted successfully.');
    }
}
