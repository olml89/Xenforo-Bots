<?php declare(strict_types=1);

namespace olml89\Subscriptions\Exceptions;

use Exception;

abstract class InputException extends ApiException
{
    public function __construct(string $message, string $errorCode)
    {
        parent::__construct(
            message: $message,
            errorCode: $errorCode,
            httpCode: 400,
        );
    }
}
