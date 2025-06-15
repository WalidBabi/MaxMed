<?php

namespace App\Http\Controllers;

use App\Models\CrmLead;
use App\Models\CrmActivity;
use App\Models\CrmDeal;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CrmController extends Controller
{
    public function dashboard()
    {
        $totalLeads = CrmLead::count();
        $activeLeads = CrmLead::whereNotIn('status', ['won', 'lost'])->count();
        $overdueLeads = CrmLead::overdue()->count();
        
        $totalDeals = CrmDeal::count();
        $openDeals = CrmDeal::open()->count();
        $wonDeals = CrmDeal::won()->count();
        $totalDealValue = CrmDeal::open()->sum('deal_value');
        $weightedPipeline = CrmDeal::open()->get()->sum('weighted_value');
        
        // Recent activities
        $recentActivities = CrmActivity::with(['lead', 'user'])
            ->latest('activity_date')
            ->take(10)
            ->get();
        
        // Upcoming tasks
        $upcomingTasks = CrmActivity::upcoming()
            ->with(['lead', 'user'])
            ->take(5)
            ->get();
        
        // Overdue tasks
        $overdueTasks = CrmActivity::overdue()
            ->with(['lead', 'user'])
            ->count();
        
        // Pipeline by stage
        $pipelineByStage = CrmDeal::open()
            ->selectRaw('stage, COUNT(*) as count, SUM(deal_value) as total_value')
            ->groupBy('stage')
            ->get();
        
        // Lead sources
        $leadSources = CrmLead::selectRaw('source, COUNT(*) as count')
            ->groupBy('source')
            ->get();
        
        // Monthly metrics
        $monthlyLeads = CrmLead::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        $monthlyWonDeals = CrmDeal::won()
            ->whereMonth('actual_close_date', now()->month)
            ->whereYear('actual_close_date', now()->year)
            ->sum('deal_value');
        
        return view('crm.dashboard', compact(
            'totalLeads', 'activeLeads', 'overdueLeads',
            'totalDeals', 'openDeals', 'wonDeals', 'totalDealValue', 'weightedPipeline',
            'recentActivities', 'upcomingTasks', 'overdueTasks',
            'pipelineByStage', 'leadSources',
            'monthlyLeads', 'monthlyWonDeals'
        ));
    }
    
    public function reports()
    {
        // Sales performance over time
        $salesData = CrmDeal::won()
            ->selectRaw('DATE_FORMAT(actual_close_date, "%Y-%m") as month, SUM(deal_value) as total')
            ->where('actual_close_date', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // Conversion rates
        $conversionRates = [
            'lead_to_qualified' => $this->calculateConversionRate('new', 'qualified'),
            'qualified_to_proposal' => $this->calculateConversionRate('qualified', 'proposal'),
            'proposal_to_won' => $this->calculateConversionRate('proposal', 'won'),
        ];
        
        // Top performers
        $topPerformers = CrmDeal::won()
            ->with('assignedUser')
            ->selectRaw('assigned_to, COUNT(*) as deals_won, SUM(deal_value) as total_value')
            ->groupBy('assigned_to')
            ->orderByDesc('total_value')
            ->get();
        
        return view('crm.reports', compact('salesData', 'conversionRates', 'topPerformers'));
    }
    
    private function calculateConversionRate($fromStatus, $toStatus)
    {
        $fromCount = CrmLead::where('status', $fromStatus)->count();
        $toCount = CrmLead::where('status', $toStatus)->count();
        
        if ($fromCount == 0) return 0;
        
        return round(($toCount / $fromCount) * 100, 2);
    }
} 