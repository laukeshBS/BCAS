<?php
namespace App\Http\Controllers\Cms;

use App\Models\Cms\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Cms\BaseController as BaseController;

class MenuController extends BaseController
{
    public function data(Request $request)
    {
        $perPage = $request->input('limit');
        $page = $request->input('currentPage');
        $menus = Menu::select(
            'id',
            'menu_type',
            'menu_child_id',
            'menu_position',
            'language_id',
            'menu_name',
            'menu_url',
            'menu_title',
            'menu_keyword',
            'menu_description',
            'content',
            'doc_upload',
            'menu_links',
            'page_order',
            'current_version',
            'welcomedescription',
            'banner_img',
            'img_upload'
        )
        ->orderBy('id', 'ASC')
        ->paginate($perPage, ['*'], 'page', $page);
    


        if ($menus->isNotEmpty()) {
            $menus->transform(function ($item) {
                $item->created_at = date('d-m-Y', strtotime($item->created_at));
                if ($item->doc_upload) {
                    $item->doc_upload = asset('public/uploads/admin/cmsfiles/menus/' . $item->doc_upload) ;
                }
                if ($item->banner_img) {
                    $item->banner_img = asset('public/uploads/admin/cmsfiles/menus/' . $item->banner_img) ;
                }
                if ($item->img_upload) {
                    $item->img_upload = asset('public/uploads/admin/cmsfiles/menus/' . $item->img_upload) ;
                }
                return $item;
            });
            return response()->json([
                'title' => 'Menu List',
                'data' => $menus->items(), // Get items for the current page
                'total' => $menus->total(), // Total number of items
                'current_page' => $menus->currentPage(), // Current page number
                'last_page' => $menus->lastPage(), // Last page number
                'per_page' => $menus->perPage(), // Items per page
            ]);
        } else {
            return $this->sendError('No menus found for the given language code.', 404);
        }
    }
    public function data_by_id($id)
    {
        // Validate the ID
        $validatedId = filter_var($id, FILTER_VALIDATE_INT);
        if (!$validatedId) {
            return response()->json([
                'error' => 'Invalid ID format'
            ], 400);
        }

        // Retrieve the data by ID
        $data = Menu::find($validatedId);

        // Return a 404 response if data is not found
        if (!$data) {
            return response()->json([
                'error' => 'Data not found'
            ], 404);
        }
        $data->created_at = date('d-m-Y', strtotime($data->created_at));

        // Return the data as JSON
        return response()->json($data);
    }
    public function store(Request $request)
    {
        // Validate incoming data
        $validator = Validator::make($request->all(), [
            'menu_type' => 'required|string|max:255',
            'menu_child_id' => 'nullable|integer',
            'menu_position' => 'required|string|max:255',
            'language_id' => 'required|string|max:10',
            'menu_name' => 'required|string|max:255',
            'menu_url' => 'required|max:255',
            'menu_title' => 'nullable|string|max:255',
            'menu_keyword' => 'nullable|string|max:255',
            'menu_description' => 'nullable|string',
            'content' => 'nullable|string',
            'doc_upload' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'banner_img' => 'nullable|file|image|max:2048',
            'img_upload' => 'nullable|file|image|max:2048',
            'page_order' => 'required|integer',
            'current_version' => 'nullable|string|max:10',
            'welcomedescription' => 'nullable|string',
            'approve_status' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Handle file uploads
        $data = $request->all();

        if ($request->hasFile('doc_upload')) {
            $docUpload = $request->file('doc_upload');
            $docPath = time() . '_' . $docUpload->getClientOriginalName();
            $docUpload->move(public_path('uploads/admin/cmsfiles/menus/'), $docPath);
            $data['doc_upload'] = $docPath;
        }

        if ($request->hasFile('banner_img')) {
            $bannerImg = $request->file('banner_img');
            $bannerPath = time() . '_' . $bannerImg->getClientOriginalName();
            $bannerImg->move(public_path('uploads/admin/cmsfiles/menus/'), $bannerPath);
            $data['banner_img'] = $bannerPath;
        }

        if ($request->hasFile('img_upload')) {
            $imgUpload = $request->file('img_upload');
            $imgPath = time() . '_' . $imgUpload->getClientOriginalName();
            $imgUpload->move(public_path('uploads/admin/cmsfiles/menus/'), $imgPath);
            $data['img_upload'] = $imgPath;
        }

        // Create a new menu entry
        $menu = Menu::create($data);

        return response()->json(['data' => $menu, 'message' => 'Menu created successfully.'], 201);
    }
    public function update(Request $request, $id)
    {
        // Find the existing menu entry
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json(['message' => 'Menu not found.'], 404);
        }

        // Validate incoming data
        $validator = Validator::make($request->all(), [
            'menu_type' => 'required|string|max:255',
            'menu_child_id' => 'nullable|integer',
            'menu_position' => 'required|string|max:255',
            'language_id' => 'required|string|max:10',
            'menu_name' => 'required|string|max:255',
            'menu_url' => 'required|string|max:255',
            'menu_title' => 'nullable|string|max:255',
            'menu_keyword' => 'nullable|string|max:255',
            'menu_description' => 'nullable|string',
            'content' => 'nullable|string',
            'doc_upload' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'banner_img' => 'nullable|file|image|max:2048',
            'img_upload' => 'nullable|file|image|max:2048',
            'page_order' => 'required|integer',
            'current_version' => 'nullable|string|max:10',
            'welcomedescription' => 'nullable|string',
            'approve_status' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Update data with request input
        $data = $request->all();

        try {
            // Handle file uploads
            if ($request->hasFile('doc_upload')) {
                $docUpload = $request->file('doc_upload');
                $docPath = time() . '_' . $docUpload->getClientOriginalName();
                $docUpload->move(public_path('uploads/admin/cmsfiles/menus/'), $docPath);
                $data['doc_upload'] = $docPath;
            }

            if ($request->hasFile('banner_img')) {
                $docUpload = $request->file('banner_img');
                $docPath = time() . '_' . $docUpload->getClientOriginalName();
                $docUpload->move(public_path('uploads/admin/cmsfiles/menus/'), $docPath);
                $data['banner_img'] = $docPath;
            }

            if ($request->hasFile('img_upload')) {
                $docUpload = $request->file('img_upload');
                $docPath = time() . '_' . $docUpload->getClientOriginalName();
                $docUpload->move(public_path('uploads/admin/cmsfiles/menus/'), $docPath);
                $data['img_upload'] = $docPath;
            }

            // Update the menu entry
            $menu->update($data);

            return response()->json(['data' => $menu, 'message' => 'Menu updated successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while updating the menu.'], 500);
        }
    }
    public function delete($id)
    {
        $actandpolicy = Menu::find($id);

        if (!$actandpolicy) {
            return response()->json([
                'error' => 'Not Found.'
            ], 400);
        }
        $actandpolicy->delete();

        return response()->json($actandpolicy);
    }


    // web
    public function index(Request $request): JsonResponse
    {
        // Get the parameters from the request
        $lang_code = $request->input('lang_code');
        $menu_child_id = $request->input('menu_child_id', 0);
        $menu_position = $request->input('menu_position');
//dd($request);
        // Validate the lang_code parameter
        if (!$lang_code) {
            return response()->json(['error' => 'Lang code parameter is missing.'], 400);
        }

        // Retrieve the menu items
        $menus = Menu::where('language_id', $lang_code)
            ->where('menu_child_id', $menu_child_id)
            ->where('approve_status', 3)
            ->when($menu_position, function ($query) use ($menu_position) {
                return $query->where('menu_position', $menu_position);
            })
            ->with(['children' => function ($query) use ($menu_position) {
                $query->where('approve_status', 3)
                    ->when($menu_position, function ($query) use ($menu_position) {
                        return $query->where('menu_position', $menu_position);
                    })
                    ->orderBy('page_order', 'ASC');
            }])
            ->orderBy('page_order', 'ASC')
            ->get();

        // Check if any menus were found
        if ($menus->isEmpty()) {
            return response()->json(['error' => 'No menus found for the given language code.'], 404);
        }

        // Prepare the base URL for media paths
        $baseUrl = url('public/uploads/admin/cmsfiles/menus/');

        // Append base URL to media paths in the menu items
        $this->appendBaseUrlToMedia($menus, $baseUrl);

        // Return the menu list as a successful response
        return response()->json(['success' => true, 'data' => $menus], 200);
    }

    private function appendBaseUrlToMedia($menus, $baseUrl)
    {
        foreach ($menus as $menu) {
            $this->updateMediaUrls($menu, $baseUrl);
        }
    }

    private function updateMediaUrls($menu, $baseUrl)
    {
        if ($menu->doc_upload) {
            $menu->doc_upload = $baseUrl . '/' . $menu->doc_upload;
        }
        if ($menu->img_upload) {
            $menu->img_upload = $baseUrl . '/' . $menu->img_upload;
        }
        if ($menu->banner_img) {
            $menu->banner_img = $baseUrl . '/' . $menu->banner_img;
        }

        foreach ($menu->children as $childMenu) {
            $this->updateMediaUrls($childMenu, $baseUrl);
        }
    }
    public function lang_slugs_wise(Request $request): JsonResponse
    {
        $data = $request->all();
        $menu_url = $data['menu_url'] ?? null;
       
        $lang_code = $data['lang_code'] ?? null;
        //dd($data);
        if (!$lang_code) {
            return $this->sendError('Lang code parameter is missing.', 400);
        }
      
        // Retrieve all matching menu items, not just the first one
        $menus = Menu::where('language_id', $lang_code)->where('approve_status', 3)
            ->where('menu_url', $menu_url)->with(['children' => function ($query) {
                $query->where('approve_status', 3)
                      ->orderBy('page_order', 'ASC');
            }])
            ->select(
                'id',
                'menu_type',
                'menu_child_id',
                'menu_position',
                'language_id',
                'menu_name',
                'menu_url',
                'menu_title',
                'menu_keyword',
                'menu_description',
                'content',
                'doc_upload',
                'menu_links',
                'page_order',
                'current_version',
                'welcomedescription',
                'banner_img',
                'img_upload'
            )->orderBy('page_order','ASC')
            ->get(); // Use get() to retrieve all matching records

            if ($menus->isNotEmpty()) {
                $baseUrl = url('public/uploads/admin/cmsfiles/menus/');
    
                // Append base URL to media paths in the `children` relationship
                $menus->each(function ($menu) use ($baseUrl) {
                    if ($menu->doc_upload) {
                        $menu->doc_upload = $baseUrl . '/' . $menu->doc_upload;
                    }
                    if ($menu->img_upload) {
                        $menu->img_upload = $baseUrl . '/' . $menu->img_upload;
                    }
                    if ($menu->banner_img) {
                        $menu->banner_img = $baseUrl . '/' . $menu->banner_img;
                    }
                    $menu->children->each(function ($childMenu) use ($baseUrl) {
                        if ($childMenu->doc_upload) {
                            $childMenu->doc_upload = $baseUrl . '/' . $childMenu->doc_upload;
                        }
                        if ($childMenu->img_upload) {
                            $childMenu->img_upload = $baseUrl . '/' . $childMenu->img_upload;
                        }
                        if ($childMenu->banner_img) {
                            $childMenu->banner_img = $baseUrl . '/' . $childMenu->banner_img;
                        }
                    });
                });
    
                return $this->sendResponse($menus, 'Menu List For Instructor Retrieved Successfully.');
            } else {
                return $this->sendError('No menus found for the given language code.', 404);
            }
    }
    public function lang_pid_wise(Request $request): JsonResponse
    {
        $data = $request->all();
        $pid = $data['pid'] ?? null;
       
        $lang_code = $data['lang_code'] ?? null;
        //dd($data);
        if (!$lang_code) {
            return $this->sendError('Lang code parameter is missing.', 400);
        }
      
        // Retrieve all matching menu items, not just the first one
        $menus = Menu::where('language_id', $lang_code)->where('approve_status', 3)
            ->where('menu_child_id', $pid)->with(['children' => function ($query) {
                $query->where('approve_status', 3)
                      ->orderBy('page_order', 'ASC');
            }])
            ->select(
                'id',
                'menu_type',
                'menu_child_id',
                'menu_position',
                'language_id',
                'menu_name',
                'menu_url',
                'menu_title',
                'menu_keyword',
                'menu_description',
                'content',
                'doc_upload',
                'menu_links',
                'page_order',
                'current_version',
                'welcomedescription',
                'img_upload'
            )->orderBy('page_order','ASC')
            ->get(); // Use get() to retrieve all matching records

            if ($menus->isNotEmpty()) {
                $baseUrl = url('public/uploads/admin/cmsfiles/menus/');
    
                // Append base URL to media paths in the `children` relationship
                $menus->each(function ($menu) use ($baseUrl) {
                    if ($menu->doc_upload) {
                        $menu->doc_upload = $baseUrl . '/' . $menu->doc_upload;
                    }
                    if ($menu->img_upload) {
                        $menu->img_upload = $baseUrl . '/' . $menu->img_upload;
                    }
                    if ($menu->banner_img) {
                        $menu->banner_img = $baseUrl . '/' . $menu->banner_img;
                    }
                    $menu->children->each(function ($childMenu) use ($baseUrl) {
                        if ($childMenu->doc_upload) {
                            $childMenu->doc_upload = $baseUrl . '/' . $childMenu->doc_upload;
                        }
                        if ($childMenu->img_upload) {
                            $childMenu->img_upload = $baseUrl . '/' . $childMenu->img_upload;
                        }
                        if ($childMenu->banner_img) {
                            $childMenu->banner_img = $baseUrl . '/' . $childMenu->banner_img;
                        }
                    });
                });
    
                return $this->sendResponse($menus, 'Menu List For Instructor Retrieved Successfully.');
            } else {
                return $this->sendError('No menus found for the given language code.', 404);
            }
    }
    
    
    public function importCSVairports(Request $request)
    {
        // Validate the uploaded file
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Open the file
        $file = fopen($request->file('csv_file')->getRealPath(), 'r');

        if (!$file) {
            return response()->json(['error' => 'Failed to open the file.'], 500);
        }

        // Read the header row
        $header = fgetcsv($file, 1000, ',');

        // Check if header is valid
        if (!$header || count($header) < 13) { // Ensure at least 13 columns for the header
            fclose($file);
            return response()->json(['error' => 'Invalid CSV file format.'], 400);
        }

        $rowNumber = 1;
        while (($row = fgetcsv($file, 1000, ',')) !== false) {
            // Check if the row length matches the header length
            if (count($row) != count($header)) {
                echo "count error";
                // Skip this row and proceed to the next one
                continue;
            }

            // Create an associative array using header names
            $data = array_combine($header, $row);

            if (!$data) {
                echo "count combinations";
                continue; // Skip if array_combine fails
            }

            // Prepare data for insertion
            $insertData = [
                'region_name' => $data['region_name'] ?? null,
                'sr_no' => isset($data['sr_no']) && is_numeric($data['sr_no']) ? (int)$data['sr_no'] : null,
                'airport_name' => $data['airport_name'] ?? null,
                'entity_name' => $data['entity_name'] ?? null,
                'address' => $data['address'] ?? null,
                'mobile_no' => $data['mobile_no'] ?? null,
                'phone_no' => $data['phone_no'] ?? null,
                'unique_reference_number' => $data['unique_reference_number'] ?? null,
                'approved_status_clearance' => $data['approved_status_clearance'] ?? null,
                'date_of_approval_clearance' => $data['date_of_approval_clearance'] ?? null,
                'approved_status_programme' => $data['approved_status_programme'] ?? null,
                'date_of_approval_programme' => $data['date_of_approval_programme'] ?? null,
                'valid_till' => $data['valid_till'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert data into the database
            try {
                DB::table('working_airports')->insert($insertData);
            } catch (\Exception $e) {
                // Log or handle the error
                // Example: Log::error('Import Error', ['row' => $row, 'error' => $e->getMessage()]);
                continue; // Skip the row with error and continue
            }

            $rowNumber++;
        }

        fclose($file);

        return response()->json(['message' => 'CSV data imported successfully.'], 200);
    }
    public function importCSVAirlines(Request $request)
    {
        // Validate the uploaded file
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Open the file
        $file = fopen($request->file('csv_file')->getRealPath(), 'r');

        if (!$file) {
            return response()->json(['error' => 'Failed to open the file.'], 500);
        }

        // Read the header row
        $header = fgetcsv($file, 1000, ',');

        // Check if header is valid
        if (!$header || count($header) < 10) { // Ensure at least 13 columns for the header
            fclose($file);
            return response()->json(['error' => 'Invalid CSV file format.'], 400);
        }

        $rowNumber = 1;
        while (($row = fgetcsv($file, 1000, ',')) !== false) {
            // Check if the row length matches the header length
            if (count($row) != count($header)) {
                echo "count error";
                // Skip this row and proceed to the next one
                continue;
            }

            // Create an associative array using header names
            $data = array_combine($header, $row);

            if (!$data) {
                echo "count combinations";
                continue; // Skip if array_combine fails
            }

            // Prepare data for insertion
            $insertData = [
                'application_id' => $data['application_id'] ?? null,
                'entity_name' => $data['entity_name'] ?? null,
                'cso_acso_name' => $data['cso_acso_name'] ?? null,
                'cso_acso_email' => $data['cso_acso_email'] ?? null,
                'station_name' => $data['station_name'] ?? null,
                'air_type' => $data['air_type'] ?? null,
                'date_of_approval' => isset($data['date_of_approval']) ? date('Y-m-d', strtotime($data['date_of_approval'])) : null,
                'status' => $data['status'] ?? null,
                'date_of_validity' => isset($data['date_of_validity']) ? date('Y-m-d', strtotime($data['date_of_validity'])) : null,
                'lang_code' => $data['lang_code'] ?? null,
                'created_by' => isset($data['created_by']) && is_numeric($data['created_by']) ? (int)$data['created_by'] : null,
                'created_at' => now(),
                'updated_at' => now(),
               
            ];
            

            // Insert data into the database
            try {
            //     echo"<pre>";
              
            //    print_r($insertData );
            //   echo"</pre>";

                DB::table('airlines')->insert($insertData);
            } catch (\Exception $e) {
                // Log or handle the error
              echo"<pre>";
              echo $e->getMessage();
               print_r($row );
              echo"</pre>";
               //  Example: Log::error('Import Error', ['row' => $row, 'error' => $e->getMessage()]);
                continue; // Skip the row with error and continue
            }

            $rowNumber++;
        }

        fclose($file);

        return response()->json(['message' => 'CSV data imported successfully.'], 200);
    }
    public function importCSVcatring(Request $request)
    {
        // Validate the uploaded file
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Open the file
        $file = fopen($request->file('csv_file')->getRealPath(), 'r');

        if (!$file) {
            return response()->json(['error' => 'Failed to open the file.'], 500);
        }

        // Read the header row
        $header = fgetcsv($file, 1000, ',');

        // Check if header is valid
        if (!$header || count($header) < 9) { // Ensure at least 13 columns for the header
            fclose($file);
            return response()->json(['error' => 'Invalid CSV file format.'], 400);
        }

        $rowNumber = 1;
        while (($row = fgetcsv($file, 1000, ',')) !== false) {
            // Check if the row length matches the header length
            if (count($row) != count($header)) {
                echo "count error";
                // Skip this row and proceed to the next one
                continue;
            }

            // Create an associative array using header names
            $data = array_combine($header, $row);

            if (!$data) {
                echo "count combinations";
                continue; // Skip if array_combine fails
            }

            // Prepare data for insertion
            $insertData = [
                'regional_office' => $data['regional_office'] ?? null,
                'airport_name' => $data['airport_name'] ?? null,
                'entity_name' => $data['entity_name'] ?? null,
                'date_of_security_clearance' => isset($data['date_of_security_clearance']) ? date('Y-m-d', strtotime($data['date_of_security_clearance'])) : null,
                'date_of_security_programme_approval' => isset($data['date_of_security_programme_approval']) ? date('Y-m-d', strtotime($data['date_of_security_programme_approval'])) : null,
                'status' => $data['status'] ?? null,
                'division' => $data['division'] ?? null,
                'date_of_validity' => isset($data['date_of_validity']) ? date('Y-m-d', strtotime($data['date_of_validity'])) : null,
                'lang_code' => $data['lang_code'] ?? null,
                'created_by' => isset($data['created_by']) && is_numeric($data['created_by']) ? (int)$data['created_by'] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            // Insert data into the database
            try {
            //     echo"<pre>";
              
            //    print_r($insertData );
            //   echo"</pre>";

                DB::table('catering_companies')->insert($insertData);
            } catch (\Exception $e) {
                // Log or handle the error
              echo"<pre>";
              echo $e->getMessage();
               print_r($row );
              echo"</pre>";
               //  Example: Log::error('Import Error', ['row' => $row, 'error' => $e->getMessage()]);
                continue; // Skip the row with error and continue
            }

            $rowNumber++;
        }

        fclose($file);

        return response()->json(['message' => 'CSV data imported successfully.'], 200);
    }
    public function importCSVOps(Request $request)
    {
        // Validate the uploaded file
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Open the file
        $file = fopen($request->file('csv_file')->getRealPath(), 'r');

        if (!$file) {
            return response()->json(['error' => 'Failed to open the file.'], 500);
        }

        // Read the header row
        $header = fgetcsv($file, 1000, ',');

        // Check if header is valid
        if (!$header || count($header) < 13) { // Ensure at least 13 columns for the header
            fclose($file);
            return response()->json(['error' => 'Invalid CSV file format.'], 400);
        }

        $rowNumber = 1;
        while (($row = fgetcsv($file, 1000, ',')) !== false) {
            // Check if the row length matches the header length
            if (count($row) != count($header)) {
                echo "count error";
                // Skip this row and proceed to the next one
                continue;
            }

            // Create an associative array using header names
            $data = array_combine($header, $row);

            if (!$data) {
                echo "count combinations";
                continue; // Skip if array_combine fails
            }

            // Prepare data for insertion
            $insertData = [
                'application_id' => $data['application_id'] ?? null,
                'airport_name' => $data['airport_name'] ?? null,
                'entity_name' => $data['entity_name'] ?? null,
                'resion_name' => $data['resion_name'] ?? null,
                'cso_acso_name' => $data['cso_acso_name'] ?? null,
                'cso_acso_email' => $data['cso_acso_email'] ?? null,
                'cso_acso_mobile' => $data['cso_acso_mobile'] ?? null,
                'station_name' => $data['station_name'] ?? null,
                'date_of_approval' => isset($data['date_of_approval']) ? date('Y-m-d', strtotime($data['date_of_approval'])) : null,
                'status' => $data['status'] ?? null,
                'division' => $data['division'] ?? null,
                'sec_type' => $data['sec_type'] ?? null,
                'date_of_validity' => isset($data['date_of_validity']) ? date('Y-m-d', strtotime($data['date_of_validity'])) : null,
                'lang_code' => $data['lang_code'] ?? null,
                'created_by' => isset($data['created_by']) && is_numeric($data['created_by']) ? (int)$data['created_by'] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            
            // Insert data into the database
            try {
            //     echo"<pre>";
              
            //    print_r($insertData );
            //   echo"</pre>";

                DB::table('ops_securities')->insert($insertData);
            } catch (\Exception $e) {
                // Log or handle the error
              echo"<pre>";
              echo $e->getMessage();
               print_r($row );
              echo"</pre>";
               //  Example: Log::error('Import Error', ['row' => $row, 'error' => $e->getMessage()]);
                continue; // Skip the row with error and continue
            }

            $rowNumber++;
        }

        fclose($file);

        return response()->json(['message' => 'CSV data imported successfully.'], 200);
    }
    public function importCSV(Request $request)
    {
        // Validate the uploaded file
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Open the file
        $file = fopen($request->file('csv_file')->getRealPath(), 'r');

        if (!$file) {
            return response()->json(['error' => 'Failed to open the file.'], 500);
        }

        // Read the header row
        $header = fgetcsv($file, 1000, ',');

        // Check if header is valid
        if (!$header || count($header) < 18) { // Ensure at least 13 columns for the header
            fclose($file);
            return response()->json(['error' => 'Invalid CSV file format.'], 400);
        }

        $rowNumber = 1;
        while (($row = fgetcsv($file, 1000, ',')) !== false) {
            // Check if the row length matches the header length
            if (count($row) != count($header)) {
                echo "count error";
                // Skip this row and proceed to the next one
                continue;
            }

            // Create an associative array using header names
            $data = array_combine($header, $row);

            if (!$data) {
                echo "count combinations";
                continue; // Skip if array_combine fails
            }

            // Prepare data for insertion
            $insertData = [
                'avSec_training' => $data['avSec_training'] ?? null,
                'January' => $data['January'] ?? null,
                'February' => $data['February'] ?? null,
                'March' => $data['March'] ?? null,
                'April' => $data['April'] ?? null,
                'May' => $data['May'] ?? null,
                'June' => $data['June'] ?? null,
                'July' => $data['July'] ?? null,
                'August' => $data['August'] ?? null,
                'September' => $data['September'] ?? null,
                'October' => $data['October'] ?? null,
                'November' => $data['November'] ?? null,
                'December' => $data['December'] ?? null,
                'remarks' => $data['remarks'] ?? null,
                'status' => $data['status'] ?? null,
                'positions' => $data['positions'] ?? null, // Corrected spelling from 'postions' to 'positions'
                'lang_code' => $data['lang_code'] ?? null,
                'created_by' => !empty($data['created_by']) && is_numeric($data['created_by']) ? (int)$data['created_by'] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            
            
            // Insert data into the database
            try {
            //     echo"<pre>";
              
            //    print_r($insertData );
            //   echo"</pre>";

                DB::table('avsec_training_calendars')->insert($insertData);
            } catch (\Exception $e) {
                // Log or handle the error
              echo"<pre>";
              echo $e->getMessage();
               print_r($row );
              echo"</pre>";
               //  Example: Log::error('Import Error', ['row' => $row, 'error' => $e->getMessage()]);
                continue; // Skip the row with error and continue
            }

            $rowNumber++;
        }

        fclose($file);

        return response()->json(['message' => 'CSV data imported successfully.'], 200);
    }

    public function importops1CSV(Request $request)
    {
        // Validate the uploaded file
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Open the file
        $file = fopen($request->file('csv_file')->getRealPath(), 'r');

        if (!$file) {
            return response()->json(['error' => 'Failed to open the file.'], 500);
        }

        // Read the header row
        $header = fgetcsv($file, 1000, ',');

        // Check if header is valid
        if (!$header || count($header) < 9) { // Ensure at least 13 columns for the header
            fclose($file);
            return response()->json(['error' => 'Invalid CSV file format.'], 400);
        }

        $rowNumber = 1;
        while (($row = fgetcsv($file, 1000, ',')) !== false) {
            // Check if the row length matches the header length
            if (count($row) != count($header)) {
                echo "count error";
                // Skip this row and proceed to the next one
                continue;
            }

            // Create an associative array using header names
            $data = array_combine($header, $row);

            if (!$data) {
                echo "count combinations";
                continue; // Skip if array_combine fails
            }

            // Prepare data for insertion
            $insertData = [
                'application_id' => isset($data['application_id']) ? preg_replace('/[^\x20-\x7E]/', '', $data['application_id']) : null,
                'company_name' => isset($data['company_name']) ? preg_replace('/[^\x20-\x7E]/', '', $data['company_name']) : null,
                'date_of_application_submitted' => isset($data['date_of_application_submitted']) ? date('Y-m-d', strtotime($data['date_of_application_submitted'])) : null,
                'date_of_approval' => isset($data['date_of_approval']) ? date('Y-m-d', strtotime($data['date_of_approval'])) : null,
                'status' => $data['status'] ?? null,
                'division' => $data['division'] ?? null,
                'sec_type' => $data['sec_type'] ?? null,
                'date_of_validity' => isset($data['date_of_validity']) ? date('Y-m-d', strtotime($data['date_of_validity'])) : null,
                'positions' => $data['positions'] ?? null,
                'lang_code' => $data['lang_code'] ?? null,
                'created_by' => !empty($data['created_by']) && is_numeric($data['created_by']) ? (int)$data['created_by'] : 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            
            
            
            // Insert data into the database
            try {
            //     echo"<pre>";
              
            //    print_r($insertData );
            //   echo"</pre>";

                DB::table('opsi_securities')->insert($insertData);
            } catch (\Exception $e) {
                // Log or handle the error
              echo"<pre>";
              echo $e->getMessage();
               print_r($row );
              echo"</pre>";
               //  Example: Log::error('Import Error', ['row' => $row, 'error' => $e->getMessage()]);
                continue; // Skip the row with error and continue
            }

            $rowNumber++;
        }

        fclose($file);

        return response()->json(['message' => 'CSV data imported successfully.'], 200);
    }
    
}
