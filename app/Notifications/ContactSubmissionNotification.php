<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactSubmissionNotification extends Notification
{
    public $submission;

    /**
     * Create a new notification instance.
     */
    public function __construct($submission)
    {
        $this->submission = $submission;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $subject = '🔔 New Contact Submission - MaxMed';
        
        return (new MailMessage)
            ->subject($subject)
            ->greeting('New Contact Submission Received')
            ->line('A new contact submission has been received through your website.')
            ->line('**Customer Details:**')
            ->line('- **Name:** ' . $this->submission->name)
            ->line('- **Email:** ' . $this->submission->email)
            ->line('- **Subject:** ' . $this->submission->subject)
            ->line('- **Phone:** ' . ($this->submission->phone ?: 'Not provided'))
            ->line('- **Company:** ' . ($this->submission->company ?: 'Not provided'))
            ->line('- **Date:** ' . $this->submission->created_at->format('F j, Y \a\t g:i A'))
            ->line('')
            ->line('**Message:**')
            ->line($this->submission->message)
            ->action('View Submission Details', url('/admin/contact-submissions/' . $this->submission->id))
            ->line('Please review and respond to this submission promptly.')
            ->salutation('Best regards,  
MaxMed Admin System');
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