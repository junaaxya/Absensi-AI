<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LateCheckinNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $userName,
        protected string $checkinTime,
        protected string $date,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Late Check-in Alert - ' . $this->userName)
            ->greeting('Late Check-in Notification')
            ->line("User **{$this->userName}** checked in late at **{$this->checkinTime}** on {$this->date}.")
            ->line('Please review the attendance record.')
            ->action('View Dashboard', url('/admin/dashboard'));
    }
}
