<?php

namespace App\Services\Sms;

use App\Models\Setting;
use App\Models\SmsLog;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Gère les quotas SMS des candidats.
 *
 * - Les candidats sans abonnement utilisent leur quota gratuit (2 SMS par défaut).
 * - Les candidats avec un abonnement actif consomment le quota de leur formule.
 * - Les formules payantes voient leur quota réinitialisé chaque mois.
 */
class SmsQuotaService
{
    /**
     * Retourne le nombre de SMS restants pour un utilisateur.
     */
    public function remaining(User $user): int
    {
        $subscriptions = $user->activeSubscriptions()->with('plan')->get();

        if ($subscriptions->isNotEmpty()) {
            $subscriptions->each(fn (Subscription $subscription) => $this->resetIfDue($subscription));

            return max((int) $subscriptions->sum('sms_remaining'), 0);
        }

        return max($this->freeLimit($user) - (int) $user->sms_sent, 0);
    }

    /**
     * Retourne le quota total de l'utilisateur (restant ou consommé inclus).
     */
    public function quotaLimit(User $user): int
    {
        $subscriptions = $user->activeSubscriptions()->with('plan')->get();

        if ($subscriptions->isNotEmpty()) {
            return max((int) $subscriptions->sum(fn (Subscription $subscription) => $subscription->plan?->sms_quota ?? 0), 0);
        }

        return $this->freeLimit($user);
    }

    /**
     * Réserve atomiquement un SMS sur le quota de l'utilisateur.
     *
     * Retourne false lorsque le quota est épuisé, sans lever d'exception.
     */
    public function reserve(User $user, SmsLog $log): bool
    {
        return DB::transaction(function () use ($user, $log): bool {
            $lockedUser = User::query()->lockForUpdate()->find($user->id);

            if (! $lockedUser) {
                return false;
            }

            $subscriptions = $lockedUser->activeSubscriptions()
                ->with('plan')
                ->lockForUpdate()
                ->orderBy('id')
                ->get();

            foreach ($subscriptions as $subscription) {
                $this->resetIfDue($subscription);
            }

            $subscription = $subscriptions->first(fn (Subscription $subscription) => $subscription->sms_remaining > 0);

            if ($subscription) {
                $subscription->decrement('sms_remaining');

                $log->forceFill([
                    'subscription_id' => $subscription->id,
                    'quota_source' => 'subscription',
                    'quota_consumed' => true,
                ])->save();

                return true;
            }

            if ($subscriptions->isNotEmpty()) {
                return false;
            }

            if ($lockedUser->sms_sent >= $this->freeLimit($lockedUser)) {
                return false;
            }

            $lockedUser->increment('sms_sent');

            $log->forceFill([
                'subscription_id' => null,
                'quota_source' => 'free',
                'quota_consumed' => true,
            ])->save();

            return true;
        });
    }

    /**
     * Rend un SMS réservé lorsque l'envoi échoue définitivement.
     */
    public function release(SmsLog $log): void
    {
        DB::transaction(function () use ($log): void {
            $lockedLog = SmsLog::query()->lockForUpdate()->find($log->getKey());

            if (! $lockedLog || ! $lockedLog->quota_consumed) {
                return;
            }

            if ($lockedLog->quota_source === 'subscription' && $lockedLog->subscription_id) {
                $subscription = Subscription::with('plan')->lockForUpdate()->find($lockedLog->subscription_id);

                if ($subscription) {
                    $limit = (int) ($subscription->plan?->sms_quota ?? $subscription->sms_remaining + 1);
                    $subscription->update([
                        'sms_remaining' => min((int) $subscription->sms_remaining + 1, $limit),
                    ]);
                }
            } else {
                $user = User::query()->lockForUpdate()->find($lockedLog->user_id);

                if ($user && $user->sms_sent > 0) {
                    $user->decrement('sms_sent');
                }
            }

            $lockedLog->update(['quota_consumed' => false]);
        });
    }

    /**
     * Réinitialise les quotas mensuels des abonnements payants arrivés à échéance.
     *
     * @return int Nombre d'abonnements réinitialisés
     */
    public function resetExpiredQuotas(): int
    {
        $count = 0;

        Subscription::with('plan')
            ->where('status', 'active')
            ->whereHas('plan', fn ($query) => $query->where('price', '>', 0))
            ->chunkById(100, function ($subscriptions) use (&$count): void {
                foreach ($subscriptions as $subscription) {
                    if ($this->resetIfDue($subscription)) {
                        $count++;
                    }
                }
            });

        return $count;
    }

    /**
     * Réinitialise un abonnement payant si son cycle mensuel est terminé.
     */
    private function resetIfDue(Subscription $subscription): bool
    {
        if (! $subscription->plan || (float) $subscription->plan->price <= 0) {
            return false;
        }

        $lastReset = $subscription->sms_reset_at ?? $subscription->starts_at;

        if (! $lastReset || $lastReset->copy()->addMonth()->isFuture()) {
            return false;
        }

        $subscription->update([
            'sms_remaining' => (int) $subscription->plan->sms_quota,
            'sms_reset_at' => now(),
        ]);

        return true;
    }

    /**
     * Retourne la limite gratuite configurée pour un utilisateur.
     */
    private function freeLimit(User $user): int
    {
        return max((int) Setting::get('sms.free_trial_limit', $user->sms_quota ?: 2), 0);
    }
}
