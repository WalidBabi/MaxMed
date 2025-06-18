<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\SystemFeedback;

class SystemFeedbackNotification extends Notification
{
    use Queueable;

    public $systemFeedback;

    /**
     * Create a new notification instance.
     */
    public function __construct(SystemFeedback $systemFeedback)
    {
        $this->systemFeedback = $systemFeedback;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $subject = '🔧 New System Feedback - ' . config('app.name');
        
        $typeEmoji = match($this->systemFeedback->type) {
            'bug_report' => '🐛',
            'feature_request' => '✨',
            'improvement' => '🚀',
            'general' => '💬',
            default => '📝'
        };
        
        return (new MailMessage)
            ->subject($subject)
            ->greeting('New System Feedback Received')
            ->line('A user has submitted system feedback. Please review the details below.')
            ->line('**User Details:**')
            ->line('- **Name:** ' . $this->systemFeedback->user->name)
            ->line('- **Email:** ' . $this->systemFeedback->user->email)
            ->line('- **Type:** ' . $typeEmoji . ' ' . ucfirst(str_replace('_', ' ', $this->systemFeedback->type)))
            ->line('- **Priority:** ' . ucfirst($this->systemFeedback->priority))
            ->line('- **Date:** ' . $this->systemFeedback->created_at->format('F j, Y \a\t g:i A'))
            ->line('')
            ->line('**Title:** ' . $this->systemFeedback->title)
            ->line('')
            ->line('**Description:**')
            ->line($this->systemFeedback->description)
            ->action('View Feedback Details', url('/admin/system-feedback/' . $this->systemFeedback->id))
            ->line('Please review and respond to this feedback to improve the system.')
            ->salutation('Best regards,  
' . config('app.name') . ' Admin System');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'system_feedback',
            'feedback_id' => $this->systemFeedback->id,
            'user_name' => $this->systemFeedback->user->name,
            'user_email' => $this->systemFeedback->user->email,
            'feedback_type' => $this->systemFeedback->type,
            'priority' => $this->systemFeedback->priority,
            'title' => $this->systemFeedback->title,
            'description' => substr($this->systemFeedback->description, 0, 100),
            'created_at' => $this->systemFeedback->created_at->toISOString(),
            'message' => 'New ' . str_replace('_', ' ', $this->systemFeedback->type) . ' feedback: ' . $this->systemFeedback->title
        ];
    }
} 