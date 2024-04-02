<?php declare(strict_types=1);

namespace olml89\XenforoBots\Exception;

final class BotSubscriptionValidationException extends EntityValidationException
{
    protected static function errorCode(): string
    {
        return 'botSubscription.validation';
    }
}
