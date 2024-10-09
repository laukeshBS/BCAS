<?php

namespace App\Http\Controllers\Cms\Division;
use App\Models\Cms\Division\Division_document_category as documentCategories;
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

class DocumentCategoriesController extends Controller
{
    /**
     * Display a listing of the Document Categories.
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
        if (is_null($this->user) || !$this->user->can('document.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any Document Categories !');
        }
       // $lists='';
        $parentdocument="";
            $title="Document Categories List";
            $approve_status=session()->get('status');
            $sertitle=Session::get('Crtitle');
            $approve_status=Session::get('status');
            $lang_code=Session::get('lang_code');
            $lists = documentCategories::whereNotNull('title');
            if (!empty($sertitle)) {
                $lists = documentCategories::whereNotNull('title');
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
        return view('cms/division/documentCategories/index',compact(['list','title','parentdocument']));
    }

    /**
     * Show the form for creating a new Document Categories.
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('document.create')) {
            abort(403, 'Sorry !! You are Unauthorized to view any Document Categories !');
        }
        $title="Add Document Categories";
        
        return view('cms/division/documentCategories/add',compact(['title']));
    }

    /**
     * Store a newly created Document Categories in storage.
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
             return redirect('admin/division/document_categories');
           }
        if(isset($request->cmdsubmit)){  
         $txtupload1 ='';
         $rules = array(
            'title' => 'required|max:64',
            'description' => 'required',
            'division' => 'required',
            'status' => 'required',
            'lang_code' => 'required',
            
        );
        $valid
        =array(
             'title.required'=>'Document category title field  is required',
             'description.required'=>'Document categorydescription  field  is required',
             'lang_code.required'=>'Languages  field  is required',
             'status.required' =>'Document category status field is required',
             'division.required' =>'Document category division field is required',
            
        );
        $validator = '';
        $img_upload1="";
        
            $validator = Validator::make($request->all(), $rules,$valid);
        
        if ($validator->fails()) {
      
            return redirect('admin/division/document_categories/create')->withErrors($validator)->withInput();
            
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
            $pArray['division']  				    = clean_single_input($request->division);
			$pArray['created_by']  					= clean_single_input($user_login_id);
			$create 	= documentCategories::create($pArray);
           
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
                    'action_name'           =>  'Add Document',
                    'lang_id'               =>  clean_single_input($request->lang_code),
                    'action_type'        	=> 'Document Model',
                    'approve_status'        => clean_single_input($request->status),
                    'action_by_role'        =>  $action_by_role
                );
                audit_trails($logs_data);
                return redirect('admin/division/document_categories')->with('success','Document category has successfully added');
			}
           
        }
    }
      
    }

    /**
     * Display the specified document.
     */
    public function show(string $id)
    {
        if (is_null($this->user) || !$this->user->can('document.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any Document category !');
        }
        // $title="Child document List";
        // $whEre  = "";
        // $id=clean_single_input($id);
        // $parentdocument = document::where('id', $id)->first();

        // $list = document::withTrashed()->whereNull('deleted_at')->paginate(10);
        //  return view('cms/division/training/documentCategories/index',compact(['list','title','id','parentdocument']));
    }

    /**
     * Show the form for editing the specified document Categories.
     */
    public function edit(string $id)
    {
        if (is_null($this->user) || !$this->user->can('document.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to view any Document !');
        }
        $id=clean_single_input($id);
        $title="Edit document";
        $data = documentCategories::find($id);
        //dd( $data );
        return view('cms/division/documentCategories/edit',compact(['title','data']));
    }

    /**
     * Update the specified document Categories in storage. update
     */
    public function update(Request $request, string $id)
    {
        $id= clean_single_input($id);
        $rules = array(
            'title' => 'required|max:64',
            'description' => 'required',
            'division' => 'required',
            'status' => 'required',
            'lang_code' => 'required',
            
        );
        $valid
        =array(
             'title.required'=>'Document category title field  is required',
             'description.required'=>'Document categorydescription  field  is required',
             'lang_code.required'=>'Languages  field  is required',
             'status.required' =>'Document category status field is required',
             'division.required' =>'Document category division field is required',
            
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
            $pArray['division']  				    = clean_single_input($request->division);
			$pArray['created_by']  					= clean_single_input($user_login_id);
           // dd($pArray);
			$create 	= documentcategories::where('id', $id)->update($pArray);
            $lastInsertID = $id;
           
            $data = documentCategories::find($lastInsertID);
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
                        'action_name'           =>  'Update Document Categories ',
                        //'page_category'         =>  '',
                        'lang_id'               =>   clean_single_input($request->lang_code),
                        'action_type'        	=>  'Document Categories Model',
                        'approve_status'        =>  clean_single_input($request->status),
                        'action_by_role'        =>  $action_by_role
                    );
                    audit_trails($logs_data);
            
                   return redirect('admin/division/document_categories')->with('success','Document Category has successfully Updated');
			    }
            }
           
        }
    }

    /**
     * Remove the specified document from storage.
     */
    public function destroy(documentCategories $documentCategories,$id)
    {
        // Authorization check
        if (is_null($this->user) || !$this->user->can('document.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any Document Categories !');
        }
    
        // Delete the category
      
        $documentdelete = documentCategories::find($id);
        $delete= $documentdelete->delete();
        if ($delete) {
            $user_login_id = Auth::guard('admin')->user()->id;
            $action_by_role = Auth::guard('admin')->user()->username;
            
            // Create audit log data
            $logs_data = [
                'module_item_title' => $documentdelete->title,
                'module_item_id' => $documentdelete->id,
                'action_by' => $user_login_id,
                'old_data' => json_encode($documentdelete), // Log the deleted category data
                'new_data' => null,
                'action_name' => 'Delete',
                'lang_id' => clean_single_input($documentdelete->lang_code),
                'action_type' => 'Document Model',
                'approve_status' => clean_single_input($documentdelete->status),
                'action_by_role' => $action_by_role
            ];
    
            // Call the audit_trails function to log the action
            audit_trails($logs_data);
    
            // Redirect with success message
            return redirect('admin/division/document_categories')->with('success', 'Document Category deleted successfully');
        }
    
        // Redirect with error message if deletion fails
        return redirect('admin/division/document_categories')->with('error', 'Failed to delete Document Category');
    }
    
   
}
