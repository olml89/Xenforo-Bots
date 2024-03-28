<?php declare(strict_types=1);

namespace olml89\XenforoBots\XF\Mvc\Reply;

abstract class DomainException extends ApiException
{
    abstract protected static function errorCode(): string;
}
