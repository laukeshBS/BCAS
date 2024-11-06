<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\File;
use Illuminate\Http\Request;
use App\Models\Admin\Document;
use GPBMetadata\Google\Api\Auth;
use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Crypt;
use App\Models\Admin\DocumentCategory;
use App\Models\Admin\SecurityQuestion;
use Illuminate\Support\Facades\Session;
use App\Models\Admin\UserSecurityAnswer;
use Illuminate\Support\Facades\Validator;

class SecurityQuestionController extends Controller
{
    public $user;

    public function index(Request $request)
    {
    }

    // API
    public function questions(Request $request)
    {
        $data = SecurityQuestion::select('id','question')->get();
        return response()->json($data);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required',
            'security_questions_id' => 'required',
            'answer' => 'required|min:2|max:255',
        ]);


        $data = new UserSecurityAnswer(); // Assuming you have a Document model
        $data->user_id = $validated['user_id'];
        $data->security_questions_id = $validated['security_questions_id'];
        $data->answer = $validated['answer'];

        $data->save();

        return response()->json(['data' => $data, 'message' => 'Stored successfully.'], 201);
    }

    public function reRegister(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email', // Validate the email and check if it exists in the users table
            'questions' => 'required|array', // Questions should be an array
            'questions.*.questionId' => 'required|exists:security_questions,id', // Validate that the question ID exists in the security_questions table
            'questions.*.answer' => 'required|string|min:3', // Ensure each answer is at least 3 characters long
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        // Extract email and questions from request
        $email = $request->input('email');
        $questions = $request->input('questions');

        // Find the user by email
        $user = Admin::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        // Check if the answers are correct for each question
        foreach ($questions as $questionData) {
            $question = SecurityQuestion::find($questionData['questionId']);

            if (!$question) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid security question.'
                ], 400);
            }

            // Assuming you have a method to check the answer (you can hash or encrypt answers)
            if ($question->answer !== $questionData['answer']) {
                return response()->json([
                    'success' => false,
                    'message' => 'One or more answers are incorrect.'
                ], 400);
            }
        }

        // All checks passed, proceed to re-registration (e.g., reset password, etc.)
        // You can update the user's information here if necessary

        return response()->json([
            'success' => true,
            'message' => 'Re-registration successful!'
        ], 200);
    }

    public function delete($id)
    {
        $data = UserSecurityAnswer::find($id);

        if (!$data) {
            return $this->sendError('No data found.', 404);
        }
        $data->delete();

        return response()->json(['data' => $data, 'message' => 'Deleted successfully.'], 201);
    }
    

}
