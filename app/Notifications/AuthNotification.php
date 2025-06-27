<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AuthNotification extends Notification
{
    public $type;
    public $user;
    public $method;

    /**
     * Create a new notification instance.
     */
    public function __construct($user, $type, $method = 'Email')
    {
        $this->user = $user;
        $this->type = $type;
        $this->method = $method;
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
            ? '👤 New User Registration - ' . config('app.name') 
            : '🔐 User Login Activity - ' . config('app.name');
            
        $actionUrl = $this->type === 'registered'
            ? url('/admin/users/' . $this->user->id)
            : url('/admin/dashboard');

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.auth-notification', [
                'type' => $this->type,
                'user' => $this->user,
                'method' => $this->method,
                'actionUrl' => $actionUrl
            ]);
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
            'method' => $this->method,
        ];
    }
}
