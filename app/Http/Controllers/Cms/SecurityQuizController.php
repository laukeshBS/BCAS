<?php

namespace App\Http\Controllers\Cms;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Models\Cms\SecurityQuiz;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class SecurityQuizController  extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    public function quiz_list(Request $request)
    {
        $lang= $request->lang_code;
        // Fetching the quiz data from the database
        $data = SecurityQuiz::where('lang_code',$lang)->inRandomOrder()->limit(10)->get();
    
        // Transforming the data to match the desired format
        $formattedData = $data->map(function($quiz) {
            return [
                'question' => $quiz->question,
                'options' => [
                    $quiz->A,
                    $quiz->B,
                    $quiz->C,
                    $quiz->D
                ],
                'answer' => $quiz->answer // In this case 'A', 'B', 'C', or 'D'
            ];
        });
    
        // Returning the formatted data as JSON
        return response()->json($formattedData);
    }

    // SecurityQuiz
    public function data(Request $request)
    {
        $data = SecurityQuiz::get();

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

        $data = SecurityQuiz::find($validatedId);

        if (!$data) {
            return response()->json([
                'error' => 'Data not found'
            ], 404);
        }
        $data->optionA=$data->A;
        $data->optionB=$data->B;
        $data->optionC=$data->C;
        $data->optionD=$data->D;
        $data->created_at = date('d-m-Y', strtotime($data->created_at));

        return response()->json($data);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'status' => 'required',
        ]);

        $region = new SecurityQuiz();
        $region->name = $validated['name'];
        $region->status = $validated['status'];
        $region->save();
        
        return response()->json($region);
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required',
            'status' => 'required',
        ]);

        $region = SecurityQuiz::find($id);

        if (!$region) {
            return response()->json([
                'error' => 'Data Not Found.'
            ], 400);
        }

        $region->name = $validated['name'];
        $region->status = $validated['status'];
        $region->save();

        return response()->json($region);
    }
    public function delete($id)
    {
        $region = SecurityQuiz::find($id);

        if (!$region) {
            return response()->json([
                'error' => 'Data Not Found.'
            ], 400);
        }
        $region->delete();

        return response()->json($region);
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

        $query = SecurityQuiz::query();

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
            'question'    => 'required|string|max:255',
            'A'       => 'required|string|max:255',
            'B'     => 'required|string|max:255',
            'C'    => 'required|string|max:255',
            'D'      => 'required|string|max:255',
            'answer'  => 'required|string|max:255',
            'quizs_type'            => 'required|string|max:255',
            'lang_code'          => 'required|string|max:10',
            'positions'  => 'required|string|max:255',
            'status'         => 'required',
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
            'question','A','B','C','D','answer','quizs_type','lang_code','positions','status'
        ]);

        // Create new Airline record
        try {
            $airlinedata = SecurityQuiz::create($data);
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
            'question'    => 'required|string|max:255',
            'A'       => 'required|string|max:255',
            'B'     => 'required|string|max:255',
            'C'    => 'required|string|max:255',
            'D'      => 'required|string|max:255',
            'answer'  => 'required|string|max:255',
            'quizs_type'            => 'required|string|max:255',
            'lang_code'          => 'required|string|max:10',
            'positions'  => 'required|string|max:255',
            'status'         => 'required',
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
        $airline = SecurityQuiz::find($id);

        if (!$airline) {
            return response()->json([
                'error' => 'Record not found.',
                'message' => 'The requested airline record does not exist.'
            ], 404);  // 404 Not Found
        }

        // Prepare data for update
        $data = $request->only([
            'question','A','B','C','D','answer','quizs_type','lang_code','positions','status'
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
        $airlinedata = SecurityQuiz::find($id);

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
