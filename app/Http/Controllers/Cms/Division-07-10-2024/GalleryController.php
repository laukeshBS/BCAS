<?php

namespace App\Http\Controllers\Cms\Division;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cms\Division\Division_gallery as gallery;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class GalleryController extends Controller
{
  /**
     * Display a listing of the gallery.
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
        if (is_null($this->user) || !$this->user->can('gallery.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any Gallery !');
        }
       // $lists='';
        $parentgallery="";
            $title="Gallery List";
            $approve_status=session()->get('status');
            $sertitle=Session::get('Crtitle');
            $approve_status=Session::get('status');
            $lang_code=Session::get('lang_code');
            $lists = gallery::whereNotNull('title');
            //$lists = gallery::with(['galleryCategory', 'center']);
          // dd( $lists);
            if (!empty($sertitle)) {
                $lists = gallery::whereNotNull('title');
                $lists->where('title', 'LIKE', "%{$sertitle}%");
            }
            if (!empty($approve_status)) {
               
                $lists->where('status',$approve_status);
            }
            if (!empty($lang_code)) {
               
                $lists->where('lang_code',$lang_code);
            }
            $list = $lists->orderBy('position', 'ASC')->select('*')->paginate(10);
           // dd($list);
        return view('cms/division/gallery/index',compact(['list','title','parentgallery']));
    }

    /**
     * Show the form for creating a new Gallery.
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('gallery.create')) {
            abort(403, 'Sorry !! You are Unauthorized to view any Gallery !');
        }
        $title="Add Gallery";
        
        return view('cms/division/gallery/add',compact(['title']));
    }

    /**
     * Store a newly created Gallery in storage.
     */
    public function store(Request $request): mixed {
     //dd($request);
        if(isset($request->search)){
            $title=clean_single_input(trim($request->title));
             $approve_status=clean_single_input($request->status);
             $lang_code=clean_single_input($request->lang_code);
             Session::put('title', $title);
             Session::put('status', $approve_status);
             Session::put('lang_code', $lang_code);
             return redirect('admin/division/gallery');
           }
        if(isset($request->cmdsubmit)){  
         $txtupload1 ='';
         $rules = array(
            'title' => 'required|max:64',
            'description' => 'required',
            'status' => 'required',
            'lang_code' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'division' => 'required',
            'image' => 'required',
            'category_id' => 'required'
        );
        $valid
        =array(
             'title.required'=>'Gallery title field  is required',
             'description.required'=>'Gallery description  field  is required',
             'lang_code.required'=>'Languages  field  is required',
             'status.required' =>'Gallery Status field is required',
             'start_date.required' =>'Gallery Start date field is required',
             'end_date.required' =>'Gallery End date field is required',
             'division.required' =>'Gallery division field is required',
             'category_id.required' =>'Gallery category field is required'
        );
        $validator = '';
        $img_upload1="";
        
            $validator = Validator::make($request->all(), $rules,$valid);
        
        if ($validator->fails()) {
      
            return redirect('admin/division/gallery/create')->withErrors($validator)->withInput();
            
        }else{
            if (!empty($request->image)){
                if (!is_dir('public/uploads/admin/cmsfiles/division/gallery')) {
                    mkdir('public/uploads/admin/cmsfiles/division/gallery', 0777, TRUE);
                }
                    $rulesdsad = array(
                        'image' => 'required|mimes:jpg,png,web,jpge|max:2048',
                    );
                    $randomString = Str::random(4);
                    $image = str_replace(' ','_',clean_single_input($request->title.$randomString)).'image'.'.'.$request->image->extension();  
            
                    $res= $request->image->move(public_path('uploads/admin/cmsfiles/division/gallery'), $image);
                    if($res){
                        $image =$image;
                    }
                    $image2 ='uploads/admin/cmsfiles//division/gallery'.$image; //die();
                    
                    if (file_exists($image2)) {
                        unlink($image2);
                    }
                        $validator = Validator::make($request->all(), $rulesdsad);
                        $pArray['image']  				    = clean_single_input($image);
			}
            
            $user_login_id=Auth::guard('admin')->user()->id;
            $dataArr = array(); 
           //dd $image="";
            $pArray['title']    				    = trim($request->title); 
            $pArray['slugs']    				    = Str::slug(clean_single_input($request->title));
			$pArray['lang_code']    			    = clean_single_input($request->lang_code);
			$pArray['description']    			    = clean_single_input($request->description);
			$pArray['status']  					    = clean_single_input($request->status);
            $pArray['position']  					= clean_single_input($request->position);
            $pArray['division']  				    = clean_single_input($request->division);
			$pArray['start_date']		            = clean_single_input($request->start_date);
			$pArray['end_date']    					= clean_single_input($request->end_date); //clean_single_input($request->description);
		    $pArray['is_news']  				    = $request->is_news;
			$pArray['created_by']  					= clean_single_input($user_login_id);
			$pArray['category_id']  			    = clean_single_input($request->category_id);
            //dd($pArray);
			$create 	= gallery::create($pArray);
           
            $lastInsertID = $create->id;
            $user_login_id=Auth::guard('admin')->user()->id;
            $action_by_role=Auth::guard('admin')->user()->username;
            if($lastInsertID > 0){
                $logs_data = array(
                    'module_item_title'     => $request->title,
                    'module_item_id'        => $lastInsertID,
                    'action_by'             =>  $user_login_id,
                    'old_data'              =>  json_encode($pArray),
                    'new_data'              =>  json_encode($pArray),
                    'action_name'           =>  'Add Gallery',
                    'lang_id'               =>  clean_single_input($request->lang_code),
                    'action_type'        	=> 'Gallery Model',
                    'approve_status'        => clean_single_input($request->status),
                    'action_by_role'        =>  $action_by_role
                );
                audit_trails($logs_data);
                return redirect('admin/division/gallery')->with('success','Gallery has successfully added');
			}
           
        }
    }
      
    }

