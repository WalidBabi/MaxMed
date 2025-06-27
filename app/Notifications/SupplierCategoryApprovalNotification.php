<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Category;
use App\Models\SupplierCategory;

class SupplierCategoryApprovalNotification extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Category $category,
        public ?SupplierCategory $supplierCategory,
        public User $approvedBy,
        public bool $isApproved = true
    ) {}

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
    public function toMail(object $notifiable): Mailable
    {
        $subject = $this->isApproved 
            ? 'Category Approval - ' . $this->category->name
            : 'Category Request Update - ' . $this->category->name;
            
        $view = $this->isApproved 
            ? 'emails.supplier-category-approved'
            : 'emails.supplier-category-rejected';

        return (new Mailable)
            ->to($notifiable->email)
            ->subject($subject)
            ->view($view, [
                'supplier' => $notifiable,
                'category' => $this->category,
                'supplierCategory' => $this->supplierCategory,
                'approvedBy' => $this->approvedBy,
                'isApproved' => $this->isApproved,
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
            'category_id' => $this->category->id,
            'category_name' => $this->category->name,
            'approved_by' => $this->approvedBy->id,
            'approved_by_name' => $this->approvedBy->name,
            'is_approved' => $this->isApproved,
            'approved_at' => now(),
        ];
    }
} 