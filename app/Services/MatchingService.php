<?php

namespace App\Services;

use App\Models\CandidateProfile;
use App\Models\JobMatch;
use App\Models\Offer;
use App\Models\Setting;
use App\Services\Sms\SmsAlertService;
use Illuminate\Database\Eloquent\Collection;

/**
 * Service de calcul du score de correspondance entre un profil candidat et une offre.
 *
 * Critères et pondérations configurables via settings :
 * - Compétences : 40% (défaut)
 * - Métier/intitulé : 25% (défaut)
 * - Localisation : 15% (défaut)
 * - Expérience : 10% (défaut)
 * - Contrat : 10% (défaut)
 *
 * Formule : score = Σ(poids_critère × score_critère) ∈ [0, 1]
 * Seuil d'alerte par défaut : 70% (configurable)
 */
class MatchingService
{
    public function __construct(
        private readonly SmsAlertService $smsAlertService,
    ) {}

    /**
     * Calcule le score de correspondance pour un couple profil/offre.
     *
     * @return array{score: float, details: array<string, float>, meets_threshold: bool}
     */
    public function scoreFor(CandidateProfile $profile, Offer $offer): array
    {
        $weights = $this->getWeights();

        $skillsScore = $this->skillsScore($profile, $offer);
        $jobScore = $this->jobTitleScore($profile, $offer);
        $locationScore = $this->locationScore($profile, $offer);
        $experienceScore = $this->experienceScore($profile, $offer);
        $contractScore = $this->contractScore($profile, $offer);

        $score = round(
            $skillsScore * $weights['skills'] +
            $jobScore * $weights['job'] +
            $locationScore * $weights['location'] +
            $experienceScore * $weights['experience'] +
            $contractScore * $weights['contract'],
            4
        );

        $score = min(max($score, 0.0), 1.0);

        $threshold = $this->getAlertThreshold();
        $meetsThreshold = $score >= $threshold;

        return [
            'score' => $score,
            'details' => [
                'skills' => $skillsScore,
                'job' => $jobScore,
                'location' => $locationScore,
                'experience' => $experienceScore,
                'contract' => $contractScore,
            ],
            'meets_threshold' => $meetsThreshold,
            'threshold' => $threshold,
        ];
    }

    /**
     * Calcule et persiste les matchings pour un profil contre toutes les offres actives.
     *
     * @return int Nombre de matchings calculés
     */
    public function computeForProfile(CandidateProfile $profile): int
    {
        $count = 0;

        Offer::where('status', 'active')->each(function (Offer $offer) use ($profile, &$count) {
            $result = $this->scoreFor($profile, $offer);

            JobMatch::updateOrCreate(
                ['user_id' => $profile->user_id, 'offer_id' => $offer->id],
                [
                    'score' => $result['score'],
                    'details_json' => $result['details'],
                ]
            );
            $count++;
        });

        return $count;
    }

    /**
     * Calcule les matchings pour une nouvelle offre contre tous les profils candidats.
     * Utilisé quand une nouvelle offre est scrapée.
     *
     * @return int Nombre de matchings créés/mis à jour
     */
    public function computeForOffer(Offer $offer): int
    {
        $count = 0;

        CandidateProfile::with('skills', 'experiences', 'user')->each(function (CandidateProfile $profile) use ($offer, &$count) {
            $result = $this->scoreFor($profile, $offer);

            JobMatch::updateOrCreate(
                ['user_id' => $profile->user_id, 'offer_id' => $offer->id],
                [
                    'score' => $result['score'],
                    'details_json' => $result['details'],
                ]
            );

            if ($result['meets_threshold']) {
                $this->smsAlertService->dispatchOfferAlert($profile, $offer, $result['score']);
            }

            $count++;
        });

        return $count;
    }

    /**
     * Récupère les recommandations pour un candidat (matchings au-dessus du seuil).
     *
     * @param  int  $limit  Nombre max de recommandations
     * @return Collection<JobMatch>
     */
    public function getRecommendations(int $userId, int $limit = 10)
    {
        $threshold = $this->getAlertThreshold();

        return JobMatch::with(['offer.source', 'offer.offerSkills.skill'])
            ->where('user_id', $userId)
            ->where('score', '>=', $threshold)
            ->orderByDesc('score')
            ->limit($limit)
            ->get();
    }

    /**
     * Récupère les poids depuis les settings (avec fallback sur défauts).
     *
     * @return array{skills: float, job: float, location: float, experience: float, contract: float}
     */
    protected function getWeights(): array
    {
        return [
            'skills' => (float) Setting::get('matching.skills_weight', 0.40),
            'job' => (float) Setting::get('matching.job_weight', 0.25),
            'location' => (float) Setting::get('matching.location_weight', 0.15),
            'experience' => (float) Setting::get('matching.experience_weight', 0.10),
            'contract' => (float) Setting::get('matching.contract_weight', 0.10),
        ];
    }

    /**
     * Récupère le seuil d'alerte depuis les settings.
     */
    protected function getAlertThreshold(): float
    {
        return (float) Setting::get('matching.alert_threshold', 0.70);
    }

