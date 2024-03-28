<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\Exception;

use olml89\XenforoSubscriptions\XF\Mvc\Reply\InternalServerErrorException;
use Throwable;
use XF\Mvc\Entity\Entity;

abstract class EntityStorageException extends InternalServerErrorException
{
    public static function entity(Entity $entity, ?Throwable $exception = null): static
    {
        return static::fromMessageAndErrorCode(
            message: sprintf(
                'Error trying to save \'%s\' with id \'%s\'',
                $entity::class,
                $entity->getEntityId(),
            ),
            errorCode: static::errorCode(),
            context: $exception,
        );
    }
}
