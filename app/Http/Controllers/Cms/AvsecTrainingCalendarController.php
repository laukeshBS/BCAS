<?php
namespace App\Http\Controllers\Cms;

use App\Models\Cms\AvsecTrainingCalendar;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

class AvsecTrainingCalendarController  extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    /**
     * Display a listing of the training calendars.
     */
    public function index(Request $request): View
    {
        // if (is_null($this->user) || !$this->user->can('training-calendar.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized to view any Training Calendar!');
        // }

        $title = "Training Calendar List";
        $calendars = AvsecTrainingCalendar::orderBy('created_at', 'ASC')->paginate(10);
       // dd($calendars);
        return view('cms.avsec_training_calendar.index', compact('calendars', 'title'));
    }

    /**
     * Show the form for creating a new training calendar.
     */
    public function create()
    {
        // if (is_null($this->user) || !$this->user->can('training-calendar.create')) {
        //     abort(403, 'Sorry !! You are Unauthorized to create a Training Calendar!');
        // }

        $title = "Add Training Calendar";
        return view('cms.avsec_training_calendar.add', compact('title'));
    }

    /**
     * Store a newly created training calendar in storage.
     */
    public function store(Request $request)
    {
        // Define validation rules
        $rules = [
            'avSec_training' => 'required',
            'January' => 'nullable',
            'February' => 'nullable',
            'March' => 'nullable',
            'April' => 'nullable',
            'May' => 'nullable',
            'June' => 'nullable',
            'July' => 'nullable',
            'August' => 'nullable',
            'September' => 'nullable',
            'October' => 'nullable',
            'November' => 'nullable',
            'December' => 'nullable',
            'positions' => 'nullable',
            'remarks' => 'nullable',
            'lang_code' => 'required',
            'status' => 'required',
        ];
    
        // Custom validation messages
        $validations = [
            'avSec_training.required' => 'Training name is required',
            'lang_code.required' => 'Language code is required',
            'status.required' => 'Status is required',
        ];
    
        // Validate request
        $validator = Validator::make($request->all(), $rules, $validations);
    
        // Handle validation failure
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        } else {
            // Create a new calendar entry
            $calendar = AvsecTrainingCalendar::create($request->only([
                'avSec_training',
                'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'December',
                'positions',
                'remarks',
                'lang_code',
                'status',
            ]));
    
            // Redirect back with success message
            return redirect()->route('admin.training-calendars.index')->with('success', 'Training Calendar has been successfully added');
        }
    }
    

    /**
     * Show the form for editing the specified training calendar.
     */
    public function edit(string $id)
    {
        // if (is_null($this->user) || !$this->user->can('training-calendar.edit')) {
        //     abort(403, 'Sorry !! You are Unauthorized to edit this Training Calendar!');
        // }

        $data= AvsecTrainingCalendar::findOrFail($id);
        $title = "Edit Training Calendar";

        return view('cms.avsec_training_calendar.edit', compact('data', 'title'));
    }

    /**
     * Update the specified training calendar in storage.
     */
    public function update(Request $request, string $id)
    {
        // Define validation rules
        $rules = [
            'avSec_training' => 'required',
            'January' => 'nullable',
            'February' => 'nullable',
            'March' => 'nullable',
            'April' => 'nullable',
            'May' => 'nullable',
            'June' => 'nullable',
            'July' => 'nullable',
            'August' => 'nullable',
            'September' => 'nullable',
            'October' => 'nullable',
            'November' => 'nullable',
            'December' => 'nullable',
            'positions' => 'nullable',
            'remarks' => 'nullable',
            'lang_code' => 'required',
            'status' => 'required',
        ];
    
        // Custom validation messages
        $validations = [
            'avSec_training.required' => 'Training name is required',
            'lang_code.required' => 'Language code is required',
            'status.required' => 'Status is required',
        ];
    
        // Validate request
        $validator = Validator::make($request->all(), $rules, $validations);
    
        // Handle validation failure
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        } else {
            // Find the calendar entry
            $calendar = AvsecTrainingCalendar::findOrFail($id);
            
            // Update the calendar entry
            $calendar->update($request->only([
                'avSec_training',
                'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'December',
                'positions',
                'remarks',
                'lang_code',
                'status',
            ]));
    
            // Redirect back with success message
            return redirect()->route('admin.training-calendars.index')->with('success', 'Training Calendar has been successfully updated');
        }
    }
    

    /**
     * Remove the specified training calendar from storage.
     */
    public function destroy(string $id)
    {
        // if (is_null($this->user) || !$this->user->can('training-calendar.delete')) {
        //     abort(403, 'Sorry !! You are Unauthorized to delete this Training Calendar!');
        // }

        $calendar = AvsecTrainingCalendar::findOrFail($id);
        $calendar->delete();

        return redirect()->route('admin.training-calendars.index')->with('success', 'Training Calendar has been successfully deleted');
    }
    public function avsecTrainingCalendar_list_approved(Request $request)
    {
        // Default pagination parameters
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $lang_code = $request->input('lang_code');
        // Filter parameters
        $division = $request->input('division');
        $sec_type = $request->input('sec_type');
    
        // Query to fetch approved AvsecTrainingCalendar based on the provided filters
        $AvsecTrainingCalendar = AvsecTrainingCalendar::select('*')
        ->where('status', '3')
        ->where('lang_code', $lang_code) // Using like for partial match
         ->orderBy('positions', 'ASC')
        ->paginate($perPage, ['*'], 'page', $page);

        // Transforming the collection to format the created_at date
        $AvsecTrainingCalendar->getCollection()->transform(function ($item) {
            $item->created_at = date('d-m-Y', strtotime($item->created_at));
            return $item;
        });
        //dd($AvsecTrainingCalendar);
        // Returning the paginated list of AvsecTrainingCalendar as a JSON response
        return response()->json($AvsecTrainingCalendar);
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
        $data = AvsecTrainingCalendar::find($validatedId);

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

    // CMS Api
    public function cms_data(Request $request)
    {
        $request->validate([
            'limit' => 'required|integer',
            'currentPage' => 'required|integer',
        ]);

        $perPage = $request->input('limit');
        $page = $request->input('currentPage');

        $query = AvsecTrainingCalendar::query();

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
            'avSec_training'    => 'required|string|max:255',
            'January'  => 'nullable|string|max:255',
            'February' => 'nullable|string|max:255',
            'March'    => 'nullable|string|max:255',
            'April'    => 'nullable|string|max:255',
            'May'  => 'nullable|string|max:255',
            'June'    => 'nullable|string|max:255',
            'July'    => 'nullable|string|max:255',
            'August'  => 'nullable|string|max:255',
            'September' => 'nullable|string|max:255',
            'October'    => 'nullable|string|max:255',
            'November'    => 'nullable|string|max:255',
            'December'  => 'nullable|string|max:255',
            'status'    => 'required|string|max:255',
            'remarks'    => 'nullable|string|max:255',
            'positions'  => 'required',
            'lang_code' => 'required|string|max:10',
        ];

        $request->merge(['created_by' => auth()->id()]);

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
            'avSec_training','January','February','March','April','May','June','July','August','September','October','November','December','status','remarks','positions','lang_code','created_by',
        ]);

        // Create new Airline record
        try {
            $airlinedata = AvsecTrainingCalendar::create($data);
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
            'avSec_training'    => 'required|string|max:255',
            'January'  => 'nullable|string|max:255',
            'February' => 'nullable|string|max:255',
            'March'    => 'nullable|string|max:255',
            'April'    => 'nullable|string|max:255',
            'May'  => 'nullable|string|max:255',
            'June'    => 'nullable|string|max:255',
            'July'    => 'nullable|string|max:255',
            'August'  => 'nullable|string|max:255',
            'September' => 'nullable|string|max:255',
            'October'    => 'nullable|string|max:255',
            'November'    => 'nullable|string|max:255',
            'December'  => 'nullable|string|max:255',
            'status'    => 'required|string|max:255',
            'remarks'    => 'nullable|string|max:255',
            'positions'  => 'required',
            'lang_code' => 'required|string|max:10',
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
        $airline = AvsecTrainingCalendar::find($id);

        if (!$airline) {
            return response()->json([
                'error' => 'Record not found.',
                'message' => 'The requested record does not exist.'
            ], 404);  // 404 Not Found
        }

        // Prepare data for update
        $data = $request->only([
            'avSec_training','January','February','March','April','May','June','July','August','September','October','November','December','status','remarks','positions','lang_code'
        ]);

        // Update the existing Airline record
        try {
            $airline->update($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error updating the record.',
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
        $airlinedata = AvsecTrainingCalendar::find($id);

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
