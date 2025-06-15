<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CrmLead;
use App\Models\CrmActivity;
use App\Models\CrmDeal;
use App\Models\User;
use Carbon\Carbon;

class CrmSeeder extends Seeder
{
    public function run()
    {
        // Get admin user or create one
        $adminUser = User::first() ?? User::create([
            'name' => 'MaxMed Admin',
            'email' => 'admin@maxmedme.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        // Sample leads for MaxMed laboratory equipment business
        $leads = [
            [
                'first_name' => 'Ahmed',
                'last_name' => 'Al-Mansouri',
                'email' => 'ahmed.mansouri@dubailab.ae',
                'phone' => '+971-4-555-0101',
                'company_name' => 'Dubai Medical Laboratory',
                'job_title' => 'Laboratory Director',
                'company_address' => 'Dubai Healthcare City, Dubai, UAE',
                'status' => 'qualified',
                'source' => 'linkedin',
                'priority' => 'high',
                'estimated_value' => 250000.00,
                'notes' => 'Interested in upgrading their molecular diagnostics equipment. Budget approved for Q2.',
                'expected_close_date' => now()->addMonth(),
                'assigned_to' => $adminUser->id,
                'last_contacted_at' => now()->subDays(2),
            ],
            [
                'first_name' => 'Fatima',
                'last_name' => 'Al-Zahra',
                'email' => 'f.alzahra@abudhabilab.ae',
                'phone' => '+971-2-555-0102',
                'company_name' => 'Abu Dhabi Clinical Research Lab',
                'job_title' => 'Head of Procurement',
                'company_address' => 'Al Mafraq, Abu Dhabi, UAE',
                'status' => 'proposal',
                'source' => 'website',
                'priority' => 'high',
                'estimated_value' => 180000.00,
                'notes' => 'Needs PCR equipment for COVID-19 testing expansion. Very urgent requirement.',
                'expected_close_date' => now()->addWeeks(3),
                'assigned_to' => $adminUser->id,
                'last_contacted_at' => now()->subDay(),
            ],
            [
                'first_name' => 'Mohammad',
                'last_name' => 'Hassan',
                'email' => 'm.hassan@sharjahmed.ae',
                'phone' => '+971-6-555-0103',
                'company_name' => 'Sharjah Medical Center',
                'job_title' => 'Chief Medical Officer',
                'company_address' => 'Al Qasimia, Sharjah, UAE',
                'status' => 'new',
                'source' => 'google_ads',
                'priority' => 'medium',
                'estimated_value' => 95000.00,
                'notes' => 'New medical center opening. Looking for complete lab setup.',
                'expected_close_date' => now()->addMonths(2),
                'assigned_to' => $adminUser->id,
                'last_contacted_at' => null,
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Al-Rashid',
                'email' => 'sarah.rashid@healthcareuae.com',
                'phone' => '+971-4-555-0104',
                'company_name' => 'Emirates Healthcare Group',
                'job_title' => 'Laboratory Manager',
                'company_address' => 'DIFC, Dubai, UAE',
                'status' => 'negotiation',
                'source' => 'referral',
                'priority' => 'high',
                'estimated_value' => 320000.00,
                'notes' => 'Large healthcare group expanding to 3 new locations. Multi-site equipment deal.',
                'expected_close_date' => now()->addWeeks(2),
                'assigned_to' => $adminUser->id,
                'last_contacted_at' => now()->subHours(8),
            ],
            [
                'first_name' => 'Khalid',
                'last_name' => 'bin Rashid',
                'email' => 'k.binrashid@ajmanlab.ae',
                'phone' => '+971-7-555-0105',
                'company_name' => 'Ajman University Medical Center',
                'job_title' => 'Research Coordinator',
                'company_address' => 'Ajman University, Ajman, UAE',
                'status' => 'contacted',
                'source' => 'trade_show',
                'priority' => 'medium',
                'estimated_value' => 75000.00,
                'notes' => 'University research lab upgrade. Academic pricing required.',
                'expected_close_date' => now()->addMonths(3),
                'assigned_to' => $adminUser->id,
                'last_contacted_at' => now()->subDays(5),
            ],
            [
                'first_name' => 'Amina',
                'last_name' => 'Al-Suwaidi',
                'email' => 'amina.suwaidi@alainmed.ae',
                'phone' => '+971-3-555-0106',
                'company_name' => 'Al Ain Medical District',
                'job_title' => 'Clinical Laboratory Supervisor',
                'company_address' => 'Al Ain, Abu Dhabi, UAE',
                'status' => 'won',
                'source' => 'email',
                'priority' => 'medium',
                'estimated_value' => 140000.00,
                'notes' => 'Successfully closed deal for hematology analyzers. Great client for referrals.',
                'expected_close_date' => now()->subWeeks(2),
                'assigned_to' => $adminUser->id,
                'last_contacted_at' => now()->subWeeks(1),
            ],
            [
                'first_name' => 'Omar',
                'last_name' => 'Al-Maktoum',
                'email' => 'omar.maktoum@rak-health.ae',
                'phone' => '+971-7-555-0107',
                'company_name' => 'RAK Hospital Laboratory',
                'job_title' => 'Laboratory Director',
                'company_address' => 'Ras Al Khaimah, UAE',
                'status' => 'lost',
                'source' => 'phone',
                'priority' => 'low',
                'estimated_value' => 65000.00,
                'notes' => 'Budget constraints. May revisit next fiscal year.',
                'expected_close_date' => now()->subMonth(),
                'assigned_to' => $adminUser->id,
                'last_contacted_at' => now()->subWeeks(3),
            ],
            [
                'first_name' => 'Layla',
                'last_name' => 'Al-Qasimi',
                'email' => 'layla.qasimi@fujairahmed.ae',
                'phone' => '+971-9-555-0108',
                'company_name' => 'Fujairah Medical Complex',
                'job_title' => 'Chief of Laboratory Services',
                'company_address' => 'Fujairah, UAE',
                'status' => 'qualified',
                'source' => 'linkedin',
                'priority' => 'medium',
                'estimated_value' => 110000.00,
                'notes' => 'Government hospital tender process. Long sales cycle expected.',
                'expected_close_date' => now()->addMonths(4),
                'assigned_to' => $adminUser->id,
                'last_contacted_at' => now()->subDays(7),
            ],
        ];

        foreach ($leads as $leadData) {
            $lead = CrmLead::create($leadData);
            
            // Create sample activities for each lead
            $this->createSampleActivities($lead, $adminUser);
            
            // Create deals for qualified and negotiation leads
            if (in_array($lead->status, ['qualified', 'proposal', 'negotiation', 'won', 'lost'])) {
                $this->createSampleDeal($lead, $adminUser);
            }
        }
        
        $this->command->info('CRM sample data created successfully!');
    }
    
    private function createSampleActivities($lead, $user)
    {
        $activities = [];
        
        // Initial contact activity
        $activities[] = [
            'lead_id' => $lead->id,
            'user_id' => $user->id,
            'type' => 'note',
            'subject' => 'Lead created',
            'description' => "Initial lead from {$lead->source}. Contact: {$lead->full_name} at {$lead->company_name}.",
            'activity_date' => $lead->created_at,
            'status' => 'completed',
            'created_at' => $lead->created_at,
            'updated_at' => $lead->created_at,
        ];
        
        // Status-based activities
        switch ($lead->status) {
            case 'contacted':
                $activities[] = [
                    'lead_id' => $lead->id,
                    'user_id' => $user->id,
                    'type' => 'call',
                    'subject' => 'Initial contact call',
                    'description' => 'Made first contact. Discussed their laboratory equipment needs.',
                    'activity_date' => $lead->created_at->addDay(),
                    'status' => 'completed',
                    'created_at' => $lead->created_at->addDay(),
                    'updated_at' => $lead->created_at->addDay(),
                ];
                break;
                
            case 'qualified':
                $activities[] = [
                    'lead_id' => $lead->id,
                    'user_id' => $user->id,
                    'type' => 'call',
                    'subject' => 'Qualification call',
                    'description' => 'Confirmed budget and decision-making process. Lead qualified.',
                    'activity_date' => $lead->created_at->addDays(2),
                    'status' => 'completed',
                    'created_at' => $lead->created_at->addDays(2),
                    'updated_at' => $lead->created_at->addDays(2),
                ];
                break;
                
            case 'proposal':
                $activities[] = [
                    'lead_id' => $lead->id,
                    'user_id' => $user->id,
                    'type' => 'quote_sent',
                    'subject' => 'Proposal sent',
                    'description' => 'Detailed proposal sent with equipment specifications and pricing.',
                    'activity_date' => $lead->created_at->addDays(3),
                    'status' => 'completed',
                    'created_at' => $lead->created_at->addDays(3),
                    'updated_at' => $lead->created_at->addDays(3),
                ];
                break;
                
            case 'negotiation':
                $activities[] = [
                    'lead_id' => $lead->id,
                    'user_id' => $user->id,
                    'type' => 'meeting',
                    'subject' => 'Contract negotiation meeting',
                    'description' => 'In-person meeting to discuss terms and finalize contract details.',
                    'activity_date' => $lead->created_at->addDays(5),
                    'status' => 'completed',
                    'created_at' => $lead->created_at->addDays(5),
                    'updated_at' => $lead->created_at->addDays(5),
                ];
                break;
                
            case 'won':
                $activities[] = [
                    'lead_id' => $lead->id,
                    'user_id' => $user->id,
                    'type' => 'note',
                    'subject' => 'Deal closed - Won!',
                    'description' => 'Contract signed! Equipment delivery scheduled for next month.',
                    'activity_date' => $lead->created_at->addWeek(),
                    'status' => 'completed',
                    'created_at' => $lead->created_at->addWeek(),
                    'updated_at' => $lead->created_at->addWeek(),
                ];
                break;
                
            case 'lost':
                $activities[] = [
                    'lead_id' => $lead->id,
                    'user_id' => $user->id,
                    'type' => 'note',
                    'subject' => 'Deal lost',
                    'description' => 'Client chose competitor due to budget constraints. Keep for future opportunities.',
                    'activity_date' => $lead->created_at->addWeek(),
                    'status' => 'completed',
                    'created_at' => $lead->created_at->addWeek(),
                    'updated_at' => $lead->created_at->addWeek(),
                ];
                break;
        }
        
        // Add follow-up tasks for active leads
        if (!in_array($lead->status, ['won', 'lost'])) {
            $activities[] = [
                'lead_id' => $lead->id,
                'user_id' => $user->id,
                'type' => 'follow_up',
                'subject' => 'Weekly follow-up call',
                'description' => 'Scheduled follow-up to check on decision timeline.',
                'activity_date' => now()->addDays(3),
                'status' => 'scheduled',
                'due_date' => now()->addDays(3),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        foreach ($activities as $activity) {
            CrmActivity::create($activity);
        }
    }
    
    private function createSampleDeal($lead, $user)
    {
        $stageMapping = [
            'qualified' => 'qualification',
            'proposal' => 'proposal',
            'negotiation' => 'negotiation',
            'won' => 'closed_won',
            'lost' => 'closed_lost',
        ];
        
        $probabilityMapping = [
            'qualification' => 25,
            'proposal' => 50,
            'negotiation' => 75,
            'closed_won' => 100,
            'closed_lost' => 0,
        ];
        
        $stage = $stageMapping[$lead->status];
        $dealName = $lead->company_name . ' - ' . ($lead->estimated_value > 200000 ? 'Major' : 'Standard') . ' Equipment Upgrade';
        
        $deal = CrmDeal::create([
            'deal_name' => $dealName,
            'lead_id' => $lead->id,
            'deal_value' => $lead->estimated_value,
            'stage' => $stage,
            'probability' => $probabilityMapping[$stage],
            'expected_close_date' => $lead->expected_close_date,
            'actual_close_date' => in_array($stage, ['closed_won', 'closed_lost']) ? $lead->expected_close_date : null,
            'description' => 'Laboratory equipment upgrade including molecular diagnostics, hematology analyzers, and consumables.',
            'products_interested' => json_encode(['molecular_diagnostics', 'hematology_analyzers', 'lab_consumables']),
            'assigned_to' => $user->id,
            'loss_reason' => $stage === 'closed_lost' ? 'Budget constraints' : null,
        ]);
    }
} 