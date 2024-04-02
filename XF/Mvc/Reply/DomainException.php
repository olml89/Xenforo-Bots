<?php declare(strict_types=1);

namespace olml89\XenforoBots\XF\Mvc\Reply;

use XF\Api\ErrorMessage;
use XF\Mvc\Reply\Error;

abstract class DomainException extends ApiException
{
    abstract protected static function errorCode(): string;

    public static function fromDomainException(DomainException $domainException): static
    {
        /** @var Error $error */
        $error = $domainException->getReply();

        /** @var ErrorMessage $errorMessage */
        $errorMessage = $error->getErrors()[0];

        return static::fromMessageAndErrorCode(
            message: $errorMessage->getMessage(),
            errorCode: static::errorCode() . '.' . $errorMessage->getCode(),
            context: $domainException->getContext() ?? $domainException,
        );
    }
}
