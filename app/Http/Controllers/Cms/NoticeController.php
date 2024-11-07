<?php

namespace App\Http\Controllers\Cms;

use ZipArchive;
use App\Models\Admin;
use App\Models\Cms\Slider;
use App\Models\Cms\Notice;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class NoticeController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    public function notice_list_for_homepage(Request $request)
    {
        $limit = $request->input('limit', 5);
        $lang_code = $request->input('lang_code');
        $important = $request->input('important');

        if (!empty($important)) {
            $notices = Notice::select('*')
            ->where('important',$important)
            ->where('lang_code',$lang_code)
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();
        }else{
            $notices = Notice::select('*')
            ->where('lang_code',$lang_code)
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();
        }

        

        $notices->transform(function ($item) {
            $item->created_at = date('d-m-Y', strtotime($item->created_at));
            $item->document = asset('public/documents/' . $item->document) ;
            return $item;
        });

        return response()->json($notices);
    }
    public function notice_list(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page');

        $notices = Notice::select('*')
            ->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $notices->getCollection()->transform(function ($item) {
            $item->created_at = date('d-m-Y', strtotime($item->created_at));
            return $item;
        });

        return response()->json($notices);
    }
    public function data(Request $request)
    {
        $limit = $request->input('limit');
        $lang_code = $request->input('lang_code');

        $data = Notice::select('*')
            ->where('lang_code',$lang_code)
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();

        $data->transform(function ($item) {
            $item->start_date = date('d-m-Y', strtotime($item->start_date));
            $item->end_date = date('d-m-Y', strtotime($item->end_date));
            $item->created_at = date('d-m-Y', strtotime($item->created_at));
            $item->document = asset('public/documents/' . $item->document) ;
            return $item;
        });

        return response()->json($data);
    }
    public function cms_data(Request $request)
    {
        $perPage = $request->input('limit');
        $page = $request->input('currentPage');

        $slider = Notice::select('*')->orderBy('id', 'desc')->paginate($perPage, ['*'], 'page', $page);
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
        $data = Notice::find($validatedId);

        // Return a 404 response if data is not found
        if (!$data) {
            return response()->json([
                'error' => 'Data not found'
            ], 404);
        }
        // $data->start_date = date('d-m-Y', strtotime($data->start_date));
        // $data->end_date = date('d-m-Y', strtotime($data->end_date));
        $data->created_at = date('d-m-Y', strtotime($data->created_at));
        // $data->document = asset('public/documents/' . $data->document) ;

        // Return the data as JSON
        return response()->json($data);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|max:500',
            'status' => 'required',
            'lang_code' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'important' => 'required',
            'document' => 'required|file|mimes:pdf|max:2048',
        ]);

        // Handle file upload
        if ($request->hasFile('document')) {
            $docUpload = $request->file('document');
            $docPath = time() . '_' . $docUpload->getClientOriginalName();
            $docUpload->move(public_path('documents/notices/'), $docPath);
            $filePath = 'notices/'.$docPath;
        }

        // Create a new Act and Policy instance
        $notice = new Notice();
        $notice->title = $validated['title'];
        $notice->description = $validated['description'];
        $notice->status = $validated['status'];
        $notice->lang_code = $validated['lang_code'];
        $notice->start_date = $validated['start_date'];
        $notice->end_date = $validated['end_date'];
        $notice->important = $validated['important'];
        $notice->document = $filePath; // Store file path in the database
        $notice->save();
        
        // Return the data as JSON
        return response()->json($notice);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|max:500',
            'status' => 'required',
            'lang_code' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'important' => 'required',
            'document' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $notice = Notice::find($id);

        if (!$notice) {
            return response()->json(['error' => 'Not Found.'], 404);
        }

        if ($request->hasFile('document')) {
            $docUpload = $request->file('document');
            $docPath = time() . '_' . $docUpload->getClientOriginalName();
            $docUpload->move(public_path('documents/notices/'), $docPath);
            $validated['document'] = 'notices/' . $docPath;
        }
        // Use fill to update attributes
        $notice->fill($validated);

        $notice->save();

        return response()->json($validated);
    }


    public function delete($id)
    {
        // Find the notice by id
        $notice = Notice::find($id);

        if (!$notice) {
            return response()->json([
                'error' => 'Not Found.'
            ], 400);
        }

        // Delete the notice
        $notice->delete();

        // Return the data as JSON
        return response()->json($notice);
    }
}
