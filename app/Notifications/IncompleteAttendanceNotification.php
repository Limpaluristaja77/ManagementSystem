<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IncompleteAttendanceNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $type,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = $this->type === 'check-out'
            ? 'You have checked in today, but you have not checked out yet.'
            : 'You have not checked in today yet.';

        return (new MailMessage)
            ->subject('Attendance reminder')
            ->greeting('Hello '.$notifiable->name)
            ->line($message)
            ->line('Please update your attendance record as soon as possible.')
            ->action('Open attendance', url('/attendance'));
    }

    /**
     * @return array<string, string>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->type,
        ];
    }
}
