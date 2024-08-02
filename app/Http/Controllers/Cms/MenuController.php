<?php
namespace App\Http\Controllers\Cms;

use App\Models\Cms\Menu;
use Illuminate\Http\Request;
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
        ->when($menu_position, function ($query, $menu_position) {
            return $query->where('menu_position', $menu_position);
        })
        ->with(['children' => function ($query) {
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
        )
        ->orderBy('page_order', 'ASC')
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
    public function lang_slgus_wise(Request $request): JsonResponse
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
}
