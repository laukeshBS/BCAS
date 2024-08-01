<?php

use Carbon\Carbon;
use App\Models\Admin\AuditTrail;
use App\Models\Admin\Menu;
use App\Models\Admin\Language;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;



/**
 * Write code on Method
 *
 * @return response()
 */
if (!function_exists('convertYmdToMdy')) {
	function convertYmdToMdy($date)
	{
		return Carbon::createFromFormat('Y-m-d', $date)->format('m-d-Y');
	}
}

/**
 * Write code on Method
 * //dd(convertYmdToMdy('2022-02-12'));
 * @return response()
 */
if (!function_exists('convertMdyToYmd')) {
	function convertMdyToYmd($date)
	{
		return Carbon::createFromFormat('m-d-Y', $date)->format('Y-m-d');
	}
}

if (!function_exists('convertMdyToYmd')) {
	function convertMdyToYmd($date)
	{
		return Carbon::createFromFormat('m-d-Y', $date)->format('Y-m-d');
	}
}

// functions for remove html tags form input filed  created by laukesh 
if (!function_exists('clean_single_input')) {
	function clean_single_input($content_desc)
	{
		//$content_desc = trim($content_desc);
		$content_desc = Str::of($content_desc)->trim();
		$content_desc = str_replace('\'', '', $content_desc);
		$content_desc = str_replace('&lt;script', ' ', $content_desc);
		$content_desc = str_replace('&lt;iframe', ' ', $content_desc);
		$content_desc = str_replace('&lt;script&gt;', '', $content_desc);
		$content_desc = str_replace('&lt;SCRIPT&gt;', '', $content_desc);
		$content_desc = str_replace('&lt;SCRIPT', ' ', $content_desc);
		$content_desc = str_replace('&lt;ScRiPt&gt', '', $content_desc);
		$content_desc = str_replace('&lt;ScRiPt &gt', '', $content_desc);
		$content_desc = str_replace('&lt;IFRAME', ' ', $content_desc);

		$content_desc = str_replace('sleep', '', $content_desc);
		$content_desc = str_replace('waitfor delay', '', $content_desc);

		$content_desc = str_replace('iframe', '', $content_desc);
		$content_desc = str_replace('script', '', $content_desc);
		$content_desc = str_replace('window.', '', $content_desc);
		$content_desc = str_replace('prompt', '', $content_desc);
		$content_desc = str_replace('Prompt', '', $content_desc);

		$content_desc = str_replace('confirm', '', $content_desc);
		$content_desc = str_replace('CONTENT=', '', $content_desc);
		$content_desc = str_replace('HTTP-EQUIV', '', $content_desc);
		$content_desc = str_replace('&lt;meta', '', $content_desc);
		$content_desc = str_replace('&lt;META', '', $content_desc);
		$content_desc = str_replace('data:text/html', '', $content_desc);
		$content_desc = str_replace('document.', '', $content_desc);
		$content_desc = str_replace('url', '', $content_desc);
		$content_desc = str_replace('document.createTextNode', '', $content_desc);
		$content_desc = str_replace('document.writeln', '', $content_desc);
		$content_desc = str_replace('document.write', '', $content_desc);
		$content_desc = str_replace('alert', '', $content_desc);
		$content_desc = str_replace('javascript', '', $content_desc);
		$content_desc = str_replace('DROP', '', $content_desc);
		$content_desc = str_replace('CREATE', '', $content_desc);
		$content_desc = str_replace('onsubmit', '', $content_desc);
		$content_desc = str_replace('onblur', '', $content_desc);
		$content_desc = str_replace('onclick', '', $content_desc);
		$content_desc = str_replace('ondatabinding', '', $content_desc);
		$content_desc = str_replace('ondblclick', '', $content_desc);
		$content_desc = str_replace('ondisposed', '', $content_desc);
		$content_desc = str_replace('onfocus', '', $content_desc);
		$content_desc = str_replace('onkeydown', '', $content_desc);
		$content_desc = str_replace('onkeyup', '', $content_desc);
		$content_desc = str_replace('onload', '', $content_desc);
		$content_desc = str_replace('onmousedown', '', $content_desc);
		$content_desc = str_replace('onmousemove', '', $content_desc);
		$content_desc = str_replace('onmouseout', '', $content_desc);
		$content_desc = str_replace('onmouseover', '', $content_desc);
		$content_desc = str_replace('onmouseup', '', $content_desc);
		$content_desc = str_replace('onprerender', '', $content_desc);
		$content_desc = str_replace('onserverclick', '', $content_desc);
		$content_desc = str_replace('[removed]', '', $content_desc);

		$content_desc = str_replace('A=A', '', $content_desc);
		$content_desc = str_replace('1=1', '', $content_desc);

		$content_desc = str_replace('<', '', $content_desc);
		$content_desc = str_replace('>', '', $content_desc);
		$content_desc = str_replace('< >', '', $content_desc);
		$content_desc = str_replace("<''>", "", $content_desc);

		$content_desc = str_replace("%", "", $content_desc);

		$content_desc = str_replace("'or'", "", $content_desc);
		$content_desc = str_replace("'OR'", "", $content_desc);
		$content_desc = str_replace('"OR"', '', $content_desc);
		$content_desc = str_replace('"or"', '', $content_desc);
		$content_desc = str_replace("'A", "", $content_desc);
		$content_desc = str_replace("A'", "", $content_desc);
		$content_desc = str_replace('"A', '', $content_desc);
		$content_desc = str_replace('A"', '', $content_desc);

		$content_desc = str_replace("'1", "", $content_desc);
		$content_desc = str_replace("1'", "", $content_desc);
		$content_desc = str_replace('"1', '', $content_desc);
		$content_desc = str_replace('1"', '', $content_desc);

		$content_desc = str_replace('(', '', $content_desc);
		$content_desc = str_replace(')', '', $content_desc);
		//$content_desc = str_replace("(", "",$content_desc);
		//$content_desc = str_replace(")", "",$content_desc);

		$content_desc = str_replace('||', '', $content_desc);
		$content_desc = str_replace('|', '', $content_desc);
		$content_desc = str_replace('&&', '', $content_desc);
		$content_desc = str_replace('&', '', $content_desc);
		$content_desc = str_replace(';', '', $content_desc);
		$content_desc = str_replace('%', '', $content_desc);
		$content_desc = str_replace('$', '', $content_desc);
		$content_desc = str_replace('"', '', $content_desc);
		$content_desc = str_replace("'", '', $content_desc);
		$content_desc = str_replace('\"', '', $content_desc);
		$content_desc = str_replace("\'", "", $content_desc);
		$content_desc = str_replace('+', '', $content_desc);
		//$content_desc = preg_replace('#[^\w()/.%\-&]#','',$content_desc);
		//$content_desc = str_replace('LF','',$content_desc);
		$content_desc = str_replace('*', '', $content_desc);
		$content_desc = str_replace("'<", "", $content_desc);
		$content_desc = str_replace("'>", "", $content_desc);
		$content_desc = str_replace("<'", "", $content_desc);
		$content_desc = str_replace("'>'", "", $content_desc);
		$content_desc = str_replace("#40", "", $content_desc);
		$content_desc = str_replace("#41", "", $content_desc);
		//$content_desc = preg_replace("/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s","",$content_desc);

		return $content_desc;

	}

}
// functions for  replace Special Char form input filed  created by laukesh 
if (!function_exists('replaceSpecialChar')) {
	function replaceSpecialChar($content_desc)
	{

		$returnText = preg_replace('/[^A-Za-z0-9-.\s]/', '', $content_desc);

		return $returnText;
	}
}
// functions for remove  form input filed  created by laukesh 
if (!function_exists('clean_data_array')) {
	function clean_data_array($aRR)
	{
		$retArr = array();
		foreach ($aRR as $key => $content_desc) {

			//$content_desc = trim($content_desc);
			//$content_desc = Str::of($content_desc)->trim();
			$content_desc = str_replace('\'', '', $content_desc);

			$content_desc = str_replace('&lt;script', ' ', $content_desc);
			$content_desc = str_replace('&lt;iframe', ' ', $content_desc);
			$content_desc = str_replace('&lt;script&gt;', '', $content_desc);
			$content_desc = str_replace('&lt;SCRIPT&gt;', '', $content_desc);
			$content_desc = str_replace('&lt;SCRIPT', ' ', $content_desc);
			$content_desc = str_replace('&lt;ScRiPt&gt', '', $content_desc);
			$content_desc = str_replace('&lt;ScRiPt &gt', '', $content_desc);
			$content_desc = str_replace('&lt;IFRAME', ' ', $content_desc);

			$content_desc = str_replace('sleep', '', $content_desc);
			$content_desc = str_replace('waitfor delay', '', $content_desc);

			$content_desc = str_replace('iframe', '', $content_desc);
			$content_desc = str_replace('script', '', $content_desc);
			$content_desc = str_replace('window.', '', $content_desc);
			$content_desc = str_replace('prompt', '', $content_desc);
			$content_desc = str_replace('Prompt', '', $content_desc);

			$content_desc = str_replace('confirm', '', $content_desc);
			$content_desc = str_replace('CONTENT=', '', $content_desc);
			$content_desc = str_replace('HTTP-EQUIV', '', $content_desc);
			$content_desc = str_replace('&lt;meta', '', $content_desc);
			$content_desc = str_replace('&lt;META', '', $content_desc);
			$content_desc = str_replace('data:text/html', '', $content_desc);
			$content_desc = str_replace('document.', '', $content_desc);
			$content_desc = str_replace('url', '', $content_desc);
			$content_desc = str_replace('document.createTextNode', '', $content_desc);
			$content_desc = str_replace('document.writeln', '', $content_desc);
			$content_desc = str_replace('document.write', '', $content_desc);
			$content_desc = str_replace('alert', '', $content_desc);
			$content_desc = str_replace('javascript', '', $content_desc);
			$content_desc = str_replace('DROP', '', $content_desc);
			$content_desc = str_replace('CREATE', '', $content_desc);
			$content_desc = str_replace('onsubmit', '', $content_desc);
			$content_desc = str_replace('onblur', '', $content_desc);
			$content_desc = str_replace('onclick', '', $content_desc);
			$content_desc = str_replace('ondatabinding', '', $content_desc);
			$content_desc = str_replace('ondblclick', '', $content_desc);
			$content_desc = str_replace('ondisposed', '', $content_desc);
			$content_desc = str_replace('onfocus', '', $content_desc);
			$content_desc = str_replace('onkeydown', '', $content_desc);
			$content_desc = str_replace('onkeyup', '', $content_desc);
			$content_desc = str_replace('onload', '', $content_desc);
			$content_desc = str_replace('onmousedown', '', $content_desc);
			$content_desc = str_replace('onmousemove', '', $content_desc);
			$content_desc = str_replace('onmouseout', '', $content_desc);
			$content_desc = str_replace('onmouseover', '', $content_desc);
			$content_desc = str_replace('onmouseup', '', $content_desc);
			$content_desc = str_replace('onprerender', '', $content_desc);
			$content_desc = str_replace('onserverclick', '', $content_desc);
			$content_desc = str_replace('[removed]', '', $content_desc);

			$content_desc = str_replace('A=A', '', $content_desc);
			$content_desc = str_replace('1=1', '', $content_desc);

			//$content_desc = str_replace('<','',$content_desc);
			//$content_desc = str_replace('>','',$content_desc);
			$content_desc = str_replace('< >', '', $content_desc);
			$content_desc = str_replace("<''>", "", $content_desc);

			$content_desc = str_replace("%", "", $content_desc);

			$content_desc = str_replace("'or'", "", $content_desc);
			$content_desc = str_replace("'OR'", "", $content_desc);
			$content_desc = str_replace('"OR"', '', $content_desc);
			$content_desc = str_replace('"or"', '', $content_desc);
			$content_desc = str_replace("'A", "", $content_desc);
			$content_desc = str_replace("A'", "", $content_desc);
			$content_desc = str_replace('"A', '', $content_desc);
			$content_desc = str_replace('A"', '', $content_desc);

			$content_desc = str_replace("'1", "", $content_desc);
			$content_desc = str_replace("1'", "", $content_desc);
			$content_desc = str_replace('"1', '', $content_desc);
			$content_desc = str_replace('1"', '', $content_desc);

			$content_desc = str_replace('(', '', $content_desc);
			$content_desc = str_replace(')', '', $content_desc);
			//$content_desc = str_replace("(", "",$content_desc);
			//$content_desc = str_replace(")", "",$content_desc);

			$content_desc = str_replace('||', '', $content_desc);
			$content_desc = str_replace('|', '', $content_desc);
			$content_desc = str_replace('&&', '', $content_desc);
			$content_desc = str_replace('&', '', $content_desc);
			$content_desc = str_replace(';', '', $content_desc);
			$content_desc = str_replace('%', '', $content_desc);
			$content_desc = str_replace('$', '', $content_desc);
			$content_desc = str_replace('"', '', $content_desc);
			$content_desc = str_replace("'", '', $content_desc);
			$content_desc = str_replace('\"', '', $content_desc);
			$content_desc = str_replace("\'", "", $content_desc);
			$content_desc = str_replace('+', '', $content_desc);
			//$content_desc = str_replace('CR','',$content_desc);
			//$content_desc = str_replace('LF','',$content_desc);
			$content_desc = str_replace('*', '', $content_desc);
			$content_desc = str_replace("'<", "", $content_desc);
			$content_desc = str_replace("'>", "", $content_desc);
			$content_desc = str_replace("<'", "", $content_desc);
			$content_desc = str_replace("'>'", "", $content_desc);
			$content_desc = str_replace("#40", "", $content_desc);
			$content_desc = str_replace("#41", "", $content_desc);

			//print_R($content_desc); die();
			$retArr[$key] = $content_desc;
		}

		return $retArr;

	}

}
// functions for check File Extention in pdf format created by laukesh 
if (!function_exists('checkFileExtention')) {
	function checkFileExtention($file)
	{
		$gfex = explode('.', $file);
		if (strtolower(end($gfex)) == 'pdf') {
			return 1;
		} else {
			return 0;
		}
	}
}
// functions for store login user activity  created by Laukesh 
if (!function_exists('audit_trails')) {
	function audit_trails($data_array = array())
	{

		$whEre = array(
			'module_item_title' => isset($data_array['module_item_title']) ? $data_array['module_item_title'] : '',
			'module_item_id' => isset($data_array['module_item_id']) ? $data_array['module_item_id'] : '',
			'action_name' => isset($data_array['action_name']) ? $data_array['action_name'] : '',
			'old_data' => isset($data_array['old_data']) ? $data_array['old_data'] : '',
			'new_data' => isset($data_array['new_data']) ? $data_array['new_data'] : '',
			'action_type' => isset($data_array['action_type']) ? $data_array['action_type'] : '',
			'action_date' => date('Y-m-d h:i:s'),
			'ip_address' => $_SERVER['REMOTE_ADDR'],
			'lang_id' => isset($data_array['lang_id']) ? $data_array['lang_id'] : 1,
			'approve_status' => isset($data_array['approve_status']) ? $data_array['approve_status'] : 1,
			'action_by' => isset($data_array['action_by']) ? $data_array['action_by'] : '',
			'action_by_role' => isset($data_array['action_by_role']) ? $data_array['action_by_role'] : ''
		);

		$numRows = AuditTrail::create($whEre);
		return $numRows;
	}
}

