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
    public function index(Request $request): JsonResponse
    {
        $data = $request->all();
        $menu_position = $data['menu_position'] ?? null;
        $menu_child_id = $data['menu_child_id'] ?? 0;
        $lang_code = $data['lang_code'] ?? null;
        //dd($data);
        if (!$lang_code) {
            return $this->sendError('Lang code parameter is missing.', 400);
        }
        // if (!$menu_child_id) {
        //     return $this->sendError('Chid menu id parameter is missing.', 400);
        // }

        // Retrieve all matching menu items, not just the first one
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
        )
        ->orderBy('page_order', 'ASC')
        ->get();
    


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
    if (!$header || count($header) < 13) { // Ensure at least 13 columns for the header
        fclose($file);
        return response()->json(['error' => 'Invalid CSV file format.'], 400);
    }

    // Debug: Print the header
    echo "<pre>";
    echo "<hr>Header:";
    print_r($header);
    echo "</pre>";

    // Loop through the rest of the file and insert data
    $rowNumber = 1;
    while (($row = fgetcsv($file, 1000, ';')) !== false) {
        // Debug: Print raw row data
        echo "<pre>";
        echo "<hr>Raw Row ($rowNumber):";
        print_r($row);
        echo "</pre>";

        // Check if the row length matches the header length
        if (count($row) != count($header)) {
            echo "<pre>";
            echo "<hr>Error: Row $rowNumber does not match header length.";
            echo "Header Length: " . count($header) . " | Row Length: " . count($row);
            echo "</pre>";
            continue; // Skip this row and proceed to the next one
        }

        // Create an associative array using header names
        $data = array_combine($header, $row);

        // Check if data is created successfully
        if (!$data) {
            echo "<pre>";
            echo "<hr>Error: Failed to combine header and row data.";
            echo "</pre>";
            continue;
        }

        // Debug: Print the combined data
        echo "<pre>";
        echo "<hr>Row Data ($rowNumber):";
        print_r($data);
        echo "</pre>";

        // Prepare data for insertion
        $insertData = [
            'airport_orders' => isset($data['airport_orders']) && is_numeric($data['airport_orders']) ? (int)$data['airport_orders'] : null,
            'region_name' => $data['region_name'] ?? null,
            'sr_no' => isset($data['sr_no']) && is_numeric($data['sr_no']) ? (int)$data['sr_no'] : null,
            'airport_name' => $data['airport_name'] ?? null,
            'entity_name' => $data['entity_name'] ?? null,
            // 'address' => $data['address'] ?? null,
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

        // Debug: Print the prepared data for insertion
        echo "<pre>";
        echo "<hr>Insert Data ($rowNumber):";
        print_r($insertData);
        echo "</pre>";

        // Insert data into the database
        try {
           // DB::table('working_airports')->insert($insertData);
        } catch (\Exception $e) {
            echo "<pre>";
            echo "<hr>Error ($rowNumber):";
            print_r([
                'row' => $row,
                'error' => $e->getMessage()
            ]);
            echo "</pre>";
        }

        $rowNumber++;
    }

    fclose($file);

    return response()->json(['message' => 'CSV data imported successfully.'], 200);
}
    
}
