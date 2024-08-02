<?php

namespace App\Http\Controllers\Cms;
use App\Models\Visitor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    public function getVisitorCount(Request $request)
    {
        $pageUrl = $request->input('url');
       // $lang_code = $request->input('lang_code');
        $lang_code = 'en'; // Example lang_code; set this as needed
        $visitor = Visitor::where('lang_code', $lang_code)->first();
        //dd($visitor);
        if ($visitor) {
            return response()->json(['count' => $visitor->count]);
        } else {
            return response()->json(['count' => 0]);
        }
    }

    public function incrementVisitorCount(Request $request)
    {
        $url = $request->input('url');
        $langid = $request->input('lang_code');
        $ipAddress =  $request->input('ip');
        $userAgent = $request->input('user_agent');
        $visitorData = [
            'ip_address' => $ipAddress,
            'url' => $url,
            'lang_code' => $langid,
            'user_agent' => $userAgent,
        ];
     

        // Use upsert to insert or update the record
        Visitor::upsert(
            [$visitorData], // Array of records to insert or update
            ['ip_address', 'url'], // Unique keys
            ['lang_code', 'user_agent'] // Columns to update
        );

        return response()->json(['success' => true]);
    }
}
