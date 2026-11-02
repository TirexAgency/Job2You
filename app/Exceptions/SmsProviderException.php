<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

/**
 * Exception levée lorsqu'un fournisseur SMS refuse ou ne peut pas traiter un envoi.
 */
class SmsProviderException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly ?int $httpStatus = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $httpStatus ?? 0, $previous);
    }
}
