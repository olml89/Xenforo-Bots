<?php declare(strict_types=1);

namespace olml89\Subscriptions\XF\Mvc\Reply;

abstract class InternalServerErrorException extends ApiException
{
    protected function httpCode(): int
    {
        return 500;
    }
}
