<?php declare(strict_types=1);

namespace olml89\Subscriptions\Exceptions\Http;

abstract class InternalServerErrorException extends ApiException
{
    protected function httpCode(): int
    {
        return 500;
    }
}
