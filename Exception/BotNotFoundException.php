<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions\Exception;

use olml89\XenforoSubscriptions\Entity\Bot;

final class BotNotFoundException extends EntityNotFoundException
{
    protected static function errorCode(): string
    {
        return 'bot.not_found';
    }

    protected static function entityClass(): string
    {
        return Bot::class;
    }
}
