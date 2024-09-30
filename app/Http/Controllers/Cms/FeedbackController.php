<?php

namespace App\Http\Controllers\Cms;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; 
use App\Models\Cms\Feedback;

use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Cms\BaseController as BaseController;
class FeedbackController extends BaseController
{
    public function index(Request $request): JsonResponse
    {  
        $data = $request->all();
        $lang_code = $data['lang_code'];
       // $slugs = explode(',',$data['slugs']);

        if ($lang_code === null) {
            return $this->sendError('Lang code parameter is missing.', 400);
        }
        //dd($slugs);
        // if ($slugs === null) {
        //     return $this->sendError('Slugs parameter is missing.', 400);
        // }
        $Feedback = Feedback::where('lang_code', $lang_code)
        //->whereIn('slugs', $slugs)
        ->select('id', 'firstName','lastName','email','phoneNumber','lang_code','user_ip','suggestions','status')
        ->get();
        
        return $this->sendResponse($Feedback, 'Feedback  For Instructor Retrieved Successfully.');
    }
    public function store(Request $request)
    {
       // dd($request);
        $rules = [
            'firstName' => 'required|max:32',
            'lastName' => 'required|max:32',
            'email' => 'required|email|max:150',
            'phoneNumber' => 'required',
            'lang_code' => 'required|max:5',
            //'user_ip' => 'required',
            'suggestions' => 'nullable|max:500',
            //'status' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error.',
                'messages' => $validator->errors()
            ], 422); // 422 Unprocessable Entity
        }
       
        $data = $request->only([
            'firstName', 
            'lastName', 
            'email', 
            'phoneNumber', 
            'lang_code', 
            'user_ip', 
            'suggestions', 
            'status',
            
        ]);
        $Feedbackdata = Feedback::create($data);
        $messages='';
        if($Feedbackdata){
          $messages='Thank you';
        }
        return response()->json($messages, 201); // 201 Created
    }
    
}
