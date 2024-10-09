<?php

namespace App\Http\Controllers\Cms\Division;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cms\Division\Division_document as document;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DocumentController extends Controller
{
  /**
     * Display a listing of the document.
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
            abort(403, 'Sorry !! You are Unauthorized to view any Document !');
        }
       // $lists='';
        $parentdocument="";
            $title="Document List";
            $approve_status=session()->get('status');
            $sertitle=Session::get('Crtitle');
            $approve_status=Session::get('status');
            $lang_code=Session::get('lang_code');
            $lists = document::with('category');
         // dd( $lists);
            if (!empty($sertitle)) {
                $lists = document::whereNotNull('title');
                $lists->where('title', 'LIKE', "%{$sertitle}%");
            }
            if (!empty($approve_status)) {
               
                $lists->where('status',$approve_status);
            }
            if (!empty($lang_code)) {
               
                $lists->where('lang_code',$lang_code);
            }
            $list = $lists->orderBy('position', 'ASC')->select('*')->paginate(10);
        return view('cms/division/documents/index',compact(['list','title','parentdocument']));
    }

    /**
     * Show the form for creating a new Document.
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('document.create')) {
            abort(403, 'Sorry !! You are Unauthorized to view any Document !');
        }
        $title="Add Document";
        
        return view('cms/division/documents/add',compact(['title']));
    }

    /**
     * Store a newly created Document in storage.
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
             return redirect('admin/division/document');
           }
        if(isset($request->cmdsubmit)){  
         $document1 ='';
         $rules = array(
            'title' => 'required|max:64',
            'description' => 'required',
            'status' => 'required',
            'lang_code' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'division' => 'required',
            'category_id' => 'required',
            'document' => 'required|mimes:pdf,xlx,csv|max:2048'
        );
        $valid
        =array(
             'title.required'=>'Document title field  is required',
             'description.required'=>'Document description  field  is required',
             'lang_code.required'=>'Languages  field  is required',
             'status.required' =>'Document Status field is required',
             'start_date.required' =>'Document Start date field is required',
             'end_date.required' =>'Document End date field is required',
             'category_id.required' =>'Document category field is required',
             'division.required' =>'Document division field is required',
             'document.required' =>'Document file field is required',
        );
        $validator = '';
        $document1="";
        
            $validator = Validator::make($request->all(), $rules,$valid);
        
        if ($validator->fails()) {
      
            return redirect('admin/division/document/create')->withErrors($validator)->withInput();
            
        }else{
            if ($request->hasFile('document')) {

                if (!is_dir('public/uploads/admin/cmsfiles/division/document/')) {
                    mkdir('public/uploads/admin/cmsfiles/division/document/', 0777, TRUE);
                }
                
                 
                $randomString = Str::random(4);
                $document1 = str_replace(' ','_',clean_single_input($request->title.$randomString)).'document'.'.'.$request->document->extension();  
                $rulesdsad = array(
                        'document' => 'required|mimes:pdf,xlx,csv|max:2048',
                    );
                    $valid
                        =array(
                            'document.required'=>'Files field  is required',
                            
                
                        );
                    $res= $request->document->move(public_path('uploads/admin/cmsfiles/division/document/'), $document1);
                    
                    
                    if($res){
                        $document1 =$document1;
                    }
                    $document2 ='uploads/admin/cmsfiles/division/document/'.$document1; //die();
                    
                    if (file_exists($document2)) {
                        unlink($document2);
                    }
                        $validator = Validator::make($request->all(), $rulesdsad,$valid);
			}
            
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
			$pArray['start_date']		            = clean_single_input($request->start_date);
			$pArray['end_date']    					= $request->end_date; //clean_single_input($request->description);
			$pArray['document']    				    = $document1;
            $pArray['is_news']  				    = $request->is_news;;
			//$pArray['document_links']    				= $request->txtweblink;
			$pArray['created_by']  					= clean_single_input($user_login_id);
			$pArray['category_id']  			    = clean_single_input($request->category_id);
			$create 	= document::create($pArray);
           
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
                    'action_name'           =>  'Add document',
                    'lang_id'               =>  clean_single_input($request->lang_code),
                    'action_type'        	=> 'document Model',
                    'approve_status'        => clean_single_input($request->status),
                    'action_by_role'        =>  $action_by_role
                );
                audit_trails($logs_data);
                return redirect('admin/division/document')->with('success','document has successfully added');
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
            abort(403, 'Sorry !! You are Unauthorized to view any document !');
        }

        dd("Here");
        // $title="Child document List";
        // $whEre  = "";
        // $id=clean_single_input($id);
        // $parentdocument = document::where('id', $id)->first();

        // $list = document::withTrashed()->whereNull('deleted_at')->where('document_child_id',$id)->paginate(10);
        //  return view('cms/division/training/documents/index',compact(['list','title','id','parentdocument']));
    }

    /**
     * Show the form for editing the specified document.
     */
    public function edit(string $id)
    {
        if (is_null($this->user) || !$this->user->can('document.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to view any document !');
        }
        $id=clean_single_input($id);
        $title="Edit document";

        $data = document::find($id);
        //dd($data);
        return view('cms/division/documents/edit',compact(['title','data']));
    }

