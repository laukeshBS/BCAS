<?php

namespace App\Http\Controllers\Admin;

use TCPDF;
use Carbon\Carbon;
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
    public function exportPDF(Request $request)
{
    // Validate the date inputs
    $validated = $request->validate([
        'from_date' => 'required|date',
        'to_date' => 'required|date|after_or_equal:from_date',
    ]);

    $fromDate = Carbon::parse($request->from_date);
    $toDate = Carbon::parse($request->to_date);

    // Query data within the date range
    $data = AuditTrail::with(['user'])->whereBetween('created_at', [$fromDate, $toDate])
                     ->get();

    // Generate and return the PDF
    return $this->generatePDF($data, $fromDate, $toDate);
}

private function generatePDF($data, $fromDate, $toDate)
{
    // File name for the PDF
    $fileName = 'EXPORT_' . Carbon::now()->format('Y_m_d_H_i_s') . '.pdf';

    // Create PDF content (HTML format)
    $pdfContent = view('pdf.export', compact('data', 'fromDate', 'toDate'))->render();

    // Initialize TCPDF
    $pdf = new TCPDF();
    $pdf->AddPage();

    // Write HTML content into the PDF
    $pdf->writeHTML($pdfContent);

    // Generate PDF and return as a response with the correct headers
    return response($pdf->Output($fileName, 'S'), 200)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
}


}
