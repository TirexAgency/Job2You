<?php

namespace App\DTO;

/**
 * Résultat normalisé d'un envoi SMS auprès d'un fournisseur.
 */
readonly class SmsSendResult
{
    public function __construct(
        public bool $success,
        public ?string $providerReference = null,
        public ?string $providerStatus = null,
        public ?string $errorMessage = null,
    ) {}

    /**
     * Crée un résultat d'envoi réussi.
     */
    public static function success(
        ?string $providerReference = null,
        ?string $providerStatus = null,
    ): self {
        return new self(
            success: true,
            providerReference: $providerReference,
            providerStatus: $providerStatus,
        );
    }

    /**
     * Crée un résultat d'envoi en échec.
     */
    public static function failure(string $errorMessage): self
    {
        return new self(success: false, errorMessage: $errorMessage);
    }
}