    /**
     * Display the specified Gallery.
     */
    public function show(string $id)
    {
        if (is_null($this->user) || !$this->user->can('gallery.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any Gallery !');
        }

        dd("Here");
        // $title="Child Gallery List";
        // $whEre  = "";
        // $id=clean_single_input($id);
        // $parentgallery = gallery::where('id', $id)->first();

        // $list = gallery::withTrashed()->whereNull('deleted_at')->where('gallery_child_id',$id)->paginate(10);
        //  return view('cms/division/training/gallerys/index',compact(['list','title','id','parentgallery']));
    }

    /**
     * Show the form for editing the specified gallery.
     */
    public function edit(string $id)
    {
        if (is_null($this->user) || !$this->user->can('gallery.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to view any Gallery !');
        }
        $id=clean_single_input($id);
        $title="Edit Gallery";

        $data = gallery::find($id);
        //dd($data);
        return view('cms/division/gallery/edit',compact(['title','data']));
    }

    /**
     * Update the specified gallery in storage. update
     */
    public function update(Request $request, string $id)
    {
        $id= clean_single_input($id);
        $rules = array(
            'title' => 'required|max:64',
            'description' => 'required',
            'status' => 'required',
            'lang_code' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'division' => 'required',
            'category_id' => 'required'
        );
        $valid
        =array(
             'title.required'=>'Gallery title field  is required',
             'description.required'=>'Gallery description  field  is required',
             'lang_code.required'=>'Languages  field  is required',
             'status.required' =>'Gallery Status field is required',
             'start_date.required' =>'Gallery Start date field is required',
             'end_date.required' =>'Gallery End date field is required',
             'division.required' =>'Gallery division field is required',
             'category_id.required' =>'Gallery category field is required'
        );
        
            $validator = Validator::make($request->all(), $rules, $valid);
        
        if ($validator->fails()) {
      
            return  back()->withErrors($validator)->withInput();
            
        }else{
    
            $user_login_id=Auth::guard('admin')->user()->id;
            $dataArr = array(); 
            if (!empty($request->image)){
                if (!is_dir('public/uploads/admin/cmsfiles/division/gallery')) {
                    mkdir('public/uploads/admin/cmsfiles/division/gallery', 0777, TRUE);
                }
                    $rulesdsad = array(
                        'image' => 'required|mimes:jpg,png,web,jpge|max:2048',
                    );
                    $randomString = Str::random(4);
                    $image = str_replace(' ','_',clean_single_input($request->title.$randomString)).'image'.'.'.$request->image->extension();  
                    $res= $request->image->move(public_path('uploads/admin/cmsfiles/division/gallery'), $image);
                    if($res){
                        $image =$image;
                    }
                    $image2 ='uploads/admin/cmsfiles/division/gallery'.$image; //die();
                    
                    if (file_exists($image2)) {
                        unlink($image2);
                    }
                        $validator = Validator::make($request->all(), $rulesdsad);
                        $pArray['image']  				    = clean_single_input($image);
			}else{
                $pArray['image']  				    = clean_single_input($request->oldimage);
            }
            $pArray['title']    				    = trim($request->title); 
            $pArray['slugs']    				    = Str::slug(clean_single_input($request->title));
			$pArray['lang_code']    			    = clean_single_input($request->lang_code);
			$pArray['description']    			    = clean_single_input($request->description);
			$pArray['status']  					    = clean_single_input($request->status);
            $pArray['position']  					= clean_single_input($request->position);
            $pArray['division']  				    =  clean_single_input($request->division);
			$pArray['start_date']		            = clean_single_input($request->start_date);
			$pArray['end_date']    					= $request->end_date; 
            $pArray['is_news']  				    = $request->is_news;
			$pArray['created_by']  					= clean_single_input($user_login_id);
			$pArray['category_id']  			    = clean_single_input($request->category_id);
		
			$create 	= gallery::where('id', $id)->update($pArray);
            $lastInsertID = $id;
           
            $data = gallery::find($lastInsertID);
            if($create > 0){

                $lastInsertID = $id;
                $user_login_id=Auth::guard('admin')->user()->id;
                $action_by_role=Auth::guard('admin')->user()->username;
                if($lastInsertID > 0){
                    $logs_data = array(
                        'module_item_title'     =>  $request->title,
                        'module_item_id'        =>  $lastInsertID,
                        'action_by'             =>  $user_login_id,
                        'old_data'              =>  json_encode($data),
                        'new_data'              =>  json_encode($pArray),
                        'action_name'           =>  'Update Gallery ',
                        //'page_category'         =>  '',
                        'lang_id'               =>   clean_single_input($request->lang_code),
                        'action_type'        	=>  'Gallery Model',
                        'approve_status'        =>  clean_single_input($request->status),
                        'action_by_role'        =>  $action_by_role
                    );
                    audit_trails($logs_data);
            
                   return redirect('admin/division/gallery')->with('success','Gallery has successfully Updated');
			    }
            }
           
        }
    }

    /**
     * Remove the specified Gallery from storage.
     */
    public function destroy(gallery $gallery)
    {
        if (is_null($this->user) || !$this->user->can('gallery.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to view any Gallery !');
        }
        $delete= $gallery->delete();
        $data = gallery::find($gallery->id);
        if($delete > 0){
            $user_login_id=Auth::guard('admin')->user()->id;
            $action_by_role=Auth::guard('admin')->user()->username;
                        $logs_data = array(
                            'module_item_title'     =>  $gallery->gallery_title,
                            'module_item_id'        =>  $gallery->id,
                            'action_by'             =>  $user_login_id,
                            'old_data'             =>  json_encode($data),
                            'new_data'             =>  json_encode($gallery),
                            'action_name'           =>  'Delete',
                            'lang_id'               =>  clean_single_input($gallery->lang_code),
                            'action_type'        	=> 'Gallery Model',
                            'approve_status'        => clean_single_input($gallery->status),
                            'action_by_role'        => $action_by_role
                        );
                        
            audit_trails($logs_data);

            return redirect('admin/division/gallery')->with('success','Gallery deleted successfully');
        }
        
    }
}
