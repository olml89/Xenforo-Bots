<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\Exception;

use olml89\XenforoSubscriptions\XF\Mvc\Reply\DomainException;
use olml89\XenforoSubscriptions\XF\Mvc\Reply\UnprocessableEntityException;
use XF\Api\ErrorMessage;
use XF\Mvc\Entity\Entity;
use XF\Mvc\Reply\Error;
use XF\PrintableException;

abstract class EntityValidationException extends UnprocessableEntityException
{
    public static function entity(Entity $entity): static
    {
        $printableException = new PrintableException($entity->getErrors());

        return static::fromMessageAndErrorCode(
            message: $printableException->getMessage(),
            errorCode: static::errorCode() . '.' . $printableException->getCode(),
            context: $printableException,
        );
    }

    public static function childEntityException(DomainException $domainException): static
    {
        /** @var Error $error */
        $error = $domainException->getReply();

        /** @var ErrorMessage $errorMessage */
        $errorMessage = $error->getErrors()[0];

        return static::fromMessageAndErrorCode(
            message: $errorMessage->getMessage(),
            errorCode: static::errorCode() . '.' . $errorMessage->getCode(),
            context: $domainException,
        );
    }
}
