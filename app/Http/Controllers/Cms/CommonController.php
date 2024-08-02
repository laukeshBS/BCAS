<?php

namespace App\Http\Controllers\Cms;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; 
use App\Models\Cms\Common\CommonTitle;
use App\Http\Controllers\Cms\BaseController as BaseController;
class CommonController extends BaseController
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
        $commonTitle = CommonTitle::where('lang_code', $lang_code)
        //->whereIn('slugs', $slugs)
        ->select('id', 'lang_code', 'title', 'slugs')
        ->get();
        $result = $commonTitle->pluck('title', 'slugs')->toArray();
        return $this->sendResponse($result, 'Common Title  For Instructor Retrieved Successfully.');
    }
}
