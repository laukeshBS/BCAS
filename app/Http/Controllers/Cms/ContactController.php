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
        $limit = $request->input('limit', 5);
        $lang_code = $request->input('lang_code');
        $division_id = $request->input('division_id');
        $region_id = $request->input('region_id');

        // Start building the query
        $query = Contact::select('*');

        // Apply the 'lang_code' filter
        if ($lang_code) {
            $query->where('lang_code', $lang_code);
        }

        // Apply the 'division_id' filter if provided
        if ($division_id) {
            $query->where('division_id', $division_id);
        }

        // Apply the 'region_id' filter if provided
        if ($region_id) {
            $query->where('region_id', $region_id);
        }

        // Limit the results
        $data = $query->limit($limit)->get();

        // Transform the 'created_at' attribute
        $data->transform(function ($item) {
            $item->created_at = date('d-m-Y', strtotime($item->created_at));
            return $item;
        });

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
            'division_id' => 'required',
            'region_id' => 'required',
            'lang_code' => 'required',
            'status' => 'required',
        ]);

        $contact = new Contact();
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
