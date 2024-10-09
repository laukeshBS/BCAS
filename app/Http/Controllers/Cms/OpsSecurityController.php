<?php

namespace App\Http\Controllers\Cms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Cms\OpsSecurity;
use App\Helpers\AuditTrail;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
class OpsSecurityController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

  
    public function opssecurity_list(Request $request)
    {
       // dd('here');
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $opssecuritys = OpsSecurity::select('*')
            ->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $opssecuritys->getCollection()->transform(function ($item) {
            $item->created_at = date('d-m-Y', strtotime($item->created_at));
            return $item;
        });

        return response()->json($opssecuritys);
    }
public function opssecurity_list_approved(Request $request)
{
    // Default pagination parameters
    $perPage = $request->input('per_page', 10);
    $page = $request->input('page', 1);

    // Filter parameters
    $division = $request->input('division');
    $sec_type = $request->input('sec_type');
    $region_name = $request->input('region_name');
    $airport_name = $request->input('airport_name');
    // Query to fetch approved opssecuritys based on the provided filters
    $opssecuritys = OpsSecurity::select('*')
    ->where('status', 'APPROVED')
    ->where('sec_type', 'like', "%{$sec_type}%") // Using like for partial match
    ->when($region_name, function ($query, $region_name) {
        return $query->where('region_name', $region_name);
    })
    ->when($airport_name, function ($query, $airport_name) {
        return $query->where('airport_name', $airport_name);
    })
    ->when($division, function ($query, $division) {
        return $query->where('division', $division);
    })
       
        ->orderBy('date_of_approval', 'DESC')
        ->paginate($perPage, ['*'], 'page', $page);

    // Transforming the collection to format the created_at date
    $opssecuritys->getCollection()->transform(function ($item) {
        $item->created_at = date('d-m-Y', strtotime($item->created_at));
        return $item;
    });

    // Returning the paginated list of opssecuritys as a JSON response
    return response()->json($opssecuritys);
}

    public function data(Request $request)
    {
        $limit = $request->input('limit', 5);
        $lang_code = $request->input('lang_code');

        $data = OpsSecurity::select('*')
            ->where('lang_code',$lang_code)
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();

        $data->transform(function ($item) {
            $item->start_date = date('d-m-Y', strtotime($item->start_date));
            $item->end_date = date('d-m-Y', strtotime($item->end_date));
            $item->created_at = date('d-m-Y', strtotime($item->created_at));
           
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
        $data = OpsSecurity::find($validatedId);

        // Return a 404 response if data is not found
        if (!$data) {
            return response()->json([
                'error' => 'Data not found'
            ], 404);
        }
        $data->created_at = date('d-m-Y', strtotime($data->created_at));
      

        // Return the data as JSON
        return response()->json($data);
    }
    public function store(Request $request): mixed
    {
        // Define validation rules
        $rules = [
            'region_name' => 'required|string|max:255',
            'sr_no' => 'required|numeric',
            'opssecurity_name' => 'required|string|max:255',
            'entity_name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'mobile_no' => 'required|numeric',
            'phone_no' => 'required|numeric',
            'unique_reference_number' => 'required|string|max:255',
            'approved_status_clearance' => 'required',
            'date_of_approval_clearance' => 'required|date',
            'approved_status_programme' => 'required',
            'date_of_approval_programme' => 'required|date',
            'valid_till' => 'required|date',
            //'opssecurity_orders' => 'required|string|max:255'
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error.',
                'messages' => $validator->errors()
            ], 422); // 422 Unprocessable Entity
        }

        // Prepare data for insertion
        $data = $request->only([
            'region_name', 
            'sr_no', 
            'opssecurity_name', 
            'entity_name', 
            'address', 
            'mobile_no', 
            'phone_no', 
            'unique_reference_number', 
            'approved_status_clearance', 
            'date_of_approval_clearance', 
            'approved_status_programme', 
            'date_of_approval_programme', 
            'valid_till', 
            'opssecurity_orders'
        ]);
//dd($data);
        //$data['created_by'] = Auth::guard('admin')->user()->id;

        // // Create new OpsSecurity record
        $opssecuritydata = OpsSecurity::create($data);

        // // Log audit trail
        // $user_login_id = Auth::guard('admin')->user()->id;
        // $action_by_role = Auth::guard('admin')->user()->username;

        // $logs_data = [
        //     'module_item_title' => $request->unique_reference_number,
        //     'module_item_id' => $opssecuritydata->id,
        //     'action_by' => $user_login_id,
        //     'old_data' => json_encode($data),
        //     'new_data' => json_encode($data),
        //     'action_name' => 'Add Working Airport',
        //     'action_type' => 'Working Airport Model',
        //     'approve_status' => $request->approved_status_clearance,
        //     'action_by_role' => $action_by_role
        // ];

        // // Assuming there's a helper function for logging
        // AuditTrail::log($logs_data);

        // Return JSON response
        return response()->json($opssecuritydata, 201); // 201 Created
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'max:500',
            'status' => 'required',
            'lang_code' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'opssecurity_orders' => 'required|string|max:255',
            'region_name' => 'required|string|max:255',
            'sr_no' => 'required|numeric',
            'opssecurity_name' => 'required|string|max:255',
            'entity_name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'mobile_no' => 'required|numeric',
            'phone_no' => 'required|numeric',
            'unique_reference_number' => 'required|string|max:255',
            'approved_status_clearance' => 'required|boolean',
            'date_of_approval_clearance' => 'required|date',
            'approved_status_programme' => 'required|boolean',
            'date_of_approval_programme' => 'required|date',
            'valid_till' => 'required|date'
        ]);
        

        $opssecuritydata = OpsSecurity::find($id);

        if (!$opssecuritydata) {
            return response()->json([
                'error' => 'Not Found.'
            ], 400);
        }

        $opssecuritydata->title = $validated['title'];
        $opssecuritydata->description = $validated['description'];
        $opssecuritydata->status = $validated['status'];
        $opssecuritydata->lang_code = $validated['lang_code'];
        $opssecuritydata->start_date = $validated['start_date'];
        $opssecuritydata->end_date = $validated['end_date'];
        $opssecuritydata->opssecurity_orders = $validated['opssecurity_orders'] ?? null;
        $opssecuritydata->region_name = $validated['region_name'] ?? null;
        $opssecuritydata->sr_no = $validated['sr_no'] ?? null;
        $opssecuritydata->opssecurity_name = $validated['opssecurity_name'] ?? null;
        $opssecuritydata->entity_name = $validated['entity_name'] ?? null;
        $opssecuritydata->address = $validated['address'] ?? null;
        $opssecuritydata->mobile_no = $validated['mobile_no'] ?? null;
        $opssecuritydata->phone_no = $validated['phone_no'] ?? null;
        $opssecuritydata->unique_reference_number = $validated['unique_reference_number'] ?? null;
        $opssecuritydata->approved_status_clearance = $validated['approved_status_clearance'] ?? null;
        $opssecuritydata->date_of_approval_clearance = $validated['date_of_approval_clearance'] ?? null;
        $opssecuritydata->approved_status_programme = $validated['approved_status_programme'] ?? null;
        $opssecuritydata->date_of_approval_programme = $validated['date_of_approval_programme'] ?? null;
        $opssecuritydata->valid_till = $validated['valid_till'] ?? null;
        $opssecuritydata->save();

        // Return the data as JSON
        return response()->json($opssecuritydata);
    }

    public function delete($id)
    {
        // Find the opssecuritydata by id
        $opssecuritydata = OpsSecurity::find($id);

        if (!$opssecuritydata) {
            return response()->json([
                'error' => 'Not Found.'
            ], 400);
        }

        // Delete the opssecuritydata
        $opssecuritydata->delete();

        // Return the data as JSON
        return response()->json($opssecuritydata);
    }

}
