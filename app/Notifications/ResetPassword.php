<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordBase;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;

class ResetPassword extends ResetPasswordBase
{
    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $resetUrl = $this->resetUrl($notifiable);
        
        // Determine user type for the template
        $userType = 'customer'; // Default
        if ($notifiable->role && $notifiable->role->name === 'supplier') {
            $userType = 'supplier';
        }

        return (new MailMessage)
            ->subject('🔐 Reset Your Password - ' . Config::get('app.name'))
            ->view('emails.reset-password', [
                'user' => $notifiable,
                'resetUrl' => $resetUrl,
                'userType' => $userType,
                'token' => $this->token
            ]);
    }

    /**
     * Get the reset URL for the given notifiable.
     */
    protected function resetUrl($notifiable): string
    {
        $url = URL::route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], true);

        // Ensure proper URL encoding for special characters
        return str_replace(['?', '&'], ['%3F', '%26'], $url);
    }
} 