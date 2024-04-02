<?php declare(strict_types=1);

namespace olml89\XenforoBots\Exception;

final class BotSubscriptionRemovalException extends EntityRemovalException
{
    protected static function errorCode(): string
    {
        return 'botSubscription.removal';
    }
}
