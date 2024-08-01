<?php

namespace App\Http\Controllers\Cms;

use ZipArchive;
use App\Models\Admin;
use App\Models\Cms\Slider;
use App\Models\Cms\Tender;
use App\Models\Cms\Training;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class TrainingController extends Controller
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

        return view('backend.cms.training.list');
    }
    public function data()
    {
        $trainings = Training::select('*')
        ->get()
            ->map(function ($item) {
                // Format the created_at date field
                $item->created_at = date('d-m-Y', strtotime($item->created_at));
                return $item;
            });

        return DataTables::of($trainings)->make(true);
    }
    public function add_training()
    {
        // if (is_null($this->user) || !$this->user->can('training-add.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized to view dashboard !');
        // }

        return view('backend.cms.training.add');
    }
    public function store_training(Request $request)
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
            $zipFilePath = storage_path('app/public/training/') . $zipFileName;

            if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
                $zip->addFile($file->getPathname(), $file->getClientOriginalName());
                $zip->close();
            }

            $filePath = 'training/' . $zipFileName;
        }

        // Create a new training instance
        $training = new Training(); // Assuming you have a Training model
        $training->title = $validated['title'];
        $training->description = $validated['description'];
        $training->status = $validated['status'];
        $training->document = $filePath; // Store file path in the database
        $training->save();

        $request->session()->flash('success', 'Training added successfully!');

        return redirect()->route('cms.training');
    }
    public function edit_training($id)
    {
        // Find the training by id
        $training = Tender::find($id);

        if (!$training) {
            return redirect()->route('cms.training')->with('error', 'training not found.');
        }

        // Pass the training data to the view
        return view('backend.cms.training.edit', compact('training'));
    }

    public function update_training(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'max:500',
            'status' => 'required',
            'document' => 'file|mimes:pdf|max:2048',
        ]);

        $training = Training::find($id); // Assuming you have a Training model

        if (!$training) {
            return redirect()->route('cms.training.list')->with('error', 'Training not found.');
        }

        $training->title = $request->input('title');
        $training->description = $request->input('description');
        $training->status = $request->input('status');

        if ($request->hasFile('document')) {
            // Handle file upload
            $file = $request->file('document');
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Create a ZIP file
            $zipFileName = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.zip';
            $zip = new ZipArchive;
            $zipFilePath = storage_path('app/public/training/') . $zipFileName;

            if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
                $zip->addFile($file->getPathname(), $file->getClientOriginalName());
                $zip->close();
            }

            $filePath = 'training/' . $zipFileName;

            // Update the document path in the database
            $training->document = $filePath;
        }

        $training->save();

        return redirect()->route('cms.training')->with('success', 'Training updated successfully.');
    }

    public function delete_training($id)
    {
        // Find the training by id
        $training = Training::find($id);

        if (!$training) {
            return redirect()->route('cms.training')->with('error', 'training not found.');
        }

        // Delete the training
        $training->delete();

        // Redirect back with success message
        return redirect()->route('cms.training')->with('success', 'training deleted successfully.');
    }
}
