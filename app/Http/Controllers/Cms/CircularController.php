<?php

namespace App\Http\Controllers\Cms;

use ZipArchive;
use App\Models\Admin;
use App\Models\Cms\Slider;
use App\Models\Cms\Circular;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class CircularController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    // APIs
    public function circular_list_for_homepage(Request $request)
    {
        $limit = $request->input('limit', 5);

        $circulars = Circular::select('*')
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();

        $circulars->transform(function ($item) {
            $item->created_at = date('d-m-Y', strtotime($item->created_at));
            $item->document = url(Storage::url('app/public/' . $item->document)) ;
            return $item;
        });

        return response()->json($circulars);
    }
    public function circular_list(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $circulars = Circular::select('*')
            ->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $circulars->getCollection()->transform(function ($item) {
            $item->created_at = date('d-m-Y', strtotime($item->created_at));
            return $item;
        });

        return response()->json($circulars);
    }
    public function circular_store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'max:500',
            'status' => 'required',
            'document' => 'required|file|mimes:pdf',
        ]);

        $filePath = null;

        // Handle file upload
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('public/circulars', $fileName);
            $filePath = str_replace('public/', '', $filePath);
        }

        $circular = new Circular();
        $circular->title = $validated['title'];
        $circular->description = $validated['description'];
        $circular->status = $validated['status'];
        $circular->document = $filePath;
        $circular->save();

        return response()->json($circular);
    }

    // Web

    public function index()
    {
        // if (is_null($this->user) || !$this->user->can('slider-list.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized to view dashboard !');
        // }

        return view('backend.cms.circular.list');
    }
    public function data()
    {
        $circulars = Circular::select('*')
        ->get()
            ->map(function ($item) {
                // Format the created_at date field
                $item->created_at = date('d-m-Y', strtotime($item->created_at));
                return $item;
            });

        return DataTables::of($circulars)->make(true);
    }
    public function add_circular()
    {
        // if (is_null($this->user) || !$this->user->can('circular-add.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized to view dashboard !');
        // }

        return view('backend.cms.circular.add');
    }
    public function store_circular(Request $request)
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
            $zipFilePath = storage_path('app/public/circulars/') . $zipFileName;

            if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
                $zip->addFile($file->getPathname(), $file->getClientOriginalName());
                $zip->close();
            }

            $filePath = 'circulars/' . $zipFileName;
        }

        // Create a new circular instance
        $circular = new Circular();
        $circular->title = $validated['title'];
        $circular->description = $validated['description'];
        $circular->status = $validated['status'];
        $circular->document = $filePath; // Store file path in the database
        $circular->save();

        $request->session()->flash('success', 'Circular added successfully!');

        return redirect()->route('cms.circular');
    }
    public function edit_circular($id)
    {
        // Find the circular by id
        $circular = Circular::find($id);

        if (!$circular) {
            return redirect()->route('cms.circular')->with('error', 'circular not found.');
        }

        // Pass the circular data to the view
        return view('backend.cms.circular.edit', compact('circular'));
    }

    public function update_circular(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'max:500',
            'status' => 'required',
            'document' => 'file|mimes:pdf|max:2048',
        ]);

        $circular = Circular::find($id);

        if (!$circular) {
            return redirect()->route('cms.circular.list')->with('error', 'Circular not found.');
        }

        $circular->title = $request->input('title');
        $circular->description = $request->input('description');
        $circular->status = $request->input('status');

        if ($request->hasFile('document')) {
            // Handle file upload
            $file = $request->file('document');
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Create a ZIP file
            $zipFileName = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.zip';
            $zip = new ZipArchive;
            $zipFilePath = storage_path('app/public/circulars/') . $zipFileName;

            if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
                $zip->addFile($file->getPathname(), $file->getClientOriginalName());
                $zip->close();
            }

            $filePath = 'circulars/' . $zipFileName;

            // Update the document path in the database
            $circular->document = $filePath;
        }

        $circular->save();

        return redirect()->route('cms.circular')->with('success', 'Circular updated successfully.');
    }

    public function delete_circular($id)
    {
        // Find the circular by id
        $circular = Circular::find($id);

        if (!$circular) {
            return redirect()->route('cms.circular')->with('error', 'circular not found.');
        }

        // Delete the circular
        $circular->delete();

        // Redirect back with success message
        return redirect()->route('cms.circular')->with('success', 'circular deleted successfully.');
    }
}
