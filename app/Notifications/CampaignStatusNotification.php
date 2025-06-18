<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CampaignStatusNotification extends Notification
{
    use Queueable;

    public $campaign;
    public $status;

    /**
     * Create a new notification instance.
     */
    public function __construct($campaign, $status)
    {
        $this->campaign = $campaign;
        $this->status = $status;
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
            'type' => 'campaign',
            'campaign_id' => $this->campaign->id,
            'campaign_name' => $this->campaign->name,
            'campaign_status' => $this->status,
            'campaign_type' => $this->campaign->type,
            'created_at' => now()->toISOString(),
            'title' => 'Campaign status update',
            'message' => 'Campaign "' . $this->campaign->name . '" status changed to ' . ucfirst($this->status)
        ];
    }
} 