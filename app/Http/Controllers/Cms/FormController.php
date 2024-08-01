<?php

namespace App\Http\Controllers\Cms;

use ZipArchive;
use App\Models\Admin;
use App\Models\Cms\Form;
use App\Models\Cms\Slider;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class FormController extends Controller
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

        return view('backend.cms.form.list');
    }
    public function data()
    {
        $forms = Form::select('*')
        ->get()
            ->map(function ($item) {
                // Format the created_at date field
                $item->created_at = date('d-m-Y', strtotime($item->created_at));
                return $item;
            });

        return DataTables::of($forms)->make(true);
    }
    public function add_form()
    {
        // if (is_null($this->user) || !$this->user->can('form-add.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized to view dashboard !');
        // }

        return view('backend.cms.form.add');
    }
    public function store_form(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'max:500',
            'status' => 'required',
            'document' => 'required|file|mimes:pdf|max:2048',
        ]);

        $filePath = null;

        // Handle file upload
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Create a ZIP file
            $zipFileName = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.zip';
            $zip = new ZipArchive;
            $zipFilePath = storage_path('app/public/forms/') . $zipFileName;

            if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
                $zip->addFile($file->getPathname(), $file->getClientOriginalName());
                $zip->close();
            }

            $filePath = 'forms/' . $zipFileName;
        }

        // Create a new form instance
        $form = new Form(); // Assuming you have a Form model
        $form->title = $validated['title'];
        $form->description = $validated['description'];
        $form->status = $validated['status'];
        $form->document = $filePath; // Store file path in the database
        $form->save();

        $request->session()->flash('success', 'Form added successfully!');

        return redirect()->route('cms.form');
    }
    public function edit_form($id)
    {
        // Find the form by id
        $form = Form::find($id);

        if (!$form) {
            return redirect()->route('cms.form')->with('error', 'form not found.');
        }

        // Pass the form data to the view
        return view('backend.cms.form.edit', compact('form'));
    }

    public function update_form(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'max:500',
            'status' => 'required',
            'document' => 'file|mimes:pdf|max:2048',
        ]);

        $form = Form::find($id);

        if (!$form) {
            return redirect()->route('cms.form.list')->with('error', 'Form not found.');
        }

        $form->title = $request->input('title');
        $form->description = $request->input('description');
        $form->status = $request->input('status');

        if ($request->hasFile('document')) {
            // Handle file upload
            $file = $request->file('document');
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Create a ZIP file
            $zipFileName = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.zip';
            $zip = new ZipArchive;
            $zipFilePath = storage_path('app/public/forms/') . $zipFileName;

            if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
                $zip->addFile($file->getPathname(), $file->getClientOriginalName());
                $zip->close();
            }

            $filePath = 'forms/' . $zipFileName;

            // Update the document path in the database
            $form->document = $filePath;
        }

        $form->save();

        return redirect()->route('cms.form')->with('success', 'Form updated successfully.');
    }

    public function delete_form($id)
    {
        // Find the form by id
        $form = Form::find($id);

        if (!$form) {
            return redirect()->route('cms.form')->with('error', 'form not found.');
        }

        // Delete the form
        $form->delete();

        // Redirect back with success message
        return redirect()->route('cms.form')->with('success', 'form deleted successfully.');
    }
}
