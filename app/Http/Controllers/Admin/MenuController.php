<?php

namespace App\Http\Controllers\Admin;
use App\Models\Cms\Menu;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    /**
     * Display a listing of the Menu.
     */
    public $user;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }
    public function index(Request $request): View
    {
        if (is_null($this->user) || !$this->user->can('menu.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any menu !');
        }

        $parentMenu="";
            $title="Menu List";
            $approve_status=session()->get('approve_status');
            $sertitle=Session::get('Mtitle');
            $approve_status=Session::get('approve_status');
            $language_id=Session::get('language_id');
           
            if (!empty($sertitle)) {
                $lists = Menu::whereNotNull('menu_title');
                $lists->where('menu_title', 'LIKE', "%{$sertitle}%");
            }else{
                $lists = Menu::where('menu_child_id',0);
            }
            if (!empty($approve_status)) {
               
                $lists->where('approve_status',$approve_status);
            }
            if (!empty($language_id)) {
               
                $lists->where('language_id',$language_id);
            }
            $list=$lists->whereNull('deleted_at')->orderBy('page_order', 'ASC')->select('id','menu_type','menu_child_id','menu_position','language_id','menu_name','menu_url','menu_title','menu_keyword','menu_description','content','doc_upload','menu_links','approve_status','page_order','welcomedescription')->paginate(10);
        return view('admin/pages/menus/menu',compact(['list','title','parentMenu']));
    }

    /**
     * Show the form for creating a new Menu.
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('menu.create')) {
            abort(403, 'Sorry !! You are Unauthorized to view any menu !');
        }
        $title="Add Menu";
        
        return view('admin/pages/menus/add_menu',compact(['title']));
    }

    /**
     * Store a newly created Menu in storage.
     */
    public function store(Request $request): mixed {
     //dd($request);
        if(isset($request->search)){
            $Mtitle=clean_single_input(trim($request->title));
             $approve_status=clean_single_input($request->approve_status);
             $language_id=clean_single_input($request->language_id);
             Session::put('Mtitle', $Mtitle);
             Session::put('approve_status', $approve_status);
             Session::put('language_id', $language_id);
             return redirect('admin/menus');
           }
        if(isset($request->cmdsubmit)){  
         $txtupload1 ='';
         $rules = array(
            'menu_title' => 'required|max:64',
            'url' => 'required|max:64',
            'language' => 'required',
            'menutype' => 'required',
            'txtposition' => 'required',
            'txtstatus' => 'required',
            'welcomedescription' => 'required|max:120'
        );
        $valid
        =array(
             'menutype.required'=>'Menu type field  is required',
             'txtposition.required'=>'Menu position  field  is required',
             'welcomedescription.required'=>'Short description  field  is required',
             'txtstatus.required' =>'Menu Status field is required',
             'url.required' =>'Slug field is required',
             'menu_title.required' =>'Menu title field is required',
        );
        $validator = '';
        $img_upload1="";
        if($request->menutype == 1){
            $rules = array(
                'description' => 'required',
                // 'metakeyword' => 'required|max:64',
                // 'metadescription' => 'required|max:250'
            );
            $valid
            =array(
                 'description.required'=>'Description field  is required',
                //  'metakeyword.required'=>'Meta keyword  field  is required',
                //  'metadescription.required'=>'Meta description  field  is required',
                 
                 
    
            );
         
            $validator = Validator::make($request->all(), $rules,$valid);
           
            if (!empty($request->img_upload)){

                if (!is_dir('public/uploads/admin/cmsfiles/menus/')) {
                    mkdir('public/uploads/admin/cmsfiles/menus/', 0777, TRUE);
                }
                
                    // $rulesdsad = array(
                    //     'img_upload' => 'required|mimes:jpg,jpeg,png,svg|max:2048',
                    // );
                    $img_upload1 = str_replace(' ','_',clean_single_input($request->menu_title)).'menu'.'.'.$request->img_upload->extension();  
            
                    $res= $request->img_upload->move(public_path('uploads/admin/cmsfiles/menus/'), $img_upload1);
                    
                    
                    if($res){
                        $img_upload1 =$img_upload1;
                    }
                    $img_upload2 ='uploads/admin/cmsfiles/menus/'.$img_upload1; //die();
                    
                    if (file_exists($img_upload2)) {
                        unlink($img_upload2);
                    }
                       // $validator = Validator::make($request->all(), $rulesdsad);
			}
		}elseif($request->menutype == 2){
			if (!empty($request->txtupload)){

                if (!is_dir('public/uploads/admin/cmsfiles/menus/')) {
                    mkdir('public/uploads/admin/cmsfiles/menus/', 0777, TRUE);
                }
                
                    $rulesdsad = array(
                        'txtupload' => 'required|mimes:pdf,xlx,csv|max:2048',
                    );
                    $valid
                        =array(
                            'txtupload.required'=>'Files field  is required',
                            
                
                        );
                    $txtupload1 = str_replace(' ','_',clean_single_input($request->menu_title)).'menu'.'.'.$request->txtupload->extension();  
            
                    $res= $request->txtupload->move(public_path('uploads/admin/cmsfiles/menus/'), $txtupload1);
                    
                    
                    if($res){
                        $txtupload1 =$txtupload1;
                    }
                    $txtupload2 ='uploads/admin/cmsfiles/menus/'.$txtupload1; //die();
                    
                    if (file_exists($txtupload2)) {
                        unlink($txtupload2);
                    }
                        $validator = Validator::make($request->all(), $rulesdsad,$valid);
			}
		}elseif($request->menutype == 3){
            $rules = array(
                'txtweblink' => 'required|max:164'
            );
            $valid
            =array(
                'txtweblink.required'=>'URl field  is required',
                
    
            );
			   
            $validator = Validator::make($request->all(), $rules,$valid);
        }
            $validator = Validator::make($request->all(), $rules,$valid);
        
        if ($validator->fails()) {
      
            return redirect('admin/menus/create')->withErrors($validator)->withInput();
            
        }else{
            
            $user_login_id=Auth::guard('admin')->user()->id;
            $dataArr = array(); 
           //dd $banner_img="";
            if (!empty($request->banner_img)){
                if (!is_dir('uploads/admin/cmsfiles/menus/')) {
                    mkdir('uploads/admin/cmsfiles/menus/', 0777, TRUE);
                }
                    $rulesdsad = array(
                        'banner_img' => 'required|mimes:pdf,xlx,csv|max:2048',
                    );
                    $randomString = Str::random(4);
                    $banner_img = str_replace(' ','_',clean_single_input($request->menu_title.$randomString)).'menu'.'.'.$request->banner_img->extension();  
                    $res= $request->banner_img->move(public_path('uploads/admin/cmsfiles/menus/'), $banner_img);
                    if($res){
                        $banner_img =$banner_img;
                    }
                    $banner_img2 ='uploads/admin/cmsfiles/menus/'.$banner_img; //die();
                    
                    if (file_exists($banner_img2)) {
                        unlink($banner_img2);
                    }
                        $validator = Validator::make($request->all(), $rulesdsad);
                        $pArray['banner_img']  				    = clean_single_input($banner_img);
			}

            $pArray['menu_name']    				= clean_single_input(strip_tags($request->menu_title)); 
			$pArray['menu_url']  					= Str::slug(clean_single_input($request->url));
			$pArray['language_id']    			    = clean_single_input($request->language);
			$pArray['menu_child_id']    			= clean_single_input(!empty($request->menucategory)?$request->menucategory:0);
			$pArray['menu_type']  					= clean_single_input($request->menutype);
			$pArray['menu_title']  					=  $request->menu_title;
			$pArray['menu_keyword']    				= clean_single_input($request->metakeyword);
			$pArray['welcomedescription']  	        = $request->welcomedescription;
			$pArray['menu_description']				= clean_single_input($request->metadescription);
			$pArray['content']    					= $request->description; //clean_single_input($request->description);
			$pArray['img_upload']    				= clean_single_input($img_upload1);
            $pArray['doc_upload']  				    = clean_single_input($txtupload1);
			$pArray['menu_links']    				= $request->txtweblink;
			$pArray['created_by']  					= clean_single_input($user_login_id);
			$pArray['approve_status']  			    = clean_single_input($request->txtstatus);
			$pArray['menu_position']  		        =clean_single_input($request->txtposition);
			$pArray['current_version']  			= 0;
           
            $create 	= Menu::create($pArray);
           
            $lastInsertID = $create->id;
            $user_login_id=Auth::guard('admin')->user()->id;
            $action_by_role=Auth::guard('admin')->user()->username;
            if($lastInsertID > 0){
                $logs_data = array(
                    'module_item_title'     => $request->menu_title,
                    'module_item_id'        => $lastInsertID,
                    'action_by'             =>  $user_login_id,
                    'old_data'              =>  json_encode($pArray),
                    'new_data'              =>  json_encode($pArray),
                    'action_name'           =>  'Add Menu',
                    'lang_id'               =>  clean_single_input($request->language),
                    'action_type'        	=> 'Menu Model',
                    'approve_status'        => clean_single_input($request->txtstatus),
                    'action_by_role'        =>  $action_by_role
                );
                audit_trails($logs_data);
                return redirect('admin/menus')->with('success','Menu has successfully added');
			}
           
        }
    }
      
    }

    /**
     * Display the specified Menu.
     */
    public function show(string $id)
    {
        if (is_null($this->user) || !$this->user->can('menu.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any menu !');
        }
        $title="Child Menu List";
        $whEre  = "";
        $id=clean_single_input($id);
        $parentMenu = Menu::where('id', $id)->first();

        $list = Menu::withTrashed()->whereNull('deleted_at')->where('menu_child_id',$id)->paginate(10);
         return view('admin/pages/menus/menu',compact(['list','title','id','parentMenu']));
    }

    /**
     * Show the form for editing the specified Menu.
     */
    public function edit(string $id)
    {
        if (is_null($this->user) || !$this->user->can('menu.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to view any menu !');
        }
        $id=clean_single_input($id);
        $title="Edit Menu";
        $data = Menu::find($id);
        return view('admin/pages/menus/edit_menu',compact(['title','data']));
    }

    /**
     * Update the specified Menu in storage. update
     */
    public function update(Request $request, string $id)
    {
        $id= clean_single_input($id);
        $txtupload1 ='';
        $rules = array(
            //'menu_title' => 'required|max:64',
            //'url' => 'required|max:64',
            'language' => 'required',
            'menutype' => 'required',
            'txtposition' => 'required',
            'txtstatus' => 'required',
            'welcomedescription' => 'required|max:120'
        );
        $valid
        =array(
             'menutype.required'=>'Menu type field  is required',
             'txtposition.required'=>'Content position  field  is required',
             'welcomedescription.required'=>'Short description  field  is required',
             'txtstatus.required' =>'Pages Status field is required',
             'url.required' =>'Slug field is required',
             'menu_title.required' =>'Menu title field is required',

        );
        $validator = '';
        if($request->menutype == 1){
            $rules = array(
                'description' => 'required',
                // 'metakeyword' => 'required|max:155',
                // 'metadescription' => 'required|max:250'
            );
            $valid
            =array(
                 'description.required'=>'Description field  is required',
                //  'metakeyword.required'=>'Meta keyword  field  is required',
                //  'metadescription.required'=>'Meta description  field  is required'
                 
    
            );
            $validator = Validator::make($request->all(), $rules,$valid);
          
            if (!empty($request->img_upload)){

                if (!is_dir('public/uploads/admin/cmsfiles/menus/')) {
                    mkdir('public/uploads/admin/cmsfiles/menus/', 0777, TRUE);
                }
                
                //    $rulesdsad = array(
                //        'img_upload' => 'required|mimes:jpg,png,svg,jpeg|max:2048',
                //    );
                   $img_upload1 = str_replace(' ','_',clean_single_input($request->menu_title)).'menu'.'.'.$request->img_upload->extension();  
           
                    $res= $request->img_upload->move(public_path('uploads/admin/cmsfiles/menus/'), $img_upload1);
                  
                   
                    if($res){
                       $img_upload1 =$img_upload1;
                    }
                    $img_upload2 ='uploads/admin/cmsfiles/menus/'.$img_upload1; //die();
                    
                    if (file_exists($img_upload2)) {
                        unlink($img_upload2);
                    }
                     //$validator = Validator::make($request->all(), $rulesdsad);
			}else{
                $img_upload1 =$request->oldimg_upload;
            }
		}elseif($request->menutype == 2){
			if (!empty($request->txtupload)){

                if (!is_dir('public/uploads/admin/cmsfiles/menus/')) {
                    mkdir('public/uploads/admin/cmsfiles/menus/', 0777, TRUE);
                }
                
                   $rulesdsad = array(
                       'txtupload' => 'required|mimes:pdf,xlx,csv|max:2048',
                   );
                   $valid
                        =array(
                            'txtupload.required'=>'Files field  is required',
                            
                
                        );
                        
                        $randomString = Str::random(4);
                   $txtupload1 = str_replace(' ','_',clean_single_input($request->menu_title.$randomString)).'menu'.'.'.$request->txtupload->extension();  
           
                    $res= $request->txtupload->move(public_path('uploads/admin/cmsfiles/menus/'), $txtupload1);
                  
                   
                    if($res){
                       $txtupload1 =$txtupload1;
                    }
                    $txtupload2 ='uploads/admin/cmsfiles/menus/'.$txtupload1; //die();
                    
                    if (file_exists($txtupload2)) {
                        unlink($txtupload2);
                    }
                     $validator = Validator::make($request->all(), $rulesdsad,$valid);
			}else{
                $txtupload1 =$request->oldupload;
            }
		}elseif($request->menutype == 3){
            $rules = array(
                'txtweblink' => 'required|max:164'
            );
            $valid
            =array(
                'txtweblink.required'=>'URL field  is required',
                
    
            );  
            $validator = Validator::make($request->all(), $rules,$valid);
        }
            $validator = Validator::make($request->all(), $rules, $valid);
        
        if ($validator->fails()) {
      
            return  back()->withErrors($validator)->withInput();
            
        }else{
    
            $user_login_id=Auth::guard('admin')->user()->id;
            $dataArr = array(); 
            $pArray['menu_name']    				    = clean_single_input(strip_tags($request->menu_title)); 
			$pArray['menu_url']  						= Str::slug(clean_single_input($request->url));
			$pArray['language_id']    			        = clean_single_input($request->language);
			$pArray['menu_child_id']    				= clean_single_input($request->menucategory);
			$pArray['menu_type']  						= clean_single_input($request->menutype);
			$pArray['menu_title']  					    =  $request->menu_title;
			$pArray['welcomedescription']  	             = $request->welcomedescription;
            if($request->menutype == 1){
                $pArray['doc_upload']  				    = ''; 
                $pArray['menu_links']    				= '';
                $pArray['img_upload']    				= clean_single_input($img_upload1);
                $pArray['content']    			        = $request->description; 
                // $pArray['menu_keyword']    			    = clean_single_input($request->metakeyword); 
                // $pArray['menu_description']				= clean_single_input($request->metadescription); 
             }elseif($request->menutype == 2){
                 $pArray['menu_keyword']    			    = ''; 
                 $pArray['menu_description']				= ''; 
                 $pArray['menu_links']    				= ''; 
                 $pArray['content']    			        = '';
                 $pArray['img_upload']    				= '';
                 $pArray['doc_upload']  				= clean_single_input($txtupload1); 
                 
            }elseif($request->menutype == 3){
             $pArray['menu_keyword']    			    = ''; 
             $pArray['menu_description']				= ''; 
             $pArray['doc_upload']    				= ''; 
             $pArray['content']    			        = '';
             $pArray['img_upload']    				= '';
             $pArray['menu_links']  				    = $request->txtweblink; 
            }else{
 
            }
            if (!empty($request->banner_img)){
                if (!is_dir('uploads/admin/cmsfiles/menus/')) {
                    mkdir('uploads/admin/cmsfiles/menus/', 0777, TRUE);
                }
                    $rulesdsad = array(
                        'banner_img' => 'required|mimes:pdf,xlx,csv|max:2048',
                    );
                    $banner_img = str_replace(' ','_',clean_single_input($request->menu_title)).'menu'.'.'.$request->banner_img->extension();  
                    $res= $request->banner_img->move(public_path('uploads/admin/cmsfiles/menus/'), $banner_img);
                    if($res){
                        $banner_img =$banner_img;
                    }
                    $banner_img2 ='uploads/admin/cmsfiles/menus/'.$banner_img; //die();
                    
                    if (file_exists($banner_img2)) {
                        unlink($banner_img2);
                    }
                        $validator = Validator::make($request->all(), $rulesdsad);
                        $pArray['banner_img']  				    = clean_single_input($banner_img);
			}
			$pArray['created_by']  					= $user_login_id;
			$pArray['approve_status']  			    = clean_single_input($request->txtstatus);
			$pArray['menu_position']  		        = clean_single_input($request->txtposition);
			$pArray['current_version']  			= 0;
       
			//dd($pArray);
			$create 	= Menu::where('id', $id)->update($pArray);
            $lastInsertID = $id;
           
            $data = Menu::find($lastInsertID);
            if($create > 0){

                $lastInsertID = $id;
                $user_login_id=Auth::guard('admin')->user()->id;
                $action_by_role=Auth::guard('admin')->user()->username;
                if($lastInsertID > 0){
                    $logs_data = array(
                        'module_item_title'     =>  $request->menu_title,
                        'module_item_id'        =>  $lastInsertID,
                        'action_by'             =>  $user_login_id,
                        'old_data'              =>  json_encode($data),
                        'new_data'              =>  json_encode($pArray),
                        'action_name'           =>  'Update Menu ',
                        //'page_category'         =>  '',
                        'lang_id'               =>   clean_single_input($request->language),
                        'action_type'        	=>  'Menu Model',
                        'approve_status'        =>  clean_single_input($request->txtstatus),
                        'action_by_role'        =>  $action_by_role
                    );
                    audit_trails($logs_data);
            
                   return redirect('admin/menus')->with('success','Menu has successfully Updated');
			    }
            }
           
        }
    }

    /**
     * Remove the specified Menu from storage.
     */
    public function destroy(Menu $menu)
    {
        if (is_null($this->user) || !$this->user->can('menu.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to view any menu !');
        }
        $delete= $menu->delete();
        $data = Menu::find($menu->id);
        if($delete > 0){
            $user_login_id=Auth::guard('admin')->user()->id;
            $action_by_role=Auth::guard('admin')->user()->username;
                        $logs_data = array(
                            'module_item_title'     =>  $menu->menu_title,
                            'module_item_id'        =>  $menu->id,
                            'action_by'             =>  $user_login_id,
                            'old_data'             =>  json_encode($data),
                            'new_data'             =>  json_encode($menu),
                            'action_name'           =>  'Delete',
                            'lang_id'               =>  clean_single_input($menu->language_id),
                            'action_type'        	=> 'Menu Model',
                            'approve_status'        => clean_single_input($menu->approve_status),
                            'action_by_role'        => $action_by_role
                        );
                        
            audit_trails($logs_data);

            return redirect('admin/menus')->with('success','Menu deleted successfully');
        }
        
    }
   
}
