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

    public function circular_list_for_homepage(Request $request)
    {
        $limit = $request->input('limit', 5);
        $lang_code = $request->input('lang_code');

        $circulars = Circular::select('*')
            ->where('lang_code',$lang_code)
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();

        $circulars->transform(function ($item) {
            $item->created_at = date('d-m-Y', strtotime($item->created_at));
            // $item->document = asset('public/documents/' . $item->document) ;
            return $item;
        });

        return response()->json($circulars);
    }
    public function circular_list(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $lang_code = $request->input('lang_code','en');

        $circulars = Circular::select('*')
            ->orderBy('id', 'desc')
            ->where('lang_code', $lang_code)
            ->paginate($perPage, ['*'], 'page', $page);

        $circulars->getCollection()->transform(function ($item) {
            $item->created_at = date('d-m-Y', strtotime($item->created_at));
            return $item;
        });

        return response()->json([
            'title' => 'List',
            'data' => $circulars->items(),
            'total' => $circulars->total(),
            'current_page' => $circulars->currentPage(),
            'last_page' => $circulars->lastPage(),
            'per_page' => $circulars->perPage(),
        ]);
    }
    
    public function data(Request $request)
    {
        $limit = $request->input('limit', 5);
        $lang_code = $request->input('lang_code');

        $data = Circular::select('*')
            ->where('lang_code',$lang_code)
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();

        $data->transform(function ($item) {
            // $item->start_date = date('d-m-Y', strtotime($item->start_date));
            // $item->end_date = date('d-m-Y', strtotime($item->end_date));
            $item->created_at = date('d-m-Y', strtotime($item->created_at));
            // $item->document = asset('public/documents/' . $item->document) ;
            return $item;
        });

        return response()->json([
            'title' => 'List',
            'data' => $data->items(), 
            'total' => $data->total(), 
            'current_page' => $data->currentPage(), 
            'last_page' => $data->lastPage(), 
            'per_page' => $data->perPage(), 
        ]);
    }
    public function cms_data(Request $request)
    {
        $perPage = $request->input('limit');
        $page = $request->input('currentPage');

        $slider = Circular::select('*')->orderBy('id', 'desc')->paginate($perPage, ['*'], 'page', $page);
        if ($slider->isNotEmpty()) {
            $slider->transform(function ($item) {
                $item->created_at = date('d-m-Y', strtotime($item->created_at));
                // if ($item->document) {
                //     $item->document = asset('public/documents/'.$item->document);
                // }
                return $item;
            });
        }

            return response()->json([
                'title' => 'Tender List',
                'data' => $slider->items(),
                'total' => $slider->total(),
                'current_page' => $slider->currentPage(),
                'last_page' => $slider->lastPage(),
                'per_page' => $slider->perPage(),
            ]);
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
        $data = Circular::find($validatedId);

        // Return a 404 response if data is not found
        if (!$data) {
            return response()->json([
                'error' => 'Data not found'
            ], 404);
        }
        $data->created_at = date('d-m-Y', strtotime($data->created_at));
        // $data->document = asset('public/documents/' . $data->document) ;

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
            $docUpload->move(public_path('documents/circulars/'), $docPath);
            $filePath = 'circulars/'.$docPath;
        }

        // Create a new Act and Policy instance
        $circulars = new Circular();
        $circulars->title = $validated['title'];
        $circulars->description = $validated['description'];
        $circulars->status = $validated['status'];
        $circulars->lang_code = $validated['lang_code'];
        $circulars->start_date = $validated['start_date'];
        $circulars->end_date = $validated['end_date'];
        $circulars->document = $filePath; // Store file path in the database
        $circulars->save();
        
        // Return the data as JSON
        return response()->json($circulars);
    }

    public function update(Request $request, $id)
{
    $validated = $request->validate([
        'title' => 'required|max:255',
        'description' => 'max:500',
        'status' => 'required',
        'lang_code' => 'required',
        'start_date' => 'required|date_format:Y-m-d',
        'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
        'document' => 'nullable|file|mimes:pdf|max:2048',
    ]);

    $circulars = Circular::find($id);

    if (!$circulars) {
        return response()->json([
            'error' => 'Not Found.'
        ], 404);
    }

    $circulars->title = $request->input('title');
    $circulars->description = $request->input('description');
    $circulars->status = $request->input('status');
    $circulars->lang_code = $request->input('lang_code');
    $circulars->start_date = $request->input('start_date');
    $circulars->end_date = $request->input('end_date');

    if ($request->hasFile('document')) {
        $docUpload = $request->file('document');
        $docPath = time() . '_' . $docUpload->getClientOriginalName();
        $docUpload->move(public_path('documents/circulars/'), $docPath);
        $filePath = 'circulars/'.$docPath;
        $circulars->document = $filePath;
    }

    $circulars->save();

    // Return the updated Circular as JSON
    return response()->json($circulars);
}


    public function delete($id)
    {
        // Find the circulars by id
        $circulars = Circular::find($id);

        if (!$circulars) {
            return response()->json([
                'error' => 'Not Found.'
            ], 400);
        }

        // Delete the circulars
        $circulars->delete();

        // Return the data as JSON
        return response()->json($circulars);
    }

}
