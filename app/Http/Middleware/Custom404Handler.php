<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Custom404Handler
{
    // Cache the arrays to avoid recreating them on every request
    private static $fourOhFourProductIds = null;
    private static $legacyCategoryNames = null;
    private static $legacyCategoryIds = null;
    private static $legacyCategoryPaths = null;

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
            
            // Initialize cached arrays if not already done
            if (self::$fourOhFourProductIds === null) {
                self::$fourOhFourProductIds = [
                    138, 147, 150, 129, 124, 169, 121, 149, 148, 158, 145, 142, 
                    181, 151, 116, 160, 155, 68, 162, 173, 122, 32, 275, 31, 
                    170, 30, 139, 114, 172, 182, 236, 167, 67, 177, 180, 171, 
                    178, 176, 179, 282, 270, 281, 285
                ];
            }
            
            // Check if it's a 404 product URL
            if (preg_match('/^product\/(\d+)$/', $path, $matches)) {
                $productId = (int)$matches[1];
                if (in_array($productId, self::$fourOhFourProductIds)) {
                    return redirect('/products', 301);
                }
            }
            
            // Legacy category names that should redirect to homepage or products page
            $legacyCategoryNames = [
                'education%26-training-tools',
                'analytical-chemistry',
                'genomics-%26-life-sciences',
                'veterinary-%26-agri-tools',
                'forensic-supplies',
                'molecular-biology',
                'research-%26-life-sciences',
                'clinical-diagnostics-ivd',
                'educational-lab',
                'industrial-qc-tools',
                'medical-rapid-tests',
                'clinical-diagnostics',
                'clinical-diagnostics-lab',
                'medical-diagnostics-lab',
                'medical-consumables',
                'lab-equipment',
                'maxtest©-rapid-tests-ivd',
                'genomics-&-life-sciences',
                'education&-training-tools',
                'molecular-biology',
                'analytical-chemistry',
                'forensic-supplies',
                'research-&-life-sciences',
                'maxtest©-rapid-tests',
                'food-&-enviromint-lab',
                'microbiology',
                'hplc-&-gc-columns',
                'lab-glassware',
                'contact-us',
                'analytical-chemistry-lab',
                'molecular-biology-lab',
                'microbiology-lab',
                'lab-plastic-ware',
                'buy-online'
            ];
            
            // Legacy category IDs that should redirect to the products page
            $legacyCategoryIds = [
                55, 43, 34, 50, 46, 44, 40, 45, 76, 72, 77, 79, 56,
                52, 35, 41, 36, 38, 33, 32, 37
            ];
            
            // Legacy category paths from Search Console 404 data
            $legacyCategoryPaths = [
                '51/55/58', '43/46', '43/45', '51/60', '51/55/59', 
                '51/39/84', '51/39/86', '51/39/83', '66/71/72', 
                '66/71/73', '57/74', '60/77', '57/75', '51/55', 
                '49/39', '51/52', '43/44', '34/35', '34/37'
            ];
            
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
            
            // Check if it's a legacy category path URL
            foreach ($legacyCategoryPaths as $categoryPath) {
                if ($path === "categories/{$categoryPath}") {
                    return redirect('/products', 301);
                }
            }
            
            // Handle quotation form URLs that should redirect instead of 404
            if (preg_match('/^quotation\/(\d+)\/form$/', $path, $matches)) {
                $quotationId = (int)$matches[1];
                // Redirect to main quotation form instead of 404
                return redirect('/quotation/form', 301);
            }
        }
        
        return $response;
    }
} 