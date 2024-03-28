<?php declare(strict_types=1);

namespace olml89\XenforoBots\XF\Mvc\Reply;

abstract class NotFoundException extends DomainException
{
    protected static function httpCode(): int
    {
        return 404;
    }
}
