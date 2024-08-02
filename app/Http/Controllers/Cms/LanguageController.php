<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Language;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Cms\BaseController as BaseController;

class LanguageController extends BaseController
{
    public function index(Request $request): JsonResponse
    {  
      
        $language = Language::all()->select('id','lang_code','name');

        return $this->sendResponse($language, 'Language List For Instructor Retrieved Successfully.');
    }
}
