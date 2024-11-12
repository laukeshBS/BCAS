<?php

namespace App\Http\Controllers\Cms;

use ZipArchive;
use App\Models\Admin;
use App\Models\Cms\Event;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class EventController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    public function event_list_for_homepage(Request $request)
    {
        $limit = $request->input('limit', 5);
        $lang_code = $request->input('lang_code');

        $events = Event::select('*')
            ->where('lang_code',$lang_code)
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();

        $events->transform(function ($item) {
            $item->created_at = date('d-m-Y', strtotime($item->created_at));
            // $item->document = asset('public/documents/' . $item->document) ;
            return $item;
        });

        return response()->json($events);
    }
    
    public function event_list_for_frontend(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $lang_code = $request->input('lang_code');

        $events = Event::select('*')
            ->where('lang_code', $lang_code)
            ->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $events->getCollection()->transform(function ($item) {
            $item->created_at = date('d-m-Y', strtotime($item->created_at));
            return $item;
        });

        return response()->json($events);
    }
    public function event_list(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $events = Event::select('*')
            ->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $events->getCollection()->transform(function ($item) {
            $item->created_at = date('d-m-Y', strtotime($item->created_at));
            return $item;
        });

        return response()->json($events);
    }
    public function data(Request $request)
    {
        $limit = $request->input('limit', 5);
        $lang_code = $request->input('lang_code');

        $data = Event::select('*')
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

        $events = Event::select('*')->orderBy('id', 'desc')->paginate($perPage, ['*'], 'page', $page);
        if ($events->isNotEmpty()) {
            $events->transform(function ($item) {
                $item->created_at = date('d-m-Y', strtotime($item->created_at));
                // if ($item->document) {
                //     $item->document = asset('public/documents/'.$item->document);
                // }
                return $item;
            });
        }

            return response()->json([
                'title' => 'Tender List',
                'data' => $events->items(),
                'total' => $events->total(),
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'per_page' => $events->perPage(),
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
        $data = Event::find($validatedId);

        // Return a 404 response if data is not found
        if (!$data) {
            return response()->json([
                'error' => 'Data not found'
            ], 404);
        }
        // $data->start_date = date('d-m-Y', strtotime($data->start_date));
        // $data->end_date = date('d-m-Y', strtotime($data->end_date));
        $data->start_date = date('d-m-Y', strtotime($data->start_date));
        $data->end_date = date('d-m-Y', strtotime($data->end_date));
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
            'document' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle file upload
        if ($request->hasFile('document')) {
            $docUpload = $request->file('document');
            $docPath = time() . '_' . $docUpload->getClientOriginalName();
            $docUpload->move(public_path('documents/events/'), $docPath);
            $filePath = 'events/'.$docPath;
        }

        // Create a new Act and Policy instance
        $event = new Event();
        $event->title = $validated['title'];
        $event->description = $validated['description'];
        $event->status = $validated['status'];
        $event->lang_code = $validated['lang_code'];
        $event->start_date = $validated['start_date'];
        $event->end_date = $validated['end_date'];
        $event->document = $filePath; // Store file path in the database
        $event->save();
        
        // Return the data as JSON
        return response()->json($event);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|max:500',
            'status' => 'required',
            'lang_code' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'document' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $event = Event::find($id);

        if (!$event) {
            return response()->json(['error' => 'Not Found.'], 404);
        }

        

        if ($request->hasFile('document')) {
            $docUpload = $request->file('document');
            $docPath = time() . '_' . $docUpload->getClientOriginalName();
            $docUpload->move(public_path('documents/events/'), $docPath);
            $validated['document'] = 'events/' . $docPath;
        }
        // Use fill to update attributes
        $event->fill($validated);

        $event->save();

        return response()->json($event);
    }

    public function delete($id)
    {
        // Find the event by id
        $event = Event::find($id);

        if (!$event) {
            return response()->json([
                'error' => 'Not Found.'
            ], 400);
        }

        // Delete the event
        $event->delete();

        // Return the data as JSON
        return response()->json($event);
    }
}
