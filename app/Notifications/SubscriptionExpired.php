<?php

namespace App\Notifications;

use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpired extends Notification
{
    use Queueable;

    public function __construct(
        public Subscription $subscription,
        public ?Plan $fallbackPlan = null,
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
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Votre abonnement '.$this->subscription->plan->name.' a expiré — Job2You')
            ->view('emails.subscription-expired', [
                'user' => $notifiable,
                'subscription' => $this->subscription,
                'plan' => $this->subscription->plan,
                'fallbackPlan' => $this->fallbackPlan,
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
            'subscription_id' => $this->subscription->id,
            'plan_id' => $this->subscription->plan_id,
            'fallback_plan_id' => $this->fallbackPlan?->id,
            'status' => 'expired',
        ];
    }
}