    /**
     * Update the specified document in storage. update
     */
    public function update(Request $request, string $id)
    {
        $id = clean_single_input($id);
        $documentName = $request->oldupload ?? '';
        $user_login_id = Auth::guard('admin')->user()->id;
    
        // Validate main form inputs
        $rules = [
            'title' => 'required|max:64',
            'description' => 'required',
            'status' => 'required',
            'lang_code' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'division' => 'required',
            'category_id' => 'required',
        ];
        $messages = [
            'title.required' => 'Document title field is required',
            'description.required' => 'Document description field is required',
            'lang_code.required' => 'Languages field is required',
            'status.required' => 'Document Status field is required',
            'start_date.required' => 'Document Start date field is required',
            'end_date.required' => 'Document End date field is required',
            'category_id.required' => 'Document category field is required',
            'division.required' => 'Document division field is required',
        ];
        
        $validator = Validator::make($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
    
        // Handle file upload if a new file is provided
        if ($request->hasFile('document')) {
            $fileRules = [
                'document' => 'required|mimes:pdf,xlx,csv|max:2048',
            ];
            $fileMessages = [
                'document.required' => 'Files field is required',
                'document.mimes' => 'Only PDF, XLX, and CSV files are allowed',
                'document.max' => 'File size should not exceed 2MB',
            ];
    
            $fileValidator = Validator::make($request->all(), $fileRules, $fileMessages);
    
            if ($fileValidator->fails()) {
                return back()->withErrors($fileValidator)->withInput();
            }
    
            if (!is_dir(public_path('uploads/admin/cmsfiles/division/document/'))) {
                mkdir(public_path('uploads/admin/cmsfiles/division/document/'), 0777, true);
            }
    
            $randomString = Str::random(4);
            $documentName = str_replace(' ', '_', clean_single_input($request->title . $randomString)) . 'document.' . $request->document->extension();
    
            try {
                $res = $request->document->move(public_path('uploads/admin/cmsfiles/division/document/'), $documentName);
    
                if ($res) {
                    if ($request->filled('oldupload') && file_exists(public_path($request->oldupload))) {
                        unlink(public_path($request->oldupload));
                    }
                } else {
                    return back()->withErrors(['document' => 'Failed to upload the document.'])->withInput();
                }
            } catch (\Exception $e) {
                return back()->withErrors(['document' => 'Failed to upload the document: ' . $e->getMessage()])->withInput();
            }
        }
    
        // Prepare data for updating the document
        $dataArr = [
            'title' => trim($request->title),
            'slugs' => Str::slug(clean_single_input($request->title)),
            'lang_code' => clean_single_input($request->lang_code),
            'description' => clean_single_input($request->description),
            'status' => clean_single_input($request->status),
            'position' => clean_single_input($request->position),
            'division' => clean_single_input($request->division),
            'start_date' => clean_single_input($request->start_date),
            'end_date' => $request->end_date,
            'is_news' => $request->is_news,
            'created_by' => clean_single_input($user_login_id),
            'category_id' => clean_single_input($request->category_id),
            'document' => clean_single_input($documentName),
        ];
    
        // Update the document
        $update = document::where('id', $id)->update($dataArr);
    
        if ($update > 0) {
            $data = document::find($id);
    
            // Log the update action
            $logs_data = [
                'module_item_title' => $request->title,
                'module_item_id' => $id,
                'action_by' => $user_login_id,
                'old_data' => json_encode($data),
                'new_data' => json_encode($dataArr),
                'action_name' => 'Update document',
                'lang_id' => clean_single_input($request->lang_code),
                'action_type' => 'document Model',
                'approve_status' => clean_single_input($request->status),
                'action_by_role' => Auth::guard('admin')->user()->username,
            ];
            audit_trails($logs_data);
    
            return redirect('admin/division/document')->with('success', 'Document has been successfully updated');
        }
    
        return back()->withErrors(['update' => 'Failed to update the document'])->withInput();
    }
    

    /**
     * Remove the specified document from storage.
     */
    public function destroy(document $document)
    {
        if (is_null($this->user) || !$this->user->can('document.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to view any document !');
        }
        $delete= $document->delete();
        $data = document::find($document->id);
        if($delete > 0){
            $user_login_id=Auth::guard('admin')->user()->id;
            $action_by_role=Auth::guard('admin')->user()->username;
                        $logs_data = array(
                            'module_item_title'     =>  $document->document_title,
                            'module_item_id'        =>  $document->id,
                            'action_by'             =>  $user_login_id,
                            'old_data'             =>  json_encode($data),
                            'new_data'             =>  json_encode($document),
                            'action_name'           =>  'Delete',
                            'lang_id'               =>  clean_single_input($document->lang_code),
                            'action_type'        	=> 'document Model',
                            'approve_status'        => clean_single_input($document->status),
                            'action_by_role'        => $action_by_role
                        );
                        
            audit_trails($logs_data);

            return redirect('admin/division/document')->with('success','Document deleted successfully');
        }
        
    }
}
