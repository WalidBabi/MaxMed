<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Custom404Handler
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
        $response = $next($request);
        
        // Only proceed if this is a 404 response
        if ($response->getStatusCode() === 404) {
            $path = $request->path();
            
            // Legacy category names that should redirect to homepage or products page
            $legacyCategoryNames = [
                'education%26-training-tools',
                'analytical-chemistry',
                'genomics-%26-life-sciences',
                'veterinary-%26-agri-tools',
                'forensic-supplies',
                'molecular-biology',
                'research-%26-life-sciences'
            ];
            
            // Legacy category IDs that should redirect to the products page
            $legacyCategoryIds = [55, 43, 34];
            
            // Check if it's a legacy named category URL
            if (in_array($path, $legacyCategoryNames)) {
                return redirect('/products', 301);
            }
            
            // Check if it's a legacy category ID URL
            foreach ($legacyCategoryIds as $id) {
                if ($path === "categories/{$id}" || 
                    strpos($path, "categories/{$id}/") === 0) {
                    return redirect('/products', 301);
                }
            }
        }
        
        return $response;
    }
} 