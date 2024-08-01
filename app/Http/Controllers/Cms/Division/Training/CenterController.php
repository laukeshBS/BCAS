<?php

namespace App\Http\Controllers\Cms\Division\Training;
use App\Models\Cms\Division\Training\Division_register_centers as center;
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

class CenterController extends Controller
{
    /**
     * Display a listing of the center.
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
        if (is_null($this->user) || !$this->user->can('course.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any center !');
        }
       // $lists='';
        $parentcenter="";
            $title="center List";
            $approve_status=session()->get('status');
            $sertitle=Session::get('Crtitle');
            $approve_status=Session::get('status');
            $lang_code=Session::get('lang_code');
            $lists = center::whereNotNull('title');
            if (!empty($sertitle)) {
                $lists = center::whereNotNull('title');
                $lists->where('title', 'LIKE', "%{$sertitle}%");
            }
            if (!empty($approve_status)) {
               
                $lists->where('status',$approve_status);
            }
            if (!empty($lang_code)) {
               
                $lists->where('lang_code',$lang_code);
            }
            $list = $lists->orderBy('position', 'ASC')->select('*')->paginate(10);
        return view('cms/division/training/centers/index',compact(['list','title','parentcenter']));
    }

    /**
     * Show the form for creating a new center.
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('course.create')) {
            abort(403, 'Sorry !! You are Unauthorized to view any center !');
        }
        $title="Add center";
        
        return view('cms/division/training/centers/add',compact(['title']));
    }

    /**
     * Store a newly created center in storage.
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
             return redirect('admin/division/training/center');
           }
        if(isset($request->cmdsubmit)){  
         $txtupload1 ='';
         $rules = array(
            'title' => 'required|max:64',
            'description' => 'required',
            'status' => 'required',
            'lang_code' => 'required',
            'address' => 'required',
            'url' => 'required',
          
        );
        $valid
        =array(
             'title.required'=>'Center title field  is required',
             'description.required'=>'Center description  field  is required',
             'lang_code.required'=>'Languages  field  is required',
             'status.required' =>'Center Status field is required',
             'address.required' =>'Center Address field is required',
             'url.required' =>'Center URL field is required',
         
        );
        $validator = '';
        $img_upload1="";
        
            $validator = Validator::make($request->all(), $rules,$valid);
        
        if ($validator->fails()) {
      
            return redirect('admin/division/training/center/create')->withErrors($validator)->withInput();
            
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
			$pArray['address']		            = clean_single_input($request->address);
			$pArray['url']    					= $request->url; //clean_single_input($request->description);
		    $pArray['created_by']  					= clean_single_input($user_login_id);
			//$pArray['category_id']  			    = clean_single_input($request->category_id);
			$create 	= center::create($pArray);
           
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
                    'action_name'           =>  'Add center',
                    'lang_id'               =>  clean_single_input($request->lang_code),
                    'action_type'        	=> 'center Model',
                    'approve_status'        => clean_single_input($request->status),
                    'action_by_role'        =>  $action_by_role
                );
                audit_trails($logs_data);
                return redirect('admin/division/training/center')->with('success','center has successfully added');
			}
           
        }
    }
      
    }

    /**
     * Display the specified center.
     */
    public function show(string $id)
    {
        if (is_null($this->user) || !$this->user->can('course.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any center !');
        }

       // dd("Here");
        // $title="Child center List";
        // $whEre  = "";
        // $id=clean_single_input($id);
        // $parentcenter = center::where('id', $id)->first();

        // $list = center::withTrashed()->whereNull('deleted_at')->where('center_child_id',$id)->paginate(10);
        //  return view('cms/division/training/centers/index',compact(['list','title','id','parentcenter']));
    }

    /**
     * Show the form for editing the specified center.
     */
    public function edit(string $id)
    {
        if (is_null($this->user) || !$this->user->can('course.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to view any Center !');
        }
        $id=clean_single_input($id);
        $title="Edit center";
        $data = center::find($id);
        return view('cms/division/training/centers/edit',compact(['title','data']));
    }

    /**
     * Update the specified center in storage. update
     */
    public function update(Request $request, string $id)
    {
        $id= clean_single_input($id);
        $rules = array(
            'title' => 'required|max:64',
            'description' => 'required',
            'status' => 'required',
            'lang_code' => 'required',
            'address' => 'required',
            'url' => 'required',
          
        );
        $valid
        =array(
             'title.required'=>'Center title field  is required',
             'description.required'=>'Center description  field  is required',
             'lang_code.required'=>'Languages  field  is required',
             'status.required' =>'Center Status field is required',
             'address.required' =>'Center Address field is required',
             'url.required' =>'Center URL field is required',
         
        );

        
            $validator = Validator::make($request->all(), $rules, $valid);
        
        if ($validator->fails()) {
      
            return  back()->withErrors($validator)->withInput();
            
        }else{
    
            $user_login_id=Auth::guard('admin')->user()->id;
            $dataArr = array(); 
			$pArray['title']    				    = trim($request->title); 
			$pArray['lang_code']    			    = clean_single_input($request->lang_code);
			$pArray['description']    			    = clean_single_input($request->description);
			$pArray['status']  					    = clean_single_input($request->status);
            $pArray['position']  					= clean_single_input($request->position);
            $pArray['division']  				    = 'training';
			$pArray['address']		                = clean_single_input($request->address);
			$pArray['url']    				    	= $request->url; //clean_single_input($request->description);
		    $pArray['created_by']  					= clean_single_input($user_login_id);
			$create 	= center::where('id', $id)->update($pArray);
            $lastInsertID = $id;
           
            $data = center::find($lastInsertID);
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
                        'action_name'           =>  'Update Center ',
                        //'page_category'         =>  '',
                        'lang_id'               =>   clean_single_input($request->lang_code),
                        'action_type'        	=>  'Center Model',
                        'approve_status'        =>  clean_single_input($request->status),
                        'action_by_role'        =>  $action_by_role
                    );
                    audit_trails($logs_data);
            
                   return redirect('admin/division/training/center')->with('success','Center has successfully Updated');
			    }
            }
           
        }
    }

    /**
     * Remove the specified center from storage.
     */
    public function destroy(center $center)
    {
        if (is_null($this->user) || !$this->user->can('course.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to view any center !');
        }
        $delete= $center->delete();
        $data = center::find($center->id);
        if($delete > 0){
            $user_login_id=Auth::guard('admin')->user()->id;
            $action_by_role=Auth::guard('admin')->user()->username;
                        $logs_data = array(
                            'module_item_title'     =>  $center->title,
                            'module_item_id'        =>  $center->id,
                            'action_by'             =>  $user_login_id,
                            'old_data'             =>  json_encode($data),
                            'new_data'             =>  json_encode($center),
                            'action_name'           =>  'Delete',
                            'lang_id'               =>  clean_single_input($center->lang_code),
                            'action_type'        	=> 'Center Model',
                            'approve_status'        => clean_single_input($center->status),
                            'action_by_role'        => $action_by_role
                        );
                        
            audit_trails($logs_data);

            return redirect('admin/division/training/center')->with('success','center deleted successfully');
        }
        
    }
   
}
