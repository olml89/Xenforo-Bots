<?php declare(strict_types=1);

namespace olml89\XenforoBots\XF\Mvc\Reply;

abstract class UnprocessableEntityException extends DomainException
{
    protected static function httpCode(): int
    {
        return 422;
    }
}