############################ Menu For  admin
// functions for get primary  menu  created by laukesh
// modify by  laukesh 

if (!function_exists('primarylink_menu')) {
	function primarylink_menu($language_id, $menu_positions = '')
	{
		$selected = "";
		if ($menu_positions != '') {
			if ($menu_positions == 0)
				$selected = "selected";
		}

		$returnValue = '<div class="row"> 
		                    <div class="col-12 col-md-3 col-lg-3">
							<div class="form-group">
								<label>Primary Link:</label>
								<span class="star">*</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<select name="menucategory" class="input_class form-control" id="menucategory" autocomplete="off">
									<option value=""> Select </option>
									<option value ="0" ' . $selected . '>It is Root Category</option>';

		$whEre = array(
			'approve_status' => 3,
			'menu_child_id' => 0,
			'language_id' => $language_id
		);
		$nav_query = DB::table('menus')->select('*')->where($whEre)->get();
		foreach ($nav_query as $row) {
			$selected = "";
			if ($menu_positions != '') {
				if ($row->id == $menu_positions)
					$selected = "selected";
			}
			$returnValue .= '<option value="' . $row->id . '" ' . $selected . '><strong>' . $row->menu_name . '</strong></option>';

			$returnValue .= build_child_one($row->id, '', $menu_positions);
		}
		$returnValue .= '</select>
							</div>
						</div>
						</div>';

		return $returnValue;
	}
}
// functions for get child  menu  created by laukesh
// modify by  laukesh 
if (!function_exists('build_child_one')) {
	function build_child_one($parent_id, $tempReturnValue, $menu_positions)
	{

		$tempReturnValue .= $tempReturnValue;
		$whEre = array(
			'approve_status' => 3,
			'menu_child_id' => $parent_id
		);
		$nav_query = DB::table('menus')->select('*')->where($whEre)->get();
		foreach ($nav_query as $row) {
			$selected = "";
			if ($menu_positions != '') {
				if ($row->id == $menu_positions)
					$selected = "selected";
			}
			$tempReturnValue .= '<option value="' . $row->id . '" ' . $selected . '><strong>&nbsp;--&nbsp;' . $row->menu_name . '</strong></option>';
			$tempReturnValue .= build_child_two($row->id, $tempReturnValueAnother = '', $menu_positions);
		}

		return $tempReturnValue;
	}
}
// functions for get child  menu  created by laukesh
// modify by  laukesh 
if (!function_exists('build_child_two')) {
	function build_child_two($parent_id, $tempReturnValue, $menu_positions)
	{

		$tempReturnValue .= $tempReturnValue;
		$whEre = array(
			'approve_status' => 3,
			'menu_child_id' => $parent_id
		);
		$nav_query = DB::table('menus')->select('*')->where($whEre)->get();
		foreach ($nav_query as $row) {
			$selected = "";
			if ($menu_positions != '') {
				if ($row->id == $menu_positions)
					$selected = "selected";
			}
			$tempReturnValue .= '<option value="' . $row->id . '" ' . $selected . '><strong>&nbsp;&nbsp;&nbsp;--&nbsp;' . $row->menu_name . '</strong></option>';
			$tempReturnValue .= build_child_three($row->id, $tempReturnValueAnother = '', $menu_positions);
		}

		return $tempReturnValue;
	}
}
// functions for get child  menu  created by laukesh
// modify by  laukesh 
if (!function_exists('build_child_three')) {
	function build_child_three($parent_id, $tempReturnValue, $menu_positions)
	{

		$tempReturnValue .= $tempReturnValue;
		$whEre = array(
			'approve_status' => 3,
			'menu_child_id' => $parent_id
		);
		$nav_query = DB::table('menus')->select('*')->where($whEre)->get();
		foreach ($nav_query as $row) {
			$selected = "";
			if ($menu_positions != '') {
				if ($row->id == $menu_positions)
					$selected = "selected";
			}
			$tempReturnValue .= '<option value="' . $row->id . '" ' . $selected . '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;' . $row->menu_name . '</option>';
			$tempReturnValue .= build_child_four($row->id, $tempReturnValueAnother = '', $menu_positions);
		}

		return $tempReturnValue;
	}
}

