<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContactList;
use App\Models\User;

class ContactListSeeder extends Seeder
{
    public function run()
    {
        $adminUser = User::where('email', 'admin@maxmed.com')->first();
        if (!$adminUser) {
            $adminUser = User::first();
        }

        if (!$adminUser) {
            $this->command->warn('No users found. Please create a user first.');
            return;
        }

        // Static Contact Lists
        ContactList::create([
            'name' => 'VIP Customers',
            'description' => 'High-value customers who deserve special attention and exclusive offers.',
            'type' => 'static',
            'is_active' => true,
            'created_by' => $adminUser->id,
        ]);

        ContactList::create([
            'name' => 'Product Launch Subscribers',
            'description' => 'Contacts who specifically requested updates about new product launches.',
            'type' => 'static',
            'is_active' => true,
            'created_by' => $adminUser->id,
        ]);

        // Dynamic Contact Lists
        ContactList::create([
            'name' => 'Healthcare Professionals',
            'description' => 'Automatically includes all contacts in the healthcare industry.',
            'type' => 'dynamic',
            'criteria' => [
                [
                    'field' => 'industry',
                    'operator' => 'equals',
                    'value' => 'healthcare'
                ]
            ],
            'is_active' => true,
            'created_by' => $adminUser->id,
        ]);

        ContactList::create([
            'name' => 'UAE Based Contacts',
            'description' => 'Dynamic list of all contacts located in the UAE.',
            'type' => 'dynamic',
            'criteria' => [
                [
                    'field' => 'country',
                    'operator' => 'equals',
                    'value' => 'UAE'
                ]
            ],
            'is_active' => true,
            'created_by' => $adminUser->id,
        ]);

        ContactList::create([
            'name' => 'Laboratory Managers',
            'description' => 'Contacts with laboratory management positions.',
            'type' => 'dynamic',
            'criteria' => [
                [
                    'field' => 'job_title',
                    'operator' => 'contains',
                    'value' => 'laboratory manager'
                ]
            ],
            'is_active' => true,
            'created_by' => $adminUser->id,
        ]);

        $this->command->info('Contact lists seeded successfully!');
    }
} 