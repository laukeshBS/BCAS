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
}
