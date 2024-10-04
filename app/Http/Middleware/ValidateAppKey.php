<?php 
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ValidateAppKey
{
    public function handle(Request $request, Closure $next)
    {
        // Get the APP_KEY_API from the request headers
        // $appKey = $_SERVER['APP_KEY_API'];
    //     $appKey =$request->header('APP_KEY_API') ?? $request->header('app_key_api');
    //     // dd($request->headers->all());
    //      $configuredAppKey = env('APP_KEY_API');
    //    // Debugging output
    //     //dd('Received APP_KEY_API:', $appKey, 'Configured APP_KEY_API:', $configuredAppKey);

    //     // Check if APP_KEY_API is empty
    //     if (empty($appKey)) {
    //         return response()->json(['message' => 'Unauthorized: APP_KEY_API is missing'], 401);   
    //     }

    //     // Compare the provided APP_KEY_API with the one in .env
    //     if ($appKey !== $configuredAppKey) {
    //         return response()->json(['message' => 'Unauthorized: Invalid APP_KEY_API'], 401);
    //     }

        return $next($request);
    }
}
