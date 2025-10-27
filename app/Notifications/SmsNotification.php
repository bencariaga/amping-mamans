<?php

namespace App\Notifications;

use Humans\Semaphore\Laravel\SemaphoreChannel;
use Humans\Semaphore\Laravel\SemaphoreMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SmsNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $smsMessage = 'Hello, Semaphore!')
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [SemaphoreChannel::class];
    }

    /**
     * Get the Semaphore representation of the notification.
     */
    public function toSemaphore(object $notifiable): SemaphoreMessage
    {
        return (new SemaphoreMessage)
            ->message($this->smsMessage);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
