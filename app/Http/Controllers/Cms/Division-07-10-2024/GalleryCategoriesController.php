<?php

namespace App\Http\Controllers\Cms\Division;
use App\Models\Cms\Division\Division_gallery_category as galleryCategories;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class GalleryCategoriesController extends Controller
{
    /**
     * Display a listing of the gallery Categories.
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
            abort(403, 'Sorry !! You are Unauthorized to view any gallery Categories !');
        }
       // $lists='';
        $parentgallery="";
            $title="gallery Categories List";
            $approve_status=session()->get('status');
            $sertitle=Session::get('Crtitle');
            $approve_status=Session::get('status');
            $lang_code=Session::get('lang_code');
            $lists = galleryCategories::whereNotNull('title');
            if (!empty($sertitle)) {
                $lists = galleryCategories::whereNotNull('title');
                $lists->where('title', 'LIKE', "%{$sertitle}%");
            }
            if (!empty($approve_status)) {
               
                $lists->where('status',$approve_status);
            }
            if (!empty($lang_code)) {
               
                $lists->where('lang_code',$lang_code);
            }
            $list = $lists->orderBy('position', 'ASC')->select('*')->paginate(10);
           // dd("hetees");
        return view('cms/division/galleryCategories/index',compact(['list','title','parentgallery']));
    }

    /**
     * Show the form for creating a new gallery Categories.
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('gallery.create')) {
            abort(403, 'Sorry !! You are Unauthorized to view any gallery Categories !');
        }
        $title="Add gallery Categories";
        
        return view('cms/division/galleryCategories/add',compact(['title']));
    }

    /**
     * Store a newly created gallery Categories in storage.
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
             return redirect('admin/division/gallery_categories');
           }
        if(isset($request->cmdsubmit)){  
         $txtupload1 ='';
         $rules = array(
            'title' => 'required|max:64',
            'description' => 'required',
            'status' => 'required',
            'lang_code' => 'required',
            
        );
        $valid
        =array(
             'title.required'=>'gallery title field  is required',
             'description.required'=>'gallery description  field  is required',
             'lang_code.required'=>'Short languages  field  is required',
             'status.required' =>'gallery Status field is required',
            
        );
        $validator = '';
        $img_upload1="";
        
            $validator = Validator::make($request->all(), $rules,$valid);
        
        if ($validator->fails()) {
      
            return redirect('admin/division/gallery_categories/create')->withErrors($validator)->withInput();
            
        }else{
            
            $user_login_id=Auth::guard('admin')->user()->id;
            $dataArr = array(); 
           //dd $banner_img="";
            $pArray['title']    				    = trim($request->title); 
            $pArray['slugs']    				    = Str::slug(clean_single_input($request->title));
			$pArray['lang_code']    			    = clean_single_input($request->lang_code);
			$pArray['description']    			    = clean_single_input($request->description);
			$pArray['status']  					    = clean_single_input($request->status);
            $pArray['position']  					= clean_single_input($request->position);
            $pArray['division']  				    = 'training';
			$pArray['created_by']  					= clean_single_input($user_login_id);
			$create 	= galleryCategories::create($pArray);
           
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
                    'action_name'           =>  'Add gallery',
                    'lang_id'               =>  clean_single_input($request->lang_code),
                    'action_type'        	=> 'gallery Model',
                    'approve_status'        => clean_single_input($request->status),
                    'action_by_role'        =>  $action_by_role
                );
                audit_trails($logs_data);
                return redirect('admin/division/gallery_categories')->with('success','gallery has successfully added');
			}
           
        }
    }
      
    }

    /**
     * Display the specified gallery.
     */
    public function show(string $id)
    {
        if (is_null($this->user) || !$this->user->can('gallery.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any gallery !');
        }
        // $title="Child gallery List";
        // $whEre  = "";
        // $id=clean_single_input($id);
        // $parentgallery = gallery::where('id', $id)->first();

        // $list = gallery::withTrashed()->whereNull('deleted_at')->paginate(10);
        //  return view('cms/division/galleryCategories/index',compact(['list','title','id','parentgallery']));
    }

    /**
     * Show the form for editing the specified gallery Categories.
     */
    public function edit(string $id)
    {
        if (is_null($this->user) || !$this->user->can('gallery.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to view any gallery !');
        }
        $id=clean_single_input($id);
        $title="Edit gallery";
        $data = galleryCategories::find($id);
        //dd( $data );
        return view('cms/division/galleryCategories/edit',compact(['title','data']));
    }

    /**
     * Update the specified gallery Categories in storage. update
     */
    public function update(Request $request, string $id)
    {
        $id= clean_single_input($id);
        $rules = array(
            'title' => 'required|max:64',
            'description' => 'required',
            'status' => 'required',
            'lang_code' => 'required',
            
        );
        $valid
        =array(
             'title.required'=>'gallery title field  is required',
             'description.required'=>'gallery description  field  is required',
             'lang_code.required'=>'Short languages  field  is required',
             'status.required' =>'gallery Status field is required',
            
        );
        
            $validator = Validator::make($request->all(), $rules, $valid);
        
        if ($validator->fails()) {
      
            return  back()->withErrors($validator)->withInput();
            
        }else{
    
            $user_login_id=Auth::guard('admin')->user()->id;
            $dataArr = array(); 
            $pArray['title']    				    = trim($request->title); 
            $pArray['slugs']    				    = Str::slug(clean_single_input($request->title));
			$pArray['lang_code']    			    = clean_single_input($request->lang_code);
			$pArray['description']    			    = clean_single_input($request->description);
			$pArray['status']  					    = clean_single_input($request->status);
            $pArray['position']  					= clean_single_input($request->position);
            $pArray['division']  				    = 'training';
			$pArray['created_by']  					= clean_single_input($user_login_id);
           // dd($pArray);
			$create 	= gallerycategories::where('id', $id)->update($pArray);
            $lastInsertID = $id;
           
            $data = galleryCategories::find($lastInsertID);
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
                        'action_name'           =>  'Update gallery Categories ',
                        //'page_category'         =>  '',
                        'lang_id'               =>   clean_single_input($request->lang_code),
                        'action_type'        	=>  'gallery Categories Model',
                        'approve_status'        =>  clean_single_input($request->status),
                        'action_by_role'        =>  $action_by_role
                    );
                    audit_trails($logs_data);
            
                   return redirect('admin/division/gallery_categories')->with('success','gallery Category has successfully Updated');
			    }
            }
           
        }
    }

    /**
     * Remove the specified gallery from storage.
     */
    public function destroy(galleryCategories $galleryCategories)
    {
        if (is_null($this->user) || !$this->user->can('gallery.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to view any gallery Categories !');
        }
        $delete= $galleryCategories->delete();
        $data = gallery::find($galleryCategories->id);
        if($delete > 0){
            $user_login_id=Auth::guard('admin')->user()->id;
            $action_by_role=Auth::guard('admin')->user()->username;
                        $logs_data = array(
                            'module_item_title'     =>  $galleryCategories->title,
                            'module_item_id'        =>  $galleryCategories->id,
                            'action_by'             =>  $user_login_id,
                            'old_data'             =>  json_encode($data),
                            'new_data'             =>  json_encode($galleryCategories),
                            'action_name'           =>  'Delete',
                            'lang_id'               =>  clean_single_input($galleryCategories->lang_code),
                            'action_type'        	=> 'gallery Model',
                            'approve_status'        => clean_single_input($galleryCategories->approve_status),
                            'action_by_role'        => $action_by_role
                        );
                        
            audit_trails($logs_data);

            return redirect('admin/division/gallery_categories')->with('success','gallery Categories deleted successfully');
        }
        
    }
   
}
