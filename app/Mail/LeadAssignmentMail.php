<?php

namespace App\Mail;

use App\Models\CrmLead;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeadAssignmentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $lead;
    public $assignedUser;
    public $previousAssignee;
    public $reassignedBy;
    public $isNewLead;

    /**
     * Create a new message instance.
     */
    public function __construct(CrmLead $lead, User $assignedUser, ?User $previousAssignee = null, ?User $reassignedBy = null, bool $isNewLead = false)
    {
        $this->lead = $lead;
        $this->assignedUser = $assignedUser;
        $this->previousAssignee = $previousAssignee;
        $this->reassignedBy = $reassignedBy;
        $this->isNewLead = $isNewLead;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->isNewLead 
            ? '👥 New Lead Assigned - ' . $this->lead->full_name
            : '🔄 Lead Reassigned - ' . $this->lead->full_name;

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.lead-assignment',
            with: [
                'lead' => $this->lead,
                'assignedUser' => $this->assignedUser,
                'previousAssignee' => $this->previousAssignee,
                'reassignedBy' => $this->reassignedBy,
                'isNewLead' => $this->isNewLead,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
