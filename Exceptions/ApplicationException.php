<?php declare(strict_types=1);

namespace olml89\Subscriptions\Exceptions;

use RuntimeException;
use Throwable;

abstract class ApplicationException extends RuntimeException
{
    private string $errorCode;

    public function __construct(string $message, string $errorCode, ?Throwable $previous = null)
    {
        $this->errorCode = $errorCode;

        parent::__construct(
            message: $message,
            previous: $previous
        );
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }
}