// functions for get child  menu  created by laukesh
// modify by  laukesh 

if (!function_exists('build_child_four')) {
	function build_child_four($parent_id, $tempReturnValue, $menu_positions)
	{

		$tempReturnValue .= $tempReturnValue;
		$whEre = array(
			'approve_status' => 3,
			'menu_child_id' => $parent_id
		);
		$nav_query = DB::table('menus')->select('*')->where($whEre)->get();
		foreach ($nav_query as $row) {
			$selected = "";
			if ($menu_positions != '') {
				if ($row->id == $menu_positions)
					$selected = "selected";
			}
			$tempReturnValue .= '<option value="' . $row->id . '" ' . $selected . '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;' . $row->menu_name . '</option>';
			$tempReturnValue .= build_child_five($row->id, $tempReturnValueAnother = '', $menu_positions);
		}

		return $tempReturnValue;
	}
}

// functions for get child  menu  created by laukesh
// modify by  laukesh 

if (!function_exists('build_child_five')) {
	function build_child_five($parent_id, $tempReturnValue, $menu_positions)
	{

		$tempReturnValue .= $tempReturnValue;
		$whEre = array(
			'approve_status' => 3,
			'menu_child_id' => $parent_id
		);
		$nav_query = DB::table('menus')->select('*')->where($whEre)->get();
		foreach ($nav_query as $row) {
			$selected = "";
			if ($menu_positions != '') {
				if ($row->id == $menu_positions)
					$selected = "selected";
			}
			$tempReturnValue .= '<option value="' . $row->id . '" ' . $selected . '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;' . $row->menu_name . '</option>';
			$tempReturnValue .= build_child_six($row->id, $tempReturnValueAnother = '', $menu_positions);
		}

		return $tempReturnValue;
	}
}
// functions for get child  menu  created by laukesh
// modify by  laukesh 

