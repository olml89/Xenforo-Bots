<?php declare(strict_types=1);

namespace olml89\XenforoBots\Exception;

use olml89\XenforoBots\Entity\Bot;
use olml89\XenforoBots\Entity\BotSubscription;
use olml89\XenforoBots\XF\Mvc\Reply\ForbiddenException;

final class BotNotAuthorizedException extends ForbiddenException
{
    public static function notAllowed(Bot $bot): self
    {
        return self::fromMessageAndErrorCode(
            message: 'Authorized Bot is trying to access another Bot\'s realm',
            errorCode: 'bot.unauthorized',
            params: [
                'bot_id' => $bot->bot_id,
            ]
        );
    }

    public static function doesNotOwn(Bot $bot, BotSubscription $botSubscription): self
    {
        return self::fromMessageAndErrorCode(
            message: 'Bot does not own this BotSubscription',
            errorCode: 'bot.unauthorized',
            params: [
                'bot_id' => $bot->bot_id,
                'bot_subscription_id' => $botSubscription->bot_subscription_id,
            ]
        );
    }
}
