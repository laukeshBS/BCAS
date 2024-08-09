<?php

namespace App\Http\Controllers\Cms;

use App\Models\Admin;
use App\Models\Cms\Slider;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\Cms\ActAndPolicies;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class ActandpoliciesController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }
    // API

    public function data_api(Request $request)
    {
        $limit = $request->input('limit', 5);
        $lang_code = $request->input('lang_code');

        $data = ActAndPolicies::select('*')
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
    public function get_data_by_id_api($id)
    {
        // Validate the ID
        $validatedId = filter_var($id, FILTER_VALIDATE_INT);
        if (!$validatedId) {
            return response()->json([
                'error' => 'Invalid ID format'
            ], 400);
        }

        // Retrieve the data by ID
        $data = ActAndPolicies::find($validatedId);

        // Return a 404 response if data is not found
        if (!$data) {
            return response()->json([
                'error' => 'Data not found'
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

    public function index()
    {
        // if (is_null($this->user) || !$this->user->can('slider-list.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized to view dashboard !');
        // }

        return view('backend.cms.act-and-policies.list');
    }
    public function data()
    {
        $actsandpolicies = ActAndPolicies::select('*')
        ->get()
            ->map(function ($item) {
                // Format the created_at date field
                $item->created_at = date('d-m-Y', strtotime($item->created_at));
                return $item;
            });

        return DataTables::of($actsandpolicies)->make(true);
    }
    public function list(){
        $actsandpolicies = ActAndPolicies::select('*')
            ->get()
            ->map(function ($item) {
                // Format the created_at date field
                $item->created_at = date('d-m-Y', strtotime($item->created_at));
                return $item;
            });

        return response()->json($actsandpolicies);
    }
    // public function data()
    // {
    //     $tenders = ActAndPolicies::select('*')
    //         ->get()
    //         ->map(function ($item) {
    //             // Format the created_at date field
    //             $item->created_at = date('d-m-Y', strtotime($item->created_at));
    //             return $item;
    //         });

    //     return response()->json($tenders);
    // }
    public function add_actandpolicy()
    {
        return view('backend.cms.act-and-policies.add');
    }
    public function store_actandpolicy(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'max:500',
            'status' => 'required',
            'document' => 'required|file|mimes:pdf|max:2048',
        ]);

        // Handle file upload
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads', $fileName, 'public');
        }

        // Create a new Act and Policy instance
        $actandpolicy = new ActAndPolicies();
        $actandpolicy->title = $validated['title'];
        $actandpolicy->description = $validated['description'];
        $actandpolicy->status = $validated['status'];
        $actandpolicy->document = $filePath; // Store file path in the database
        $actandpolicy->save();
        
        $request->session()->flash('success', 'Act and Policy added successfully!');

        return redirect()->route('cms.actandpolicies');
    }
    public function edit_actandpolicy($id)
    {
        // Find the actandpolicy by id
        $actandpolicy = ActAndPolicies::find($id);

        if (!$actandpolicy) {
            return redirect()->route('cms.actandpolicies')->with('error', 'Act and Policy not found.');
        }

        // Pass the actandpolicy data to the view
        return view('backend.cms.act-and-policies.edit', compact('actandpolicy'));
    }

    public function update_actandpolicy(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'max:500',
            'status' => 'required',
            'document' => 'file|mimes:pdf|max:2048',
        ]);

        $actandpolicy = ActAndPolicies::find($id);

        if (!$actandpolicy) {
            return redirect()->route('cms.actandpolicies.list')->with('error', 'Act and Policy not found.');
        }

        $actandpolicy->title = $request->input('title');
        $actandpolicy->description = $request->input('description');
        $actandpolicy->status = $request->input('status');

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('documents'), $filename);
            $actandpolicy->document = $filename;
        }

        $actandpolicy->save();

        return redirect()->route('cms.actandpolicies')->with('success', 'Act and Policy updated successfully.');
    }

    public function delete_actandpolicy($id)
    {
        // Find the actandpolicy by id
        $actandpolicy = ActAndPolicies::find($id);

        if (!$actandpolicy) {
            return redirect()->route('cms.actandpolicies')->with('error', 'Act and Policy not found.');
        }

        // Delete the actandpolicy
        $actandpolicy->delete();

        // Redirect back with success message
        return redirect()->route('cms.actandpolicies')->with('success', 'Act and Policy deleted successfully.');
    }
}
