<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\File;
use App\Models\Admin\Admin;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Admin\Document;
use GPBMetadata\Google\Api\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
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
            'email' => 'required|email', // Validate the email and check if it exists in the users table
            'questions' => 'required|array', // Questions should be an array
            'questions.*.questionId' => 'required', // Validate that the question ID exists in the security_questions table
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

        // Store the answers in the database
        foreach ($questions as $questionData) {
            $question = SecurityQuestion::find($questionData['questionId']);

            if (!$question) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid security question.'
                ], 400);
            }

            // Store the answer (hash the answer for security reasons)
            UserSecurityAnswer::create([
                'user_id' => $user->id,
                'security_questions_id' => $question->id,
                'answer' => $questionData['answer'], // Hashing the answer for security
            ]);
        }
        // // Generate a random password (e.g., 12 characters long)
        // $randomPassword = Str::random(12); // Generates a 12-character random string

        // // Hash the password before updating the user's record
        // $hashedPassword = Hash::make($randomPassword);
        $hashedPassword = Hash::make('admin@123');

        // Update the user's password
        $user->password = $hashedPassword;
        $user->status = 2;
        $user->save();

        // Optionally, send the new password to the user via email
        // Mail::to($user->email)->send(new PasswordResetMail($newPassword));

        // All checks passed, proceed to re-registration (e.g., reset password, etc.)
        return response()->json([
            'success' => true,
            'message' => 'Re-registration successful!'
        ], 200);
    }

    public function forgotPassword(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email', // Validate the email address
            'questions' => 'required|array', // Questions should be an array
            'questions.*.questionId' => 'required', // Validate that the question ID exists
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

        // Verify answers to the security questions
        foreach ($questions as $questionData) {
            $question = SecurityQuestion::find($questionData['questionId']);
            $storedAnswer = UserSecurityAnswer::where('user_id', $user->id)
                                            ->where('security_questions_id', $question->id)
                                            ->orderBy('id','desc')
                                            ->first();

            if (!$storedAnswer || $questionData['answer'] !== $storedAnswer->answer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Incorrect answer to security question.'
                ], 400);
            }
        }

        // Generate OTP (6-digit number)
        $otp = mt_rand(100000, 999999); // Simple OTP generation

        // Store OTP temporarily in the database or cache for verification (e.g., using Redis or the database)
        $user->otp = $otp;
        $user->save();

        // Send OTP via external API (assuming it's an SMS or other notification)
        $apiUrl = "https://pgapi.smartping.ai/fe/api/v1/send?username=cscetrpg6.trans&password=LuRXO&unicode=false&from=BCASRM&to=8302649083&dltPrincipalEntityId=1401665390000071833&dltContentId=1407173021512730911&text=".$otp."";
        $response = Http::post($apiUrl, [
            'message' => "Your OTP for password reset is: $otp",
            'recipient' => $user->email,  // You can change this to 'phone' if it's for SMS
        ]);

        // Check if the request was successful
        if ($response->successful()) {
            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully to your phone.',
            ],200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again later.',
            ], 500);
        }
    }
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric'
        ]);

        // Find user by email
        $user = Admin::where('email', $request->email)->first();

        // Check if OTP matches and if it's still valid
        if ($user->otp != $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP or OTP expired.'
            ], 400);
        }

        // // Generate a new random password (12 characters long)
        // $newPassword = Str::random(12);

        // // Hash the new password
        // $hashedPassword = Hash::make($newPassword);
        $hashedPassword = Hash::make('admin@1234');

        // Update the user's password
        $user->password = $hashedPassword;
        $user->otp = null;
        $user->save();

        // OTP is valid, allow user to reset password
        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully.'
        ]);
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
