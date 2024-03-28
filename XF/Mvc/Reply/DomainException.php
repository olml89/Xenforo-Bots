<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\XF\Mvc\Reply;

abstract class DomainException extends ApiException
{
    abstract protected static function errorCode(): string;
}
