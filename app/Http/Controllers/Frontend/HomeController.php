<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Support\Str;
use App\Models\Cms\Menu;
use Session;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

   

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $title="Home";
        $abtdata="";
        $langid = Session::get('locale') ?? app()->getLocale();
        $abtdata1 =  Menu::whereIn('id', [46, 63])->where('language_id', $langid)->select('id','menu_type','menu_child_id','menu_position','language_id','menu_name','menu_url','menu_title','menu_keyword','menu_description','content','menu_links','approve_status','page_order','welcomedescription')->first();
        // dd( $abtdata1 );
        if(!empty($abtdata1)){
            $abtdata=$abtdata1;
        }
        return view('frontend/pages/home', compact('title','abtdata'));

    }
    public function changeLanguage(Request $request)
    {
        $request->validate([
            'lang' => 'required|string|in:en,hi', // Add more languages as needed
        ]);

        session()->put('locale', $request->lang);

        return response()->json(['success' => true]);
    }
     
}
