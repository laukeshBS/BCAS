<?php

namespace App\Http\Controllers\Cms;

use App\Models\Admin;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Models\Cms\QuizResult;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class QuizResultController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    // QuizResult
    public function data(Request $request)
    {
        $data = QuizResult::get();

        $data->transform(function ($item) {
            $item->created_at = date('d-m-Y', strtotime($item->created_at));
            return $item;
        });

        return response()->json($data);
    }
    public function result_list(Request $request)
{
    // Fetching the result data from the database
    $data = QuizResult::inRandomOrder()->limit(10)->get();

    // Transforming the data to match the desired format
    $formattedData = $data->map(function($result) {
        return [
            'question' => $result->question,
            'options' => [
                $result->A,
                $result->B,
                $result->C,
                $result->D
            ],
            'answer' => $result->answer // In this case 'A', 'B', 'C', or 'D'
        ];
    });

    // Returning the formatted data as JSON
    return response()->json($formattedData);
}

    public function data_by_id($id)
    {
        $validatedId = filter_var($id, FILTER_VALIDATE_INT);
        if (!$validatedId) {
            return response()->json([
                'error' => 'Invalid ID format'
            ], 400);
        }

        $data = QuizResult::find($validatedId);

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
        // Define validation rules
      
        $rules = [
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:15',
            'score' => 'required|integer',
            'total' => 'required|integer',
        ];
        
        // Validate the request input
        $validator = Validator::make($request->all(), $rules);
    
        // Handle validation errors
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error.',
                'messages' => $validator->errors()
            ], 422); // 422 Unprocessable Entity
        }
        dd($request);
        // Clean and sanitize the validated data
        $validated = $validator->validated();
        
        // Store the quiz result
        $result = new QuizResult();
        $result->fullname = clean_single_input(strip_tags($validated['fullname']));
        $result->email = clean_single_input(strip_tags($validated['email']));
        $result->phone = clean_single_input(strip_tags($validated['phone']));
        $result->score = clean_single_input(strip_tags($validated['score']));
        $result->total = clean_single_input(strip_tags($validated['total']));
        $result->save();
    
        // Check if the result is saved successfully
        if ($result) {
            $responseMessage['message'] = "Thank You";
        } else {
            $responseMessage['error'] = "Failed to save the result";
        }
    
        // Return the response as JSON
        return response()->json($responseMessage);
    }
    
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'fullname' => 'required',
            //'status' => 'required',
        ]);

        $result = QuizResult::find($id);

        if (!$result) {
            return response()->json([
                'error' => 'Data Not Found.'
            ], 400);
        }

        $result->name = $validated['name'];
        $result->status = $validated['status'];
        $result->save();

        return response()->json($result);
    }
    public function delete($id)
    {
        $result = QuizResult::find($id);

        if (!$result) {
            return response()->json([
                'error' => 'Data Not Found.'
            ], 400);
        }
        $result->delete();

        return response()->json($result);
    }
}
