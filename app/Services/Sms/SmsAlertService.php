<?php

namespace App\Services\Sms;

use App\Jobs\SendSmsJob;
use App\Models\CandidateProfile;
use App\Models\Offer;
use Illuminate\Support\Str;

/**
 * Prépare et met en file les alertes SMS liées aux nouvelles offres.
 */
class SmsAlertService
{
    /**
     * Dispatch une alerte SMS si le candidat le souhaite et possède un numéro.
     */
    public function dispatchOfferAlert(CandidateProfile $profile, Offer $offer, float $score): void
    {
        if (! $profile->alerts_enabled) {
            return;
        }

        $user = $profile->user;

        if (! $user || ! $user->phone) {
            return;
        }

        $idempotencyKey = "offer-alert:{$user->id}:{$offer->id}";
        $message = $this->buildMessage($offer, $score);

        SendSmsJob::dispatch(
            $user->id,
            $offer->id,
            $user->phone,
            $message,
            $idempotencyKey,
        );
    }

    /**
     * Construit un message court adapté au SMS.
     */
    private function buildMessage(Offer $offer, float $score): string
    {
        $company = $offer->company ? " chez {$offer->company}" : '';
        $percentage = (int) round(min(max($score, 0), 1) * 100);

        $message = sprintf(
            'Job2You : %s%s correspond à %d%% de votre profil. %s',
            Str::limit($offer->title, 70),
            Str::limit($company, 45),
            $percentage,
            route('offers.show', $offer),
        );

        return Str::limit($message, 480);
    }
}