if (!function_exists('build_child_six')) {
	function build_child_six($parent_id, $tempReturnValue, $menu_positions)
	{

		$tempReturnValue .= $tempReturnValue;


		$whEre = array(
			'approve_status' => 3,
			'menu_child_id' => $parent_id
		);

		$nav_query = DB::table('menus')->select('*')->where($whEre)->get();
		foreach ($nav_query as $row) {
			$selected = "";
			if ($menu_positions != '') {
				if ($row->id == $menu_positions)
					$selected = "selected";
			}
			$tempReturnValue .= '<option value="' . $row->id . '" ' . $selected . '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;' . $row->menu_name . '</option>';
			$tempReturnValue .= build_child_seven($row->id, $tempReturnValueAnother = '', $menu_positions);
		}

		return $tempReturnValue;
	}
}

// functions for get child  menu  created by laukesh
// modify by  laukesh 
if (!function_exists('build_child_seven')) {
	function build_child_seven($parent_id, $tempReturnValue, $menu_positions)
	{

		$tempReturnValue .= $tempReturnValue;


		$whEre = array(
			'approve_status' => 3,
			'menu_child_id' => $parent_id
		);

		$nav_query = DB::table('menus')->select('*')->where($whEre)->get();
		foreach ($nav_query as $row) {
			$selected = "";
			if ($menu_positions != '') {
				if ($row->id == $menu_positions)
					$selected = "selected";
			}
			$tempReturnValue .= '<option value="' . $row->id . '" ' . $selected . '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;' . $row->menu_name . '</option>';
		}

		return $tempReturnValue;
	}
}


