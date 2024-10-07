<?php

namespace App\Http\Controllers\Cms\Division\Training;
use App\Models\Cms\Division\Training\Division_certificates_category as certificatesCategories;
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

class CertificatesCategoriesController extends Controller
{
    /**
     * Display a listing of the Certificates Categories.
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
        if (is_null($this->user) || !$this->user->can('certificates.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any Certificates Categories !');
        }
       // $lists='';
        $parentcertificates="";
            $title="Certificates Categories List";
            $approve_status=session()->get('status');
            $sertitle=Session::get('Crtitle');
            $approve_status=Session::get('status');
            $lang_code=Session::get('lang_code');
            $lists = certificatesCategories::whereNotNull('title');
            if (!empty($sertitle)) {
                $lists = certificatesCategories::whereNotNull('title');
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
        return view('cms/division/training/certificatesCategories/index',compact(['list','title','parentcertificates']));
    }

    /**
     * Show the form for creating a new certificates Categories.
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('certificates.create')) {
            abort(403, 'Sorry !! You are Unauthorized to view any Certificates Categories !');
        }
        $title="Add certificates Categories";
        
        return view('cms/division/training/certificatesCategories/add',compact(['title']));
    }

    /**
     * Store a newly created Certificates Categories in storage.
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
             return redirect('admin/division/training/certificates_categories');
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
             'title.required'=>'certificates title field  is required',
             'description.required'=>'certificates description  field  is required',
             'lang_code.required'=>'Languages  field  is required',
             'status.required' =>'certificates Status field is required',
            
        );
        $validator = '';
        $img_upload1="";
        
            $validator = Validator::make($request->all(), $rules,$valid);
        
        if ($validator->fails()) {
      
            return redirect('admin/division/training/certificates_categories/create')->withErrors($validator)->withInput();
            
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
			$create 	= certificatesCategories::create($pArray);
           
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
                    'action_name'           =>  'Add certificates',
                    'lang_id'               =>  clean_single_input($request->lang_code),
                    'action_type'        	=> 'certificates Model',
                    'approve_status'        => clean_single_input($request->status),
                    'action_by_role'        =>  $action_by_role
                );
                audit_trails($logs_data);
                return redirect('admin/division/training/certificates_categories')->with('success','Certificates has successfully added');
			}
           
        }
    }
      
    }

    /**
     * Display the specified certificates.
     */
    public function show(string $id)
    {
        if (is_null($this->user) || !$this->user->can('certificates.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any certificates !');
        }
        // $title="Child certificates List";
        // $whEre  = "";
        // $id=clean_single_input($id);
        // $parentcertificates = certificates::where('id', $id)->first();

        // $list = certificates::withTrashed()->whereNull('deleted_at')->paginate(10);
        //  return view('cms/division/training/certificatesCategories/index',compact(['list','title','id','parentcertificates']));
    }

    /**
     * Show the form for editing the specified certificates Categories.
     */
    public function edit(string $id)
    {
        if (is_null($this->user) || !$this->user->can('certificates.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to view any Certificates !');
        }
        $id=clean_single_input($id);
        $title="Edit certificates";
        $data = certificatesCategories::find($id);
        //dd( $data );
        return view('cms/division/training/certificatesCategories/edit',compact(['title','data']));
    }

    /**
     * Update the specified certificates Categories in storage. update
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
             'title.required'=>'certificates title field  is required',
             'description.required'=>'certificates description  field  is required',
             'lang_code.required'=>'Languages  field  is required',
             'status.required' =>'certificates Status field is required',
            
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
			$create 	= certificatescategories::where('id', $id)->update($pArray);
            $lastInsertID = $id;
           
            $data = certificatesCategories::find($lastInsertID);
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
                        'action_name'           =>  'Update Certificates Categories ',
                        //'page_category'         =>  '',
                        'lang_id'               =>   clean_single_input($request->lang_code),
                        'action_type'        	=>  'Certificates Categories Model',
                        'approve_status'        =>  clean_single_input($request->status),
                        'action_by_role'        =>  $action_by_role
                    );
                    audit_trails($logs_data);
            
                   return redirect('admin/division/training/certificates_categories')->with('success','Certificates Category has successfully Updated');
			    }
            }
           
        }
    }

    /**
     * Remove the specified certificates from storage.
     */
    public function destroy(certificatesCategories $certificatesCategories)
    {
        if (is_null($this->user) || !$this->user->can('certificates.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to view any certificates Categories !');
        }
        $delete= $certificatesCategories->delete();
        $data = certificates::find($certificatesCategories->id);
        if($delete > 0){
            $user_login_id=Auth::guard('admin')->user()->id;
            $action_by_role=Auth::guard('admin')->user()->username;
                        $logs_data = array(
                            'module_item_title'     =>  $certificatesCategories->title,
                            'module_item_id'        =>  $certificatesCategories->id,
                            'action_by'             =>  $user_login_id,
                            'old_data'             =>  json_encode($data),
                            'new_data'             =>  json_encode($certificatesCategories),
                            'action_name'           =>  'Delete',
                            'lang_id'               =>  clean_single_input($certificatesCategories->lang_code),
                            'action_type'        	=> 'Certificates Model',
                            'approve_status'        => clean_single_input($certificatesCategories->approve_status),
                            'action_by_role'        => $action_by_role
                        );
                        
            audit_trails($logs_data);

            return redirect('admin/division/training/certificates_categories')->with('success','Certificates Categories deleted successfully');
        }
        
    }
   
}
