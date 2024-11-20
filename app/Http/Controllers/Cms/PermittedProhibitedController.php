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
use App\Models\Cms\PermittedProhibited;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class PermittedProhibitedController extends Controller
{
    public $user;

    public function __construct() {}

    public function index(Request $request)
    {
        $lang_code = $request->input('lang_code');

        $data = PermittedProhibited::where('lang_code', $lang_code)->where('status', 3)->get();
        if ($data->isNotEmpty()) {
            $data->transform(function ($item) {
                try {
                    $item->created_at = \Carbon\Carbon::parse($item->created_at)->format('d-m-Y');
                } catch (\Carbon\Exceptions\InvalidFormatException $e) {
                    // Handle invalid date format (set to null or a default value)
                    $item->created_at = null; // or set a default date
                }
                // if ($item->images) {
                //     $item->images = asset('public/' . $item->images);
                // }
                return $item;
            });
        }
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
        $data = Form::find($validatedId);

        // Return a 404 response if data is not found
        if (!$data) {
            return response()->json([
                'error' => 'Data not found'
            ], 404);
        }
        // $data->start_date = date('d-m-Y', strtotime($data->start_date));
        // $data->end_date = date('d-m-Y', strtotime($data->end_date));
        $data->created_at = date('d-m-Y', strtotime($data->created_at));
        $data->document = asset('public/documents/' . $data->document) ;

        // Return the data as JSON
        return response()->json($data);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'max:500',
            'status' => 'required',
            'lang_code' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'document' => 'required|file|mimes:pdf|max:2048',
        ]);

        // Handle file upload
        if ($request->hasFile('document')) {
            $docUpload = $request->file('document');
            $docPath = time() . '_' . $docUpload->getClientOriginalName();
            $docUpload->move(public_path('documents/forms/'), $docPath);
            $filePath = 'forms/'.$docPath;
        }

        // Create a new Act and Policy instance
        $actandpolicy = new Form();
        $actandpolicy->title = $validated['title'];
        $actandpolicy->description = $validated['description'];
        $actandpolicy->status = $validated['status'];
        $actandpolicy->lang_code = $validated['lang_code'];
        $actandpolicy->start_date = $validated['start_date'];
        $actandpolicy->end_date = $validated['end_date'];
        $actandpolicy->document = $filePath; // Store file path in the database
        $actandpolicy->save();
        
        // Return the data as JSON
        return response()->json($actandpolicy);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'max:500',
            'status' => 'required',
            'document' => 'file|mimes:pdf|max:2048',
        ]);

        $actandpolicy = Form::find($id);

        if (!$actandpolicy) {
            return response()->json([
                'error' => 'Not Found.'
            ], 400);
        }

        $actandpolicy->title = $request->input('title');
        $actandpolicy->description = $request->input('description');
        $actandpolicy->status = $request->input('status');

        if ($request->hasFile('document')) {
            $docUpload = $request->file('document');
            $docPath = time() . '_' . $docUpload->getClientOriginalName();
            $docUpload->move(public_path('documents/forms/'), $docPath);
            $actandpolicy->document = 'forms/'.$docPath;
        }

        $actandpolicy->save();

        // Return the data as JSON
        return response()->json($actandpolicy);
    }

    public function delete($id)
    {
        // Find the actandpolicy by id
        $actandpolicy = Form::find($id);

        if (!$actandpolicy) {
            return response()->json([
                'error' => 'Not Found.'
            ], 400);
        }

        // Delete the actandpolicy
        $actandpolicy->delete();

        // Return the data as JSON
        return response()->json($actandpolicy);
    }
    
}