############################menu end

/// Memu for Themes 
// functions for get   menu  created by  laukesh 
if (!function_exists('get_menu')) {
	function get_menu($language_id, $menu_positions, $menu_child_id = '')
	{      // dd($menu_positions);
		$whEre = array(
			'approve_status' => 3,
			'menu_child_id' => $menu_child_id,
			'language_id' => $language_id
		);
		$nav_query = DB::table('menus')->select('*')->where($whEre)->whereIn('menu_position', $menu_positions)->whereNull('deleted_at')->orderBy('page_order', 'asc')->get();


		return $nav_query;
	}
}
// functions for create seo link and text created by  laukesh 
if (!function_exists('seo_url')) {
	function seo_url($seo_url)
	{

		$seo_url = preg_replace('/\s+/', ' ', $seo_url);
		$seo_url = str_replace('&', '-', $seo_url);
		$seo_url = str_replace('amp;', 'and', $seo_url);
		$seo_url = str_replace('/', '', $seo_url);
		$seo_url = str_replace('%', '', $seo_url);
		$seo_url = str_replace('*', '', $seo_url);
		$seo_url = str_replace('(', '', $seo_url);
		$seo_url = str_replace(')', '', $seo_url);
		$seo_url = str_replace('!', '', $seo_url);
		$seo_url = str_replace('@', '', $seo_url);
		$seo_url = str_replace('#', '', $seo_url);
		$seo_url = str_replace('}', '', $seo_url);
		$seo_url = str_replace('{', '', $seo_url);
		$seo_url = str_replace(']', '', $seo_url);
		$seo_url = str_replace('[', '', $seo_url);
		$seo_url = str_replace(',', '-', $seo_url);
		$seo_url = str_replace('.', '', $seo_url);
		$seo_url = str_replace('?', '', $seo_url);
		$seo_url = str_replace("'", '', $seo_url);
		$seo_url = str_replace(' ', '-', $seo_url);
		return strtolower($seo_url) . '.php';
	}
}
// functions for cheked status created by  laukesh 
if (!function_exists('get_status')) {
	function get_status()
	{

		$status = array(
			'1' => "Draft",
			'2' => "Aproval",
			'3' => "Publish"
		);
		return $status;
	}
}




