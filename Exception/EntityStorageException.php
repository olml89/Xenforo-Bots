<?php declare(strict_types=1);

namespace olml89\XenforoBots\Exception;

use olml89\XenforoBots\XF\Mvc\Reply\InternalServerErrorException;
use Throwable;
use XF\Mvc\Entity\Entity;

abstract class EntityStorageException extends InternalServerErrorException
{
    public static function entity(Entity $entity, ?Throwable $context = null): static
    {
        return static::fromMessageAndErrorCode(
            message: 'Error trying to save entity',
            errorCode: static::errorCode(),
            params: [
                'entity' => $entity::class,
                'id' => $entity->getEntityId(),
            ],
            context: $context,
        );
    }
}
