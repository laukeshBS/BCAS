<?php

namespace App\Http\Controllers\Cms\Division\Training;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cms\Division\Training\Division_course as Course;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CourseController extends Controller
{
  /**
     * Display a listing of the Course.
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
            abort(403, 'Sorry !! You are Unauthorized to view any Course !');
        }
       // $lists='';
        $parentCourse="";
            $title="Course List";
            $approve_status=session()->get('status');
            $sertitle=Session::get('Crtitle');
            $approve_status=Session::get('status');
            $lang_code=Session::get('lang_code');
            $lists = Course::with(['courseCategory', 'center']);
          // dd( $lists);
            if (!empty($sertitle)) {
                $lists = Course::whereNotNull('title');
                $lists->where('title', 'LIKE', "%{$sertitle}%");
            }
            if (!empty($approve_status)) {
               
                $lists->where('status',$approve_status);
            }
            if (!empty($lang_code)) {
               
                $lists->where('lang_code',$lang_code);
            }
            $list = $lists->orderBy('position', 'ASC')->select('*')->paginate(10);
        return view('cms/division/training/courses/index',compact(['list','title','parentCourse']));
    }

    /**
     * Show the form for creating a new Course.
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('course.create')) {
            abort(403, 'Sorry !! You are Unauthorized to view any Course !');
        }
        $title="Add Course";
        
        return view('cms/division/training/courses/add',compact(['title']));
    }

    /**
     * Store a newly created Course in storage.
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
             return redirect('admin/division/training/course');
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
            'category_id' => 'required',
            'center_id' => 'required'
        );
        $valid
        =array(
             'title.required'=>'Course title field  is required',
             'description.required'=>'Course description  field  is required',
             'lang_code.required'=>'Languages  field  is required',
             'status.required' =>'Course Status field is required',
             'start_date.required' =>'Course Start date field is required',
             'end_date.required' =>'Course End date field is required',
             'category_id.required' =>'Course category field is required',
             'center_id.required' =>'Course center field is required',
        );
        $validator = '';
        $img_upload1="";
        
            $validator = Validator::make($request->all(), $rules,$valid);
        
        if ($validator->fails()) {
      
            return redirect('admin/division/training/course/create')->withErrors($validator)->withInput();
            
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
			$pArray['start_date']		            = clean_single_input($request->start_date);
			$pArray['end_date']    					= $request->end_date; //clean_single_input($request->description);
			$pArray['center_id']    				= $request->center_id;
            $pArray['is_news']  				    = $request->is_news;;
			//$pArray['Course_links']    				= $request->txtweblink;
			$pArray['created_by']  					= clean_single_input($user_login_id);
			$pArray['category_id']  			    = clean_single_input($request->category_id);
			$create 	= Course::create($pArray);
           
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
                return redirect('admin/division/training/course')->with('success','Course has successfully added');
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

        dd("Here");
        // $title="Child Course List";
        // $whEre  = "";
        // $id=clean_single_input($id);
        // $parentCourse = Course::where('id', $id)->first();

        // $list = Course::withTrashed()->whereNull('deleted_at')->where('Course_child_id',$id)->paginate(10);
        //  return view('cms/division/training/courses/index',compact(['list','title','id','parentCourse']));
    }

    /**
     * Show the form for editing the specified Course.
     */
    public function edit(string $id)
    {
        if (is_null($this->user) || !$this->user->can('course.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to view any Course !');
        }
        $id=clean_single_input($id);
        $title="Edit Course";

        $data = Course::find($id);
        //dd($data);
        return view('cms/division/training/courses/edit',compact(['title','data']));
    }

    /**
     * Update the specified Course in storage. update
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
            'category_id' => 'required',
            'center_id' => 'required'
        );
        $valid
        =array(
             'title.required'=>'Course title field  is required',
             'description.required'=>'Course description  field  is required',
             'lang_code.required'=>'Languages  field  is required',
             'status.required' =>'Course Status field is required',
             'start_date.required' =>'Course Start date field is required',
             'end_date.required' =>'Course End date field is required',
             'category_id.required' =>'Course category field is required',
             'center_id.required' =>'Course center field is required',
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
			$pArray['start_date']		            = clean_single_input($request->start_date);
			$pArray['end_date']    					= $request->end_date; //clean_single_input($request->description);
			$pArray['center_id']    				= $request->center_id;
            $pArray['is_news']  				    = $request->is_news;;
			//$pArray['Course_links']    				= $request->txtweblink;
			$pArray['created_by']  					= clean_single_input($user_login_id);
			$pArray['category_id']  			    = clean_single_input($request->category_id);
		
			$create 	= Course::where('id', $id)->update($pArray);
            $lastInsertID = $id;
           
            $data = Course::find($lastInsertID);
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
                        'action_name'           =>  'Update Course ',
                        //'page_category'         =>  '',
                        'lang_id'               =>   clean_single_input($request->lang_code),
                        'action_type'        	=>  'Course Model',
                        'approve_status'        =>  clean_single_input($request->status),
                        'action_by_role'        =>  $action_by_role
                    );
                    audit_trails($logs_data);
            
                   return redirect('admin/division/training/course')->with('success','Course has successfully Updated');
			    }
            }
           
        }
    }

    /**
     * Remove the specified Course from storage.
     */
    public function destroy(Course $Course)
    {
        if (is_null($this->user) || !$this->user->can('course.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to view any Course !');
        }
        $delete= $Course->delete();
        $data = Course::find($Course->id);
        if($delete > 0){
            $user_login_id=Auth::guard('admin')->user()->id;
            $action_by_role=Auth::guard('admin')->user()->username;
                        $logs_data = array(
                            'module_item_title'     =>  $Course->Course_title,
                            'module_item_id'        =>  $Course->id,
                            'action_by'             =>  $user_login_id,
                            'old_data'             =>  json_encode($data),
                            'new_data'             =>  json_encode($Course),
                            'action_name'           =>  'Delete',
                            'lang_id'               =>  clean_single_input($Course->lang_code),
                            'action_type'        	=> 'Course Model',
                            'approve_status'        => clean_single_input($Course->status),
                            'action_by_role'        => $action_by_role
                        );
                        
            audit_trails($logs_data);

            return redirect('admin/division/training/course')->with('success','Course deleted successfully');
        }
        
    }
}
