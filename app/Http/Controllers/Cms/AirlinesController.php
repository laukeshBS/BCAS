<?php

namespace App\Http\Controllers\Cms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Cms\Airline;
use App\Helpers\AuditTrail;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
class AirlinesController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }


    public function airline_list(Request $request)
    {
       // dd('here');
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $airlines = Airline::select('*')
            ->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $airlines->getCollection()->transform(function ($item) {
            $item->created_at = date('d-m-Y', strtotime($item->created_at));
            return $item;
        });

        return response()->json($airlines);
    }
    public function airline_list_approved(Request $request)
    {
        // Default pagination parameters
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        // Filter parameters
        $date_of_approval = $request->input('date_of_approval');
        $air_type = $request->input('air_type');
        $station_name = $request->input('station_name');
        $entity_name = $request->input('entity_name');
        // Query to fetch approved airlines based on the provided filters
        $airlines = Airline::select('*')
        ->where('status', 'APPROVED')
        ->where('station_name', 'like', "%{$station_name}%")
        ->where('air_type', 'like', "%{$air_type}%") // Using like for partial match
        ->when($entity_name, function ($query, $entity_name) {
            return $query->where('entity_name', 'like', "%{$entity_name}%");
        })
        ->when($date_of_approval, function ($query, $date_of_approval) {
            return $query->whereRaw('YEAR(date_of_approval) = ?', [$date_of_approval]);
        })

            ->orderBy('date_of_approval', 'DESC')
            ->paginate($perPage, ['*'], 'page', $page);

        // Transforming the collection to format the created_at date
        $airlines->getCollection()->transform(function ($item) {
            $item->created_at = date('d-m-Y', strtotime($item->created_at));
            return $item;
        });

        // Returning the paginated list of airlines as a JSON response
        return response()->json($airlines);
    }

    public function data(Request $request)
    {
        $limit = $request->input('limit', 5);
        $lang_code = $request->input('lang_code');

        $data = Airline::select('*')
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
        $data = Airline::find($validatedId);

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
            'airline_name' => 'required|string|max:255',
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
            //'airline_orders' => 'required|string|max:255'
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
            'airline_name',
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
            'airline_orders'
        ]);

        // // Create new Airline record
        $airlinedata = Airline::create($data);

        // Return JSON response
        return response()->json($airlinedata, 201); // 201 Created
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
            'airline_orders' => 'required|string|max:255',
            'region_name' => 'required|string|max:255',
            'sr_no' => 'required|numeric',
            'airline_name' => 'required|string|max:255',
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


        $airlinedata = Airline::find($id);

        if (!$airlinedata) {
            return response()->json([
                'error' => 'Not Found.'
            ], 400);
        }

        $airlinedata->title = $validated['title'];
        $airlinedata->description = $validated['description'];
        $airlinedata->status = $validated['status'];
        $airlinedata->lang_code = $validated['lang_code'];
        $airlinedata->start_date = $validated['start_date'];
        $airlinedata->end_date = $validated['end_date'];
        $airlinedata->airline_orders = $validated['airline_orders'] ?? null;
        $airlinedata->region_name = $validated['region_name'] ?? null;
        $airlinedata->sr_no = $validated['sr_no'] ?? null;
        $airlinedata->airline_name = $validated['airline_name'] ?? null;
        $airlinedata->entity_name = $validated['entity_name'] ?? null;
        $airlinedata->address = $validated['address'] ?? null;
        $airlinedata->mobile_no = $validated['mobile_no'] ?? null;
        $airlinedata->phone_no = $validated['phone_no'] ?? null;
        $airlinedata->unique_reference_number = $validated['unique_reference_number'] ?? null;
        $airlinedata->approved_status_clearance = $validated['approved_status_clearance'] ?? null;
        $airlinedata->date_of_approval_clearance = $validated['date_of_approval_clearance'] ?? null;
        $airlinedata->approved_status_programme = $validated['approved_status_programme'] ?? null;
        $airlinedata->date_of_approval_programme = $validated['date_of_approval_programme'] ?? null;
        $airlinedata->valid_till = $validated['valid_till'] ?? null;
        $airlinedata->save();

        // Return the data as JSON
        return response()->json($airlinedata);
    }

    public function delete($id)
    {
        // Find the airlinedata by id
        $airlinedata = Airline::find($id);

        if (!$airlinedata) {
            return response()->json([
                'error' => 'Not Found.'
            ], 400);
        }

        // Delete the airlinedata
        $airlinedata->delete();

        // Return the data as JSON
        return response()->json($airlinedata);
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

        $query = Airline::query();

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
            'application_id'    => 'required|string|max:255',
            'entity_name'       => 'required|string|max:255',
            'cso_acso_name'     => 'required|string|max:255',
            'cso_acso_email'    => 'required|email|max:255',
            'station_name'      => 'required|string',
            'date_of_approval'  => 'required|date',  // Ensure date format
            'status'            => 'required|string|max:255',
            'air_type'          => 'required|string|max:255',
            'date_of_validity'  => 'required|date',  // Ensure date format
            'lang_code'         => 'required|string|max:10',  // Make sure it's a string with a reasonable length
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
            'application_id', 'entity_name', 'cso_acso_name', 'cso_acso_email',
            'station_name', 'date_of_approval', 'status', 'air_type',
            'date_of_validity', 'lang_code'
        ]);

        // Create new Airline record
        try {
            $airlinedata = Airline::create($data);
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
            'application_id'    => 'required|string|max:255',
            'entity_name'       => 'required|string|max:255',
            'cso_acso_name'     => 'required|string|max:255',
            'cso_acso_email'    => 'required|email|max:255',
            'station_name'      => 'required|string',
            'date_of_approval'  => 'required|date',  // Ensure date format
            'status'            => 'required|string|max:255',
            'air_type'          => 'required|string|max:255',
            'date_of_validity'  => 'required|date',  // Ensure date format
            'lang_code'         => 'required|string|max:10',  // Make sure it's a string with a reasonable length
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
        $airline = Airline::find($id);

        if (!$airline) {
            return response()->json([
                'error' => 'Record not found.',
                'message' => 'The requested airline record does not exist.'
            ], 404);  // 404 Not Found
        }

        // Prepare data for update
        $data = $request->only([
            'application_id', 'entity_name', 'cso_acso_name', 'cso_acso_email',
            'station_name', 'date_of_approval', 'status', 'air_type',
            'date_of_validity', 'lang_code'
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
        $airlinedata = Airline::find($id);

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
