<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\Exception;

final class BotRemovalException extends EntityRemovalException
{
    protected static function errorCode(): string
    {
        return 'bot.removal';
    }
}
