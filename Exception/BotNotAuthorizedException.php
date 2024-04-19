<?php declare(strict_types=1);

namespace olml89\XenforoBots\Exception;

use olml89\XenforoBots\Entity\Bot;
use olml89\XenforoBots\XF\Mvc\Reply\ForbiddenException;

final class BotNotAuthorizedException extends ForbiddenException
{
    public static function notAllowed(Bot $bot): self
    {
        return self::fromMessageAndErrorCode(
            message: sprintf(
                'Bot \'%s\' cannot perform this action on this resource',
                $bot->User->username,
            ),
            errorCode: 'bot.unauthorized',
            params: [
                'bot_id' => $bot->bot_id,
            ]
        );
    }
}
