<?php

namespace App\Notifications;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentProofDeclinedNotification extends Notification
{
    use Queueable;

    public Document $document;

    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $docType = Document::TYPES[$this->document->document_type] ?? $this->document->document_type;
        $reason = $this->document->payment_notes ?: 'Payment reference or receipt screenshot could not be verified against transaction records.';
        $url = route('portal.track.detail', $this->document->document_number);

        return (new MailMessage)
            ->subject("[Action Required] Payment Proof Declined — Document #{$this->document->document_number}")
            ->greeting("Hello {$notifiable->first_name},")
            ->line("Your proof of payment for **{$docType}** (Document No. `{$this->document->document_number}`) was reviewed by the Barangay Office and has been declined.")
            ->line("**Barangay Office Explanation Note:**")
            ->line("\"{$reason}\"")
            ->line("To avoid delays in releasing your document, please review the explanation above and submit a corrected GCash receipt screenshot or valid reference number.")
            ->action('Submit Corrected Payment Proof', $url)
            ->line("If you prefer to pay in cash, you may also settle the fee of ₱" . number_format($this->document->fee, 2) . " directly at the Barangay Hall upon claiming your document.");
    }
}
