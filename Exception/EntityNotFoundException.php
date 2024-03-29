<?php declare(strict_types=1);

namespace olml89\XenforoBots\Exception;

use olml89\XenforoBots\XF\Mvc\Reply\NotFoundException;

abstract class EntityNotFoundException extends NotFoundException
{
    abstract protected static function entityClass(): string;

    public static function id(int|string $id): static
    {
        return static::fromMessageAndErrorCode(
            message: sprintf(
                'Entity \'%s\' with id \'%s\' not found',
                static::entityClass(),
                $id,
            ),
            errorCode: static::errorCode(),
        );
    }
}
