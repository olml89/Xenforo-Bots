<?php declare(strict_types=1);

namespace olml89\Subscriptions\Exceptions\Http;

use Throwable;

abstract class InputException extends ApiException
{
    public function __construct(string $message, string $errorCode, ?Throwable $context = null)
    {
        parent::__construct(
            message: $message,
            errorCode: $errorCode,
            httpCode: 400,
            context: $context,
        );
    }
}
