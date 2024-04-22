<?php declare(strict_types=1);

namespace olml89\XenforoBots\Exception;

use olml89\XenforoBots\Entity\Bot;
use olml89\XenforoBots\XF\Mvc\Reply\ConflictException;

final class BotAlreadyExistsException extends ConflictException
{
    protected static function errorCode(): string
    {
        return 'bot.already_exists';
    }

    public static function bot(Bot $bot): self
    {
        return self::fromMessageAndErrorCode(
            message: 'Bot already exists',
            errorCode: self::errorCode(),
            params: [
                'bot_id' => $bot->bot_id,
            ],
        );
    }
}