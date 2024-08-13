<?php

namespace App\Http\Controllers\Cms;

use App\Models\Admin;
use App\Models\Cms\Slider;
use App\Models\Cms\Tender;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Models\Cms\Contact;
use App\Models\Cms\Division;
use App\Models\Cms\Region;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class ContactController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    // contact
    public function data(Request $request)
{
    $lang_code = $request->input('lang_code');
    $division_id = $request->input('division_id');
    $region_id = $request->input('region_id');
    $type = $request->input('type');

    if ($type == 1) {
        // Query for Division model
        $query = Division::whereHas('contacts', function ($query) use ($lang_code) {
            $query->where('lang_code', $lang_code);
        })->with('contacts');
        
        // Apply division_id filter if provided
        if ($division_id) {
            $query->where('id', $division_id);
        }
        
        $data = $query->get();
    } else if ($type == 2) {
        // Query for Region model
        $query = Region::whereHas('contacts', function ($query) use ($lang_code) {
            $query->where('lang_code', $lang_code);
        })->with('contacts');
        
        // Apply region_id filter if provided
        if ($region_id) {
            $query->where('id', $region_id);
        }
        
        $data = $query->get();
    } else {
        // Return an empty array if type is not 1 or 2
        $data = [];
    }

    return response()->json($data);
}


    public function data_by_id($id)
    {
        $validatedId = filter_var($id, FILTER_VALIDATE_INT);
        if (!$validatedId) {
            return response()->json([
                'error' => 'Invalid ID format'
            ], 400);
        }

        $data = Contact::find($validatedId);

        if (!$data) {
            return response()->json([
                'error' => 'Data not found'
            ], 404);
        }
        $data->created_at = date('d-m-Y', strtotime($data->created_at));

        return response()->json($data);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'rank' => 'required',
            'phone' => 'required',
            'email' => 'required',
            'lang_code' => 'required',
            'status' => 'required',
            'type' => 'required',
        ]);

        $contact = new Contact();
        $contact->name = $validated['name'];
        $contact->rank = $validated['rank'];
        $contact->phone = $validated['phone'];
        $contact->email = $validated['email'];
        $contact->division_id = $request->division_id;
        $contact->region_id = $request->region_id;
        $contact->lang_code = $validated['lang_code'];
        $contact->status = $validated['status'];
        $contact->type = $validated['type'];
        $contact->save();
        
        return response()->json($contact);
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required',
            'rank' => 'required|max:255',
            'phone' => 'required',
            'email' => 'required',
            'division_id' => 'required',
            'region_id' => 'required',
            'lang_code' => 'required',
            'status' => 'required',
        ]);

        $contact = Contact::find($id);

        if (!$contact) {
            return response()->json([
                'error' => 'Data Not Found.'
            ], 400);
        }

        $contact->name = $validated['name'];
        $contact->rank = $validated['rank'];
        $contact->phone = $validated['phone'];
        $contact->email = $validated['email'];
        $contact->division_id = $validated['division_id'];
        $contact->region_id = $validated['region_id'];
        $contact->lang_code = $validated['lang_code'];
        $contact->status = $validated['status'];
        $contact->save();

        return response()->json($contact);
    }
    public function delete($id)
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return response()->json([
                'error' => 'Data Not Found.'
            ], 400);
        }
        $contact->delete();

        return response()->json($contact);
    }
}
