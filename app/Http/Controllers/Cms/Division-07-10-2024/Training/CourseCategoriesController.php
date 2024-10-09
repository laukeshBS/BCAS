<?php

namespace App\Http\Controllers\Cms\Division\Training;
use App\Models\Cms\Division\Training\division_course_category as courseCategories;
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

class CourseCategoriesController extends Controller
{
    /**
     * Display a listing of the Course Categories.
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
            abort(403, 'Sorry !! You are Unauthorized to view any Course Categories !');
        }
       // $lists='';
        $parentCourse="";
            $title="Course Categories List";
            $approve_status=session()->get('status');
            $sertitle=Session::get('Crtitle');
            $approve_status=Session::get('status');
            $lang_code=Session::get('lang_code');
            $lists = courseCategories::whereNotNull('title');
            if (!empty($sertitle)) {
                $lists = courseCategories::whereNotNull('title');
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
        return view('cms/division/training/courseCategories/index',compact(['list','title','parentCourse']));
    }

    /**
     * Show the form for creating a new Course Categories.
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('course.create')) {
            abort(403, 'Sorry !! You are Unauthorized to view any Course Categories !');
        }
        $title="Add Course Categories";
        
        return view('cms/division/training/courseCategories/add',compact(['title']));
    }

    /**
     * Store a newly created Course Categories in storage.
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
             return redirect('admin/division/training/course_categories');
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
             'title.required'=>'Course title field  is required',
             'description.required'=>'Course description  field  is required',
             'lang_code.required'=>'Short languages  field  is required',
             'status.required' =>'Course Status field is required',
            
        );
        $validator = '';
        $img_upload1="";
        
            $validator = Validator::make($request->all(), $rules,$valid);
        
        if ($validator->fails()) {
      
            return redirect('admin/division/training/course_categories/create')->withErrors($validator)->withInput();
            
        }else{
            
            $user_login_id=Auth::guard('admin')->user()->id;
            $dataArr = array(); 
           //dd $banner_img="";
            $pArray['title']    				    = trim($request->title); 
			$pArray['lang_code']    			    = clean_single_input($request->lang_code);
			$pArray['description']    			    = clean_single_input($request->description);
			$pArray['status']  					    = clean_single_input($request->status);
            $pArray['position']  					= clean_single_input($request->position);
            $pArray['division']  				    = 'training';
			$pArray['created_by']  					= clean_single_input($user_login_id);
			$create 	= courseCategories::create($pArray);
           
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
                    'action_name'           =>  'Add Course',
                    'lang_id'               =>  clean_single_input($request->lang_code),
                    'action_type'        	=> 'Course Model',
                    'approve_status'        => clean_single_input($request->status),
                    'action_by_role'        =>  $action_by_role
                );
                audit_trails($logs_data);
                return redirect('admin/division/training/course_categories')->with('success','Course has successfully added');
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
        //  return view('cms/division/training/courseCategories/index',compact(['list','title','id','parentCourse']));
    }

    /**
     * Show the form for editing the specified Course Categories.
     */
    public function edit(string $id)
    {
        if (is_null($this->user) || !$this->user->can('course.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to view any Course !');
        }
        $id=clean_single_input($id);
        $title="Edit Course";
        $data = courseCategories::find($id);
        //dd( $data );
        return view('cms/division/training/courseCategories/edit',compact(['title','data']));
    }

    /**
     * Update the specified Course Categories in storage. update
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
             'title.required'=>'Course title field  is required',
             'description.required'=>'Course description  field  is required',
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
            $pArray['slugs']    				    = Str::slug(clean_single_input($request->title));
			$pArray['lang_code']    			    = clean_single_input($request->lang_code);
			$pArray['description']    			    = clean_single_input($request->description);
			$pArray['status']  					    = clean_single_input($request->status);
            $pArray['position']  					= clean_single_input($request->position);
            $pArray['division']  				    = 'training';
			$pArray['created_by']  					= clean_single_input($user_login_id);
           // dd($pArray);
			$create 	= coursecategories::where('id', $id)->update($pArray);
            $lastInsertID = $id;
           
            $data = courseCategories::find($lastInsertID);
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
                        'action_name'           =>  'Update Course Categories ',
                        //'page_category'         =>  '',
                        'lang_id'               =>   clean_single_input($request->lang_code),
                        'action_type'        	=>  'Course Categories Model',
                        'approve_status'        =>  clean_single_input($request->status),
                        'action_by_role'        =>  $action_by_role
                    );
                    audit_trails($logs_data);
            
                   return redirect('admin/division/training/course_categories')->with('success','Course Category has successfully Updated');
			    }
            }
           
        }
    }

    /**
     * Remove the specified Course from storage.
     */
    public function destroy(courseCategories $courseCategories)
    {
        if (is_null($this->user) || !$this->user->can('course.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to view any Course Categories !');
        }
        $delete= $courseCategories->delete();
        $data = Course::find($courseCategories->id);
        if($delete > 0){
            $user_login_id=Auth::guard('admin')->user()->id;
            $action_by_role=Auth::guard('admin')->user()->username;
                        $logs_data = array(
                            'module_item_title'     =>  $courseCategories->title,
                            'module_item_id'        =>  $courseCategories->id,
                            'action_by'             =>  $user_login_id,
                            'old_data'             =>  json_encode($data),
                            'new_data'             =>  json_encode($courseCategories),
                            'action_name'           =>  'Delete',
                            'lang_id'               =>  clean_single_input($courseCategories->lang_code),
                            'action_type'        	=> 'Course Model',
                            'approve_status'        => clean_single_input($courseCategories->approve_status),
                            'action_by_role'        => $action_by_role
                        );
                        
            audit_trails($logs_data);

            return redirect('admin/division/training/course_categories')->with('success','Course Categories deleted successfully');
        }
        
    }
   
}
