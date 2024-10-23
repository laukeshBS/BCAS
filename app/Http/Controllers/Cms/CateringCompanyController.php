<?php

namespace App\Http\Controllers\Cms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Cms\CateringCompany;
use App\Helpers\AuditTrail;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
class CateringCompanyController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

  
    public function catering_list(Request $request)
    {
       // dd('here');
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $catering = CateringCompany::select('*')
            ->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $catering->getCollection()->transform(function ($item) {
            $item->created_at = date('d-m-Y', strtotime($item->created_at));
            return $item;
        });

        return response()->json($catering);
    }
public function catering_list_approved(Request $request)
{
    // Default pagination parameters
    $perPage = $request->input('per_page', 10);
    $page = $request->input('page', 1);

    // Filter parameters
    $airport_name = $request->input('airport_name');
    $regional_office = $request->input('regional_office');

    // Query to fetch approved catering based on the provided filters
    $catering = CateringCompany::select('*')
    ->where('status', 'APPROVED')
    ->when($airport_name, function ($query, $airport_name) {
        return $query->where('entity_name', $airport_name);
    })
    ->when($regional_office, function ($query, $regional_office) {
        return $query->where('regional_office', $regional_office);
    })
   
        ->orderBy('date_of_security_programme_approval', 'DESC')
        ->paginate($perPage, ['*'], 'page', $page);

    // Transforming the collection to format the created_at date
    $catering->getCollection()->transform(function ($item) {
        $item->created_at = date('d-m-Y', strtotime($item->created_at));
        return $item;
    });

    // Returning the paginated list of catering as a JSON response
    return response()->json($catering);
}

    public function data(Request $request)
    {
        $limit = $request->input('limit', 5);
        $lang_code = $request->input('lang_code');

        $data = CateringCompany::select('*')
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
        $data = CateringCompany::find($validatedId);

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
            'catering_name' => 'required|string|max:255',
            'entity_name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'mobile_no' => 'required|numeric',
            'phone_no' => 'required|numeric',
            'unique_reference_number' => 'required|string|max:255',
            'approved_status_clearance' => 'required',
            'date_of_security_programme_approval_clearance' => 'required|date',
            'approved_status_programme' => 'required',
            'date_of_security_programme_approval_programme' => 'required|date',
            'valid_till' => 'required|date',
            //'catering_orders' => 'required|string|max:255'
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
            'catering_name', 
            'entity_name', 
            'address', 
            'mobile_no', 
            'phone_no', 
            'unique_reference_number', 
            'approved_status_clearance', 
            'date_of_security_programme_approval_clearance', 
            'approved_status_programme', 
            'date_of_security_programme_approval_programme', 
            'valid_till', 
            'catering_orders'
        ]);
//dd($data);
        //$data['created_by'] = Auth::guard('admin')->user()->id;

        // // Create new CateringCompany record
        $cateringdata = CateringCompany::create($data);

        // // Log audit trail
        // $user_login_id = Auth::guard('admin')->user()->id;
        // $action_by_role = Auth::guard('admin')->user()->username;

        // $logs_data = [
        //     'module_item_title' => $request->unique_reference_number,
        //     'module_item_id' => $cateringdata->id,
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
        return response()->json($cateringdata, 201); // 201 Created
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
            'catering_orders' => 'required|string|max:255',
            'region_name' => 'required|string|max:255',
            'sr_no' => 'required|numeric',
            'catering_name' => 'required|string|max:255',
            'entity_name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'mobile_no' => 'required|numeric',
            'phone_no' => 'required|numeric',
            'unique_reference_number' => 'required|string|max:255',
            'approved_status_clearance' => 'required|boolean',
            'date_of_security_programme_approval_clearance' => 'required|date',
            'approved_status_programme' => 'required|boolean',
            'date_of_security_programme_approval_programme' => 'required|date',
            'valid_till' => 'required|date'
        ]);
        

        $cateringdata = CateringCompany::find($id);

        if (!$cateringdata) {
            return response()->json([
                'error' => 'Not Found.'
            ], 400);
        }

        $cateringdata->title = $validated['title'];
        $cateringdata->description = $validated['description'];
        $cateringdata->status = $validated['status'];
        $cateringdata->lang_code = $validated['lang_code'];
        $cateringdata->start_date = $validated['start_date'];
        $cateringdata->end_date = $validated['end_date'];
        $cateringdata->catering_orders = $validated['catering_orders'] ?? null;
        $cateringdata->region_name = $validated['region_name'] ?? null;
        $cateringdata->sr_no = $validated['sr_no'] ?? null;
        $cateringdata->catering_name = $validated['catering_name'] ?? null;
        $cateringdata->entity_name = $validated['entity_name'] ?? null;
        $cateringdata->address = $validated['address'] ?? null;
        $cateringdata->mobile_no = $validated['mobile_no'] ?? null;
        $cateringdata->phone_no = $validated['phone_no'] ?? null;
        $cateringdata->unique_reference_number = $validated['unique_reference_number'] ?? null;
        $cateringdata->approved_status_clearance = $validated['approved_status_clearance'] ?? null;
        $cateringdata->date_of_security_programme_approval_clearance = $validated['date_of_security_programme_approval_clearance'] ?? null;
        $cateringdata->approved_status_programme = $validated['approved_status_programme'] ?? null;
        $cateringdata->date_of_security_programme_approval_programme = $validated['date_of_security_programme_approval_programme'] ?? null;
        $cateringdata->valid_till = $validated['valid_till'] ?? null;
        $cateringdata->save();

        // Return the data as JSON
        return response()->json($cateringdata);
    }

    public function delete($id)
    {
        // Find the cateringdata by id
        $cateringdata = CateringCompany::find($id);

        if (!$cateringdata) {
            return response()->json([
                'error' => 'Not Found.'
            ], 400);
        }

        // Delete the cateringdata
        $cateringdata->delete();

        // Return the data as JSON
        return response()->json($cateringdata);
    }

}
