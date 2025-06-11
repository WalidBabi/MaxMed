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
            ? 'New User Registration' 
            : 'User Login Detected';
            
        if ($this->type === 'registered') {
            $message = 'A new user has registered: ' . $this->user->name . ' (' . $this->user->email . ')';
        } else {
            $message = 'User ' . $this->user->name . ' (' . $this->user->email . ') has logged in at ' . now() . 
                     ' using ' . $this->method . ' authentication';
        }

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
