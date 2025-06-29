<?php

namespace App\Mail;

use App\Models\SystemFeedback;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Route;

class AdminSystemFeedbackSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $systemFeedback;
    public $url;

    /**
     * Create a new message instance.
     */
    public function __construct(SystemFeedback $systemFeedback)
    {
        $this->systemFeedback = $systemFeedback;
        $this->url = route('admin.feedback.show-system', $systemFeedback->id);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $typeEmoji = match($this->systemFeedback->type) {
            'bug_report' => '🐛',
            'feature_request' => '✨',
            'improvement' => '🚀',
            'general' => '💬',
            default => '📝'
        };

        $priorityEmoji = match($this->systemFeedback->priority) {
            'high' => '🔴',
            'medium' => '🟡',
            'low' => '🟢',
            default => '⚪'
        };

        return $this->subject("$typeEmoji Admin Alert: New System Feedback - {$this->systemFeedback->title}")
                    ->view('emails.admin.system-feedback-submitted')
                    ->with([
                        'systemFeedback' => $this->systemFeedback,
                        'url' => $this->url,
                        'typeEmoji' => $typeEmoji,
                        'priorityEmoji' => $priorityEmoji
                    ]);
    }
} 