<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

/**
 * Exception levée lorsque FiveOne Pay refuse ou ne peut pas traiter une requête.
 */
class FiveOnePayException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly ?int $httpStatus = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $httpStatus ?? 0, $previous);
    }
}
