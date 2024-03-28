<?php declare(strict_types=1);

namespace olml89\XenforoBots\Exception;

final class BotStorageException extends EntityStorageException
{
    protected static function errorCode(): string
    {
        return 'bot.storage';
    }
}
