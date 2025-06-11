<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AuthNotification extends Notification
{
    use Queueable;

    public $type;
    public $user;

    /**
     * Create a new notification instance.
     */
    public function __construct($user, $type)
    {
        $this->user = $user;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $subject = $this->type === 'registered' 
            ? 'New User Registration' 
            : 'User Login Detected';
            
        $message = $this->type === 'registered'
            ? 'A new user has registered: ' . $this->user->name . ' (' . $this->user->email . ')'
            : 'User ' . $this->user->name . ' (' . $this->user->email . ') has logged in at ' . now();

        return (new MailMessage)
            ->subject($subject)
            ->line($message);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'user_id' => $this->user->id,
            'type' => $this->type,
            'time' => now(),
        ];
    }
}
