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
use Illuminate\Support\Facades\Storage;
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

    public function data(Request $request)
    {
        $limit = $request->input('limit', 5);
        $lang_code = $request->input('lang_code');

        $data = Form::select('*')
            ->where('lang_code',$lang_code)
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();

        $data->transform(function ($item) {
            // $item->start_date = date('d-m-Y', strtotime($item->start_date));
            // $item->end_date = date('d-m-Y', strtotime($item->end_date));
            $item->created_at = date('d-m-Y', strtotime($item->created_at));
            $item->document = asset('public/documents/' . $item->document) ;
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
