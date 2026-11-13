<?php

namespace App\Services\Subscriptions;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\SubscriptionActivated;
use App\Notifications\SubscriptionExpired;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\DB;

/**
 * Gère le cycle de vie des abonnements : activation, expiration et rétrogradation.
 */
class SubscriptionService
{
    public function __construct(
        private readonly ActivityLogger $activityLogger,
    ) {}

    /**
     * Active une formule pour un utilisateur et clôture ses abonnements précédents.
     */
    public function activate(User $user, Plan $plan, ?Payment $payment = null, bool $notify = true): Subscription
    {
        $subscription = DB::transaction(function () use ($user, $plan, $payment): Subscription {
            $lockedUser = User::query()->lockForUpdate()->findOrFail($user->id);

            $lockedUser->activeSubscriptions()
                ->get()
                ->each(function (Subscription $currentSubscription): void {
                    $currentSubscription->update([
                        'status' => 'inactive',
                        'ends_at' => $currentSubscription->ends_at?->isPast()
                            ? $currentSubscription->ends_at
                            : now(),
                    ]);
                });

            $startsAt = now();

            $subscription = $lockedUser->subscriptions()->create([
                'plan_id' => $plan->id,
                'starts_at' => $startsAt,
                'ends_at' => $plan->duration_days > 0 ? $startsAt->copy()->addDays($plan->duration_days) : null,
                'sms_remaining' => (int) $plan->sms_quota,
                'sms_reset_at' => $plan->isFree() ? null : $startsAt,
                'status' => 'active',
            ]);

            $lockedUser->update([
                'plan' => strtolower($plan->name),
                'sms_quota' => (int) $plan->sms_quota,
                'sms_sent' => 0,
            ]);

            if ($payment) {
                $payment->update(['status' => 'paid']);
            }

            return $subscription;
        });

        $subscription->loadMissing('plan', 'user');

        $this->activityLogger->subscriptionActivated($subscription);

        if ($notify) {
            $user->notify(new SubscriptionActivated($subscription));
        }

        return $subscription;
    }

    /**
     * Active la formule gratuite pour un utilisateur.
     */
    public function activateFreePlan(User $user, bool $notify = true): ?Subscription
    {
        $freePlan = $this->freePlan();

        if (! $freePlan) {
            return null;
        }

        return $this->activate($user, $freePlan, null, $notify);
    }

    /**
     * Détecte et traite les abonnements arrivés à échéance.
     *
     * @return int Nombre d'abonnements expirés
     */
    public function expireDueSubscriptions(?User $user = null): int
    {
        $query = Subscription::query()
            ->where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', now());

        if ($user) {
            $query->where('user_id', $user->id);
        }

        $count = 0;

        $query->chunkById(100, function ($subscriptions) use (&$count): void {
            foreach ($subscriptions as $subscription) {
                if ($this->expire($subscription)) {
                    $count++;
                }
            }
        });

        return $count;
    }

    /**
     * Expire un abonnement et rétrograde l'utilisateur vers la formule gratuite si nécessaire.
     */
    public function expire(Subscription $subscription): bool
    {
        return DB::transaction(function () use ($subscription): bool {
            $lockedSubscription = Subscription::query()
                ->with('plan')
                ->lockForUpdate()
                ->find($subscription->id);

            if (! $lockedSubscription
                || $lockedSubscription->status !== 'active'
                || ! $lockedSubscription->ends_at
                || $lockedSubscription->ends_at->isFuture()) {
                return false;
            }

            $user = User::query()->lockForUpdate()->find($lockedSubscription->user_id);

            if (! $user) {
                return false;
            }

            $lockedSubscription->update(['status' => 'inactive']);

            $hasOtherActiveSubscription = $user->activeSubscriptions()->exists();
            $freePlan = null;

            if (! $hasOtherActiveSubscription) {
                $freePlan = $this->freePlan();

                if ($freePlan && $freePlan->id !== $lockedSubscription->plan_id) {
                    $this->activate($user, $freePlan, null, false);
                } elseif (! $freePlan) {
                    $user->update([
                        'plan' => 'free',
                        'sms_quota' => 0,
                        'sms_sent' => 0,
                    ]);
                }
            }

            $lockedSubscription->loadMissing('plan');
            $user->notify(new SubscriptionExpired($lockedSubscription, $freePlan));

            $this->activityLogger->subscriptionExpired($lockedSubscription, $freePlan);

            return true;
        });
    }

    /**
     * Retourne la formule gratuite active.
     */
    private function freePlan(): ?Plan
    {
        return Plan::query()
            ->where('active', true)
            ->where('price', '<=', 0)
            ->orderBy('price')
            ->first()
            ?? Plan::query()->where('name', 'free')->first();
    }
}
