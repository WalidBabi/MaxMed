<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\CrmLead;

class LeadCreatedNotification extends Notification
{
    use Queueable;

    public $lead;

    /**
     * Create a new notification instance.
     */
    public function __construct(CrmLead $lead)
    {
        $this->lead = $lead;
        
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
        $subject = '👥 New Lead Assigned - ' . $this->lead->full_name;
        
        $message = (new MailMessage)
            ->subject($subject)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A new lead has been assigned to you.')
            ->line('**Lead Details:**')
            ->line('• Name: ' . $this->lead->full_name)
            ->line('• Email: ' . $this->lead->email)
            ->line('• Company: ' . $this->lead->company_name)
            ->line('• Phone: ' . ($this->lead->mobile ?: $this->lead->phone ?: 'Not provided'))
            ->line('• Source: ' . ucfirst($this->lead->source))
            ->line('• Priority: ' . ucfirst($this->lead->priority))
            ->line('• Estimated Value: ' . ($this->lead->estimated_value ? '$' . number_format($this->lead->estimated_value, 2) : 'Not specified'));
            
        if ($this->lead->notes) {
            $message->line('• Notes: ' . $this->lead->notes);
        }
        
        if ($this->lead->expected_close_date) {
            $message->line('• Expected Close Date: ' . $this->lead->expected_close_date->format('M d, Y'));
        }
        
        $message->action('View Lead Details', url('/crm/leads/' . $this->lead->id))
                ->line('Please review the lead details and follow up as appropriate.')
                ->line('Thank you for using ' . config('app.name') . '!');
                
        return $message;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'lead_assigned',
            'lead_id' => $this->lead->id,
            'lead_name' => $this->lead->full_name,
            'lead_email' => $this->lead->email,
            'lead_company' => $this->lead->company_name,
            'lead_source' => $this->lead->source ?? 'Unknown',
            'lead_priority' => $this->lead->priority,
            'estimated_value' => $this->lead->estimated_value,
            'created_at' => $this->lead->created_at->toISOString(),
            'title' => 'New Lead Assigned',
            'message' => 'A new lead "' . $this->lead->full_name . '" from ' . $this->lead->company_name . ' has been assigned to you.'
        ];
    }
} 