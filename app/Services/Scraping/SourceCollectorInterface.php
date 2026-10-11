<?php

namespace App\Services\Scraping;

use App\DTO\RawOfferDTO;
use App\Models\Source;

/**
 * Interface que tous les collecteurs de sources doivent implémenter.
 */
interface SourceCollectorInterface
{
    /**
     * Récupère les offres brutes depuis la source.
     *
     * @return array<RawOfferDTO>
     */
    public function collect(Source $source): array;

    /**
     * Normalise une offre brute en données prêtes pour la BDD.
     */
    public function normalize(RawOfferDTO $rawOffer, Source $source): array;

    /**
     * Génère un fingerprint unique pour déduplication.
     */
    public function fingerprint(array $normalizedData): string;

    /**
     * Retourne la clé unique du collecteur (doit matcher collector_key en BDD).
     */
    public static function getCollectorKey(): string;
}
