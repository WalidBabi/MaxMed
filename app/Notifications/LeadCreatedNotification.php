<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeadCreatedNotification extends Notification
{
    use Queueable;

    public $lead;

    /**
     * Create a new notification instance.
     */
    public function __construct($lead)
    {
        $this->lead = $lead;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'lead',
            'lead_id' => $this->lead->id,
            'lead_name' => $this->lead->name,
            'lead_email' => $this->lead->email,
            'lead_source' => $this->lead->source ?? 'Unknown',
            'created_at' => $this->lead->created_at->toISOString(),
            'title' => 'New lead created',
            'message' => 'A new lead "' . $this->lead->name . '" has been created from ' . ($this->lead->source ?? 'unknown source')
        ];
    }
} 