<?php

namespace App\Http\Controllers\Cms;

use App\Models\Admin;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Models\Cms\SecurityQuiz;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

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
}
