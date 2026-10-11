<?php

namespace App\DTO;

use Carbon\CarbonImmutable;
use DateTimeInterface;

/**
 * DTO pour une offre brute récupérée depuis une source.
 */
readonly class RawOfferDTO
{
    /**
     * @param  array<string, mixed>  $metadata  Données additionnelles spécifiques à la source
     */
    public function __construct(
        public string $externalId,
        public string $title,
        public ?string $company = null,
        public ?string $location = null,
        public ?string $description = null,
        public ?string $contractType = null,
        public ?string $sourceUrl = null,
        public ?DateTimeInterface $publishedAt = null,
        public ?DateTimeInterface $expiresAt = null,
        public array $metadata = [],
    ) {}

    /**
     * Crée un DTO depuis un tableau associatif.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            externalId: (string) ($data['external_id'] ?? $data['id'] ?? ''),
            title: (string) ($data['title'] ?? ''),
            company: $data['company'] ?? $data['company_name'] ?? null,
            location: $data['location'] ?? $data['city'] ?? $data['region'] ?? null,
            description: $data['description'] ?? $data['content'] ?? $data['summary'] ?? null,
            contractType: $data['contract_type'] ?? $data['contract'] ?? $data['type'] ?? null,
            sourceUrl: $data['source_url'] ?? $data['url'] ?? $data['link'] ?? null,
            publishedAt: isset($data['published_at']) ? self::parseDate($data['published_at']) : null,
            expiresAt: isset($data['expires_at']) ? self::parseDate($data['expires_at']) : null,
            metadata: $data['metadata'] ?? [],
        );
    }

    /**
     * Parse une date depuis divers formats.
     */
    private static function parseDate(mixed $value): ?DateTimeInterface
    {
        if ($value instanceof DateTimeInterface) {
            return $value;
        }

        if (is_numeric($value)) {
            return CarbonImmutable::createFromTimestamp((int) $value);
        }

        try {
            return CarbonImmutable::parse((string) $value);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Convertit en tableau pour la normalisation.
     */
    public function toArray(): array
    {
        return [
            'external_id' => $this->externalId,
            'title' => $this->title,
            'company' => $this->company,
            'location' => $this->location,
            'description' => $this->description,
            'contract_type' => $this->contractType,
            'source_url' => $this->sourceUrl,
            'published_at' => $this->publishedAt,
            'expires_at' => $this->expiresAt,
            'metadata' => $this->metadata,
        ];
    }
}
