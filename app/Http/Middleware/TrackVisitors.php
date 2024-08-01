<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Session;
use App\Models\Visitor;
class TrackVisitors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Log visitor information
        $langid = Session::get('locale') ?? app()->getLocale();
        $ipAddress = $request->ip();
        $url = $request->fullUrl();
        $userAgent = $request->header('User-Agent');
        $visitorData = [
            'ip_address' => $ipAddress,
            'url' => $url,
            'lang_code' => $langid,
            'user_agent' => $userAgent,
        ];

       // Log::info('Visitor Information:', $visitorData);

        // Use upsert to insert or update the record
        Visitor::upsert(
            [$visitorData], // Array of records to insert or update
            ['ip_address', 'url'], // Unique keys
            ['lang_code', 'user_agent'] // Columns to update
        );
        return $next($request);
    }
}
