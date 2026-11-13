<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Aligne les quotas restants des abonnements actifs sur le quota de leur plan.
     *
     * Un changement de quota dans l'administration pouvait laisser des
     * abonnements avec un solde supérieur à la limite de leur formule.
     */
    public function up(): void
    {
        DB::table('subscriptions')
            ->where('status', 'active')
            ->orderBy('id')
            ->chunkById(100, function ($subscriptions): void {
                foreach ($subscriptions as $subscription) {
                    $quota = (int) DB::table('plans')
                        ->where('id', $subscription->plan_id)
                        ->value('sms_quota');

                    if ((int) $subscription->sms_remaining > $quota) {
                        DB::table('subscriptions')
                            ->where('id', $subscription->id)
                            ->update(['sms_remaining' => $quota]);
                    }
                }
            });
    }

    /**
     * Aucune donnée n'est restaurée : la correction est une simple borne.
     */
    public function down(): void
    {
        //
    }
};
