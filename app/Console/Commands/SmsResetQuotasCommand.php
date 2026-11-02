<?php

namespace App\Console\Commands;

use App\Services\Sms\SmsQuotaService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('sms:reset-quotas')]
#[Description('Réinitialise les quotas SMS mensuels des abonnements payants')]
class SmsResetQuotasCommand extends Command
{
    /**
     * Exécute la réinitialisation des quotas.
     */
    public function handle(SmsQuotaService $quotaService): int
    {
        $count = $quotaService->resetExpiredQuotas();

        $this->info("Quotas SMS réinitialisés : {$count} abonnement(s).");

        return self::SUCCESS;
    }
}