// functions for set positions of menu created by  laukesh 
if (!function_exists('get_content_position')) {
	function get_content_position()
	{

		$position = array(
			'1' => "Header Menu",
			'2' => "Common Menu",
			'3' => "Footer Menu",
			'4' => "Header & Footer Menu",
			'5' => "Page/Section"
		);
		return $position;
	}
}
// functions for get/set language created by  laukesh 
if (!function_exists('get_language')) {
	function get_language()
	{
        $language =  DB::table('languages')->select('*')->get();
		//dd($language);
		return $language;
	}
}
// functions for get/set status active/inactive created by  laukesh 
if (!function_exists('get_active')) {
	function get_active()
	{

		$language = array(
			'1' => "Active",
			'2' => "In Active"
		);
		return $language;
	}
}

// functions for count number of child menu created by  laukesh 
if (!function_exists('has_child')) {
	function has_child($pid, $langid)
	{

		$fetchResult = DB::table('menus')->where('menu_child_id', $pid)->where('language_id', $langid)->where('approve_status', 3)->whereNull('deleted_at')->exists();
		return $fetchResult;

	}
}

// functions for checked language created by  laukesh 
function language($val)
{
	if ($val == 'hi')
		echo "Hindi";
	
	else if ($val == 'en')
		echo "English";
	else
		echo "English";
}
// functions for checked status created by  laukesh 
function status($val)
{
	if ($val == 1) {
		echo "Draft";
	} else if ($val == 2) {
		echo "For Approval";
	} else if ($val == 3) {
		echo "Publish";
	} else {
		echo "Review";
	}
}

