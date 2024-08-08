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

    // APIs
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
            $item->document = url(Storage::url('app/public/' . $item->document)) ;
            return $item;
        });

        return response()->json($notices);
    }
    public function notice_list(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $notices = Notice::select('*')
            ->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $notices->getCollection()->transform(function ($item) {
            $item->created_at = date('d-m-Y', strtotime($item->created_at));
            return $item;
        });

        return response()->json($notices);
    }
    public function notice_store(Request $request)
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
            $filePath = $file->storeAs('public/notices', $fileName);
            $filePath = str_replace('public/', '', $filePath);
        }

        $notice = new notice();
        $notice->title = $validated['title'];
        $notice->description = $validated['description'];
        $notice->status = $validated['status'];
        $notice->document = $filePath;
        $notice->save();

        return response()->json($notice);
    }

    // Web

    public function index()
    {
        // if (is_null($this->user) || !$this->user->can('slider-list.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized to view dashboard !');
        // }

        return view('backend.cms.notice.list');
    }
    public function data()
    {
        $notices = Notice::select('*')
        ->get()
            ->map(function ($item) {
                // Format the created_at date field
                $item->created_at = date('d-m-Y', strtotime($item->created_at));
                return $item;
            });

        return DataTables::of($notices)->make(true);
    }
    public function add_notice()
    {
        // if (is_null($this->user) || !$this->user->can('notice-add.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized to view dashboard !');
        // }

        return view('backend.cms.notice.add');
    }
    public function store_notice(Request $request)
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
            $zipFilePath = storage_path('app/public/notices/') . $zipFileName;

            if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
                $zip->addFile($file->getPathname(), $file->getClientOriginalName());
                $zip->close();
            }

            $filePath = 'notices/' . $zipFileName;
        }

        // Create a new notice instance
        $notice = new notice();
        $notice->title = $validated['title'];
        $notice->description = $validated['description'];
        $notice->status = $validated['status'];
        $notice->document = $filePath; // Store file path in the database
        $notice->save();

        $request->session()->flash('success', 'notice added successfully!');

        return redirect()->route('cms.notice');
    }
    public function edit_notice($id)
    {
        // Find the notice by id
        $notice = Notice::find($id);

        if (!$notice) {
            return redirect()->route('cms.notice')->with('error', 'notice not found.');
        }

        // Pass the notice data to the view
        return view('backend.cms.notice.edit', compact('notice'));
    }

    public function update_notice(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'max:500',
            'status' => 'required',
            'document' => 'file|mimes:pdf|max:2048',
        ]);

        $notice = Notice::find($id);

        if (!$notice) {
            return redirect()->route('cms.notice.list')->with('error', 'notice not found.');
        }

        $notice->title = $request->input('title');
        $notice->description = $request->input('description');
        $notice->status = $request->input('status');

        if ($request->hasFile('document')) {
            // Handle file upload
            $file = $request->file('document');
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Create a ZIP file
            $zipFileName = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.zip';
            $zip = new ZipArchive;
            $zipFilePath = storage_path('app/public/notices/') . $zipFileName;

            if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
                $zip->addFile($file->getPathname(), $file->getClientOriginalName());
                $zip->close();
            }

            $filePath = 'notices/' . $zipFileName;

            // Update the document path in the database
            $notice->document = $filePath;
        }

        $notice->save();

        return redirect()->route('cms.notice')->with('success', 'notice updated successfully.');
    }

    public function delete_notice($id)
    {
        // Find the notice by id
        $notice = Notice::find($id);

        if (!$notice) {
            return redirect()->route('cms.notice')->with('error', 'notice not found.');
        }

        // Delete the notice
        $notice->delete();

        // Redirect back with success message
        return redirect()->route('cms.notice')->with('success', 'notice deleted successfully.');
    }
}
