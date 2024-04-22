<?php declare(strict_types=1);

namespace olml89\XenforoBots\Exception;

use olml89\XenforoBots\XF\Mvc\Reply\UnprocessableEntityException;
use XF\Mvc\Entity\Entity;
use XF\PrintableException;

abstract class EntityValidationException extends UnprocessableEntityException
{
    public static function entity(Entity $entity): static
    {
        $printableException = new PrintableException($entity->getErrors());

        return static::fromMessageAndErrorCode(
            message: $printableException->getMessage(),
            errorCode: static::errorCode() . '.' . $printableException->getCode(),
            params: $entity->getErrors(),
            context: $printableException,
        );
    }
}
