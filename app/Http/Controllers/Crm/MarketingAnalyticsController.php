<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MarketingAnalyticsController extends Controller
{
    public function index()
    {
        return view('crm.marketing.analytics.index');
    }
    
    public function campaigns()
    {
        return view('crm.marketing.analytics.campaigns');
    }
    
    public function contacts()
    {
        return view('crm.marketing.analytics.contacts');
    }
}
