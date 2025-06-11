<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AuthNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $type;
    public $user;
    public $method;

    /**
     * Create a new notification instance.
     */
    public function __construct($user, string $type, string $method = 'Email')
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
            ? '🎉 Welcome to ' . config('app.name') . ' - Account Registered' 
            : '🔐 Login Detected on Your Account';
            
        if ($this->type === 'registered') {
            $greeting = 'Welcome to ' . config('app.name') . '!';
            $message = 'Your account has been successfully registered. We\'re excited to have you on board!';
            $actionText = 'Go to Dashboard';
            $actionUrl = url('/dashboard');
        } else {
            $greeting = 'New Login Alert';
            $message = 'We noticed a login to your account from a new device or location.';
            $actionText = 'View Account Activity';
            $actionUrl = url('/account/security');
        }

        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($message)
            ->line('**Account Details:**')
            ->line('- **Name:** ' . $this->user->name)
            ->line('- **Email:** ' . $this->user->email)
            ->line('- **Time:** ' . now()->toDayDateTimeString())
            ->line('- **Authentication Method:** ' . $this->method)
            ->action($actionText, $actionUrl)
            ->line('If you did not perform this action, please secure your account immediately.')
            ->salutation('Regards,  
' . config('app.name') . ' Team');
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
