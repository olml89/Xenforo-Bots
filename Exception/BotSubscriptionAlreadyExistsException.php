<?php declare(strict_types=1);

namespace olml89\XenforoBots\Exception;

use olml89\XenforoBots\Entity\Bot;
use olml89\XenforoBots\Entity\BotSubscription;
use olml89\XenforoBots\XF\Mvc\Reply\ConflictException;
use XF\Api\ErrorMessage;

final class BotSubscriptionAlreadyExistsException extends ConflictException
{
    public function __construct(Bot $bot, BotSubscription $botSubscription)
    {
        $apiErrorMessage = new ErrorMessage(
            message: sprintf(
                'Bot \'%s\' already has a BotSubscription to the webhook \'%s\'',
                $bot->bot_id,
                $botSubscription->webhook,
            ),
            code: self::errorCode(),
        );

        parent::__construct($apiErrorMessage);
    }

    protected static function errorCode(): string
    {
        return 'botSubscription.storage.already_exists';
    }
}
