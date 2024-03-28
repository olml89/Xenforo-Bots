<?php declare(strict_types=1);

namespace olml89\XenforoBots\Exception;

final class UserStorageException extends EntityStorageException
{
    protected static function errorCode(): string
    {
        return 'user.storage';
    }
}
