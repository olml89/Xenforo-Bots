<?php declare(strict_types=1);

namespace olml89\XenforoBots\Exception;

final class BotSubscriptionStorageException extends EntityStorageException
{
    protected static function errorCode(): string
    {
        return 'botSubscription.storage';
    }
}
