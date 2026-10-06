<?php

namespace App\Console\Commands;

use App\Models\CandidateProfile;
use App\Services\MatchingService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('matches:compute')]
#[Description('Recalcule les scores de matching candidat/offre')]
class MatchesComputeCommand extends Command
{
    public function handle(MatchingService $matching): int
    {
        $total = 0;
        CandidateProfile::with('skills')->each(function (CandidateProfile $profile) use ($matching, &$total) {
            $total += $matching->computeForProfile($profile);
        });

        $this->info("{$total} matchings calculés.");

        return self::SUCCESS;
    }
}
