<?php

namespace App\Services;

use App\Models\CandidateProfile;
use App\Models\JobMatch;
use App\Models\Offer;

class MatchingService
{
    /**
     * Score pondéré (0 à 1) entre un profil candidat et une offre.
     *
     * @return array{score: float, details: array<string, float|int|bool>}
     */
    public function scoreFor(CandidateProfile $profile, Offer $offer): array
    {
        $skillsScore = $this->skillsScore($profile, $offer);
        $jobScore = $this->textSimilarity((string) $profile->desired_jobs, (string) $offer->title);
        $locationScore = $this->locationScore($profile, $offer);
        $contractScore = $profile->contract_type && $offer->contract_type && $profile->contract_type === $offer->contract_type ? 1.0 : 0.0;

        $score = round(
            $skillsScore * 0.5 + $jobScore * 0.25 + $locationScore * 0.15 + $contractScore * 0.10,
            2
        );

        return [
            'score' => min(max($score, 0.0), 1.0),
            'details' => [
                'skills' => $skillsScore,
                'job' => $jobScore,
                'location' => $locationScore,
                'contract' => $contractScore,
            ],
        ];
    }

    public function computeForProfile(CandidateProfile $profile): int
    {
        $count = 0;
        Offer::where('status', 'active')->each(function (Offer $offer) use ($profile, &$count) {
            $result = $this->scoreFor($profile, $offer);
            JobMatch::updateOrCreate(
                ['user_id' => $profile->user_id, 'offer_id' => $offer->id],
                ['score' => $result['score'], 'details_json' => $result['details']]
            );
            $count++;
        });

        return $count;
    }

    private function skillsScore(CandidateProfile $profile, Offer $offer): float
    {
        $profileSkills = $profile->skills()->pluck('skills.id');
        $offerSkills = $offer->offerSkills()->pluck('skill_id');

        if ($offerSkills->isEmpty()) {
            return 0.5;
        }

        $matched = $profileSkills->intersect($offerSkills)->count();

        return round($matched / max($offerSkills->count(), 1), 2);
    }

    private function textSimilarity(string $a, string $b): float
    {
        if ($a === '' || $b === '') {
            return 0.0;
        }

        similar_text(mb_strtolower($a), mb_strtolower($b), $percent);

        return round($percent / 100, 2);
    }

    private function locationScore(CandidateProfile $profile, Offer $offer): float
    {
        if (in_array($profile->mobility, ['remote', 'national', 'international'], true)) {
            return 1.0;
        }

        if ($profile->city && $offer->location && str_contains(mb_strtolower($offer->location), mb_strtolower($profile->city))) {
            return 1.0;
        }

        return 0.3;
    }
}
