<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\CrmLead;
use App\Models\User;

class LeadReassignedNotification extends Notification
{
    use Queueable;

    public $lead;
    public $previousAssignee;
    public $newAssignee;
    public $reassignedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(CrmLead $lead, ?User $previousAssignee, User $newAssignee, User $reassignedBy)
    {
        $this->lead = $lead;
        $this->previousAssignee = $previousAssignee;
        $this->newAssignee = $newAssignee;
        $this->reassignedBy = $reassignedBy;
        
        // Use configured queue connection
        $this->onQueue('notifications');
        $this->delay(now()->addSeconds(2));
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        // Always use database for dashboard notifications
        $channels = ['database'];
        
        // Add email notification if in production or email is configured
        if (config('mail.default') && config('mail.default') !== 'log') {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $subject = '🔄 Lead Reassigned - ' . $this->lead->full_name;
        
        $message = (new MailMessage)
            ->subject($subject)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A lead has been reassigned to you.')
            ->line('**Lead Details:**')
            ->line('• Name: ' . $this->lead->full_name)
            ->line('• Email: ' . $this->lead->email)
            ->line('• Company: ' . $this->lead->company_name)
            ->line('• Phone: ' . ($this->lead->mobile ?: $this->lead->phone ?: 'Not provided'))
            ->line('• Source: ' . ucfirst($this->lead->source))
            ->line('• Priority: ' . ucfirst($this->lead->priority))
            ->line('• Current Status: ' . ucfirst(str_replace('_', ' ', $this->lead->status)))
            ->line('• Estimated Value: ' . ($this->lead->estimated_value ? '$' . number_format($this->lead->estimated_value, 2) : 'Not specified'));
            
        if ($this->lead->notes) {
            $message->line('• Notes: ' . $this->lead->notes);
        }
        
        if ($this->lead->expected_close_date) {
            $message->line('• Expected Close Date: ' . $this->lead->expected_close_date->format('M d, Y'));
        }

        $message->line('**Assignment Details:**');
        
        if ($this->previousAssignee) {
            $message->line('• Previous Assignee: ' . $this->previousAssignee->name);
        } else {
            $message->line('• Previous Assignee: Unassigned');
        }
        
        $message->line('• Reassigned By: ' . $this->reassignedBy->name);
        $message->line('• Reassigned On: ' . now()->format('M d, Y \a\t g:i A'));
        
        $message->action('View Lead Details', url('/crm/leads/' . $this->lead->id))
                ->line('Please review the lead details and continue the follow-up process.')
                ->line('Thank you for using ' . config('app.name') . '!');
                
        return $message;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'lead_reassigned',
            'lead_id' => $this->lead->id,
            'lead_name' => $this->lead->full_name,
            'lead_email' => $this->lead->email,
            'lead_company' => $this->lead->company_name,
            'lead_source' => $this->lead->source ?? 'Unknown',
            'lead_priority' => $this->lead->priority,
            'lead_status' => $this->lead->status,
            'estimated_value' => $this->lead->estimated_value,
            'previous_assignee_id' => $this->previousAssignee?->id,
            'previous_assignee_name' => $this->previousAssignee?->name ?? 'Unassigned',
            'new_assignee_id' => $this->newAssignee->id,
            'new_assignee_name' => $this->newAssignee->name,
            'reassigned_by_id' => $this->reassignedBy->id,
            'reassigned_by_name' => $this->reassignedBy->name,
            'reassigned_at' => now()->toISOString(),
            'title' => 'Lead Reassigned',
            'message' => 'Lead "' . $this->lead->full_name . '" from ' . $this->lead->company_name . ' has been reassigned to you by ' . $this->reassignedBy->name . '.'
        ];
    }
}
