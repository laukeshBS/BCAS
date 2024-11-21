<?php

namespace App\Http\Controllers\Cms;

use ZipArchive;
use App\Models\Admin;
use App\Models\Cms\Slider;
use App\Models\Cms\Tender;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class TenderController extends Controller
{
    public $user;

    public function data(Request $request)
    {
        $perPage = $request->input('limit');
        $page = $request->input('currentPage');
        $lang_code = $request->input('lang_code');



        // Fetch data from the database



        // Validate input
        if (!$lang_code) {
            return response()->json(['error' => 'Language code is required'], 400);
        }
        $date=date('Y-m-d');
        $data = Tender::select('*')
            ->where('lang_code', $lang_code)
            ->where('end_date','>', $date)
            ->where('status',3)
            ->orderBy('id', 'desc')
            ->limit($perPage)
            ->paginate($perPage, ['*'], 'page', $page);

        if ($data->isEmpty()) {
            return response()->json(['message' => 'No data found'], 404);
        }

        $data->transform(function ($item) {
            $item->start_date = date('d-m-Y', strtotime($item->start_date));
            $item->end_date = date('d-m-Y', strtotime($item->end_date));
            $item->created_at = date('d-m-Y', strtotime($item->created_at));
            // $item->document = asset('public/documents/' . $item->document);

            return $item; // Return the transformed item

        });

        return response()->json([
            'title' => 'List',
            'data' => $data->items(),
            'total' => $data->total(),
            'current_page' => $data->currentPage(),
            'last_page' => $data->lastPage(),
            'per_page' => $data->perPage(),
        ]);
    }
    public function archive(Request $request)
    {
        $perPage = $request->input('limit');
        $page = $request->input('currentPage');
        $lang_code = $request->input('lang_code');



        // Fetch data from the database



        // Validate input
        if (!$lang_code) {
            return response()->json(['error' => 'Language code is required'], 400);
        }
        $date=date('Y-m-d');
        $data = Tender::select('*')
            ->where('lang_code', $lang_code)
            ->where('end_date','<', $date)
            ->where('status',3)
            ->orderBy('id', 'desc')
            ->limit($perPage)
            ->paginate($perPage, ['*'], 'page', $page);

        if ($data->isEmpty()) {
            return response()->json(['message' => 'No data found'], 404);
        }

        $data->transform(function ($item) {
            $item->start_date = date('d-m-Y', strtotime($item->start_date));
            $item->end_date = date('d-m-Y', strtotime($item->end_date));
            $item->created_at = date('d-m-Y', strtotime($item->created_at));
            // $item->document = asset('public/documents/' . $item->document);

            return $item; // Return the transformed item

        });

        return response()->json([
            'title' => 'List',
            'data' => $data->items(),
            'total' => $data->total(),
            'current_page' => $data->currentPage(),
            'last_page' => $data->lastPage(),
            'per_page' => $data->perPage(),
        ]);
    }
    public function cms_data(Request $request)
    {
        $perPage = $request->input('limit');
        $page = $request->input('currentPage');

        $slider = Tender::select('*')->orderBy('id', 'desc')->paginate($perPage, ['*'], 'page', $page);
        if ($slider->isNotEmpty()) {
            $slider->transform(function ($item) {
                $item->created_at = date('d-m-Y', strtotime($item->created_at));
                // if ($item->document) {
                //     $item->document = asset('public/documents/'.$item->document);
                // }
                return $item;
            });
        }

            return response()->json([
                'title' => 'Tender List',
                'data' => $slider->items(),
                'total' => $slider->total(),
                'current_page' => $slider->currentPage(),
                'last_page' => $slider->lastPage(),
                'per_page' => $slider->perPage(),
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
        $data = Tender::find($validatedId);

        // Return a 404 response if data is not found
        if (!$data) {
            return response()->json([
                'error' => 'Data not found '
            ], 404);
        }
        // $data->start_date = date('d-m-Y', strtotime($data->start_date));
        // $data->end_date = date('d-m-Y', strtotime($data->end_date));
        $data->created_at = date('d-m-Y', strtotime($data->created_at));
        // $data->document = asset('public/documents/' . $data->document) ;

        // Return the data as JSON
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'max:500',
            'status' => 'required',
            'lang_code' => 'required',
            'start_date' => 'required|date|before:end_date',
            'end_date' => 'required|date',
            'positions' => 'nullable',
            'document' => 'required|file|mimes:pdf|max:2048',
        ]);

        $filePath = null;

        // Handle file upload
        if ($request->hasFile('document')) {
            try {
                $docUpload = $request->file('document');
                $docPath = time() . '_' . $docUpload->getClientOriginalName();
                $docUpload->move(public_path('documents/tenders/'), $docPath);
                $filePath = 'tenders/' . $docPath;
            } catch (\Exception $e) {
                return response()->json(['error' => 'File upload failed: ' . $e->getMessage()], 500);
            }
        }

        // Create a new Tender instance
        $tender = new Tender();
        $tender->title = $validated['title'];
        $tender->description = $validated['description'] ?? '';
        $tender->status = $validated['status'];
        $tender->lang_code = $validated['lang_code'];
        $tender->start_date = $validated['start_date'];
        $tender->end_date = $validated['end_date'];
        $tender->positions = $validated['positions'] ?? null; // Handle nullable fields
        $tender->document = $filePath; // Store file path in the database
        $tender->save();

        // Return the data as JSON with a 201 status code
        return response()->json($tender, 201);
    }


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'max:500',
            'status' => 'required',
            'lang_code' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date', // Ensure end_date is after start_date
            'positions' => 'nullable',
            'document' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $actandpolicy = Tender::find($id);

        if (!$actandpolicy) {
            return response()->json(['error' => 'Not Found.'], 404);
        }

        if ($request->hasFile('document')) {
            $docUpload = $request->file('document');
            $docPath = time() . '_' . $docUpload->getClientOriginalName();
            $docUpload->move(public_path('documents/tenders/'), $docPath);
            $validated['document'] = 'tenders/' . $docPath;
        }
        // Use fill to update attributes
        $actandpolicy->fill($validated);

        $actandpolicy->save();

        return response()->json($actandpolicy);
    }

    public function delete($id)
    {
        // Find the actandpolicy by id
        $actandpolicy = Tender::find($id);

        if (!$actandpolicy) {
            return response()->json([
                'error' => 'Not Found.'
            ], 400);
        }

        // Delete the actandpolicy
        $actandpolicy->delete();

        // Return the data as JSON
        return response()->json($actandpolicy);
    }
}
