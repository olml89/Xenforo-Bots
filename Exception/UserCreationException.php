<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\Exception;

final class UserCreationException extends EntityValidationException
{
    protected static function errorCode(): string
    {
        return 'user.creation';
    }
}
