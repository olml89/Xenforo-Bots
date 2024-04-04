<?php declare(strict_types=1);

namespace olml89\XenforoBots\Exception;

use olml89\XenforoBots\XF\Entity\UserAlert;

final class UserAlertNotFoundException extends EntityNotFoundException
{
    protected static function errorCode(): string
    {
        return 'userAlert.retrieval.not_found';
    }

    protected static function entityClass(): string
    {
        return UserAlert::class;
    }
}
