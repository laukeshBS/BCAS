<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Menu;
use App;
class AjaxRequestController extends Controller
{
   // function for get primary menu on ajax request created by laukesh suraj
   // clean_single_input is helpers function for remove html tags for input/request
    function get_primarylink_menu(Request $request)
	{
        if($request->get_primarylink_menu=='get_primarylink_menu'){
            $language =clean_single_input($request->id);
            
            $data = array();
            $data['html'] = primarylink_menu($language);
             echo json_encode($data);
            die();
        }
	}
    function get_menu_details(Request $request)
	{
        if($request->get_menu_details=='get_menu_details'){
            $language =clean_single_input($request->language);
            $id =clean_single_input($request->id);
           
            $data = Menu::where('id', $id)->where('id', $language)->first();
             echo json_encode($data);
            die();
        }
	}
    
 
    
     // function for update menu orders  on ajax request created by laukesh 
    public function update_menu_orders(Request $request)
    {
        $msg=array();
        if($request->ajax())
        {
            $id= clean_single_input( $request->id);
            $pArray['page_order'] =clean_single_input( $request->page_order);
            
            $data = Menu::where('id', $id)->first();
            $oldarr['page_order'] =clean_single_input('page_order ='.$data->page_order);
            
            if($data->page_order!==$request->page_order){

               $create 	= Menu::where('id', $id)->update($pArray);
                $msg['success']='This Postion is Updated';
            }else{
                $msg['error']='This Postion Alredy Taken';
            }
            $lastInsertID = $id;
            $user_login_id=Auth::guard('admin')->user()->id;
            $action_by_role=Auth::guard('admin')->user()->username;
            if($create > 0){
		
                $logs_data = array(
                    'module_item_title'     => $data->menu_name,
                    'module_item_id'        => $data->id,
                    'action_by'             =>  $user_login_id,
                    'old_data'              =>  json_encode($oldarr),
                    'new_data'              =>  json_encode($pArray),
                    'action_name'           =>  'Update Menu orders',
                    'lang_id'               =>  clean_single_input($data->language_id),
                    'action_type'        	=> 'Menu Model',
                    'approve_status'        => clean_single_input($data->approve_status),
                    'action_by_role'        => $action_by_role
                );
                	//dd($logs_data);            
                audit_trails($logs_data);
                echo json_encode($msg);
                die();
            			
            }
        }
        
    }
  
    
}
