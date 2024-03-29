<?php declare(strict_types=1);

namespace olml89\XenforoBots\Exception;

final class ApiKeyStorageException extends EntityStorageException
{
    protected static function errorCode(): string
    {
        return 'api_key.storage';
    }
}
