<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Crée les plans par défaut.
     */
    public function run(): void
    {
        // Plan gratuit (RG01 : 2 SMS gratuits)
        Plan::create([
            'name' => 'free',
            'price' => 0,
            'duration_days' => 0,
            'sms_quota' => 2,
            'cv_parsing_enabled' => false,
            'active' => true,
        ]);

        // Plan premium (exemple)
        Plan::create([
            'name' => 'premium',
            'price' => 15000,
            'duration_days' => 30,
            'sms_quota' => 50,
            'cv_parsing_enabled' => true,
            'active' => true,
        ]);
    }
}