    /**
     * Score compétences : intersection des compétences profil vs offre.
     * Retourne un score entre 0 et 1.
     */
    private function skillsScore(CandidateProfile $profile, Offer $offer): float
    {
        $profileSkillIds = $profile->skills()->pluck('skills.id')->toArray();
        $offerSkillIds = $offer->offerSkills()->pluck('skill_id')->toArray();

        if (empty($offerSkillIds)) {
            // Si l'offre n'a pas de compétences définies, score neutre
            return 0.5;
        }

        $matched = array_intersect($profileSkillIds, $offerSkillIds);

        // Pourcentage de compétences de l'offre couvertes par le profil
        return round(count($matched) / count($offerSkillIds), 2);
    }

    /**
     * Score métier : similarité texte entre poste souhaité et titre offre.
     * Utilise similar_text() PHP normalisé 0-1.
     */
    private function jobTitleScore(CandidateProfile $profile, Offer $offer): float
    {
        $desiredJobs = (string) $profile->desired_jobs;
        $offerTitle = (string) $offer->title;

        if ($desiredJobs === '' || $offerTitle === '') {
            return 0.0;
        }

        similar_text(mb_strtolower($desiredJobs), mb_strtolower($offerTitle), $percent);

        return round($percent / 100, 2);
    }

    /**
     * Score localisation : compatibilité géographique.
     * - Mobilité remote/national/international = 1.0
     * - Ville exacte dans localisation offre = 1.0
     * - Même région = 0.7
     * - Défaut = 0.3
     */
    private function locationScore(CandidateProfile $profile, Offer $offer): float
    {
        // Mobilité large
        if (in_array($profile->mobility, ['remote', 'national', 'international'], true)) {
            return 1.0;
        }

        $profileCity = $profile->city ? mb_strtolower($profile->city) : null;
        $offerLocation = $offer->location ? mb_strtolower($offer->location) : null;

        if ($profileCity && $offerLocation && str_contains($offerLocation, $profileCity)) {
            return 1.0;
        }

        // Même région (comparaison simple sur region)
        if ($profile->region && $offerLocation && str_contains($offerLocation, mb_strtolower($profile->region))) {
            return 0.7;
        }

        return 0.3;
    }

    /**
     * Score expérience : adéquation niveau d'expérience.
     * Compare le niveau souhaité du candidat vs exigences implicites de l'offre.
     *
     * Niveaux : junior (0-2), confirmed (3-5), senior (6-10), expert (10+)
     * L'offre n'ayant pas de champ explicite, on déduit du titre (junior/senior/lead).
     */
    private function experienceScore(CandidateProfile $profile, Offer $offer): float
    {
        $profileLevel = $profile->experience_level ?? 'junior';
        $offerTitle = mb_strtolower($offer->title ?? '');

        // Déduire le niveau requis de l'offre depuis le titre
        $offerLevel = $this->inferExperienceLevel($offerTitle);

        // Mapping niveaux vers années
        $levelYears = [
            'junior' => 1,
            'confirmed' => 4,
            'senior' => 7,
            'expert' => 12,
        ];

        $profileYears = $levelYears[$profileLevel] ?? 1;
        $offerYears = $levelYears[$offerLevel] ?? 4;

        // Score basé sur la proximité (différence en années)
        $diff = abs($profileYears - $offerYears);

        // 0 an diff = 1.0, 1 an = 0.85, 2 ans = 0.7, 3 ans = 0.55, 4 ans = 0.4, 5+ = 0.25
        if ($diff === 0) {
            return 1.0;
        }
        if ($diff === 1) {
            return 0.85;
        }
        if ($diff === 2) {
            return 0.70;
        }
        if ($diff === 3) {
            return 0.55;
        }
        if ($diff === 4) {
            return 0.40;
        }

        return 0.25;
    }

    /**
     * Infère le niveau d'expérience requis depuis le titre de l'offre.
     */
    private function inferExperienceLevel(string $title): string
    {
        $title = mb_strtolower($title);

        if (str_contains($title, 'lead') || str_contains($title, 'principal') || str_contains($title, 'architect') || str_contains($title, 'expert')) {
            return 'expert';
        }

        if (str_contains($title, 'senior') || str_contains($title, 'sr ') || str_contains($title, 'confirmé') || str_contains($title, 'expérimenté')) {
            return 'senior';
        }

        if (str_contains($title, 'junior') || str_contains($title, 'jr ') || str_contains($title, 'débutant') || str_contains($title, 'alternance') || str_contains($title, 'stage') || str_contains($title, 'apprenti')) {
            return 'junior';
        }

        return 'confirmed'; // Par défaut
    }

    /**
     * Score contrat : correspondance exacte du type de contrat.
     */
    private function contractScore(CandidateProfile $profile, Offer $offer): float
    {
        if ($profile->contract_type && $offer->contract_type && $profile->contract_type === $offer->contract_type) {
            return 1.0;
        }

        // Si le candidat accepte "any" ou n'a pas de préférence
        if (! $profile->contract_type || in_array($profile->contract_type, ['any', 'indifferent'], true)) {
            return 0.8;
        }

        return 0.0;
    }
}
