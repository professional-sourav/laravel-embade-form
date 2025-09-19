<?php

namespace App\Http\Middleware;

use Closure;

class EmbedSecurityHeaders
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Only for embed routes
        if ($request->is('embed/*')) {
            $origin = $request->header('Origin');

            if ($this->isAllowedOrigin($origin)) {
                $response->headers->set('Access-Control-Allow-Origin', $origin);
                $response->headers->set('Access-Control-Allow-Credentials', 'true');
            }

            $response->headers->set('X-Frame-Options', 'ALLOW-FROM ' . $origin);
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-XSS-Protection', '1; mode=block');
        }

        return $response;
    }

    private function isAllowedOrigin($origin)
    {
        return in_array($origin, config('embed.allowed_domains', []));
    }
}
