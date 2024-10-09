<?php

namespace App\Http\Controllers\Cms\Division\Training;
use App\Models\Cms\Division\Training\Division_awards_category as awardCategories;
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

class AwardCategoriesController extends Controller
{
    /**
     * Display a listing of the Award Categories.
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
        if (is_null($this->user) || !$this->user->can('award.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any award Categories !');
        }
       // $lists='';
        $parentaward="";
            $title="award Categories List";
            $approve_status=session()->get('status');
            $sertitle=Session::get('Crtitle');
            $approve_status=Session::get('status');
            $lang_code=Session::get('lang_code');
            $lists = awardCategories::whereNotNull('title');
            if (!empty($sertitle)) {
                $lists = awardCategories::whereNotNull('title');
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
        return view('cms/division/training/awardCategories/index',compact(['list','title','parentaward']));
    }

    /**
     * Show the form for creating a new award Categories.
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('award.create')) {
            abort(403, 'Sorry !! You are Unauthorized to view any Award Categories !');
        }
        $title="Add Award Categories";
        
        return view('cms/division/training/awardCategories/add',compact(['title']));
    }

    /**
     * Store a newly created Award Categories in storage.
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
             return redirect('admin/division/training/award_categories');
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
             'title.required'=>'Award title field  is required',
             'description.required'=>'Award description  field  is required',
             'lang_code.required'=>'Languages  field  is required',
             'status.required' =>'Award Status field is required',
            
        );
        $validator = '';
        $img_upload1="";
        
            $validator = Validator::make($request->all(), $rules,$valid);
        
        if ($validator->fails()) {
      
            return redirect('admin/division/training/award_categories/create')->withErrors($validator)->withInput();
            
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
			$create 	= awardCategories::create($pArray);
           
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
                    'action_name'           =>  'Add award',
                    'lang_id'               =>  clean_single_input($request->lang_code),
                    'action_type'        	=> 'award Model',
                    'approve_status'        => clean_single_input($request->status),
                    'action_by_role'        =>  $action_by_role
                );
                audit_trails($logs_data);
                return redirect('admin/division/training/award_categories')->with('success','award has successfully added');
			}
           
        }
    }
      
    }

    /**
     * Display the specified award.
     */
    public function show(string $id)
    {
        if (is_null($this->user) || !$this->user->can('award.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any award !');
        }
        // $title="Child award List";
        // $whEre  = "";
        // $id=clean_single_input($id);
        // $parentaward = award::where('id', $id)->first();

        // $list = award::withTrashed()->whereNull('deleted_at')->paginate(10);
        //  return view('cms/division/training/awardCategories/index',compact(['list','title','id','parentaward']));
    }

    /**
     * Show the form for editing the specified award Categories.
     */
    public function edit(string $id)
    {
        if (is_null($this->user) || !$this->user->can('award.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to view any Award !');
        }
        $id=clean_single_input($id);
        $title="Edit Award";
        $data = awardCategories::find($id);
        //dd( $data );
        return view('cms/division/training/awardCategories/edit',compact(['title','data']));
    }

    /**
     * Update the specified award Categories in storage. update
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
             'title.required'=>'Award title field  is required',
             'description.required'=>'Award description  field  is required',
             'lang_code.required'=>'Languages  field  is required',
             'status.required' =>'Award Status field is required',
            
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
			$create 	= awardcategories::where('id', $id)->update($pArray);
            $lastInsertID = $id;
           
            $data = awardCategories::find($lastInsertID);
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
                        'action_name'           =>  'Update Award Categories ',
                        //'page_category'         =>  '',
                        'lang_id'               =>   clean_single_input($request->lang_code),
                        'action_type'        	=>  'Award Categories Model',
                        'approve_status'        =>  clean_single_input($request->status),
                        'action_by_role'        =>  $action_by_role
                    );
                    audit_trails($logs_data);
            
                   return redirect('admin/division/training/award_categories')->with('success','Award Category has successfully Updated');
			    }
            }
           
        }
    }

    /**
     * Remove the specified award from storage.
     */
    public function destroy(awardCategories $awardCategories)
    {
        if (is_null($this->user) || !$this->user->can('award.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to view any Award Categories !');
        }
        $delete= $awardCategories->delete();
        $data = award::find($awardCategories->id);
        if($delete > 0){
            $user_login_id=Auth::guard('admin')->user()->id;
            $action_by_role=Auth::guard('admin')->user()->username;
                        $logs_data = array(
                            'module_item_title'     =>  $awardCategories->title,
                            'module_item_id'        =>  $awardCategories->id,
                            'action_by'             =>  $user_login_id,
                            'old_data'             =>  json_encode($data),
                            'new_data'             =>  json_encode($awardCategories),
                            'action_name'           =>  'Delete',
                            'lang_id'               =>  clean_single_input($awardCategories->lang_code),
                            'action_type'        	=> 'award Model',
                            'approve_status'        => clean_single_input($awardCategories->approve_status),
                            'action_by_role'        => $action_by_role
                        );
                        
            audit_trails($logs_data);

            return redirect('admin/division/training/award_categories')->with('success','Award Categories deleted successfully');
        }
        
    }
   
}
