<?php

namespace App\Http\Controllers\Cms;

use ZipArchive;
use App\Models\Admin;
use App\Models\Cms\Event;
use App\Models\Cms\Slider;
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

    // APIs
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
            $item->document = url(Storage::url('app/public/' . $item->document)) ;
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
    public function event_store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'max:500',
            'status' => 'required',
            'image' => 'required|file|mimes:png,jpg,jpeg',
        ]);

        $filePath = null;

        // Handle file upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('public/events', $fileName);
            $filePath = str_replace('public/', '', $filePath);
        }

        $event = new Event();
        $event->title = $validated['title'];
        $event->description = $validated['description'];
        $event->status = $validated['status'];
        $event->document = $filePath;
        $event->save();

        return response()->json($event);
    }

    // Web
    public function index()
    {
        // if (is_null($this->user) || !$this->user->can('slider-list.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized to view dashboard !');
        // }

        return view('backend.cms.event.list');
    }
    public function data()
    {
        $events = Event::select('*')
        ->get()
            ->map(function ($item) {
                // Format the created_at date field
                $item->created_at = date('d-m-Y', strtotime($item->created_at));
                return $item;
            });

        return DataTables::of($events)->make(true);
    }
    public function add_event()
    {
        // if (is_null($this->user) || !$this->user->can('event-add.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized to view dashboard !');
        // }

        return view('backend.cms.event.add');
    }
    public function store_event(Request $request)
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
            // $zipFileName = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.zip';
            // $zip = new ZipArchive;
            // $zipFilePath = storage_path('app/public/events/') . $zipFileName;

            // if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
            //     $zip->addFile($file->getPathname(), $file->getClientOriginalName());
            //     $zip->close();
            // }
            $filestore = storage_path('app/public/events/') . $fileName;
            $filePath = 'events/' . $fileName;
        }

        // Create a new event instance
        $event = new Event();
        $event->title = $validated['title'];
        $event->description = $validated['description'];
        $event->status = $validated['status'];
        $event->document = $filePath; // Store file path in the database
        $event->save();

        $request->session()->flash('success', 'Event added successfully!');

        return redirect()->route('cms.event');
    }
    public function edit_event($id)
    {
        // Find the event by id
        $event = Event::find($id);

        if (!$event) {
            return redirect()->route('cms.event')->with('error', 'event not found.');
        }

        // Pass the event data to the view
        return view('backend.cms.event.edit', compact('event'));
    }

    public function update_event(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'max:500',
            'status' => 'required',
            'document' => 'file|mimes:pdf|max:2048',
        ]);

        $event = Event::find($id);

        if (!$event) {
            return redirect()->route('cms.event.list')->with('error', 'Event not found.');
        }

        $event->title = $request->input('title');
        $event->description = $request->input('description');
        $event->status = $request->input('status');

        if ($request->hasFile('document')) {
            // Handle file upload
            $file = $request->file('document');
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Create a ZIP file
            $zipFileName = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.zip';
            $zip = new ZipArchive;
            $zipFilePath = storage_path('app/public/events/') . $zipFileName;

            if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
                $zip->addFile($file->getPathname(), $file->getClientOriginalName());
                $zip->close();
            }

            $filePath = 'events/' . $zipFileName;

            // Update the document path in the database
            $event->document = $filePath;
        }

        $event->save();

        return redirect()->route('cms.event')->with('success', 'Event updated successfully.');
    }

    public function delete_event($id)
    {
        // Find the event by id
        $event = Event::find($id);

        if (!$event) {
            return redirect()->route('cms.event')->with('error', 'event not found.');
        }

        // Delete the event
        $event->delete();

        // Redirect back with success message
        return redirect()->route('cms.event')->with('success', 'event deleted successfully.');
    }
}
