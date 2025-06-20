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
            
        if ($this->type === 'registered') {
            $greeting = 'New User Registration Alert';
            $message = 'A new user has successfully registered on ' . config('app.name') . '. Please review the registration details below.';
            $actionText = 'View User Profile';
            $actionUrl = url('/admin/users/' . $this->user->id);
        } else {
            $greeting = 'User Login Activity';
            $message = 'A user has logged into ' . config('app.name') . '. Login activity details are provided below.';
            $actionText = 'View Admin Dashboard';
            $actionUrl = url('/admin/dashboard');
        }

        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($message)
            ->line('**User Details:**')
            ->line('- **Name:** ' . $this->user->name)
            ->line('- **Email:** ' . $this->user->email)
            ->line('- **User ID:** #' . $this->user->id)
            ->line('- **Time:** ' . now()->toDayDateTimeString())
            ->line('- **Authentication Method:** ' . $this->method)
            ->action($actionText, $actionUrl)
            ->line('This is an automated notification for administrative monitoring.')
            ->line('No action is required unless you notice suspicious activity.')
            ->salutation('Best regards,  
' . config('app.name') . ' Admin System');
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
