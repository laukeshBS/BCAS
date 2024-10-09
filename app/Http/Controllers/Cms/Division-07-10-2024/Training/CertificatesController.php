<?php

namespace App\Http\Controllers\Cms\Division\Training;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cms\Division\Training\Division_certificate as certificates;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CertificatesController extends Controller
{
  /**
     * Display a listing of the certificates.
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
            abort(403, 'Sorry !! You are Unauthorized to view any Certificates !');
        }
       // $lists='';
        $parentcertificates="";
            $title="Certificates List";
            $approve_status=session()->get('status');
            $sertitle=Session::get('Crtitle');
            $approve_status=Session::get('status');
            $lang_code=Session::get('lang_code');
            $lists = certificates::whereNotNull('title');
            //$lists = certificates::with(['certificatesCategory', 'center']);
          // dd( $lists);
            if (!empty($sertitle)) {
                $lists = certificates::whereNotNull('title');
                $lists->where('title', 'LIKE', "%{$sertitle}%");
            }
            if (!empty($approve_status)) {
               
                $lists->where('status',$approve_status);
            }
            if (!empty($lang_code)) {
               
                $lists->where('lang_code',$lang_code);
            }
            $list = $lists->orderBy('position', 'ASC')->select('*')->paginate(10);
        return view('cms/division/training/certificates/index',compact(['list','title','parentcertificates']));
    }

    /**
     * Show the form for creating a new Certificates.
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('certificates.create')) {
            abort(403, 'Sorry !! You are Unauthorized to view any Certificates !');
        }
        $title="Add Certificates";
        
        return view('cms/division/training/certificates/add',compact(['title']));
    }

    /**
     * Store a newly created certificates in storage.
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
             return redirect('admin/division/training/certificates');
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
            'category_id' => 'required',
            'document' => 'required|mimes:pdf,xlx,csv|max:2048'
        );
        $valid
        =array(
             'title.required'=>'certificates title field  is required',
             'description.required'=>'certificates description  field  is required',
             'lang_code.required'=>'Languages  field  is required',
             'status.required' =>'certificates Status field is required',
             'start_date.required' =>'certificates Start date field is required',
             'end_date.required' =>'certificates End date field is required',
             'category_id.required' =>'certificates category field is required',
             'document.required' =>'Document file field is required',
             
        );
        $validator = '';
        $img_upload1="";
        
            $validator = Validator::make($request->all(), $rules,$valid);
        
        if ($validator->fails()) {
      
            return redirect('admin/division/training/certificates/create')->withErrors($validator)->withInput();
            
        }else{
            if ($request->hasFile('document')) {

                if (!is_dir('public/uploads/admin/cmsfiles/division/training/certificates/')) {
                    mkdir('public/uploads/admin/cmsfiles/division/training/certificates/', 0777, TRUE);
                }
                
                 
                $randomString = Str::random(4);
                $document1 = str_replace(' ','_',clean_single_input($request->title.$randomString)).'certificates'.'.'.$request->document->extension();  
                $rulesdsad = array(
                        'document' => 'required|mimes:pdf,xlx,csv|max:2048',
                    );
                    $valid
                        =array(
                            'document.required'=>'Files field  is required',
                            
                
                        );
                    $res= $request->document->move(public_path('uploads/admin/cmsfiles/division/training/certificates/'), $document1);
                    
                    
                    if($res){
                        $document1 =$document1;
                    }
                    $document2 ='uploads/admin/cmsfiles/division/training/certificates/'.$document1; //die();
                    
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
            $pArray['division']  				    = 'training';
			$pArray['start_date']		            = clean_single_input($request->start_date);
			$pArray['end_date']    					= $request->end_date; //clean_single_input($request->description);
            $pArray['created_by']  					= clean_single_input($user_login_id);
			$pArray['document']  			        = clean_single_input($document1); 
            $pArray['category_id']  			    = clean_single_input($request->category_id);
			$create 	= certificates::create($pArray);
           
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
                    'action_name'           =>  'Add Certificates',
                    'lang_id'               =>  clean_single_input($request->lang_code),
                    'action_type'        	=> 'Certificates Model',
                    'approve_status'        => clean_single_input($request->status),
                    'action_by_role'        =>  $action_by_role
                );
                audit_trails($logs_data);
                return redirect('admin/division/training/certificates')->with('success','Certificates has successfully added');
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
            abort(403, 'Sorry !! You are Unauthorized to view any Certificates !');
        }

        dd("Here");
        // $title="Child certificates List";
        // $whEre  = "";
        // $id=clean_single_input($id);
        // $parentcertificates = certificates::where('id', $id)->first();

        // $list = certificates::withTrashed()->whereNull('deleted_at')->where('certificates_child_id',$id)->paginate(10);
        //  return view('cms/division/training/certificates/index',compact(['list','title','id','parentcertificates']));
    }

    /**
     * Show the form for editing the specified certificates.
     */
    public function edit(string $id)
    {
        if (is_null($this->user) || !$this->user->can('certificates.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to view any Certificates !');
        }
        $id=clean_single_input($id);
        $title="Edit certificates";

        $data = certificates::find($id);
        //dd($data);
        return view('cms/division/training/certificates/edit',compact(['title','data']));
    }

