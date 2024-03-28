<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\XF\Mvc\Reply;

use XF\Mvc\Entity\Entity;
use XF\PrintableException;

abstract class UnprocessableEntityException extends DomainException
{
    protected static function httpCode(): int
    {
        return 422;
    }
}
