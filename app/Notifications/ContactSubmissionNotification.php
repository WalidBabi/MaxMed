<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactSubmissionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $submission;

    /**
     * Create a new notification instance.
     */
    public function __construct($submission)
    {
        $this->submission = $submission;
        
        // Set queue configuration for better performance
        $this->onQueue('notifications');
        $this->delay(now()->addSeconds(3)); // Small delay to avoid burst
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
            'type' => 'contact_submission',
            'submission_id' => $this->submission->id,
            'name' => $this->submission->name,
            'email' => $this->submission->email,
            'subject' => $this->submission->subject,
            'message' => substr($this->submission->message, 0, 100) . '...',
            'created_at' => $this->submission->created_at->toISOString(),
            'title' => 'New contact submission',
            'message' => 'New contact submission from ' . $this->submission->name . ' about "' . $this->submission->subject . '"'
        ];
    }
} 