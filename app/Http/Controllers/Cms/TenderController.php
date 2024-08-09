<?php

namespace App\Http\Controllers\Cms;

use ZipArchive;
use App\Models\Admin;
use App\Models\Cms\Slider;
use App\Models\Cms\Tender;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class TenderController extends Controller
{
    public $user;

    // API

    public function data(Request $request)
    {
        $limit = $request->input('limit', 5);
        $lang_code = $request->input('lang_code');

        $data = Tender::select('*')
            ->where('lang_code',$lang_code)
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();

        $data->transform(function ($item) {
            $item->start_date = date('d-m-Y', strtotime($item->start_date));
            $item->end_date = date('d-m-Y', strtotime($item->end_date));
            $item->created_at = date('d-m-Y', strtotime($item->created_at));
            $item->document = url(Storage::url('app/public/' . $item->document)) ;
            return $item;
        });

        return response()->json($data);
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
        $data = Tender::find($validatedId);

        // Return a 404 response if data is not found
        if (!$data) {
            return response()->json([
                'error' => 'Data not found '
            ], 404);
        }
        $data->start_date = date('d-m-Y', strtotime($data->start_date));
        $data->end_date = date('d-m-Y', strtotime($data->end_date));
        $data->created_at = date('d-m-Y', strtotime($data->created_at));
        $data->document = url(Storage::url('app/public/' . $data->document)) ;

        // Return the data as JSON
        return response()->json($data);
    }

    // Web
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

        return view('backend.cms.tender.list');
    }
    
    public function add_tender()
    {
        // if (is_null($this->user) || !$this->user->can('tender-add.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized to view dashboard !');
        // }

        return view('backend.cms.tender.add');
    }
    public function store_tender(Request $request)
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
            $zipFilePath = storage_path('app/public/tenders/') . $zipFileName;

            if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
                $zip->addFile($file->getPathname(), $file->getClientOriginalName());
                $zip->close();
            }

            $filePath = 'tenders/' . $zipFileName;
        }

        // Create a new Tender instance
        $tender = new Tender();
        $tender->title = $validated['title'];
        $tender->description = $validated['description'];
        $tender->status = $validated['status'];
        $tender->document = $filePath; // Store file path in the database
        $tender->save();

        $request->session()->flash('success', 'Tender added successfully!');

        return redirect()->route('cms.tender');
    }
    public function edit_tender($id)
    {
        // Find the tender by id
        $tender = Tender::find($id);

        if (!$tender) {
            return redirect()->route('cms.tender')->with('error', 'Tender not found.');
        }

        // Pass the tender data to the view
        return view('backend.cms.tender.edit', compact('tender'));
    }

    public function update_tender(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'max:500',
            'status' => 'required',
            'document' => 'file|mimes:pdf|max:2048',
        ]);

        $tender = Tender::find($id);

        if (!$tender) {
            return redirect()->route('cms.tender.list')->with('error', 'Tender not found.');
        }

        $tender->title = $request->input('title');
        $tender->description = $request->input('description');
        $tender->status = $request->input('status');

        if ($request->hasFile('document')) {
            // Handle file upload
            $file = $request->file('document');
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Create a ZIP file
            $zipFileName = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.zip';
            $zip = new ZipArchive;
            $zipFilePath = storage_path('app/public/tenders/') . $zipFileName;

            if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
                $zip->addFile($file->getPathname(), $file->getClientOriginalName());
                $zip->close();
            }

            $filePath = 'tenders/' . $zipFileName;

            // Update the document path in the database
            $tender->document = $filePath;
        }

        $tender->save();

        return redirect()->route('cms.tender')->with('success', 'Tender updated successfully.');
    }

    public function delete_tender($id)
    {
        // Find the tender by id
        $tender = Tender::find($id);

        if (!$tender) {
            return redirect()->route('cms.tender')->with('error', 'Tender not found.');
        }

        // Delete the tender
        $tender->delete();

        // Redirect back with success message
        return redirect()->route('cms.tender')->with('success', 'Tender deleted successfully.');
    }
}
