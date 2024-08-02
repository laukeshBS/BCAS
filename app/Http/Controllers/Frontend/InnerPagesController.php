<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Support\Str;
use App\Models\Cms\Menu;
use Session;

class InnerPagesController extends Controller
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
    public function index($slug="",Request $request)
    {   
        $slug= clean_single_input($slug);
        $title=''; $id='';$menu_child_id=''; $menu_url='';$chtitle='';$data='';
        if($slug=='home'){
            return redirect('/');  
        }
        $langid = Session::get('locale') ?? app()->getLocale();
        $data =  Menu::where('menu_url', 'LIKE', "%{$slug}%")->where('approve_status',3)->where('language_id', $langid)->select('id','menu_type','menu_child_id','menu_position','language_id','menu_name','menu_url','menu_title','menu_keyword','menu_description','content','menu_links','approve_status','page_order','welcomedescription')->first();
        if(!empty($data)){
            $title=$data->menu_name;
        }
        //dd($data );
        return response()->view("frontend/pages/innerpages", compact( 'data','title','id','menu_child_id','menu_url','chtitle'));

    }
     
}
