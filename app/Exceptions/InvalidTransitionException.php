<?php

namespace App\Exceptions;

use RuntimeException;

class InvalidTransitionException extends RuntimeException
{
    public static function between(string $from, string $to): self
    {
        return new self("Transisi status tidak valid: {$from} → {$to}");
    }
}
