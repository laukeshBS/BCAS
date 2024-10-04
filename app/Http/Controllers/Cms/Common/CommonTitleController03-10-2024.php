<?php

namespace App\Http\Controllers\Cms\Common;
use App\Models\Cms\Common\CommonTitle as CommonTitle;
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

class CommonTitleController extends Controller
{
    /**
     * Display a listing of the CommonTitle.
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
            abort(403, 'Sorry !! You are Unauthorized to view any CommonTitle !');
        }
       // $lists='';
        $parentCourse="";
            $title="CommonTitle List";
            $approve_status=session()->get('status');
            $sertitle=Session::get('Crtitle');
            $approve_status=Session::get('status');
            $lang_code=Session::get('lang_code');
            $lists = CommonTitle::whereNotNull('title');
            if (!empty($sertitle)) {
                $lists = CommonTitle::whereNotNull('title');
                $lists->where('title', 'LIKE', "%{$sertitle}%");
            }
            if (!empty($approve_status)) {
               
                $lists->where('status',$approve_status);
            }
            if (!empty($lang_code)) {
               
                $lists->where('lang_code',$lang_code);
            }
            $list = $lists->orderBy('created_at', 'ASC')->select('*')->paginate(10);
           // dd("hetees");
        return view('cms/commonTitle/index',compact(['list','title','parentCourse']));
    }

    /**
     * Show the form for creating a new CommonTitle.
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('course.create')) {
            abort(403, 'Sorry !! You are Unauthorized to view any CommonTitle !');
        }
        $title="Add CommonTitle";
        
        return view('cms/commonTitle/add',compact(['title']));
    }

    /**
     * Store a newly created CommonTitle in storage.
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
             return redirect('admin/common_title');
           }
        if(isset($request->cmdsubmit)){  
         $txtupload1 ='';
         $rules = array(
            'title' => 'required',
            'slugs' => 'required',
            'status' => 'required',
            'lang_code' => 'required',
            
        );
        $valid
        =array(
             'title.required'=>'Course title field  is required',
             'slugs.required'=>'Course slugs  field  is required',
             'lang_code.required'=>'Short languages  field  is required',
             'status.required' =>'Course Status field is required',
            
        );
        $validator = '';
        $img_upload1="";
        
            $validator = Validator::make($request->all(), $rules,$valid);
        
        if ($validator->fails()) {
      
            return redirect('admin/common_title/create')->withErrors($validator)->withInput();
            
        }else{
            
            $user_login_id=Auth::guard('admin')->user()->id;
            $dataArr = array(); 
           //dd $banner_img="";
            $pArray['title']    				    = trim($request->title); 
			$pArray['lang_code']    			    = clean_single_input($request->lang_code);
			$pArray['slugs']    			        = Str::slug(clean_single_input($request->slugs));
            $pArray['status']  					    = clean_single_input($request->status);
            $pArray['created_by']  					= clean_single_input($user_login_id);
			$create 	= CommonTitle::create($pArray);
           
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
                    'action_name'           =>  'Add CommonTitle',
                    'lang_id'               =>  clean_single_input($request->lang_code),
                    'action_type'        	=> 'CommonTitle Model',
                    'approve_status'        => clean_single_input($request->status),
                    'action_by_role'        =>  $action_by_role
                );
                audit_trails($logs_data);
                return redirect('admin/common_title')->with('success','CommonTitle has successfully added');
			}
           
        }
    }
      
    }

    /**
     * Display the specified Course.
     */
    public function show(string $id)
    {
        if (is_null($this->user) || !$this->user->can('course.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any Course !');
        }
        // $title="Child Course List";
        // $whEre  = "";
        // $id=clean_single_input($id);
        // $parentCourse = Course::where('id', $id)->first();

        // $list = Course::withTrashed()->whereNull('deleted_at')->paginate(10);
        //  return view('cms/commonTitle/index',compact(['list','title','id','parentCourse']));
    }

    /**
     * Show the form for editing the specified CommonTitle.
     */
    public function edit(string $id)
    {
        if (is_null($this->user) || !$this->user->can('course.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to view any Course !');
        }
        $id=clean_single_input($id);
        $title="Edit Course";
        $data = CommonTitle::find($id);
        //dd( $data );
        return view('cms/commonTitle/edit',compact(['title','data']));
    }

    /**
     * Update the specified CommonTitle in storage. update
     */
    public function update(Request $request, string $id)
    {
        $id= clean_single_input($id);
        $rules = array(
            'title' => 'required',
            'slugs' => 'required',
            'status' => 'required',
            'lang_code' => 'required',
            
        );
        $valid
        =array(
             'title.required'=>'Course title field  is required',
             'slugs.required'=>'Course slugs  field  is required',
             'lang_code.required'=>'Short languages  field  is required',
             'status.required' =>'Course Status field is required',
            
        );
        
            $validator = Validator::make($request->all(), $rules, $valid);
        
        if ($validator->fails()) {
      
            return  back()->withErrors($validator)->withInput();
            
        }else{
    
            $user_login_id=Auth::guard('admin')->user()->id;
            $dataArr = array(); 
            $pArray['title']    				    = trim($request->title); 
			$pArray['lang_code']    			    = clean_single_input($request->lang_code);
			$pArray['slugs']    			        = Str::slug(clean_single_input($request->slugs));
			$pArray['status']  					    = clean_single_input($request->status);
            $pArray['created_by']  					= clean_single_input($user_login_id);
           // dd($pArray);
			$create 	= CommonTitle::where('id', $id)->update($pArray);
            $lastInsertID = $id;
           
            $data = CommonTitle::find($lastInsertID);
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
                        'action_name'           =>  'Update CommonTitle ',
                        //'page_category'         =>  '',
                        'lang_id'               =>   clean_single_input($request->lang_code),
                        'action_type'        	=>  'CommonTitle Model',
                        'approve_status'        =>  clean_single_input($request->status),
                        'action_by_role'        =>  $action_by_role
                    );
                    audit_trails($logs_data);
            
                   return redirect('admin/common_title')->with('success','CommonTitle has successfully Updated');
			    }
            }
           
        }
    }

    /**
     * Remove the specified Course from storage.
     */
    public function destroy(CommonTitle $CommonTitle)
    {
        if (is_null($this->user) || !$this->user->can('course.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to view any CommonTitle !');
        }
        $delete= $CommonTitle->delete();
        $data = Course::find($CommonTitle->id);
        if($delete > 0){
            $user_login_id=Auth::guard('admin')->user()->id;
            $action_by_role=Auth::guard('admin')->user()->username;
                        $logs_data = array(
                            'module_item_title'     =>  $CommonTitle->title,
                            'module_item_id'        =>  $CommonTitle->id,
                            'action_by'             =>  $user_login_id,
                            'old_data'             =>  json_encode($data),
                            'new_data'             =>  json_encode($CommonTitle),
                            'action_name'           =>  'Delete',
                            'lang_id'               =>  clean_single_input($CommonTitle->lang_code),
                            'action_type'        	=> 'Course Model',
                            'approve_status'        => clean_single_input($CommonTitle->approve_status),
                            'action_by_role'        => $action_by_role
                        );
                        
            audit_trails($logs_data);

            return redirect('admin/common_title')->with('success','CommonTitle deleted successfully');
        }
        
    }
   
}
