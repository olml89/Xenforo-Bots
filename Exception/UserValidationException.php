<?php declare(strict_types=1);

namespace olml89\XenforoBots\Exception;

final class UserValidationException extends EntityValidationException
{
    protected static function errorCode(): string
    {
        return 'user.validation';
    }
}
