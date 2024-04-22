<?php declare(strict_types=1);

namespace olml89\XenforoBots\Exception;

use olml89\XenforoBots\Entity\Bot;
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
            message: 'BotSubscription is already subscribed to another Bot',
            errorCode: self::errorCode(),
            params: [
                'bot_id' => $botSubscription->bot_id,
                'bot_subscription_id' => $botSubscription->bot_subscription_id,
            ]
        );
    }

    public static function sameWebhook(Bot $bot, BotSubscription $botSubscription): self
    {
        return self::fromMessageAndErrorCode(
            message: 'Bot already has a BotSubscription to this webhook',
            errorCode: self::errorCode(),
            params: [
                'bot_id' => $bot->bot_id,
                'webhook' => $botSubscription->webhook,
            ]
        );
    }
}
