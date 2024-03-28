<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\Exception;

final class ApiKeyCreationException extends EntityValidationException
{
    protected static function errorCode(): string
    {
        return 'api_key.creation';
    }
}
