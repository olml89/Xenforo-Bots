<?php declare(strict_types=1);

namespace olml89\XenforoBots\Exception;

final class ApiKeyValidationException extends EntityValidationException
{
    protected static function errorCode(): string
    {
        return 'api_key.creation';
    }
}