// functions for get/set Noticetype  created by  laukesh 
if (!function_exists('get_noticetype')) {
	function get_noticetype()
	{

		$Theme = array(
			'1' => "Events",
			'2' => "Circulars",
			'3' => "News",
			'4' => "Notifications",
			'5' => "Important Notice",
			'6' => "Media Print"
		);
		return $Theme;
	}
}
// functions for get/set Division  created by  laukesh 
if (!function_exists('get_division')) {
	function get_division()
	{
		$Division = array(
			'admin' => "Admin",
			'operation' => "Operation",
			'oc' => "Oversight and complience (O&C)",
			'policy' => "Policy",
			'tech' => "Tech",
			'training' => "Training",
			'other' => "Other"
		);
		return $Division;
	}
}
// functions for get  center  created by  laukesh 
if (!function_exists('get_register_center')) {
	function get_register_center($id=null)
	{
		if (!empty($id)) {
			$whEre = ['status' => 3, 'id' => $id];
		} else {
			$whEre = ['status' => 3];
		}
		$nav_query = DB::table('division_register_centers')->select('*')->where($whEre)->orderBy('created_at', 'DESC')->get();
		return $nav_query;
	}
}
// functions for get  award categories  created by  laukesh 
if (!function_exists('get_award_categories')) {
	function get_award_categories($id=null)
	{
		if (!empty($id)) {
			$whEre = ['status' => 3, 'id' => $id];
		} else {
			$whEre = ['status' => 3];
		}
		$nav_query = DB::table('division_awards_categories')->select('*')->where($whEre)->orderBy('created_at', 'DESC')->get();
		return $nav_query;
	}
}
// functions for get  certificates categories  created by  laukesh 
if (!function_exists('get_certificates_categories')) {
	function get_certificates_categories($id=null)
	{
		if (!empty($id)) {
			$whEre = ['status' => 3, 'id' => $id];
		} else {
			$whEre = ['status' => 3];
		}
		$nav_query = DB::table('division_certificates_categories')->select('*')->where($whEre)->orderBy('created_at', 'DESC')->get();
		return $nav_query;
	}
}
// functions for get  award course  created by  laukesh 
if (!function_exists('get_course_categories')) {
	function get_course_categories($id=null)
	{
		if (!empty($id)) {
			$whEre = ['status' => 3, 'id' => $id];
		} else {
			$whEre = ['status' => 3];
		}
		$nav_query = DB::table('division_course_categories')->select('*')->where($whEre)->orderBy('created_at', 'DESC')->get();
		return $nav_query;
	}
}
// functions for get  center  created by  laukesh 
if (!function_exists('get_gallery_categories')) {
	function get_gallery_categories($id=null)
	{
		if (!empty($id)) {
			$whEre = ['status' => 3, 'id' => $id];
		} else {
			$whEre = ['status' => 3];
		}
		$nav_query = DB::table('division_gallery_categories')->select('*')->where($whEre)->orderBy('created_at', 'DESC')->get();
		return $nav_query;
	}
}
// functions for get  center  created by  laukesh 
if (!function_exists('get_document_categories')) {
	function get_document_categories($id=null)
	{
		if (!empty($id)) {
			$whEre = ['status' => 3, 'id' => $id];
		} else {
			$whEre = ['status' => 3];
		}
		$nav_query = DB::table('division_document_categories')->select('*')->where($whEre)->orderBy('created_at', 'DESC')->get();
		return $nav_query;
	}
}
// functions for get  center  created by  laukesh 
if (!function_exists('get_commontitle')) {
	function get_commontitle($slugs=null,$langcode=null)
	{
		$whEre = ['status' => 3, 'slugs' => $slugs, 'lang_code' => $langcode]; 
		$nav_query = DB::table('common_titles')
		->select('*')
		->where($whEre)
		->orderBy('created_at', 'DESC')
		->first();
		//dd($nav_query);
		return $nav_query;
	}
}
// functions for get/set Noticetype all date created by  laukesh   
if (!function_exists('get_withoutDatenoticelist')) {
	function get_withoutDatenoticelist($is_new, $circularstype)
	{
		$whEre = array('txtstatus' => 3, 'is_new' => $is_new, 'circularstype' => $circularstype);
		$today=date('Y-m-d');
		 $nav_query = DB::table('circulars')->select('*')->where($whEre)->orderBy('created_at', 'DESC')->get();
		return $nav_query;
	}
}
// functions for get/set circularstype  created by  laukesh 
if (!function_exists('circularstype')) {
	function circularstype($type)
	{

		if ($type == 1) {
			$type = 'Events';
		} elseif ($type == 2) {
			$type = 'Circulars';
		} elseif ($type == 3) {
			$type = 'News';

		} elseif ($type == 4) {
			$type = 'Notifications';

		}
		elseif ($type == 6) {
			$type = 'Media Print';

		} else {
			$type = 'Important Notice';
		}
		return $type;
	}
}

// functions for  get  parent menu name  using title and language id created by  laukesh 
if (!function_exists('get_parent_menu_name')) {
	function get_parent_menu_name($url, $langid1)
	{
		$result = '';
		$date = Menu::where('menu_url', 'LIKE', "%{$url}%")->where('language_id', '=', $langid1)->where('approve_status', '=', 3)->select('menu_child_id')->first();
		if ($date) {
			$result = Menu::where('id', $date->menu_child_id)->select('menu_url', 'menu_name')->first();
		}
		return $result;
	}
}




