<?php declare(strict_types=1);

namespace olml89\XenforoBots\Exception;

use olml89\XenforoBots\Entity\BotSubscription;
use olml89\XenforoBots\XF\Mvc\Reply\ConflictException;

final class BotSubscriptionAlreadyExistsException extends ConflictException
{
    protected static function errorCode(): string
    {
        return 'botSubscription.already_exists';
    }

    public static function alreadySubscribed(BotSubscription $botSubscription): self
    {
        return self::fromMessageAndErrorCode(
            message: sprintf(
                'BotSubscription \'%s\' is already subscribed to Bot \'%s\'',
                $botSubscription->bot_subscription_id,
                $botSubscription->bot_id,
            ),
            errorCode: self::errorCode(),
        );
    }

    public static function sameWebhook(BotSubscription $botSubscription): self
    {
        return self::fromMessageAndErrorCode(
            message: sprintf(
                'Bot \'%s\' already has a BotSubscription to the webhook \'%s\'',
                $botSubscription->bot_id,
                $botSubscription->webhook,
            ),
            errorCode: self::errorCode(),
        );
    }
}
