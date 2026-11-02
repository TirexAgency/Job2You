<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * Seeds des paramètres par défaut pour le matching et les SMS.
 */
class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- Poids du matching (somme = 1.0) ---
        Setting::updateOrCreate(
            ['key' => 'matching.skills_weight'],
            [
                'label' => 'Poids - Compétences',
                'value' => '0.40',
                'type' => Setting::TYPE_DECIMAL,
                'description' => 'Pondération des compétences techniques dans le score de matching (0 à 1)',
                'group' => Setting::GROUP_MATCHING,
            ]
        );

        Setting::updateOrCreate(
            ['key' => 'matching.job_weight'],
            [
                'label' => 'Poids - Métier / Intitulé',
                'value' => '0.25',
                'type' => Setting::TYPE_DECIMAL,
                'description' => 'Pondération de la similarité du titre de poste souhaité (0 à 1)',
                'group' => Setting::GROUP_MATCHING,
            ]
        );

        Setting::updateOrCreate(
            ['key' => 'matching.location_weight'],
            [
                'label' => 'Poids - Localisation',
                'value' => '0.15',
                'type' => Setting::TYPE_DECIMAL,
                'description' => 'Pondération de la compatibilité géographique (0 à 1)',
                'group' => Setting::GROUP_MATCHING,
            ]
        );

        Setting::updateOrCreate(
            ['key' => 'matching.experience_weight'],
            [
                'label' => 'Poids - Expérience',
                'value' => '0.10',
                'type' => Setting::TYPE_DECIMAL,
                'description' => 'Pondération de l\'adéquation niveau d\'expérience (0 à 1)',
                'group' => Setting::GROUP_MATCHING,
            ]
        );

        Setting::updateOrCreate(
            ['key' => 'matching.contract_weight'],
            [
                'label' => 'Poids - Type de contrat',
                'value' => '0.10',
                'type' => Setting::TYPE_DECIMAL,
                'description' => 'Pondération de la correspondance du type de contrat (0 à 1)',
                'group' => Setting::GROUP_MATCHING,
            ]
        );

        // --- Seuil de déclenchement alerte SMS ---
        Setting::updateOrCreate(
            ['key' => 'matching.alert_threshold'],
            [
                'label' => 'Seuil d\'alerte SMS',
                'value' => '0.70',
                'type' => Setting::TYPE_DECIMAL,
                'description' => 'Score minimum (0 à 1) pour déclencher une alerte SMS au candidat',
                'group' => Setting::GROUP_MATCHING,
            ]
        );

        // --- Configuration SMS ---
        Setting::updateOrCreate(
            ['key' => 'sms.free_trial_limit'],
            [
                'label' => 'Limite SMS gratuits (essai)',
                'value' => '2',
                'type' => Setting::TYPE_INTEGER,
                'description' => 'Nombre de SMS offerts aux candidats gratuits (RG01)',
                'group' => Setting::GROUP_SMS,
            ]
        );

        Setting::updateOrCreate(
            ['key' => 'sms.provider'],
            [
                'label' => 'Fournisseur SMS',
                'value' => 'tirex',
                'type' => Setting::TYPE_STRING,
                'description' => 'Fournisseur d\'envoi SMS (tirex, twilio, etc.)',
                'group' => Setting::GROUP_SMS,
            ]
        );

        // --- Configuration Scraping ---
        Setting::updateOrCreate(
            ['key' => 'scraping.max_offers_per_source'],
            [
                'label' => 'Max offres par source',
                'value' => '100',
                'type' => Setting::TYPE_INTEGER,
                'description' => 'Nombre maximum d\'offres à traiter par source et par exécution',
                'group' => Setting::GROUP_SCRAPING,
            ]
        );

        Setting::updateOrCreate(
            ['key' => 'scraping.request_timeout'],
            [
                'label' => 'Timeout requêtes scraping (secondes)',
                'value' => '30',
                'type' => Setting::TYPE_INTEGER,
                'description' => 'Délai maximal d\'attente pour les requêtes HTTP de collecte',
                'group' => Setting::GROUP_SCRAPING,
            ]
        );

        $this->command?->info('Settings par défaut créés/mis à jour.');
    }
}