<?php

namespace App\Notifications;

use App\Models\CitizenRequest;
use App\Models\ServiceLog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification
{
    use Queueable;

    /**
     * @var CitizenRequest|ServiceLog
     */
    public $task;

    /**
     * Create a new notification instance.
     *
     * @param CitizenRequest|ServiceLog $task
     */
    public function __construct($task)
    {
        $this->task = $task;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->greeting("Hello {$notifiable->name},");

        if ($this->task instanceof CitizenRequest) {
            $ref = $this->task->tracking_code ?: ('REQ-' . $this->task->id);
            $type = ucwords(str_replace('_', ' ', $this->task->request_type));
            $priority = strtoupper($this->task->urgency ?: 'NORMAL');
            $url = route('admin.citizen-requests.show', $this->task);

            $mail->subject("[Task Assigned] Citizen Report {$ref} - {$type}")
                ->line("You have been assigned to handle a Citizen Report in the SmartBarangay system.")
                ->line("**Reference Number:** {$ref}")
                ->line("**Report Type:** {$type}")
                ->line("**Priority Level:** {$priority}")
                ->line("**Location / Purok:** " . ($this->task->purok ?: 'N/A'))
                ->line("**Description:** " . substr($this->task->description, 0, 150) . (strlen($this->task->description) > 150 ? '...' : ''))
                ->action('View & Respond to Case', $url)
                ->line('Please review the case details and initiate appropriate actions or communication.');
        } elseif ($this->task instanceof ServiceLog) {
            $ref = $this->task->log_number ?: ('SL-' . $this->task->id);
            $type = ucwords(str_replace('_', ' ', $this->task->service_type));
            $date = $this->task->date_of_service ? date('F d, Y', strtotime($this->task->date_of_service)) : 'TBD';
            $url = route('admin.service-logs.show', $this->task);

            $mail->subject("[Task Assigned] Service Task {$ref} - {$type}")
                ->line("You have been assigned to perform a Barangay Service Task.")
                ->line("**Log Number:** {$ref}")
                ->line("**Service Type:** {$type}")
                ->line("**Scheduled Date:** {$date}")
                ->line("**Description:** " . substr($this->task->description, 0, 150) . (strlen($this->task->description) > 150 ? '...' : ''))
                ->action('View Service Details', $url)
                ->line('Please ensure you prepare necessary materials and complete the task on schedule.');
        } else {
            $mail->subject("[Task Assigned] New Barangay Task Assignment")
                ->line("You have a new assignment in SmartBarangay.")
                ->action('Go to Admin Dashboard', route('admin.dashboard'));
        }

        return $mail;
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        if ($this->task instanceof CitizenRequest) {
            $ref = $this->task->tracking_code ?: ('REQ-' . $this->task->id);
            $type = ucwords(str_replace('_', ' ', $this->task->request_type));
            return [
                'task_type'        => 'citizen_request',
                'task_id'          => $this->task->id,
                'reference_number' => $ref,
                'title'            => "Citizen Report: {$type}",
                'priority'         => $this->task->urgency ?: 'normal',
                'url'              => route('admin.citizen-requests.show', $this->task),
                'message'          => "You were assigned to Citizen Report [{$ref}] - {$type}.",
            ];
        }

        if ($this->task instanceof ServiceLog) {
            $ref = $this->task->log_number ?: ('SL-' . $this->task->id);
            $type = ucwords(str_replace('_', ' ', $this->task->service_type));
            return [
                'task_type'        => 'service_log',
                'task_id'          => $this->task->id,
                'reference_number' => $ref,
                'title'            => "Service Task: {$type}",
                'priority'         => 'normal',
                'url'              => route('admin.service-logs.show', $this->task),
                'message'          => "You were assigned to Service Task [{$ref}] - {$type}.",
            ];
        }

        return [
            'task_type' => 'general',
            'task_id'   => null,
            'title'     => 'New Task Assigned',
            'url'       => route('admin.dashboard'),
            'message'   => 'You have been assigned a new task in the portal.',
        ];
    }
}