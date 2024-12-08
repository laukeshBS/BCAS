<?php

namespace App\Http\Controllers\Cms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Cms\WorkingAirport;
use App\Helpers\AuditTrail;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
class WorkingAirportsController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }


    public function airport_list(Request $request)
    {
       // dd('here');
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $airports = WorkingAirport::select('*')
            ->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $airports->getCollection()->transform(function ($item) {
            $item->created_at = date('d-m-Y', strtotime($item->created_at));
            return $item;
        });

        return response()->json($airports);
    }
    public function airport_list_approved(Request $request)
    {
        // Default pagination parameters
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        // Filter parameters
        $region_name = $request->input('region_name');
        $airport_name = $request->input('airport_name');
        $entity_name = $request->input('entity_name');
      echo  $date_of_approval = $request->input('date_of_approval');
        // Query to fetch approved airports based on the provided filters
        $airports = WorkingAirport::select('*')
            // ->where('approved_status_clearance', 'APPROVED')
            ->where('approved_status_programme', 'APPROVED')
            ->when($region_name, function ($query, $region_name) {
                return $query->where('region_name', $region_name);
            })
            ->when($airport_name, function ($query, $airport_name) {
                return $query->where('airport_name', $airport_name);
            })
            ->when($entity_name, function ($query, $entity_name) {
                return $query->where('entity_name', 'like', "%{$entity_name}%");
            })
            // ->when($date_of_approval, function ($query, $date_of_approval) {
            //     return $query->whereRaw('YEAR(date_of_approval_clearance) = ?', [$date_of_approval]);
            // })
            ->when($date_of_approval, function ($query, $date_of_approval) {
                return $query->whereRaw('YEAR(date_of_approval_programme) = ?', [$date_of_approval]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        // Transforming the collection to format the created_at date
        $airports->getCollection()->transform(function ($item) {
            $item->created_at = date('d-m-Y', strtotime($item->created_at));
            return $item;
        });

        // Returning the paginated list of airports as a JSON response
        return response()->json($airports);
    }

    public function data(Request $request)
    {
        $limit = $request->input('limit', 5);
        $lang_code = $request->input('lang_code');

        $data = WorkingAirport::select('*')
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
        $data = WorkingAirport::find($validatedId);

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
            'airport_name' => 'required|string|max:255',
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
            //'airport_orders' => 'required|string|max:255'
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
            'airport_name',
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
            // 'airport_orders'
        ]);
        //$data['created_by'] = Auth::guard('admin')->user()->id;

        // // Create new WorkingAirport record
        $airportdata = WorkingAirport::create($data);

        // // Log audit trail
        // $user_login_id = Auth::guard('admin')->user()->id;
        // $action_by_role = Auth::guard('admin')->user()->username;

        // $logs_data = [
        //     'module_item_title' => $request->unique_reference_number,
        //     'module_item_id' => $airportdata->id,
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
        return response()->json($airportdata, 201); // 201 Created
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
            // 'airport_orders' => 'required|string|max:255',
            'region_name' => 'required|string|max:255',
            'sr_no' => 'required|numeric',
            'airport_name' => 'required|string|max:255',
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


        $airportdata = WorkingAirport::find($id);

        if (!$airportdata) {
            return response()->json([
                'error' => 'Not Found.'
            ], 400);
        }

        $airportdata->title = $validated['title'];
        $airportdata->description = $validated['description'];
        $airportdata->status = $validated['status'];
        $airportdata->lang_code = $validated['lang_code'];
        $airportdata->start_date = $validated['start_date'];
        $airportdata->end_date = $validated['end_date'];
        // $airportdata->airport_orders = $validated['airport_orders'] ?? null;
        $airportdata->region_name = $validated['region_name'] ?? null;
        $airportdata->sr_no = $validated['sr_no'] ?? null;
        $airportdata->airport_name = $validated['airport_name'] ?? null;
        $airportdata->entity_name = $validated['entity_name'] ?? null;
        $airportdata->address = $validated['address'] ?? null;
        $airportdata->mobile_no = $validated['mobile_no'] ?? null;
        $airportdata->phone_no = $validated['phone_no'] ?? null;
        $airportdata->unique_reference_number = $validated['unique_reference_number'] ?? null;
        $airportdata->approved_status_clearance = $validated['approved_status_clearance'] ?? null;
        $airportdata->date_of_approval_clearance = $validated['date_of_approval_clearance'] ?? null;
        $airportdata->approved_status_programme = $validated['approved_status_programme'] ?? null;
        $airportdata->date_of_approval_programme = $validated['date_of_approval_programme'] ?? null;
        $airportdata->valid_till = $validated['valid_till'] ?? null;
        $airportdata->save();

        // Return the data as JSON
        return response()->json($airportdata);
    }

    public function delete($id)
    {
        // Find the airportdata by id
        $airportdata = WorkingAirport::find($id);

        if (!$airportdata) {
            return response()->json([
                'error' => 'Not Found.'
            ], 400);
        }

        // Delete the airportdata
        $airportdata->delete();

        // Return the data as JSON
        return response()->json($airportdata);
    }

    // CMS Api
    public function cms_data(Request $request)
    {
        $request->validate([
            'limit' => 'required|integer',
            'currentPage' => 'required|integer',
        ]);

        $perPage = $request->input('limit');
        $page = $request->input('currentPage');

        $query = WorkingAirport::query();

        $data = $query->select('*')->orderBy('id', 'desc')->paginate($perPage, ['*'], 'page', $page);

        if ($data->isNotEmpty()) {
            $data->transform(function ($item) {
                $item->created_at = date('d-m-Y', strtotime($item->created_at));
                return $item;
            });
        }

        return response()->json([
            'title' => 'List',
            'data' => $data->items(),
            'total' => $data->total(),
            'current_page' => $data->currentPage(),
            'last_page' => $data->lastPage(),
            'per_page' => $data->perPage(),
        ]);
    }

    public function cms_store(Request $request): mixed
    {
        // Define validation rules
        $rules = [
            // 'airport_orders'    => 'required',
            'region_name'     => 'required|string|max:255',
            'lang_code'     => 'required|string|max:255',
            'sr_no'     => 'required|string|max:255',
            'airport_name'   => 'required|string|max:255',
            'entity_name'   => 'required|string|max:255',
            'address'    => 'required|string|max:255',
            'mobile_no'  => 'required',
            'phone_no'   => 'required',
            'unique_reference_number'    => 'required',
            'date_of_approval_clearance'   => 'required|date',
            'approved_status_programme'    => 'required|string|max:255',
            'date_of_approval_programme'  => 'required|date',
            'valid_till'  => 'required|date',
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error',
                'messages' => $validator->errors()->toArray()
            ], 422);  // 422 Unprocessable Entity
        }

        // Prepare data for insertion
        $data = $request->only([
            'region_name','lang_code','sr_no','airport_name','entity_name','address','mobile_no','phone_no','unique_reference_number','approved_status_clearance','date_of_approval_clearance','approved_status_programme','date_of_approval_programme','valid_till'
        ]);

        // Create new Airline record
        try {
            $airlinedata = WorkingAirport::create($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error creating the airline record.',
                'message' => $e->getMessage()
            ], 500);  // 500 Internal Server Error
        }

        // Return JSON response with success message
        return response()->json([
            'data' => $airlinedata,
            'message' => 'Created successfully.'
        ], 201);  // 201 Created
    }

    public function cms_update(Request $request, $id): mixed
    {
        // Define validation rules
        $rules = [
            // 'airport_orders'    => 'required',
            'region_name'     => 'required|string|max:255',
            'lang_code'     => 'required|string|max:255',
            'sr_no'     => 'required|string|max:255',
            'airport_name'   => 'required|string|max:255',
            'entity_name'   => 'required|string|max:255',
            'address'    => 'required|string|max:255',
            'mobile_no'  => 'required',
            'phone_no'   => 'required',
            'unique_reference_number'    => 'required',
            'date_of_approval_clearance'   => 'required|date',
            'approved_status_programme'    => 'required|string|max:255',
            'date_of_approval_programme'  => 'required|date',
            'valid_till'  => 'required|date',
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error',
                'messages' => $validator->errors()->toArray()
            ], 422);  // 422 Unprocessable Entity
        }

        // Find the existing Airline record by ID
        $airline = WorkingAirport::find($id);

        if (!$airline) {
            return response()->json([
                'error' => 'Record not found.',
                'message' => 'The requested airline record does not exist.'
            ], 404);  // 404 Not Found
        }

        // Prepare data for update
        $data = $request->only([
            'region_name','lang_code','sr_no','airport_name','entity_name','address','mobile_no','phone_no','unique_reference_number','approved_status_clearance','date_of_approval_clearance','approved_status_programme','date_of_approval_programme','valid_till'
        ]);

        // Update the existing Airline record
        try {
            $airline->update($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error updating the airline record.',
                'message' => $e->getMessage()
            ], 500);  // 500 Internal Server Error
        }

        // Return JSON response with success message
        return response()->json([
            'data' => $airline,
            'message' => 'Updated successfully.'
        ], 200);  // 200 OK
    }

    public function cms_delete($id)
    {
        // Find the airlinedata by id
        $airlinedata = WorkingAirport::find($id);

        if (!$airlinedata) {
            return response()->json([
                'error' => 'Not Found.'
            ], 400);
        }

        // Delete the airlinedata
        $airlinedata->delete();

        // Return the data as JSON
        return response()->json(['data' => $airlinedata, 'message' => 'Deleted successfully.'], 200);
    }

}
