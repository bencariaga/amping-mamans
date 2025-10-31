<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowBudgetNotification extends Notification
{
    use Queueable;

    private float $remainingBudget;
    private float $threshold;

    public function __construct(float $remainingBudget, float $threshold)
    {
        $this->remainingBudget = $remainingBudget;
        $this->threshold = $threshold;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Low Budget Alert - AMPING MAMANS')
            ->line('The current budget is running low.')
            ->line('Remaining Budget: ₱' . number_format($this->remainingBudget, 2))
            ->line('Threshold: ₱' . number_format($this->threshold, 2))
            ->action('View Budget Details', url('/financial/budget'))
            ->line('Please consider adding supplementary budget or adjusting expenses.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'remaining_budget' => $this->remainingBudget,
            'threshold' => $this->threshold,
            'message' => 'Budget is below threshold',
            'notified_at' => now(),
        ];
    }
}
