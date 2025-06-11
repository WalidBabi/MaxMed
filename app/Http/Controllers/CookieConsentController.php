<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CookieConsentController extends Controller
{
    public function store(Request $request)
    {
        $consent = $request->input('consent', 'denied');
        
        return response()
            ->json(['success' => true])
            ->cookie('cookie_consent', $consent, 60 * 24 * 365); // 1 year
    }
}
