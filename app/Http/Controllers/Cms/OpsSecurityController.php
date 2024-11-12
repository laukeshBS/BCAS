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
            $item->date_of_approval = date('d-m-Y', strtotime($item->date_of_approval));
            $item->date_of_validity = date('d-m-Y', strtotime($item->date_of_validity));
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
            'application_id' => 'required|string|max:255',
            'airport_name' => 'required|string|max:255',
            'entity_name' => 'required|string|max:255',
            'region_name' => 'required|string|max:255',
            'cso_acso_name' => 'required|string|max:500',
            'cso_acso_email' => 'required|email',
            'cso_acso_mobile' => 'required|numeric',
            'station_name' => 'required|string|max:255',
            'date_of_approval' => 'required|date',
            'status' => 'required',
            'division' => 'required',
            'sec_type' => 'required',
            'date_of_validity' => 'required|date',
            'lang_code' => 'required',
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
            'application_id','airport_name','entity_name','region_name','cso_acso_name','cso_acso_email','cso_acso_mobile','station_name','date_of_approval','status','division','sec_type','date_of_validity','lang_code',
        ]);

        // // Create new OpsSecurity record
        $opssecuritydata = OpsSecurity::create($data);

        // Return JSON response
        return response()->json(['data' => $opssecuritydata, 'message' => 'Creted successfully.'], 200);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'application_id' => 'required|string|max:255',
            'airport_name' => 'required|string|max:255',
            'entity_name' => 'required|string|max:255',
            'region_name' => 'required|string|max:255',
            'cso_acso_name' => 'required|string|max:500',
            'cso_acso_email' => 'required|email',
            'cso_acso_mobile' => 'required|numeric',
            'station_name' => 'required|string|max:255',
            'date_of_approval' => 'required|date',
            'status' => 'required',
            'division' => 'required',
            'sec_type' => 'required',
            'date_of_validity' => 'required|date',
            'lang_code' => 'required',
        ]);
        

        $opssecuritydata = OpsSecurity::find($id);

        if (!$opssecuritydata) {
            return response()->json([
                'error' => 'Not Found.'
            ], 400);
        }

        $opssecuritydata->application_id = $validated['application_id'];
        $opssecuritydata->airport_name = $validated['airport_name'];
        $opssecuritydata->entity_name = $validated['entity_name'];
        $opssecuritydata->region_name = $validated['region_name'];
        $opssecuritydata->cso_acso_name = $validated['cso_acso_name'];
        $opssecuritydata->cso_acso_email = $validated['cso_acso_email'];
        $opssecuritydata->cso_acso_mobile = $validated['cso_acso_mobile'];
        $opssecuritydata->station_name = $validated['station_name'];
        $opssecuritydata->date_of_approval = $validated['date_of_approval'];
        $opssecuritydata->status = $validated['status'];
        $opssecuritydata->division = $validated['division'];
        $opssecuritydata->sec_type = $validated['sec_type'];
        $opssecuritydata->date_of_validity = $validated['date_of_validity'];
        $opssecuritydata->lang_code = $validated['lang_code'];
        $opssecuritydata->save();

        // Return the data as JSON
        return response()->json(['data' => $opssecuritydata, 'message' => 'Updated successfully.'], 200);
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
        return response()->json(['data' => $opssecuritydata, 'message' => 'Deleted successfully.'], 200);
    }

}
