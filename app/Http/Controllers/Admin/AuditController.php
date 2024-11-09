<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Admin;
use Illuminate\Http\Request;
use App\Models\Admin\AuditTrail;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuditController extends Controller
{
    
    public function index(Request $request)
    {
        $perPage = $request->input('limit');
        $page = $request->input('currentPage');

        $audit = AuditTrail::with(['user'])->select('*')->orderBy('id', 'desc')->paginate($perPage, ['*'], 'page', $page);
        
        return response()->json([
            'title' => 'List',
            'data' => $audit->items(),
            'total' => $audit->total(),
            'current_page' => $audit->currentPage(),
            'last_page' => $audit->lastPage(),
            'per_page' => $audit->perPage(),
        ]);
    }
    public function exportToPdf(Request $request)
    {
        // Validate the date inputs
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        // Retrieve the data from the database based on the date range
        $data = AuditTrail::whereBetween('created_at', [$request->from_date, $request->to_date])
                         ->get(['column1', 'column2', 'column3']); // Specify the columns you need

        // Return empty if no data is found
        if ($data->isEmpty()) {
            return response()->json(['message' => 'No data found for the given date range'], 404);
        }

        // Prepare the PDF view
        $pdf = Pdf::loadView('pdf.export', compact('data')); // Make sure to create the PDF view

        // Return the generated PDF as a response
        return $pdf->download('exported_table.pdf');
    }
}
