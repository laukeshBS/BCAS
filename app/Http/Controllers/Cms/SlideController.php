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

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    // API

    public function store_slide_api(Request $request)
    {
        $validated = $request->validate([
            'slider_id' => 'required|exists:sliders,id',
            'lang_code' => 'required|exists:languages,lang_code',
            'title' => 'required|min:2|max:255',
            'description' => 'nullable|max:500',
            'url' => 'nullable|url',
            'media_type' => 'required|in:image,video',
            'media' => 'required|file|mimes:jpg,jpeg,png,mp4,mpeg,ogg|max:20480',
            'order_index' => 'required|integer',
            'status' => 'required|in:1,2',
        ]);

        $filePath = null;

        // Handle file upload
        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('public/slides', $fileName); // Store the file in the 'public/forms' directory
            $filePath = 'slides/' . $fileName; // Adjust the path if you need to store it in the database
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

        return response()->json($slide);
    }

    // Web

    public function index()
    {
        // if (is_null($this->user) || !$this->user->can('slide-list.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized to view dashboard !');
        // }

        return view('backend.cms.slide.list');
    }
    public function data()
    {
        $slide = Slide::select('*')
        ->get()
            ->map(function ($item) {
                // Format the created_at date field
                $item->created_at = date('d-m-Y', strtotime($item->created_at));
                return $item;
            });

        return DataTables::of($slide)->make(true);
    }
    public function add_slide()
    {
        // if (is_null($this->user) || !$this->user->can('slide-add.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized to view dashboard !');
        // }
        $sliders = Slider::pluck('title','id');
        $language = Language::pluck('name','lang_code');
        return view('backend.cms.slide.add',compact('sliders','language'));
    }
    public function store_slide(Request $request)
    {
        $validated = $request->validate([
            'slider_id' => 'required|exists:sliders,id',
            'lang_code' => 'required|exists:languages,lang_code',
            'title' => 'required|min:2|max:255',
            'description' => 'nullable|max:500',
            'url' => 'nullable|url',
            'media_type' => 'required|in:image,video',
            'media' => 'required|file|mimes:jpg,jpeg,png,mp4,mpeg,ogg|max:20480',
            'order_index' => 'required|integer',
            'status' => 'required|in:1,2',
        ]);

        $filePath = null;

        // Handle file upload
        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('public/slides', $fileName); // Store the file in the 'public/forms' directory
            $filePath = 'slides/' . $fileName; // Adjust the path if you need to store it in the database
        }
        // Create a new form instance
        $form = new Slide(); // Assuming you have a Form model
        $form->slider_id = $validated['slider_id'];
        $form->title = $validated['title'];
        $form->description = $validated['description'];
        $form->media_type = $validated['media_type'];
        $form->media = $filePath; // Store file path in the database
        $form->order_index = $validated['order_index'];
        $form->lang_code = $validated['lang_code'];
        $form->status = $validated['status'];
        $form->save();

        $request->session()->flash('success', 'Form added successfully!');

        return redirect()->route('cms.slide');
    }
    public function edit_slide($id)
    {
        // if (is_null($this->user) || !$this->user->can('slide-add.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized to view dashboard !');
        // }
        $slide = Slide::find($id);

        if (!$slide) {
            return redirect()->route('cms.slide')->with('error', 'Slide not found.');
        }

        $sliders = Slider::pluck('title','id');
        $language = Language::pluck('name','lang_code');
        return view('backend.cms.slide.edit',compact('sliders','language','slide'));
    }
    public function update_slide(Request $request, $id)
    {
        $validated = $request->validate([
            'slider_id' => 'required|exists:sliders,id',
            'lang_code' => 'required|exists:languages,lang_code',
            'title' => 'required|min:2|max:255',
            'description' => 'nullable|max:500',
            'url' => 'nullable|url',
            'media_type' => 'required|in:image,video',
            'media' => 'nullable|file|mimes:jpg,jpeg,png,mp4,mpeg,ogg|max:20480',
            'order_index' => 'required|integer',
            'status' => 'required|in:1,2',
        ]);
        $slide = Slide::find($id);

        if (!$slide) {
            return redirect()->route('cms.slide.list')->with('error', 'Slide not found.');
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
            $file = $request->file('media');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('public/slides', $fileName); // Store the file in the 'public/slides' directory
            $filePath = 'slides/' . $fileName; // Adjust the path if you need to store it in the database
            $inputs['media'] = $filePath; // Set the media path in the inputs array
        }

        $slide->update($inputs);

        return redirect()->route('cms.slide')->with('success', 'Slide updated successfully.');
    }
    public function delete_slide($id)
    {
        // Find the slider by id
        $slide = Slide::find($id);

        if (!$slide) {
            return redirect()->route('cms.slide')->with('error', 'Slide '.$id.' not found.');
        }

        // Delete the slider
        $slide->delete();

        // Redirect back with success message
        return redirect()->route('cms.slide')->with('success', 'Slide deleted successfully.');
    }
}
