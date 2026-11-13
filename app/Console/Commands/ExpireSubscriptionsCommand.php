<?php

namespace App\Console\Commands;

use App\Services\Subscriptions\SubscriptionService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('subscriptions:expire')]
#[Description('Détecte les abonnements échus et rétrograde les utilisateurs vers la formule gratuite')]
class ExpireSubscriptionsCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(SubscriptionService $subscriptionService): int
    {
        $count = $subscriptionService->expireDueSubscriptions();

        $this->info("Abonnements expirés : {$count}.");

        return self::SUCCESS;
    }
}
