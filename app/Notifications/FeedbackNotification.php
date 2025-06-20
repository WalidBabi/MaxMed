<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Feedback;

class FeedbackNotification extends Notification
{
    use Queueable;

    public $feedback;

    /**
     * Create a new notification instance.
     */
    public function __construct(Feedback $feedback)
    {
        $this->feedback = $feedback;
        
        // Use Redis notifications queue
        $this->onConnection('redis');
        $this->onQueue('notifications');
        $this->delay(now()->addSeconds(2));
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        // Always use database for admin dashboard, email as secondary
        $channels = ['database'];
        
        // Only add email if in production or specifically configured
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
        $subject = '📝 New Customer Feedback - ' . config('app.name');
        
        return (new MailMessage)
            ->subject($subject)
            ->greeting('New Customer Feedback Received')
            ->line('A customer has submitted feedback for their order. Please review the feedback details below.')
            ->line('**Customer Details:**')
            ->line('- **Name:** ' . $this->feedback->user->name)
            ->line('- **Email:** ' . $this->feedback->user->email)
            ->line('- **Order:** #' . $this->feedback->order->order_number)
            ->line('- **Rating:** ' . $this->feedback->rating . '/5 stars')
            ->line('- **Date:** ' . $this->feedback->created_at->format('F j, Y \a\t g:i A'))
            ->line('')
            ->line('**Feedback:**')
            ->line($this->feedback->feedback)
            ->action('View Feedback Details', url('/admin/feedback/' . $this->feedback->id))
            ->line('This feedback can help improve your services and customer satisfaction.')
            ->salutation('Best regards,  
' . config('app.name') . ' Admin System');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'feedback',
            'feedback_id' => $this->feedback->id,
            'user_name' => $this->feedback->user->name,
            'user_email' => $this->feedback->user->email,
            'order_number' => $this->feedback->order->order_number,
            'rating' => $this->feedback->rating,
            'feedback_text' => substr($this->feedback->feedback, 0, 100),
            'created_at' => $this->feedback->created_at->toISOString(),
            'title' => 'New customer feedback received',
            'message' => $this->feedback->user->name . ' rated order #' . $this->feedback->order->order_number . ' with ' . $this->feedback->rating . '/5 stars'
        ];
    }
} 