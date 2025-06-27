<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class EmailVerificationReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public $daysRemaining;

    /**
     * Create a new notification instance.
     */
    public function __construct($daysRemaining = 7)
    {
        $this->daysRemaining = $daysRemaining;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60), // URL expires in 60 minutes
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        $isSupplier = $notifiable->role && $notifiable->role->name === 'supplier';
        $userType = $isSupplier ? 'supplier' : 'customer';

        return (new MailMessage)
            ->subject('🔐 Verify Your Email - ' . Config::get('app.name'))
            ->view('emails.verify-email-reminder', [
                'user' => $notifiable,
                'verificationUrl' => $verificationUrl,
                'userType' => $userType,
                'daysRemaining' => $this->daysRemaining
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'days_remaining' => $this->daysRemaining,
            'user_type' => $notifiable->role ? $notifiable->role->name : 'customer',
        ];
    }
}
