<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCspNonce
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Default nonce value for pages that don't require dynamic content
        $nonce = '';

        // Determine if this is a login page (you can use route name or URL for this check)
        if (!$this->isStaticPage($request)) {
            // If it's not a static page, generate a nonce
            $nonce = base64_encode(random_bytes(16)); // 16-byte random nonce
        }

        // Proceed with the response
        $response = $next($request);

        // If the page needs a nonce, set the CSP header with it
        if ($nonce) {
            $response->headers->set('Content-Security-Policy', "default-src 'self' 'nonce-{$nonce}' http://bcasadmin.csc-pg2.in http://www.w3.org/2000/svg;
                script-src 'self' 'nonce-{$nonce}' https://fonts.googleapis.com/ http://bcasadmin.csc-pg2.in;
                style-src 'self' 'nonce-{$nonce}' https://fonts.googleapis.com/ http://bcasadmin.csc-pg2.in;
                img-src 'self' 'unsafe-inline' http://www.w3.org/2000/svg http://bcasadmin.csc-pg2.in;
                font-src 'blob' 'unsafe-inline' http://bcasadmin.csc-pg2.in;
                frame-src 'self' blob: http://www.w3.org/2000/svg http://bcasadmin.csc-pg2.in;");
            
            // Share the nonce with the view
            view()->share('nonce', $nonce);
        } else {
            // For static pages (e.g., login), set a simpler CSP header without nonce
            $response->headers->set('Content-Security-Policy', "default-src 'self' 'unsafe-inline' http://bcasadmin.csc-pg2.in http://www.w3.org/2000/svg;
                script-src 'self' https://fonts.googleapis.com/ http://bcasadmin.csc-pg2.in;
                style-src 'self' https://fonts.googleapis.com/ http://bcasadmin.csc-pg2.in;
                img-src 'self' http://www.w3.org/2000/svg http://bcasadmin.csc-pg2.in;
                font-src 'blob' http://bcasadmin.csc-pg2.in;");
        }

        return $response;
    }

    /**
     * Determine if the current page is static (like the login page)
     * You can customize this function based on your routes or request paths
     */
    private function isStaticPage(Request $request)
    {
        // Here we check if the route is the login page, for example:
        // Adjust this based on your route naming, or check URL directly
        return $request->route()->getName() === 'login';  // Adjust this based on your route
    }

}
