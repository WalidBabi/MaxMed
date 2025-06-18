<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MarketingController extends Controller
{
    public function dashboard()
    {
        // Sample marketing data for demonstration
        $data = [
            'total_contacts' => 0,
            'active_campaigns' => 0,
            'total_templates' => 0,
            'recent_activities' => [],
        ];

        return view('crm.marketing.dashboard', compact('data'));
    }
} 