    /**
     * Update the specified certificates in storage. update
     */
    public function update(Request $request, string $id){
            $id = clean_single_input($id);
            $rules = [
                'title' => 'required|max:64',
                'description' => 'required',
                'status' => 'required',
                'lang_code' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
                'category_id' => 'required',
            ];
            $messages = [
                'title.required' => 'Certificates title field is required',
                'description.required' => 'Certificates description field is required',
                'lang_code.required' => 'Languages field is required',
                'status.required' => 'Certificates Status field is required',
                'start_date.required' => 'Certificates Start date field is required',
                'end_date.required' => 'Certificates End date field is required',
                'category_id.required' => 'Certificates category field is required',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $documentName = $request->oldupload ?? '';

            if ($request->hasFile('document')) {
                $fileRules = [
                    'document' => 'required|mimes:pdf|max:2048',
                ];
                $fileMessages = [
                    'document.required' => 'Files field is required',
                    'document.mimes' => 'Only PDF files are allowed',
                    'document.max' => 'File size should not exceed 2MB',
                ];

                $fileValidator = Validator::make($request->all(), $fileRules, $fileMessages);

                if ($fileValidator->fails()) {
                    return back()->withErrors($fileValidator)->withInput();
                }

                if (!is_dir(public_path('uploads/admin/cmsfiles/division/training/certificates/'))) {
                    mkdir(public_path('uploads/admin/cmsfiles/division/training/certificates/'), 0777, true);
                }

                $randomString = Str::random(4);
                $documentName = str_replace(' ', '_', $request->title . $randomString) . 'certificates' . '.' . $request->document->extension();

                try {
                    $res = $request->document->move(public_path('uploads/admin/cmsfiles/division/training/certificates/'), $documentName);

                    if ($res) {
                        $documentPath = 'uploads/admin/cmsfiles/division/training/certificates/' . $documentName;

                        // If there's an old document and the new document is successfully uploaded, delete the old one.
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

            $user_login_id = Auth::guard('admin')->user()->id;
            $dataArr = [
                'title' => trim($request->title),
                'slugs' => Str::slug(clean_single_input($request->title)),
                'lang_code' => clean_single_input($request->lang_code),
                'description' => clean_single_input($request->description),
                'status' => clean_single_input($request->status),
                'position' => clean_single_input($request->position),
                'division' => 'training',
                'start_date' => clean_single_input($request->start_date),
                'end_date' => $request->end_date,
                'created_by' => clean_single_input($user_login_id),
                'category_id' => clean_single_input($request->category_id),
                'document' => clean_single_input($documentName),
            ];

            $update = certificates::where('id', $id)->update($dataArr);
            $data = certificates::find($id);

            if ($update > 0) {
                $logs_data = [
                    'module_item_title' => $request->title,
                    'module_item_id' => $id,
                    'action_by' => $user_login_id,
                    'old_data' => json_encode($data),
                    'new_data' => json_encode($dataArr),
                    'action_name' => 'Update certificates',
                    'lang_id' => clean_single_input($request->lang_code),
                    'action_type' => 'certificates Model',
                    'approve_status' => clean_single_input($request->status),
                    'action_by_role' => Auth::guard('admin')->user()->username,
                ];

                audit_trails($logs_data);

                return redirect('admin/division/training/certificates')->with('success', 'Certificates has been successfully updated');
            }

            return back()->withErrors(['update' => 'Failed to update the certificates'])->withInput();
        }
            /**
             * Remove the specified certificates from storage.
             */
    public function destroy(certificates $certificates)
            {
                if (is_null($this->user) || !$this->user->can('certificates.delete')) {
                    abort(403, 'Sorry !! You are Unauthorized to view any Certificates !');
                }
                $delete= $certificates->delete();
                $data = certificates::find($certificates->id);
        if($delete > 0){
            $user_login_id=Auth::guard('admin')->user()->id;
            $action_by_role=Auth::guard('admin')->user()->username;
                        $logs_data = array(
                            'module_item_title'     =>  $certificates->certificates_title,
                            'module_item_id'        =>  $certificates->id,
                            'action_by'             =>  $user_login_id,
                            'old_data'             =>  json_encode($data),
                            'new_data'             =>  json_encode($certificates),
                            'action_name'           =>  'Delete',
                            'lang_id'               =>  clean_single_input($certificates->lang_code),
                            'action_type'        	=> 'Certificates Model',
                            'approve_status'        => clean_single_input($certificates->status),
                            'action_by_role'        => $action_by_role
                        );
                        
            audit_trails($logs_data);

            return redirect('admin/division/training/certificates')->with('success','Certificates deleted successfully');
        }
        
    }
}
