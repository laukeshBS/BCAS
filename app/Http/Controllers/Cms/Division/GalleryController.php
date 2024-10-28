<?php

namespace App\Http\Controllers\Cms\Division;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use App\Models\Cms\Division\DivisionGallery;
use GPBMetadata\Google\Api\Auth;

class GalleryController extends Controller
{
    public $user;

    public function index(Request $request): View
    {
        if (is_null($this->user) || !$this->user->can('gallery.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any Gallery !');
        }
       // $lists='';
        $parentgallery="";
            $title="Gallery List";
            $approve_status=session()->get('status');
            $sertitle=Session::get('Crtitle');
            $approve_status=Session::get('status');
            $lang_code=Session::get('lang_code');
            $lists = DivisionGallery::whereNotNull('title');
            //$lists = gallery::with(['galleryCategory', 'center']);
          // dd( $lists);
            if (!empty($sertitle)) {
                $lists = DivisionGallery::whereNotNull('title');
                $lists->where('title', 'LIKE', "%{$sertitle}%");
            }
            if (!empty($approve_status)) {
               
                $lists->where('status',$approve_status);
            }
            if (!empty($lang_code)) {
               
                $lists->where('lang_code',$lang_code);
            }
            $list = $lists->orderBy('position', 'ASC')->select('*')->paginate(10);
           // dd($list);
        return view('cms/division/gallery/index',compact(['list','title','parentgallery']));
    }

    // API
    public function data(Request $request)
    {
        $lang_code = $request->input('lang_code'); // sOptional lang_code
        $limit = $request->input('limit'); // Optional limit

        $query = DivisionGallery::select('*');

        // Apply lang_code filter if provided
        if ($lang_code) {
            $query->where('lang_code', $lang_code);
        }

        // Apply limit if provided
        if ($limit) {
            $query->limit($limit);
        }

        $data = $query->orderBy('id', 'desc')->get();

        if ($data->isEmpty()) {
            return response()->json(['message' => 'No data found'], 404);
        }

        $data->transform(function ($item) {
            $item->created_at = date('d-m-Y', strtotime($item->created_at));
            $item->document = asset('public/documents/' . $item->document);

            return $item; // Return the transformed item
        });

        return response()->json($data);
    }

    public function cms_data(Request $request)
    {
        $perPage = $request->input('limit');
        $lang_code = $request->input('lang_code');

        $slide = DivisionGallery::select('*') ->paginate($perPage, ['*'], 'page', $page);
        if ($slide->isNotEmpty()) {
            $slide->transform(function ($item) {
                $item->created_at = date('d-m-Y', strtotime($item->created_at));
                if ($item->media) {
                    $item->media = asset('public/'.$item->media);
                }
                return $item;
            });
        }
        return response()->json([
            'title' => 'List',
            'data' => $slide->items(),
            'total' => $slide->total(),
            'current_page' => $slide->currentPage(),
            'last_page' => $slide->lastPage(),
            'per_page' => $slide->perPage(),
        ]);
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
        $data = DivisionGallery::find($validatedId);

        // Return a 404 response if data is not found
        if (!$data) {
            return response()->json([
                'error' => 'Data not found'
            ], 404);
        }
        
        $data->created_at = date('d-m-Y', strtotime($data->created_at));

        return response()->json($data);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|min:2|max:255',
            'slug' => 'required|min:2|max:255',
            'description' => 'nullable|max:500',
            'image' => 'required|file|mimes:jpg,jpeg,png|max:20480',
            'parent_id' => 'required',
            'division' => 'required',
            'position' => 'required',
            'status' => 'required',
            'lang_code' => 'required|exists:languages,lang_code',
            'start_date' => 'required',
            'end_date' => 'required',
            'is_news' => 'required',
            
        ]);

        // Handle file upload
        if ($request->hasFile('image')) {
            $docUpload = $request->file('image');
            $docPath = time() . '_' . $docUpload->getClientOriginalName();
            $docUpload->move(public_path('uploads/admin/cmsfiles/division/gallery'), $docPath);
            $filePath = $docPath;
        }

        // Create a new form instance
        $data = new DivisionGallery(); // Assuming you have a Form model
        $data->title = $validated['title'];
        $data->slug = $validated['slug'];
        $data->description = $validated['description'];
        $data->parent_id = $validated['parent_id'];
        $data->division = $validated['division'];
        $data->position = $validated['position'];
        $data->status = $validated['status'];
        $data->lang_code = $validated['lang_code'];
        $data->start_date = $validated['start_date'];
        $data->end_date = $validated['end_date'];
        $data->is_news = $validated['is_news'];
        $data->created_by = Auth::user()->id;
        $data->image = $filePath;
        
        $data->save();

        return response()->json(['data' => $data, 'message' => 'Created successfully.'], 201);
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|min:2|max:255',
            'slug' => 'required|min:2|max:255',
            'description' => 'nullable|max:500',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:20480',
            'parent_id' => 'required',
            'division' => 'required',
            'position' => 'required',
            'status' => 'required',
            'lang_code' => 'required|exists:languages,lang_code',
            'start_date' => 'required',
            'end_date' => 'required',
            'is_news' => 'required',
            'created_by' => Auth::user()->id,
        ]);
        $data = DivisionGallery::find($id);

        if (!$data) {
            return $this->sendError('No data found.', 404);
        }

        // Handle file upload
        if ($request->hasFile('image')) {
            $docUpload = $request->file('image');
            $docPath = time() . '_' . $docUpload->getClientOriginalName();
            $docUpload->move(public_path('uploads/admin/cmsfiles/division/gallery'), $docPath);
            $validated['image'] = $docPath;
        }

        $data->update($validated);

        return response()->json(['data' => $data, 'message' => 'Updated successfully.'], 201);
    }
    public function delete($id)
    {
        $data = DivisionGallery::find($id);

        if (!$data) {
            return $this->sendError('No data found.', 404);
        }
        $data->delete();

        return response()->json(['data' => $data, 'message' => 'Deleted successfully.'], 201);
    }
}
