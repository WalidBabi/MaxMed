<?php

namespace App\Console\Commands;

use App\Models\ContactList;
use App\Models\MarketingContact;
use Illuminate\Console\Command;

class DebugContactLists extends Command
{
    protected $signature = 'contact-lists:debug {--list-id= : Specific contact list ID to debug}';
    protected $description = 'Debug contact lists to verify static vs dynamic functionality';

    public function handle()
    {
        $listId = $this->option('list-id');
        
        if ($listId) {
            $this->debugSpecificList($listId);
        } else {
            $this->debugAllLists();
        }
    }

    private function debugAllLists()
    {
        $this->info('=== Contact Lists Debug Report ===');
        $this->newLine();

        $contactLists = ContactList::all();
        
        if ($contactLists->isEmpty()) {
            $this->warn('No contact lists found.');
            return;
        }

        $this->table(
            ['ID', 'Name', 'Type', 'Active', 'Contacts (Static)', 'Contacts (Dynamic)', 'Contacts (Method)'],
            $contactLists->map(function ($list) {
                return [
                    $list->id,
                    $list->name,
                    $list->type,
                    $list->is_active ? 'Yes' : 'No',
                    $list->contacts()->count(),
                    $list->isDynamic() ? $list->getDynamicContacts()->count() : 'N/A',
                    $list->getContactsCount(),
                ];
            })
        );

        $this->newLine();
        $this->info('Legend:');
        $this->line('- Contacts (Static): Contacts stored in pivot table');
        $this->line('- Contacts (Dynamic): Contacts matching criteria (for dynamic lists)');
        $this->line('- Contacts (Method): Count from getContactsCount() method');
    }

    private function debugSpecificList($listId)
    {
        $list = ContactList::find($listId);
        
        if (!$list) {
            $this->error("Contact list with ID {$listId} not found.");
            return;
        }

        $this->info("=== Debugging Contact List: {$list->name} ===");
        $this->newLine();

        $this->line("ID: {$list->id}");
        $this->line("Name: {$list->name}");
        $this->line("Type: {$list->type}");
        $this->line("Active: " . ($list->is_active ? 'Yes' : 'No'));
        $this->line("Created: {$list->created_at}");
        $this->newLine();

        if ($list->isDynamic()) {
            $this->info('=== Dynamic List Criteria ===');
            if ($list->criteria) {
                foreach ($list->criteria as $index => $criterion) {
                    $this->line("Criterion " . ($index + 1) . ":");
                    $this->line("  Field: " . ($criterion['field'] ?? 'N/A'));
                    $this->line("  Operator: " . ($criterion['operator'] ?? 'N/A'));
                    $this->line("  Value: " . ($criterion['value'] ?? 'N/A'));
                }
            } else {
                $this->warn('No criteria defined for this dynamic list.');
            }
            $this->newLine();

            $this->info('=== Dynamic Contacts ===');
            $dynamicContacts = $list->getDynamicContacts()->get();
            if ($dynamicContacts->isNotEmpty()) {
                $this->table(
                    ['ID', 'Name', 'Email', 'Industry', 'Status'],
                    $dynamicContacts->map(function ($contact) {
                        return [
                            $contact->id,
                            $contact->full_name,
                            $contact->email,
                            $contact->industry ?? 'N/A',
                            $contact->status,
                        ];
                    })
                );
            } else {
                $this->warn('No contacts match the dynamic criteria.');
            }
        } else {
            $this->info('=== Static List Contacts ===');
            $staticContacts = $list->contacts()->get();
            if ($staticContacts->isNotEmpty()) {
                $this->table(
                    ['ID', 'Name', 'Email', 'Industry', 'Status', 'Added At'],
                    $staticContacts->map(function ($contact) {
                        return [
                            $contact->id,
                            $contact->full_name,
                            $contact->email,
                            $contact->industry ?? 'N/A',
                            $contact->status,
                            $contact->pivot->added_at ?? 'N/A',
                        ];
                    })
                );
            } else {
                $this->warn('No contacts manually added to this static list.');
            }
        }

        $this->newLine();
        $this->info('=== Summary ===');
        $this->line("Total contacts in list: {$list->getContactsCount()}");
        $this->line("Active contacts in list: {$list->getActiveContactsCount()}");
    }
} 