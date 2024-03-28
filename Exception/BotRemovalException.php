<?php declare(strict_types=1);

namespace olml89\XenforoBots\Exception;

final class BotRemovalException extends EntityRemovalException
{
    protected static function errorCode(): string
    {
        return 'bot.removal';
    }
}
